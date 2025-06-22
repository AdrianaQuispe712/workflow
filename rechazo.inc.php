Notificación de Rechazo
<br>
---------------------
<br>
<div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; color: #721c24;">
    <h3 style="color: #721c24;">❌ SOLICITUD RECHAZADA</h3>
    <p><strong>Su solicitud de certificado de notas ha sido rechazada.</strong></p>
</div>
<br>
<h4>Motivos del rechazo:</h4>
<textarea name="motivo_rechazo" rows="4" cols="60" readonly>
<?php 
session_start();
echo isset($_SESSION["justificacion"]) ? htmlspecialchars($_SESSION["justificacion"]) : "No se especificó motivo";
?>
</textarea>
<br><br>
<h4>Pasos a seguir:</h4>
<ol>
    <li>Revisar los motivos del rechazo</li>
    <li>Completar la documentación faltante</li>
    <li>Realizar una nueva solicitud</li>
</ol>
<br>
<label>Fecha de notificación:</label>
<input type="text" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly/>
<br><br>
<div style="background-color: #d1ecf1; padding: 10px; border-radius: 5px;">
    <p><strong>Nota:</strong> Para más información, puede acercarse a la oficina de Kardex en horarios de atención.</p>
</div>