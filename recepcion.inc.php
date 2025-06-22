Recepción de Solicitud - Secretaría
<br>
-----------------------------
<br>
<h3>Revisión de Documentos</h3>
<p><strong>Proceso:</strong> Verificación de documentos presentados por el estudiante</p>
<br>
<label>Documentos Recibidos:</label>
<input type="checkbox" name="doc_ci" id="doc_ci" checked> <label for="doc_ci">Fotocopia de CI</label><br>
<input type="checkbox" name="doc_solicitud" id="doc_solicitud" checked> <label for="doc_solicitud">Solicitud escrita</label><br>
<input type="checkbox" name="doc_comprobante" id="doc_comprobante"> <label for="doc_comprobante">Comprobante de pago</label><br>
<br>
<label>Observaciones de Secretaría:</label>
<textarea name="observaciones_secretaria" rows="3" cols="50" placeholder="Registre observaciones sobre la documentación..."></textarea>
<br><br>
<label>Fecha de Recepción:</label>
<input type="date" name="fecha_recepcion" value="<?php echo date('Y-m-d'); ?>" readonly/>
<br><br>
<label>Estado:</label>
<select name="estado_recepcion">
    <option value="completo">Documentación Completa</option>
    <option value="incompleto">Documentación Incompleta</option>
</select>