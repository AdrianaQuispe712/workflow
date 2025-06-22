<?php
session_start();

// Verificar si está logueado
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

include "conectar.inc.php";

$flujo = $_GET["flujo"];
$proceso = $_GET["proceso"];
$nrotramite = $_GET["nrotramite"];

$sql = "SELECT * FROM flujoproceso WHERE flujo = ? AND proceso = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$flujo, $proceso]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fila) {
    die("Proceso no encontrado");
}

// Verificar si el usuario tiene permisos para este proceso
$rol_usuario = $_SESSION["rol"];
$rol_proceso = $fila["rol"];

if ($rol_proceso && $rol_proceso != $rol_usuario) {
    die("No tiene permisos para acceder a este proceso");
}

$pantalla = $fila["pantalla"];
$tipo = $fila["tipo"];
$descripcion = $fila["descripcion"];

// Verificar si existe el archivo de pantalla
$archivo_pantalla = $pantalla . ".inc.php";
if (!file_exists($archivo_pantalla)) {
    die("Archivo de pantalla no encontrado: " . $archivo_pantalla);
}

// Determinar el nombre del flujo
$nombre_flujo = '';
if ($flujo == 'F1') {
    $nombre_flujo = 'Solicitud y Revisión';
} elseif ($flujo == 'F2') {
    $nombre_flujo = 'Verificación y Emisión';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Proceso: <?php echo htmlspecialchars($flujo . '-' . $proceso); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .proceso-info { background-color: #e7f3ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .flujo-f1 { border-left: 5px solid #007bff; }
        .flujo-f2 { border-left: 5px solid #28a745; }
        .botones { margin: 20px 0; }
        .btn { padding: 10px 15px; margin: 5px; text-decoration: none; border-radius: 5px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        input[type="submit"] { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .siguiente { background-color: #28a745; color: white; }
        .anterior { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h2>🔄 Sistema de Workflow - FCPN</h2>
        <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION["usuario"]); ?> | 
           <strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION["rol"]); ?></p>
    </div>
    
    <div class="proceso-info <?php echo ($flujo == 'F1') ? 'flujo-f1' : 'flujo-f2'; ?>">
        <h3>📍 Ubicación Actual en el Workflow</h3>
        <p><strong>Trámite:</strong> #<?php echo htmlspecialchars($nrotramite); ?></p>
        <p><strong>Flujo:</strong> <?php echo htmlspecialchars($flujo); ?> - <?php echo $nombre_flujo; ?></p>
        <p><strong>Proceso:</strong> <?php echo htmlspecialchars($proceso); ?></p>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($descripcion); ?></p>
        <p><strong>Tipo de Proceso:</strong> 
            <?php 
            switch($tipo) {
                case 'P': echo '🔄 Proceso Normal'; break;
                case 'Q': echo '❓ Proceso de Decisión'; break;
                case 'E': echo '🏁 Proceso Final'; break;
            }
            ?>
        </p>
    </div>
    
    <div style="border: 2px solid #dee2e6; padding: 20px; border-radius: 5px;">
        <form action="controlador.php" method="get">
            <?php include $archivo_pantalla; ?>
            
            <input type="hidden" value="<?php echo htmlspecialchars($pantalla); ?>" name="pantalla"/>
            <input type="hidden" value="<?php echo htmlspecialchars($flujo); ?>" name="flujo"/>
            <input type="hidden" value="<?php echo htmlspecialchars($proceso); ?>" name="proceso"/>
            <input type="hidden" value="<?php echo htmlspecialchars($nrotramite); ?>" name="nrotramite"/>
            <input type="hidden" value="<?php echo htmlspecialchars($tipo); ?>" name="tipo"/>
            
            <div class="botones">
                <hr>
                <input type="submit" value="⬅️ Anterior" name="Anterior" class="anterior"/>
                <input type="submit" value="➡️ Siguiente" name="Siguiente" class="siguiente"/>
            </div>
        </form>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="bandeja.php" class="btn btn-secondary">🏠 Volver a la Bandeja</a>
    </div>
    
    <div style="background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 20px; font-size: 0.9em;">
        <h4>📋 Mapa del Workflow Completo:</h4>
        <p><strong>F1 (Solicitud y Revisión):</strong> P1→P2→P3→[Decisión: P4 o F2-P1]</p>
        <p><strong>F2 (Verificación y Emisión):</strong> P1→P2 (Final)</p>
    </div>
</body>
</html>