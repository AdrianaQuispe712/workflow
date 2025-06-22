<?php
if (isset($_GET["respuesta"])) {
    $respuesta = $_GET["respuesta"];
    $justificacion = $_GET["justificacion"] ?? '';
    
    // Registrar la decisión tomada
    // Aquí podrías guardar en una tabla de decisiones
    // Por ahora solo almacenamos en sesión para usar en el controlador
    session_start();
    $_SESSION["decision"] = $respuesta;
    $_SESSION["justificacion"] = $justificacion;
}
?>