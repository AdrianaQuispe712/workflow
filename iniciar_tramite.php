<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != 'alumno') {
    header("Location: login.php");
    exit();
}

include "conectar.inc.php";

try {
    // Generar nuevo número de trámite
    $sql_tramite = "INSERT INTO tramites (usuario_creador) VALUES (?) RETURNING nrotramite";
    $stmt_tramite = $pdo->prepare($sql_tramite);
    $stmt_tramite->execute([$_SESSION["usuario"]]);
    $tramite = $stmt_tramite->fetch(PDO::FETCH_ASSOC);
    $nrotramite = $tramite["nrotramite"];
    
    // Crear solicitud
    $sql_solicitud = "INSERT INTO solicitudes (nrotramite, tipo_solicitud, usuario_solicita) VALUES (?, 'certificado_notas', ?)";
    $stmt_solicitud = $pdo->prepare($sql_solicitud);
    $stmt_solicitud->execute([$nrotramite, $_SESSION["usuario"]]);
    
    // Iniciar el primer proceso del flujo F1
    $sql_flujo = "INSERT INTO flujoseguimiento (nrotramite, flujo, proceso, usuario, fecha_inicio) VALUES (?, 'F1', 'P1', ?, CURRENT_TIMESTAMP)";
    $stmt_flujo = $pdo->prepare($sql_flujo);
    $stmt_flujo->execute([$nrotramite, $_SESSION["usuario"]]);
    
    // Redirigir al proceso
    header("Location: flujo.php?flujo=F1&proceso=P1&nrotramite=" . $nrotramite);
    exit();
    
} catch(PDOException $e) {
    echo "Error al iniciar trámite: " . $e->getMessage();
}
?>