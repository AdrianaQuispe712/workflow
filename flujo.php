<?php
session_start();

// Verificar si está logueado
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

include "conectar.inc.php";

$flujo = $_GET["flujo"];
$proceso = $_GET["proceso"];
$nrotramite = $_GET["nrotramite"];

$sql = "SELECT * FROM flujoproceso WHERE flujo = ? AND proceso = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$flujo, $proceso]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fila) {
    die("Proceso no encontrado");
}

// Verificar si el usuario tiene permisos para este proceso
$rol_usuario = $_SESSION["rol"];
$rol_proceso = $fila["rol"];

if ($rol_proceso && $rol_proceso != $rol_usuario) {
    die("No tiene permisos para acceder a este proceso");
}

$pantalla = $fila["pantalla"];
$tipo = $fila["tipo"];

// Verificar si existe el archivo de pantalla
$archivo_pantalla = $pantalla . ".inc.php";
if (!file_exists($archivo_pantalla)) {
    die("Archivo de pantalla no encontrado: " . $archivo_pantalla);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Proceso: <?php echo htmlspecialchars($proceso); ?></title>
</head>
<body>
    <h2>Sistema de Workflow</h2>
    <p>Flujo: <?php echo htmlspecialchars($flujo); ?> | Proceso: <?php echo htmlspecialchars($proceso); ?> | Trámite: <?php echo htmlspecialchars($nrotramite); ?></p>
    <p>Usuario: <?php echo htmlspecialchars($_SESSION["usuario"]); ?> | Rol: <?php echo htmlspecialchars($_SESSION["rol"]); ?></p>
    
    <div>
        <strong>Pantalla:</strong> <?php echo htmlspecialchars($pantalla); ?> | 
        <strong>Tipo:</strong> <?php echo htmlspecialchars($tipo); ?>
    </div>
    
    <div>
        <form action="controlador.php" method="get">
            <?php
            include $archivo_pantalla;
            ?>
            <br/>
            <input type="hidden" value="<?php echo htmlspecialchars($pantalla); ?>" name="pantalla"/>
            <input type="hidden" value="<?php echo htmlspecialchars($flujo); ?>" name="flujo"/>
            <input type="hidden" value="<?php echo htmlspecialchars($proceso); ?>" name="proceso"/>
            <input type="hidden" value="<?php echo htmlspecialchars($nrotramite); ?>" name="nrotramite"/>
            <input type="hidden" value="<?php echo htmlspecialchars($tipo); ?>" name="tipo"/>
            <br/>
            <input type="submit" value="Anterior" name="Anterior"/>
            <input type="submit" value="Siguiente" name="Siguiente"/>
            <br><br>
            <a href="bandeja.php">Volver a la Bandeja</a>
        </form>
    </div>
</body>
</html>