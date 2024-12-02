<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
$datos = [];
if ($idppl > 0) {
    $stmt = $db->prepare("SELECT * FROM datos_medicos WHERE id_ppl = :id_ppl");
    $stmt->bindParam(':id_ppl', $idppl);
    $stmt->execute();
    $datos = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}
function obtenerValor($campo, $datos, $default = "")
{
    return isset($datos[$campo]) ? htmlspecialchars($datos[$campo]) : $default;
}
function marcarCheckbox($campo, $datos)
{
    return isset($datos[$campo]) && $datos[$campo] ? 'checked' : '';
}
?>
<form method="">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <h3>Informe Sanitario</h3>
            <a class="btn btn-warning ml-3 btn-sm" href='sanitario_edit.php?id=<?php echo $idppl; ?>'>Editar Informe Sanitario</a>
        </div>

        <div class="form-section">
            <div class="form-group">
                <label>
                    <input type="checkbox" name="hipertension" disabled <?php echo marcarCheckbox('hipertension', $datos); ?>> Hipertensión
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="diabetes" disabled <?php echo marcarCheckbox('diabetes', $datos); ?>> Diabetes
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="enfermedad_corazon" disabled <?php echo marcarCheckbox('enfermedad_corazon', $datos); ?>> ¿Sufre alguna enfermedad al corazón?
                </label>
            </div>

            <div class="form-group">
                <label>¿Cuál?</label>
                <input type="text" name="enfermedad_corazon_cual" disabled value="<?php echo obtenerValor('enfermedad_corazon_cual', $datos); ?>">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="asma" disabled <?php echo marcarCheckbox('asma', $datos); ?>> Asma
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="epilepsia" disabled <?php echo marcarCheckbox('epilepsia', $datos); ?>> Enfermedades del sistema nervioso - Epilepsia
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="alergia" disabled <?php echo marcarCheckbox('alergia', $datos); ?>> Alergia
                </label>
                <input type="text" name="alergia_especifique" disabled value="<?php echo obtenerValor('alergia_especifique', $datos); ?>" placeholder="Especifique...">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="es_celiaco" disabled <?php echo marcarCheckbox('es_celiaco', $datos); ?>> ¿Es celiaco?
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="bulimia_anorexia" disabled <?php echo marcarCheckbox('bulimia_anorexia', $datos); ?>> ¿Padece bulimia o anorexia?
                </label>
            </div>

            <div class="form-group">
                <label>¿Toma alguna medicación?</label>
                <input type="text" name="medicacion" disabled value="<?php echo obtenerValor('medicacion', $datos); ?>" placeholder="Especifique medicación...">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="metabolismo" disabled <?php echo marcarCheckbox('metabolismo', $datos); ?>> ¿Sufre metabolismo?
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="embarazo" disabled <?php echo marcarCheckbox('embarazo', $datos); ?>> Embarazo
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="hepatitis" disabled <?php echo marcarCheckbox('hepatitis', $datos); ?>> Hepatitis
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="mononucleosis" disabled <?php echo marcarCheckbox('mononucleosis', $datos); ?>> Mononucleosis infecciosa
                </label>
            </div>

            <div class="form-group">
                <label>Otras enfermedades, luxaciones, etc.:</label>
                <input type="text" name="otras_enfermedades" disabled value="<?php echo obtenerValor('otras_enfermedades', $datos); ?>" placeholder="Especifique...">
            </div>

            <h3 id="titulo">Datos Antropométricos</h3>

            <div class="form-group">
                <label>Peso actual (kg):</label>
                <input type="text" name="peso_actual" disabled value="<?php echo obtenerValor('peso_actual', $datos); ?>" required>
            </div>

            <div class="form-group">
                <label>Talla (cm):</label>
                <input type="text" name="talla" disabled value="<?php echo obtenerValor('talla', $datos); ?>">
            </div>

            <div class="form-group">
                <label>IMC:</label>
                <input type="text" name="imc" disabled value="<?php echo obtenerValor('imc', $datos); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Diagnóstico:</label>
                <input type="text" name="diagnostico" disabled value="<?php echo obtenerValor('diagnostico', $datos); ?>">
            </div>

            <div class="form-group">
                <label>Tipificación de dieta:</label>
                <input type="text" name="tipificacion_dieta" disabled value="<?php echo obtenerValor('tipificacion_dieta', $datos); ?>">
            </div>
        </div>
    </div>
</form>