<?php
// Crear base de datos y tablas para PostgreSQL
$host = "localhost";
$port = "5432";
$dbname = "postgres";
$username = "postgres";
$password = "123456";

try {
    // Conexión inicial para crear la base de datos
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=postgres", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos work_insc
    $pdo->exec("DROP DATABASE IF EXISTS work_insc");
    $pdo->exec("CREATE DATABASE work_insc");
    echo "Base de datos 'work_insc' creada exitosamente.\n";
    
    // Reconectar a la nueva base de datos
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=work_insc", $username, $password);
    
    // Crear esquema academico
    $pdo->exec("CREATE SCHEMA IF NOT EXISTS academico");
    echo "Esquema 'academico' creado exitosamente.\n";
    
    // Crear tabla usuarios
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS usuarios (
        id SERIAL PRIMARY KEY,
        usuario VARCHAR(20),
        clave VARCHAR(20),
        rol VARCHAR(20)
    )";
    $pdo->exec($sql_usuarios);
    echo "Tabla 'usuarios' creada exitosamente.\n";
    
    // Crear tabla flujoproceso
    $sql_flujoproceso = "
    CREATE TABLE IF NOT EXISTS flujoproceso (
        flujo VARCHAR(3) NOT NULL,
        proceso VARCHAR(3) NOT NULL,
        siguiente VARCHAR(3),
        tipo VARCHAR(1) NOT NULL,
        rol VARCHAR(20),
        pantalla VARCHAR(30),
        descripcion VARCHAR(100),
        PRIMARY KEY (flujo, proceso)
    )";
    $pdo->exec($sql_flujoproceso);
    echo "Tabla 'flujoproceso' creada exitosamente.\n";
    
    // Crear tabla flujoprocesopregunta
    $sql_flujoprocesopregunta = "
    CREATE TABLE IF NOT EXISTS flujoprocesopregunta (
        flujo VARCHAR(3) NOT NULL,
        proceso VARCHAR(3) NOT NULL,
        si VARCHAR(3),
        no VARCHAR(3),
        PRIMARY KEY (flujo, proceso)
    )";
    $pdo->exec($sql_flujoprocesopregunta);
    echo "Tabla 'flujoprocesopregunta' creada exitosamente.\n";
    
    // Crear tabla flujoseguimiento
    $sql_flujoseguimiento = "
    CREATE TABLE IF NOT EXISTS flujoseguimiento (
        id SERIAL PRIMARY KEY,
        nrotramite INTEGER,
        flujo VARCHAR(3),
        proceso VARCHAR(3),
        usuario VARCHAR(20),
        fecha_inicio TIMESTAMP,
        fecha_fin TIMESTAMP,
        observaciones TEXT
    )";
    $pdo->exec($sql_flujoseguimiento);
    echo "Tabla 'flujoseguimiento' creada exitosamente.\n";
    
    // Crear tabla alumno en esquema academico
    $sql_alumno = "
    CREATE TABLE IF NOT EXISTS academico.alumno (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100),
        paterno VARCHAR(100),
        materno VARCHAR(100),
        ci VARCHAR(20),
        codigo_sis VARCHAR(20),
        carrera VARCHAR(100),
        estado VARCHAR(20) DEFAULT 'activo'
    )";
    $pdo->exec($sql_alumno);
    echo "Tabla 'academico.alumno' creada exitosamente.\n";
    
    // Crear tabla solicitudes
    $sql_solicitudes = "
    CREATE TABLE IF NOT EXISTS solicitudes (
        id SERIAL PRIMARY KEY,
        nrotramite INTEGER,
        tipo_solicitud VARCHAR(50),
        motivo TEXT,
        estado VARCHAR(20) DEFAULT 'pendiente',
        fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        usuario_solicita VARCHAR(20)
    )";
    $pdo->exec($sql_solicitudes);
    echo "Tabla 'solicitudes' creada exitosamente.\n";
    
    // Insertar usuarios
    $pdo->exec("INSERT INTO usuarios (usuario, clave, rol) VALUES ('msilva', '123456', 'alumno')");
    $pdo->exec("INSERT INTO usuarios (usuario, clave, rol) VALUES ('edward', '123456', 'secretaria')");
    $pdo->exec("INSERT INTO usuarios (usuario, clave, rol) VALUES ('zulema', '123456', 'kardex')");
    echo "Usuarios insertados exitosamente.\n";
    
    // Insertar alumno de prueba
    $pdo->exec("INSERT INTO academico.alumno (nombre, paterno, materno, ci, codigo_sis, carrera) 
               VALUES ('Mario', 'Silva', 'Perez', '12345678', 'SIS202412345', 'Ingeniería de Sistemas')");
    echo "Alumno de prueba insertado exitosamente.\n";
    
    // Flujo F1: Certificado de Notas (5 procesos)
    $procesos = [
        // Proceso 1: Estudiante solicita
        ['F1', 'P1', 'P2', 'P', 'alumno', 'nota', 'Solicitud del estudiante'],
        // Proceso 2: Secretaria recibe y revisa documentos
        ['F1', 'P2', 'P3', 'P', 'secretaria', 'recepcion', 'Recepción por secretaria'],
        // Proceso 3: Kardex verifica datos académicos
        ['F1', 'P3', 'P4', 'P', 'kardex', 'recepcionar2', 'Verificación en kardex'],
        // Proceso 4: Kardex evalúa si aprueba o rechaza
        ['F1', 'P4', null, 'Q', 'kardex', 'pregunta', 'Evaluación final'],
        // Proceso 5a: Si es rechazado - notifica rechazo
        ['F1', 'P5', null, 'E', 'alumno', 'rechazo', 'Notificación de rechazo'],
        // Proceso 5b: Si es aprobado - emite certificado
        ['F1', 'P6', null, 'E', 'kardex', 'conclusion', 'Emisión de certificado']
    ];
    
    foreach ($procesos as $proceso) {
        $stmt = $pdo->prepare("INSERT INTO flujoproceso (flujo, proceso, siguiente, tipo, rol, pantalla, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($proceso);
    }
    echo "Flujo de procesos insertado exitosamente.\n";
    
    // Insertar pregunta de decisión
    $pdo->exec("INSERT INTO flujoprocesopregunta (flujo, proceso, si, no) VALUES ('F1', 'P4', 'P6', 'P5')");
    echo "Pregunta de flujo insertada exitosamente.\n";
    
    echo "\n¡Base de datos configurada correctamente!\n";
    echo "Flujo F1 - Certificado de Notas:\n";
    echo "P1: Alumno solicita (alumno)\n";
    echo "P2: Secretaria recibe (secretaria)\n";
    echo "P3: Kardex verifica (kardex)\n";
    echo "P4: Kardex evalúa (kardex - pregunta)\n";
    echo "P5: Rechazo (alumno ve rechazo) o P6: Aprobación (kardex emite)\n";
    echo "\nAhora puedes ejecutar: php -S localhost:3000\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>