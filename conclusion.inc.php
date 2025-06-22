Emisión del Certificado - FLUJO F2 PROCESO P2 (FINAL)
<br>
--------------------------------------------------
<br>
<div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; color: #155724;">
    <h3 style="color: #155724;">✅ CERTIFICADO APROBADO Y EMITIDO</h3>
    <p><strong>Proceso completado exitosamente. El certificado ha sido generado.</strong></p>
</div>
<br>
<h4>Datos del Certificado Emitido:</h4>
<label>Número de Certificado:</label>
<input type="text" name="nro_certificado" value="CERT-<?php echo date('Y').'-'.str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT); ?>" readonly/>
<br><br>
<label>Fecha de Emisión:</label>
<input type="date" name="fecha_emision" value="<?php echo date('Y-m-d'); ?>" readonly/>
<br><br>
<label>Tipo de Certificado:</label>
<select name="tipo_certificado">
    <option value="notas" selected>Certificado de Notas</option>
    <option value="calificaciones">Certificado de Calificaciones</option>
    <option value="record">Record Académico Completo</option>
</select>
<br><br>
<label>Datos Verificados:</label>
<textarea name="datos_verificados" rows="3" cols="50" readonly>
Código SIS: <?php echo $_SESSION["codigo_estudiante"] ?? 'Verificado'; ?>
Carrera: <?php echo $_SESSION["carrera_verificada"] ?? 'Verificada'; ?>
Estado: <?php echo $_SESSION["estado_academico"] ?? 'Activo'; ?>
</textarea>
<br><br>
<label>Observaciones Finales:</label>
<textarea name="observaciones_finales" rows="3" cols="50" placeholder="Observaciones adicionales del certificado emitido..."></textarea>
<br><br>
<div style="background-color: #cce5ff; padding: 15px; border-radius: 5px;">
    <h4>📋 Instrucciones para el Estudiante:</h4>
    <ul>
        <li>El certificado estará listo para recoger en <strong>24 horas hábiles</strong></li>
        <li>Presentarse en la oficina de <strong>Kardex - FCPN</strong></li>
        <li>Horario de atención: 8:00-12:00 y 14:30-18:30</li>
        <li>Traer Carnet de Identidad original para el retiro</li>
    </ul>
</div>
<br>
<div style="background-color: #fff3cd; padding: 10px; border-radius: 5px;">
    <p><strong>Estado del Trámite:</strong> FINALIZADO - Ambos flujos completados exitosamente</p>
    <p><strong>Flujo F1:</strong> Solicitud y Revisión ✅</p>
    <p><strong>Flujo F2:</strong> Verificación y Emisión ✅</p>
</div>