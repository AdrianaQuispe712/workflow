<?php
session_start();

// Si ya está logueado, redirigir a bandeja
if (isset($_SESSION["usuario"])) {
    header("Location: bandeja.php");
    exit();
}

include "conectar.inc.php";

$error = "";

if (isset($_POST["login"])) {
    $usuario = trim($_POST["usuario"]);
    $clave = trim($_POST["clave"]);
    
    if (!empty($usuario) && !empty($clave)) {
        $sql = "SELECT * FROM usuarios WHERE usuario = ? AND clave = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario, $clave]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION["usuario"] = $user["usuario"];
            $_SESSION["rol"] = $user["rol"];
            header("Location: bandeja.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    } else {
        $error = "Debe completar todos los campos";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistema de Workflow</title>
</head>
<body>
    <h2>Sistema de Workflow - Login</h2>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="post">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>
        
        <label>Contraseña:</label><br>
        <input type="password" name="clave" required><br><br>
        
        <input type="submit" name="login" value="Iniciar Sesión">
    </form>
    
    <br>
    <h3>Usuarios de prueba:</h3>
    <ul>
        <li><strong>msilva</strong> / 123456 (alumno)</li>
        <li><strong>edward</strong> / 123456 (secretaria)</li>
        <li><strong>zulema</strong> / 123456 (kardex)</li>
    </ul>
</body>
</html>