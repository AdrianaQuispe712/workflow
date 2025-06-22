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
    
    // Para compatibilidad con el código existente que usa mysqli
    // Creamos funciones de compatibilidad
    function pg_query_compat($query) {
        global $pdo;
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            echo "Error en consulta: " . $e->getMessage();
            return false;
        }
    }
    
    function pg_fetch_array_compat($result) {
        if ($result && $result instanceof PDOStatement) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    die();
}
?>