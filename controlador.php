<?php
session_start();
include "conectar.inc.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$pantalla = $_GET["pantalla"] ?? '';
$flujo = $_GET["flujo"] ?? '';
$proceso = $_GET["proceso"] ?? '';
$nrotramite = $_GET["nrotramite"] ?? '';
$tipo = $_GET["tipo"] ?? '';
$accion = '';

// Determinar qué botón se presionó
if (isset($_GET["Siguiente"])) {
    $accion = "Siguiente";
} elseif (isset($_GET["Anterior"])) {
    $accion = "Anterior";
}

// Procesar el archivo Up correspondiente si existe
$archivo_up = $pantalla . "Up.inc.php";
if (file_exists($archivo_up)) {
    include $archivo_up;
}

if ($accion == "Siguiente") {
    // Finalizar el proceso actual
    $sql_fin = "UPDATE flujoseguimiento SET fecha_fin = CURRENT_TIMESTAMP WHERE nrotramite = ? AND flujo = ? AND proceso = ? AND fecha_fin IS NULL";
    $stmt_fin = $pdo->prepare($sql_fin);
    $stmt_fin->execute([$nrotramite, $flujo, $proceso]);
    
    if ($tipo == 'Q') {
        // Es una pregunta, determinar el siguiente proceso
        $sql_pregunta = "SELECT si, no FROM flujoprocesopregunta WHERE flujo = ? AND proceso = ?";
        $stmt_pregunta = $pdo->prepare($sql_pregunta);
        $stmt_pregunta->execute([$flujo, $proceso]);
        $pregunta = $stmt_pregunta->fetch(PDO::FETCH_ASSOC);
        
        if ($pregunta) {
            $respuesta = $_GET["respuesta"] ?? 'no';
            $siguiente = ($respuesta == 'si') ? $pregunta["si"] : $pregunta["no"];
            
            // Verificar si el siguiente proceso está en otro flujo
            if (strpos($siguiente, 'F') === 0 && strpos($siguiente, 'P') !== false) {
                // Formato F2P1 -> Flujo F2, Proceso P1
                $nuevo_flujo = substr($siguiente, 0, 2);
                $nuevo_proceso = substr($siguiente, 2);
            } else {
                $nuevo_flujo = $flujo;
                $nuevo_proceso = $siguiente;
            }
            
            if ($nuevo_proceso) {
                // Crear nuevo registro en flujoseguimiento
                $sql_nuevo = "INSERT INTO flujoseguimiento (nrotramite, flujo, proceso, usuario, fecha_inicio) 
                             SELECT ?, ?, ?, fp.rol, CURRENT_TIMESTAMP 
                             FROM flujoproceso fp 
                             WHERE fp.flujo = ? AND fp.proceso = ?";
                $stmt_nuevo = $pdo->prepare($sql_nuevo);
                $stmt_nuevo->execute([$nrotramite, $nuevo_flujo, $nuevo_proceso, $nuevo_flujo, $nuevo_proceso]);
            }
        }
    } elseif ($tipo == 'P') {
        // Proceso normal, buscar el siguiente
        $sql_siguiente = "SELECT siguiente FROM flujoproceso WHERE flujo = ? AND proceso = ?";
        $stmt_siguiente = $pdo->prepare($sql_siguiente);
        $stmt_siguiente->execute([$flujo, $proceso]);
        $resultado = $stmt_siguiente->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado && $resultado["siguiente"]) {
            $siguiente = $resultado["siguiente"];
            
            // Crear nuevo registro en flujoseguimiento
            $sql_nuevo = "INSERT INTO flujoseguimiento (nrotramite, flujo, proceso, usuario, fecha_inicio) 
                         SELECT ?, ?, ?, fp.rol, CURRENT_TIMESTAMP 
                         FROM flujoproceso fp 
                         WHERE fp.flujo = ? AND fp.proceso = ?";
            $stmt_nuevo = $pdo->prepare($sql_nuevo);
            $stmt_nuevo->execute([$nrotramite, $flujo, $siguiente, $flujo, $siguiente]);
        }
    }
    // Si es tipo 'E' (End), no hay siguiente proceso
}

// Redirigir a la bandeja
header("Location: bandeja.php");
exit();
?>