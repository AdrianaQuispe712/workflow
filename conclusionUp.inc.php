<?php
// conclusionUp.inc.php
if (isset($_GET["nro_certificado"])) {
    $nro_certificado = $_GET["nro_certificado"] ?? '';
    $fecha_emision = $_GET["fecha_emision"] ?? date('Y-m-d');
    $tipo_certificado = $_GET["tipo_certificado"] ?? 'notas';
    $observaciones_finales = $_GET["observaciones_finales"] ?? '';
    
    // Guardar datos del certificado en sesión
    session_start();
    $_SESSION["nro_certificado"] = $nro_certificado;
    $_SESSION["fecha_emision"] = $fecha_emision;
    $_SESSION["tipo_certificado"] = $tipo_certificado;
    $_SESSION["observaciones_finales"] = $observaciones_finales;
    $_SESSION["certificado_emitido"] = true;
}
?>