<?php
if (isset($_GET["nombre"]) && isset($_GET["paterno"])) {
    $nombre = $_GET["nombre"];
    $paterno = $_GET["paterno"];
    $materno = $_GET["materno"] ?? '';
    $ci = $_GET["ci"] ?? '';
    $motivo = $_GET["motivo"] ?? '';
    
    // Actualizar datos del alumno
    $sql = "UPDATE academico.alumno SET nombre = ?, paterno = ?, materno = ?, ci = ? WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $paterno, $materno, $ci]);
    
    // Registrar la solicitud (podrías crear una tabla para esto)
    // Por ahora solo actualizamos los datos del alumno
}
?>