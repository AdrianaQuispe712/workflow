<?php
if (isset($_GET["respuesta"])) {
    $respuesta = $_GET["respuesta"];
    $observaciones = $_GET["observaciones_decision"] ?? '';
    
    // Guardar la decisión en sesión
    $_SESSION["decision_secretaria"] = $respuesta;
    $_SESSION["observaciones_secretaria"] = $observaciones;
    
    // Actualizar el estado de la solicitud
    $nrotramite = $_GET["nrotramite"] ?? '';
    if ($nrotramite) {
        $estado = ($respuesta == 'si') ? 'en_proceso' : 'rechazado';
        $sql = "UPDATE solicitudes SET estado = ? WHERE nrotramite = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$estado, $nrotramite]);
    }
}
?>