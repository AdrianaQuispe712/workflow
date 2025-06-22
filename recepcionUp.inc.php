<?php
// recepcionUp.inc.php
if (isset($_GET["observaciones_secretaria"])) {
    $observaciones = $_GET["observaciones_secretaria"] ?? '';
    $fecha_recepcion = $_GET["fecha_recepcion"] ?? date('Y-m-d');
    $estado = $_GET["estado_recepcion"] ?? 'completo';
    
    // Aquí se podría guardar en una tabla de seguimiento
    // Por ahora solo almacenamos en sesión
    session_start();
    $_SESSION["observaciones_secretaria"] = $observaciones;
    $_SESSION["estado_recepcion"] = $estado;
}
?>