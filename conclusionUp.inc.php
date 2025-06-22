<?php
// conclusionUp.inc.php
if (isset($_GET["nro_certificado"])) {
    $nro_certificado = $_GET["nro_certificado"] ?? '';
    $fecha_emision = $_GET["fecha_emision"] ?? date('Y-m-d');
    $tipo_certificado = $_GET["tipo_certificado"] ?? 'notas';
    $observaciones_finales = $_GET["observaciones_finales"] ?? '';
    $nrotramite = $_GET["nrotramite"] ?? '';
    
    // Obtener datos del estudiante del trámite
    $sql_tramite = "SELECT usuario_creador FROM tramites WHERE nrotramite = ?";
    $stmt_tramite = $pdo->prepare($sql_tramite);
    $stmt_tramite->execute([$nrotramite]);
    $tramite_data = $stmt_tramite->fetch(PDO::FETCH_ASSOC);
    
    if ($tramite_data) {
        $usuario_estudiante = $tramite_data['usuario_creador'];
        
        // Guardar certificado en la base de datos
        $sql_cert = "INSERT INTO certificados (nrotramite, nro_certificado, fecha_emision, tipo_certificado, usuario_estudiante, observaciones_finales, estado) 
                     VALUES (?, ?, ?, ?, ?, ?, 'emitido')";
        $stmt_cert = $pdo->prepare($sql_cert);
        $stmt_cert->execute([$nrotramite, $nro_certificado, $fecha_emision, $tipo_certificado, $usuario_estudiante, $observaciones_finales]);
        
        // Actualizar estado de la solicitud
        $sql_update = "UPDATE solicitudes SET estado = 'completado' WHERE nrotramite = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nrotramite]);
    }
    
    // Guardar datos del certificado en sesión
    session_start();
    $_SESSION["nro_certificado"] = $nro_certificado;
    $_SESSION["fecha_emision"] = $fecha_emision;
    $_SESSION["tipo_certificado"] = $tipo_certificado;
    $_SESSION["observaciones_finales"] = $observaciones_finales;
    $_SESSION["certificado_emitido"] = true;
}
?>