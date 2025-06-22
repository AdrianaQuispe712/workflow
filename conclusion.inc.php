Emisión del Certificado - Proceso Final
<br>
-------------------------------------
<br>
<div>
    <h3>CERTIFICADO APROBADO</h3>
    <p><strong>Su solicitud ha sido aprobada. Proceder con la emisión del certificado.</strong></p>
</div>
<br>
<h4>Datos del Certificado:</h4>
<label>Número de Certificado:</label>
<input type="text" name="nro_certificado" value="CERT-<?php echo date('Y').'-'.rand(1000,9999); ?>" readonly/>
<br><br>
<label>Fecha de Emisión:</label>
<input type="date" name="fecha_emision" value="<?php echo date('Y-m-d'); ?>" readonly/>
<br><br>
<label>Tipo de Certificado:</label>
<select name="tipo_certificado">
    <option value="notas">Certificado de Notas</option>
    <option value="calificaciones">Certificado de Calificaciones</option>
    <option value="record">Record Académico</option>
</select>
<br><br>
<label>Observaciones Finales:</label>
<textarea name="observaciones_finales" rows="3" cols="50" placeholder="Observaciones adicionales del certificado..."></textarea>
<br><br>
<div>
    <p><strong>Instrucciones:</strong> El certificado estará listo para recoger en 24 horas hábiles en la oficina de Kardex.</p>
</div>