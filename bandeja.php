<?php
session_start();

// Verificar si está logueado
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

include "conectar.inc.php";

$usuario = $_SESSION["usuario"];
$rol = $_SESSION["rol"];

// Obtener trámites según el rol del usuario
if ($rol == 'alumno') {
    // Para alumnos: sus propios trámites pendientes y finalizados
    $sql = "SELECT fs.*, fp.rol as rol_proceso, fp.descripcion, s.tipo_solicitud, s.estado as estado_solicitud
            FROM flujoseguimiento fs 
            JOIN flujoproceso fp ON fs.flujo = fp.flujo AND fs.proceso = fp.proceso 
            LEFT JOIN solicitudes s ON fs.nrotramite = s.nrotramite
            WHERE fs.usuario = ? AND fs.fecha_fin IS NULL
            ORDER BY fs.fecha_inicio DESC";
    $params = [$usuario];
} else {
    // Para secretaria y kardex: trámites pendientes de su rol
    $sql = "SELECT fs.*, fp.rol as rol_proceso, fp.descripcion, s.tipo_solicitud, s.estado as estado_solicitud
            FROM flujoseguimiento fs 
            JOIN flujoproceso fp ON fs.flujo = fp.flujo AND fs.proceso = fp.proceso 
            LEFT JOIN solicitudes s ON fs.nrotramite = s.nrotramite
            WHERE fp.rol = ? AND fs.fecha_fin IS NULL
            ORDER BY fs.fecha_inicio DESC";
    $params = [$rol];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Para alumnos: obtener certificados emitidos
$certificados_emitidos = [];
if ($rol == 'alumno') {
    $sql_cert = "SELECT c.*, s.fecha_solicitud 
                 FROM certificados c 
                 LEFT JOIN solicitudes s ON c.nrotramite = s.nrotramite
                 WHERE c.usuario_estudiante = ? 
                 ORDER BY c.fecha_emision DESC";
    $stmt_cert = $pdo->prepare($sql_cert);
    $stmt_cert->execute([$usuario]);
    $certificados_emitidos = $stmt_cert->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bandeja de Trámites - Workflow FCPN</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .flujo-f1 { background-color: #e7f3ff; }
        .flujo-f2 { background-color: #f0fff0; }
        .header-info { background-color: #f8f9fa; padding: 10px; margin: 10px 0; }
        .proceso-info { font-size: 0.9em; color: #666; }
        .certificado-emitido { background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn-certificado { background-color: #28a745; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; }
        .btn-certificado:hover { background-color: #218838; }
    </style>
</head>
<body>
    <h2>Sistema de Workflow - FCPN</h2>
    <div class="header-info">
        <table>
            <tr>
                <td><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION["usuario"]); ?></td>
                <td><strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION["rol"]); ?></td>
                <td><a href="logout.php">Cerrar Sesión</a></td>
            </tr>
        </table>
    </div>
    
    <?php if ($rol == 'alumno' && !empty($certificados_emitidos)): ?>
    <h3>🎓 Mis Certificados Emitidos</h3>
    <?php foreach ($certificados_emitidos as $cert): ?>
    <div class="certificado-emitido">
        <h4>✅ Certificado de Notas Aprobado</h4>
        <p><strong>Número:</strong> <?php echo htmlspecialchars($cert['nro_certificado']); ?></p>
        <p><strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars($cert['fecha_emision']); ?></p>
        <p><strong>Tipo:</strong> <?php echo htmlspecialchars($cert['tipo_certificado']); ?></p>
        <p><strong>Estado:</strong> <?php echo htmlspecialchars($cert['estado']); ?></p>
        <a href="ver_certificado.php?id=<?php echo $cert['id']; ?>" class="btn-certificado">📄 Ver Certificado de Notas</a>
    </div>
    <?php endforeach; ?>
    <br>
    <?php endif; ?>
    
    <h3>📋 Trámites Pendientes</h3>
    <table>
        <tr>
            <th>Nro. Trámite</th>
            <th>Flujo-Proceso</th>
            <th>Descripción</th>
            <th>Tipo Solicitud</th>
            <th>Usuario Actual</th>
            <th>Fecha Inicio</th>
            <th>Estado</th>
            <th>Operaciones</th>
        </tr>
        <?php
        $hay_tramites = false;
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hay_tramites = true;
            $clase_flujo = ($fila["flujo"] == 'F1') ? 'flujo-f1' : 'flujo-f2';
            echo "<tr class='$clase_flujo'>";
            echo "<td>" . htmlspecialchars($fila["nrotramite"]) . "</td>";
            echo "<td><strong>" . htmlspecialchars($fila["flujo"]) . "-" . htmlspecialchars($fila["proceso"]) . "</strong></td>";
            echo "<td>" . htmlspecialchars($fila["descripcion"]) . "<br><span class='proceso-info'>Flujo: " . ($fila["flujo"] == 'F1' ? 'Solicitud y Revisión' : 'Verificación y Emisión') . "</span></td>";
            echo "<td>" . htmlspecialchars($fila["tipo_solicitud"]) . "</td>";
            echo "<td>" . htmlspecialchars($fila["usuario"]) . "</td>";
            echo "<td>" . htmlspecialchars($fila["fecha_inicio"]) . "</td>";
            echo "<td>" . htmlspecialchars($fila["estado_solicitud"]) . "</td>";
            echo "<td><a href='flujo.php?flujo=" . urlencode($fila["flujo"]) . 
                 "&proceso=" . urlencode($fila["proceso"]) . 
                 "&nrotramite=" . urlencode($fila["nrotramite"]) . "'>Procesar</a></td>";
            echo "</tr>";
        }
        
        if (!$hay_tramites) {
            echo "<tr><td colspan='8'>No hay trámites pendientes</td></tr>";
        }
        ?>
    </table>
    
    <?php if ($rol == 'alumno'): ?>
    <br>
    <h3>🆕 Iniciar Nuevo Trámite</h3>
    <table>
        <tr>
            <td><a href="iniciar_tramite.php" style="text-decoration: none; background-color: #007bff; color: white; padding: 10px 15px; border-radius: 5px;">📄 Solicitar Certificado de Notas</a></td>
            <td>Iniciar proceso completo de solicitud de certificado (Flujos F1 y F2)</td>
        </tr>
    </table>
    <?php endif; ?>
    
    <br>
    <h3>📚 Historial de Trámites Finalizados</h3>
    <?php
    // Mostrar trámites finalizados
    if ($rol == 'alumno') {
        $sql_hist = "SELECT fs.*, fp.descripcion, s.tipo_solicitud, s.estado 
                     FROM flujoseguimiento fs 
                     JOIN flujoproceso fp ON fs.flujo = fp.flujo AND fs.proceso = fp.proceso 
                     LEFT JOIN solicitudes s ON fs.nrotramite = s.nrotramite
                     WHERE fs.usuario = ? AND fs.fecha_fin IS NOT NULL AND fp.tipo = 'E'
                     ORDER BY fs.fecha_fin DESC LIMIT 10";
        $stmt_hist = $pdo->prepare($sql_hist);
        $stmt_hist->execute([$usuario]);
    } else {
        $sql_hist = "SELECT fs.*, fp.descripcion, s.tipo_solicitud, s.estado 
                     FROM flujoseguimiento fs 
                     JOIN flujoproceso fp ON fs.flujo = fp.flujo AND fs.proceso = fp.proceso 
                     LEFT JOIN solicitudes s ON fs.nrotramite = s.nrotramite
                     WHERE fp.rol = ? AND fs.fecha_fin IS NOT NULL AND fp.tipo = 'E'
                     ORDER BY fs.fecha_fin DESC LIMIT 10";
        $stmt_hist = $pdo->prepare($sql_hist);
        $stmt_hist->execute([$rol]);
    }
    ?>
    <table>
        <tr>
            <th>Nro. Trámite</th>
            <th>Flujo</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Estado Final</th>
            <th>Fecha Finalización</th>
        </tr>
        <?php
        $hay_hist = false;
        while ($hist = $stmt_hist->fetch(PDO::FETCH_ASSOC)) {
            $hay_hist = true;
            echo "<tr>";
            echo "<td>" . htmlspecialchars($hist["nrotramite"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["flujo"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["tipo_solicitud"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["descripcion"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["estado"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["fecha_fin"]) . "</td>";
            echo "</tr>";
        }
        
        if (!$hay_hist) {
            echo "<tr><td colspan='6'>No hay trámites finalizados</td></tr>";
        }
        ?>
    </table>
    
    <br>
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
        <h4>📖 Información del Sistema de Workflow</h4>
        <p><strong>FLUJO F1 - Solicitud y Revisión (3 procesos):</strong></p>
        <ul>
            <li>P1: Alumno solicita certificado</li>
            <li>P2: Secretaria recibe documentos</li>
            <li>P3: Secretaria evalúa completitud → SI: pasa a F2 | NO: rechaza</li>
        </ul>
        <p><strong>FLUJO F2 - Verificación y Emisión (2 procesos):</strong></p>
        <ul>
            <li>P1: Kardex verifica datos académicos</li>
            <li>P2: Kardex emite certificado (FINAL)</li>
        </ul>
    </div>
</body>
</html>