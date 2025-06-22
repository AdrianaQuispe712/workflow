Pantalla Nota - Solicitud de Certificado
<br>
-------------
<br>
<?php
$sql = "SELECT * FROM academico.alumno WHERE id = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fila2 = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<h3>Datos del Estudiante</h3>
<label>Nombres:</label>
<input type="text" value="<?php echo htmlspecialchars($fila2["nombre"]); ?>" name="nombre" required/>
<br><br>
<label>Apellido Paterno:</label>
<input type="text" value="<?php echo htmlspecialchars($fila2["paterno"]); ?>" name="paterno" required/>
<br><br>
<label>Apellido Materno:</label>
<input type="text" value="<?php echo htmlspecialchars($fila2["materno"]); ?>" name="materno"/>
<br><br>
<label>Carnet de Identidad:</label>
<input type="text" value="<?php echo htmlspecialchars($fila2["ci"]); ?>" name="ci" required/>
<br><br>
<label>Motivo de la Solicitud:</label>
<textarea name="motivo" rows="3" cols="40" placeholder="Indique el motivo de su solicitud..."></textarea>