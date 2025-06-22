<?php
session_start();

// Verificar si está logueado y es alumno
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != 'alumno') {
    header("Location: login.php");
    exit();
}

include "conectar.inc.php";

$certificado_id = $_GET["id"] ?? '';
$usuario = $_SESSION["usuario"];

// Obtener datos del certificado
$sql_cert = "SELECT c.*, a.nombre, a.paterno, a.materno, a.ci, a.codigo_sis, a.carrera 
             FROM certificados c
             LEFT JOIN academico.alumno a ON c.usuario_estudiante = 'msilva'
             WHERE c.id = ? AND c.usuario_estudiante = ?";
$stmt_cert = $pdo->prepare($sql_cert);
$stmt_cert->execute([$certificado_id, $usuario]);
$certificado = $stmt_cert->fetch(PDO::FETCH_ASSOC);

if (!$certificado) {
    die("Certificado no encontrado o no tiene permisos para verlo.");
}

// Obtener notas del estudiante
$sql_notas = "SELECT * FROM academico.notas WHERE codigo_sis = ? ORDER BY gestion, periodo, materia";
$stmt_notas = $pdo->prepare($sql_notas);
$stmt_notas->execute([$certificado['codigo_sis']]);
$notas = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas
$total_materias = count($notas);
$suma_notas = array_sum(array_column($notas, 'nota'));
$promedio = $total_materias > 0 ? round($suma_notas / $total_materias, 2) : 0;
$total_creditos = array_sum(array_column($notas, 'creditos'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Certificado de Notas - <?php echo htmlspecialchars($certificado['nro_certificado']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .certificado { background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 3px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #007bff; }
        .universidad { font-size: 18px; margin: 10px 0; }
        .facultad { font-size: 16px; color: #666; }
        .titulo-cert { font-size: 20px; font-weight: bold; text-align: center; margin: 30px 0; color: #333; }
        .datos-estudiante { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .tabla-notas { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .tabla-notas th, .tabla-notas td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .tabla-notas th { background-color: #007bff; color: white; }
        .tabla-notas tr:nth-child(even) { background-color: #f2f2f2; }
        .resumen { background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .firma-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .firma { text-align: center; width: 200px; }
        .linea-firma { border-top: 1px solid #333; margin-top: 50px; }
        .btn-imprimir { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px; }
        .btn-volver { background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px; }
        .no-print { margin: 20px 0; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
            .certificado { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="certificado">
        <div class="header">
            <div class="logo">🎓 UNIVERSIDAD MAYOR DE SAN ANDRÉS</div>
            <div class="universidad">FACULTAD DE CIENCIAS PURAS Y NATURALES</div>
            <div class="facultad">KARDEX ACADÉMICO</div>
        </div>
        
        <div class="titulo-cert">
            📜 CERTIFICADO DE NOTAS
        </div>
        
        <div style="text-align: right; margin: 20px 0;">
            <strong>Nro. Certificado:</strong> <?php echo htmlspecialchars($certificado['nro_certificado']); ?><br>
            <strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars($certificado['fecha_emision']); ?>
        </div>
        
        <div class="datos-estudiante">
            <h3>📋 DATOS DEL ESTUDIANTE</h3>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; padding: 5px;"><strong>Nombres y Apellidos:</strong></td>
                    <td style="border: none; padding: 5px;"><?php echo htmlspecialchars($certificado['nombre'] . ' ' . $certificado['paterno'] . ' ' . $certificado['materno']); ?></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px;"><strong>Carnet de Identidad:</strong></td>
                    <td style="border: none; padding: 5px;"><?php echo htmlspecialchars($certificado['ci']); ?></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px;"><strong>Código SIS:</strong></td>
                    <td style="border: none; padding: 5px;"><?php echo htmlspecialchars($certificado['codigo_sis']); ?></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px;"><strong>Carrera:</strong></td>
                    <td style="border: none; padding: 5px;"><?php echo htmlspecialchars($certificado['carrera']); ?></td>
                </tr>
            </table>
        </div>
        
        <h3>📊 RECORD ACADÉMICO</h3>
        <table class="tabla-notas">
            <thead>
                <tr>
                    <th>Sigla</th>
                    <th>Materia</th>
                    <th>Gestión</th>
                    <th>Período</th>
                    <th>Nota</th>
                    <th>Créditos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notas as $nota): ?>
                <tr>
                    <td><?php echo htmlspecialchars($nota['sigla']); ?></td>
                    <td><?php echo htmlspecialchars($nota['materia']); ?></td>
                    <td><?php echo htmlspecialchars($nota['gestion']); ?></td>
                    <td><?php echo htmlspecialchars($nota['periodo']); ?></td>
                    <td style="text-align: center; font-weight: bold; <?php echo $nota['nota'] >= 51 ? 'color: #28a745;' : 'color: #dc3545;'; ?>">
                        <?php echo htmlspecialchars($nota['nota']); ?>
                    </td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($nota['creditos']); ?></td>
                    <td style="text-align: center;">
                        <span style="<?php echo $nota['estado'] == 'aprobado' ? 'color: #28a745;' : 'color: #dc3545;'; ?>">
                            <?php echo ucfirst(htmlspecialchars($nota['estado'])); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="resumen">
            <h4>📈 RESUMEN ACADÉMICO</h4>
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <strong>Total de Materias:</strong> <?php echo $total_materias; ?><br>
                    <strong>Total de Créditos:</strong> <?php echo $total_creditos; ?>
                </div>
                <div>
                    <strong>Promedio General:</strong> <span style="font-size: 18px; color: #007bff;"><?php echo $promedio; ?></span><br>
                    <strong>Estado:</strong> <span style="color: #28a745;">Regular</span>
                </div>
            </div>
        </div>
        
        <?php if ($certificado['observaciones_finales']): ?>
        <div style="margin: 20px 0;">
            <strong>Observaciones:</strong><br>
            <?php echo nl2br(htmlspecialchars($certificado['observaciones_finales'])); ?>
        </div>
        <?php endif; ?>
        
        <div class="firma-section">
            <div class="firma">
                <div class="linea-firma"></div>
                <p><strong>KARDEX ACADÉMICO</strong><br>
                Facultad de Ciencias Puras y Naturales</p>
            </div>
            <div class="firma">
                <div class="linea-firma"></div>
                <p><strong>SECRETARÍA ACADÉMICA</strong><br>
                Facultad de Ciencias Puras y Naturales</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
            <p>Este certificado es válido únicamente con el sello y firma correspondiente.<br>
            Emitido el <?php echo date('d/m/Y H:i:s'); ?> - La Paz, Bolivia</p>
        </div>
    </div>
    
    <div class="no-print">
        <button onclick="window.print()" class="btn-imprimir">🖨️ Imprimir Certificado</button>
        <a href="bandeja.php" class="btn-volver">🏠 Volver a la Bandeja</a>
    </div>
</body>
</html>