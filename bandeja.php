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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bandeja de Trámites - Workflow FCPN</title>
</head>
<body>
    <h2>Sistema de Workflow - FCPN</h2>
    <table border="1">
        <tr>
            <td><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION["usuario"]); ?></td>
            <td><strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION["rol"]); ?></td>
            <td><a href="logout.php">Cerrar Sesión</a></td>
        </tr>
    </table>
    
    <h3>Trámites Pendientes</h3>
    <table border="1" width="100%">
        <tr>
            <th>Nro. Trámite</th>
            <th>Flujo</th>
            <th>Proceso</th>
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
            echo "<tr>";
            echo "<td>" . htmlspecialchars($fila["nrotramite"]) . "</td>";
            echo "<td>" . htmlspecialchars($fila["flujo"]) . "</td>";
            echo "<td>" . htmlspecialchars($fila["proceso"]) . "</td>";
            echo "<td>" . htmlspecialchars($fila["descripcion"]) . "</td>";
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
            echo "<tr><td colspan='9'>No hay trámites pendientes</td></tr>";
        }
        ?>
    </table>
    
    <?php if ($rol == 'alumno'): ?>
    <br>
    <h3>Iniciar Nuevo Trámite</h3>
    <table border="1">
        <tr>
            <td><a href="iniciar_tramite.php">Solicitar Certificado de Notas</a></td>
            <td>Iniciar proceso de solicitud de certificado</td>
        </tr>
    </table>
    <?php endif; ?>
    
    <br>
    <h3>Historial de Trámites Finalizados</h3>
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
    <table border="1" width="100%">
        <tr>
            <th>Nro. Trámite</th>
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
            echo "<td>" . htmlspecialchars($hist["tipo_solicitud"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["descripcion"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["estado"]) . "</td>";
            echo "<td>" . htmlspecialchars($hist["fecha_fin"]) . "</td>";
            echo "</tr>";
        }
        
        if (!$hay_hist) {
            echo "<tr><td colspan='5'>No hay trámites finalizados</td></tr>";
        }
        ?>
    </table>
</body>
</html>