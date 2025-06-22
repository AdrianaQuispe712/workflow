<?php
// recepcionar2Up.inc.php
if (isset($_GET["codigo_estudiante"])) {
    $codigo_estudiante = $_GET["codigo_estudiante"] ?? '';
    $carrera = $_GET["carrera"] ?? '';
    $gestion_ingreso = $_GET["gestion_ingreso"] ?? '';
    $estado_academico = $_GET["estado_academico"] ?? 'activo';
    $observaciones_kardex = $_GET["observaciones_kardex"] ?? '';
    $fecha_verificacion = $_GET["fecha_verificacion"] ?? date('Y-m-d');
    
    // Guardar datos en sesión para usar en el siguiente proceso
    session_start();
    $_SESSION["codigo_estudiante"] = $codigo_estudiante;
    $_SESSION["carrera_verificada"] = $carrera;
    $_SESSION["gestion_ingreso"] = $gestion_ingreso;
    $_SESSION["estado_academico"] = $estado_academico;
    $_SESSION["observaciones_kardex"] = $observaciones_kardex;
    $_SESSION["fecha_verificacion"] = $fecha_verificacion;
}
?>