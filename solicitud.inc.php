Solicitud General - FCPN
<br>
----------------------
<br>
<h3>Formulario de Solicitud General</h3>
<p><strong>Proceso:</strong> Solicitud dirigida a la Facultad de Ciencias Puras y Naturales</p>
<br>
<label>Tipo de Solicitud:</label>
<select name="tipo_solicitud" required>
    <option value="">-- Seleccione --</option>
    <option value="certificado">Certificado de Estudios</option>
    <option value="titulo">Trámite de Título</option>
    <option value="convalidacion">Convalidación de Materias</option>
    <option value="transferencia">Transferencia</option>
    <option value="otros">Otros</option>
</select>
<br><br>
<label>Descripción Detallada:</label>
<textarea name="descripcion_solicitud" rows="4" cols="50" placeholder="Describa detalladamente su solicitud..." required></textarea>
<br><br>
<label>Dirigido a:</label>
<select name="dirigido_a">
    <option value="decano">Señor Decano</option>
    <option value="vicedecano">Señor Vicedecano</option>
    <option value="director_carrera">Director de Carrera</option>
</select>
<br><br>
<label>Carrera:</label>
<input type="text" name="carrera_estudiante" placeholder="Especifique su carrera" required/>
<br><br>
<label>Teléfono de Contacto:</label>
<input type="tel" name="telefono" placeholder="Ingrese su número de teléfono"/>
<br><br>
<label>Email:</label>
<input type="email" name="email" placeholder="correo@ejemplo.com"/>