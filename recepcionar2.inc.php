Proceso de Kardex - Verificación de Datos Académicos
<br>
-----------------------------------------------
<br>
<h3>Verificación en Sistema Académico</h3>
<p><strong>Proceso:</strong> Validación de datos académicos del estudiante</p>
<br>
<label>Código de Estudiante:</label>
<input type="text" name="codigo_estudiante" placeholder="Ingrese código SIS" required/>
<br><br>
<label>Carrera:</label>
<input type="text" name="carrera" placeholder="Verificar carrera del estudiante"/>
<br><br>
<label>Gestión de Ingreso:</label>
<input type="text" name="gestion_ingreso" placeholder="Año de ingreso"/>
<br><br>
<label>Estado Académico:</label>
<select name="estado_academico">
    <option value="activo">Activo</option>
    <option value="egresado">Egresado</option>
    <option value="abandono">Abandono</option>
    <option value="transferencia">Transferencia</option>
</select>
<br><br>
<label>Observaciones de Kardex:</label>
<textarea name="observaciones_kardex" rows="4" cols="50" placeholder="Registre observaciones sobre el estado académico del estudiante..."></textarea>
<br><br>
<label>Fecha de Verificación:</label>
<input type="date" name="fecha_verificacion" value="<?php echo date('Y-m-d'); ?>" readonly/>