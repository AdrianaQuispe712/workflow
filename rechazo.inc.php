Notificación de Documentación Incompleta
<br>
--------------------------------------
<br>
<div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; color: #721c24;">
    <h3 style="color: #721c24;">❌ SOLICITUD RECHAZADA - FLUJO F1 FINALIZADO</h3>
    <p><strong>Su solicitud de certificado de notas ha sido rechazada por documentación incompleta.</strong></p>
</div>
<br>
<h4>Motivos del rechazo:</h4>
<textarea name="motivo_rechazo" rows="4" cols="60" readonly>
<?php 
echo isset($_SESSION["observaciones_secretaria"]) ? htmlspecialchars($_SESSION["observaciones_secretaria"]) : "Documentación incompleta según revisión de secretaría";
?>
</textarea>
<br><br>
<h4>Documentos requeridos:</h4>
<ul>
    <li>Fotocopia de Carnet de Identidad (legible)</li>
    <li>Solicitud escrita dirigida al Señor Decano</li>
    <li>Comprobante de pago de aranceles universitarios</li>
    <li>Formulario de solicitud debidamente llenado</li>
</ul>
<br>
<h4>Pasos a seguir:</h4>
<ol>
    <li>Completar la documentación faltante</li>
    <li>Verificar que todos los documentos estén correctos</li>
    <li>Realizar una nueva solicitud desde el inicio</li>
</ol>
<br>
<label>Fecha de notificación:</label>
<input type="text" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly/>
<br><br>
<div style="background-color: #d1ecf1; padding: 10px; border-radius: 5px;">
    <p><strong>Nota:</strong> Para más información, puede acercarse a la oficina de Secretaría Académica en horarios de atención (8:00-12:00 y 14:30-18:30).</p>
</div>