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
    
    // Crear tabla tramites para generar números únicos
    $sql_tramites = "
    CREATE TABLE IF NOT EXISTS tramites (
        nrotramite SERIAL PRIMARY KEY,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        usuario_creador VARCHAR(20),
        estado VARCHAR(20) DEFAULT 'activo'
    )";
    $pdo->exec($sql_tramites);
    echo "Tabla 'tramites' creada exitosamente.\n";
    
    // Insertar usuarios
    $pdo->exec("INSERT INTO usuarios (usuario, clave, rol) VALUES ('msilva', '123456', 'alumno')");
    $pdo->exec("INSERT INTO usuarios (usuario, clave, rol) VALUES ('edward', '123456', 'secretaria')");
    $pdo->exec("INSERT INTO usuarios (usuario, clave, rol) VALUES ('zulema', '123456', 'kardex')");
    echo "Usuarios insertados exitosamente.\n";
    
    // Insertar alumno de prueba
    $pdo->exec("INSERT INTO academico.alumno (nombre, paterno, materno, ci, codigo_sis, carrera) 
               VALUES ('Mario', 'Silva', 'Perez', '12345678', 'SIS202412345', 'Ingeniería de Sistemas')");
    echo "Alumno de prueba insertado exitosamente.\n";
    
    // FLUJO F1: Solicitud y Revisión (3 procesos)
    $procesos_f1 = [
        // Proceso 1: Estudiante solicita certificado
        ['F1', 'P1', 'P2', 'P', 'alumno', 'nota', 'Solicitud del estudiante - Certificado de Notas'],
        // Proceso 2: Secretaria recibe y revisa documentos
        ['F1', 'P2', 'P3', 'P', 'secretaria', 'recepcion', 'Recepción y revisión por secretaria'],
        // Proceso 3: Secretaria evalúa si pasa a kardex (pregunta condicional)
        ['F1', 'P3', null, 'Q', 'secretaria', 'pregunta_secretaria', 'Evaluación de documentos completos']
    ];
    
    // FLUJO F2: Verificación y Emisión (2 procesos)
    $procesos_f2 = [
        // Proceso 1: Kardex verifica datos académicos
        ['F2', 'P1', 'P2', 'P', 'kardex', 'recepcionar2', 'Verificación académica en kardex'],
        // Proceso 2: Kardex emite certificado (proceso final)
        ['F2', 'P2', null, 'E', 'kardex', 'conclusion', 'Emisión del certificado']
    ];
    
    // Proceso de rechazo si documentos incompletos
    $proceso_rechazo = [
        ['F1', 'P4', null, 'E', 'alumno', 'rechazo', 'Notificación de documentos incompletos']
    ];
    
    // Insertar todos los procesos
    $todos_procesos = array_merge($procesos_f1, $procesos_f2, $proceso_rechazo);
    
    foreach ($todos_procesos as $proceso) {
        $stmt = $pdo->prepare("INSERT INTO flujoproceso (flujo, proceso, siguiente, tipo, rol, pantalla, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($proceso);
    }
    echo "Flujos de procesos insertados exitosamente.\n";
    
    // Insertar preguntas de decisión
    // F1-P3: Si documentos completos -> F2-P1, si incompletos -> F1-P4 (rechazo)
    $pdo->exec("INSERT INTO flujoprocesopregunta (flujo, proceso, si, no) VALUES ('F1', 'P3', 'F2P1', 'P4')");
    echo "Preguntas de flujo insertadas exitosamente.\n";
    
    echo "\n¡Base de datos configurada correctamente!\n";
    echo "===========================================\n";
    echo "FLUJO F1 - Solicitud y Revisión (3 procesos):\n";
    echo "  P1: Alumno solicita certificado (alumno)\n";
    echo "  P2: Secretaria recibe documentos (secretaria)\n";
    echo "  P3: Secretaria evalúa completitud (secretaria - pregunta)\n";
    echo "    -> SI: Pasa a F2-P1 (kardex)\n";
    echo "    -> NO: Va a F1-P4 (rechazo)\n";
    echo "  P4: Notificación de rechazo (alumno)\n";
    echo "\n";
    echo "FLUJO F2 - Verificación y Emisión (2 procesos):\n";
    echo "  P1: Kardex verifica datos académicos (kardex)\n";
    echo "  P2: Kardex emite certificado (kardex - final)\n";
    echo "\n";
    echo "USUARIOS DE PRUEBA:\n";
    echo "  msilva / 123456 (alumno)\n";
    echo "  edward / 123456 (secretaria)\n";
    echo "  zulema / 123456 (kardex)\n";
    echo "\nAhora puedes ejecutar: php -S localhost:3000\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>