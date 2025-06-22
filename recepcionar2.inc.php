Verificación Académica - Kardex
<br>
-----------------------------
<br>
<h3>FLUJO F2 - PROCESO P1: Verificación de Datos Académicos</h3>
<p><strong>Proceso:</strong> Validación completa del expediente académico del estudiante</p>
<br>
<div style="background-color: #e7f3ff; padding: 10px; border-radius: 5px; margin: 10px 0;">
    <p><strong>Trámite aprobado por Secretaría - Documentación completa</strong></p>
</div>
<br>
<label>Código de Estudiante (SIS):</label>
<input type="text" name="codigo_estudiante" placeholder="Ej: SIS202412345" required/>
<br><br>
<label>Carrera:</label>
<select name="carrera" required>
    <option value="">-- Seleccione Carrera --</option>
    <option value="Ingeniería de Sistemas">Ingeniería de Sistemas</option>
    <option value="Matemática">Matemática</option>
    <option value="Física">Física</option>
    <option value="Química">Química</option>
    <option value="Biología">Biología</option>
    <option value="Estadística">Estadística</option>
</select>
<br><br>
<label>Gestión de Ingreso:</label>
<input type="number" name="gestion_ingreso" placeholder="Ej: 2020" min="1990" max="2024" required/>
<br><br>
<label>Estado Académico Actual:</label>
<select name="estado_academico" required>
    <option value="activo">Activo - Cursando materias</option>
    <option value="egresado">Egresado - Completó pensum</option>
    <option value="abandono">Abandono temporal</option>
    <option value="transferencia">En proceso de transferencia</option>
</select>
<br><br>
<label>Número de Materias Aprobadas:</label>
<input type="number" name="materias_aprobadas" placeholder="Ej: 45" min="0" required/>
<br><br>
<label>Promedio General:</label>
<input type="number" name="promedio" placeholder="Ej: 75.5" step="0.1" min="0" max="100" required/>
<br><br>
<label>Observaciones de Kardex:</label>
<textarea name="observaciones_kardex" rows="4" cols="50" placeholder="Registre observaciones sobre el estado académico, deudas pendientes, etc..." required></textarea>
<br><br>
<label>Fecha de Verificación:</label>
<input type="date" name="fecha_verificacion" value="<?php echo date('Y-m-d'); ?>" readonly/>
<br><br>
<div style="background-color: #d4edda; padding: 10px; border-radius: 5px;">
    <p><strong>Siguiente paso:</strong> Una vez verificados los datos, proceder con la emisión del certificado (F2-P2).</p>
</div>