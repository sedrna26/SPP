<?php
// Obtener los datos existentes
$id_ppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_ppl > 0) {
    $stmt = $db->prepare("SELECT * FROM datos_medicos WHERE id_ppl = :id_ppl");
    $stmt->bindParam(':id_ppl', $id_ppl);
    $stmt->execute();
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<form method="POST">
    <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($id_ppl); ?>">

    <div class="form-section">
        <h3 id="titulo">Datos Médicos</h3>

        <div class="form-group">
            <label>
                <input type="checkbox" name="hipertension" <?php echo isset($datos['hipertension']) && $datos['hipertension'] ? 'checked' : ''; ?> disabled> Hipertensión
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="diabetes" <?php echo isset($datos['diabetes']) && $datos['diabetes'] ? 'checked' : ''; ?> disabled> Diabetes
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="enfermedad_corazon" <?php echo isset($datos['enfermedad_corazon']) && $datos['enfermedad_corazon'] ? 'checked' : ''; ?> disabled> ¿Sufre alguna enfermedad al corazón?
            </label>
        </div>

        <div class="form-group">
            <label>¿Cuál?</label>
            <input type="text" name="enfermedad_corazon_cual" value="<?php echo htmlspecialchars($datos['enfermedad_corazon_cual'] ?? ''); ?>" readonly>
        </div>

        <div class="form-group">
            <label>¿Toma alguna medicación?</label>
            <input type="text" name="medicacion" value="<?php echo htmlspecialchars($datos['medicacion'] ?? ''); ?>" readonly>
        </div>

        <h3 id="titulo">Datos Antropométricos</h3>

        <div class="form-group">
            <label>Peso actual (kg):</label>
            <input type="text" name="peso_actual" value="<?php echo htmlspecialchars($datos['peso_actual'] ?? ''); ?>" required readonly>
        </div>
        <div class="form-group">
            <label>Talla (cm):</label>
            <input type="text" name="talla" value="<?php echo htmlspecialchars($datos['talla'] ?? ''); ?>" readonly>
        </div>
        <div class="form-group">
            <label>IMC:</label>
            <input type="text" name="imc" value="<?php echo htmlspecialchars($datos['imc'] ?? ''); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Diagnóstico:</label>
            <input type="text" name="diagnostico" value="<?php echo htmlspecialchars($datos['diagnostico'] ?? ''); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Tipificación de dieta:</label>
            <input type="text" name="tipificacion_dieta" value="<?php echo htmlspecialchars($datos['tipificacion_dieta'] ?? ''); ?>" readonly>
        </div>
    </div>

    <button name="guardar" type="submit" class="btn btn-primary" disabled>Guardar Información</button>
</form>
