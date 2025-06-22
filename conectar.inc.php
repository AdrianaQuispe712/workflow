<?php
// Conexión a PostgreSQL
$host = "localhost";
$port = "5432";
$dbname = "work_insc";
$username = "postgres";
$password = "123456";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}
?>