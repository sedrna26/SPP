<?php
require 'navbar.php';
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

function registrarAuditoria($db, $accion, $tabla_afectada, $registro_id, $detalles)
{
    try {
        $sql = "INSERT INTO auditoria (id_usuario, accion, detalles, tabla_afectada, registro_id, fecha) 
                VALUES (:id_usuario, :accion, :detalles, :tabla_afectada, :registro_id, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':detalles', $detalles);
        $stmt->bindParam(':tabla_afectada', $tabla_afectada);
        $stmt->bindParam(':registro_id', $registro_id);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error en el registro de auditoría: " . $e->getMessage();
    }
}


$sql = "SELECT * FROM datos_medicos WHERE id_ppl = :id_ppl";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ppl', $idppl);
$stmt->execute();
$datos_medicos = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE datos_medicos 
            SET hipertension = ?, diabetes = ?, enfermedad_corazon = ?,enfermedad_corazon_cual = ? , asma = ?, epilepsia = ?, alergia = ?, alergia_especifique = ?,
                es_celiaco = ?, bulimia_anorexia = ?, medicacion = ?, metabolismo = ?, embarazo = ?, hepatitis = ?, 
                mononucleosis = ?, otras_enfermedades = ?, peso_actual = ?, talla = ?, imc = ?, diagnostico = ?, 
                tipificacion_dieta = ? 
            WHERE id_ppl = ?");

        $stmt->execute([
            isset($_POST['hipertension']) ? 1 : 0,
            isset($_POST['diabetes']) ? 1 : 0,
            isset($_POST['enfermedad_corazon']) ? 1 : 0,
            $_POST['enfermedad_corazon_cual'],
            isset($_POST['asma']) ? 1 : 0,
            isset($_POST['epilepsia']) ? 1 : 0,
            isset($_POST['alergia']) ? 1 : 0,
            $_POST['alergia_especifique'],
            isset($_POST['es_celiaco']) ? 1 : 0,
            isset($_POST['bulimia_anorexia']) ? 1 : 0,
            $_POST['medicacion'],
            isset($_POST['metabolismo']) ? 1 : 0,
            isset($_POST['embarazo']) ? 1 : 0,
            isset($_POST['hepatitis']) ? 1 : 0,
            isset($_POST['mononucleosis']) ? 1 : 0,
            $_POST['otras_enfermedades'],
            $_POST['peso_actual'],
            $_POST['talla'],
            $_POST['imc'],
            $_POST['diagnostico'],
            $_POST['tipificacion_dieta'],
            $_POST['id_ppl']
        ]);

        $db->commit();
        $accion = 'Editar Datos Médicos/Informe ';
        $tabla_afectada = 'datos_medicos';
        $detalles = "Se Editaron los datos médicos para el paciente con ID: " . $_POST['id_ppl'];
        registrarAuditoria($db, $accion, $tabla_afectada, $_POST['id_ppl'], $detalles);
        header("Location: ppl_informe.php?seccion=informe-sanitario&id=" . $idppl);
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }

}
?>

<style>
    .form-section {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .hidden {
        display: none;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .familiar-container {
        border-left: 3px solid #212529;
        padding-left: 15px;
        margin-bottom: 20px;
    }

    .status-fallecido {
        border-left-color: #212529;
        background-color: #f9f9f9;
    }

    .children-container {
        margin-left: 20px;
        padding: 10px;
        border-left: 2px dashed #212529;
    }

    #titulo {
        padding-bottom: 1rem;
    }
</style>
<?php if ($datos_medicos): ?>
    <div class="container mt-5">
        <form method="POST" class="shadow p-4 rounded">
            <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($datos_medicos['id_ppl']); ?>">

            <div class="mb-4">
                <h3>Editar Datos Médicos</h3>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="hipertension" id="hipertension" <?php echo ($datos_medicos['hipertension']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="hipertension">Hipertensión</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="diabetes" id="diabetes" <?php echo ($datos_medicos['diabetes']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="diabetes">Diabetes</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="enfermedad_corazon" id="enfermedad_corazon" <?php echo (isset($datos_medicos['enfermedad_corazon']) && $datos_medicos['enfermedad_corazon']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="enfermedad_corazon">Enfermedad al corazón</label>
                </div>
                <input class="form-control mb-3" type="text" name="enfermedad_corazon_cual" placeholder="Especifique" value="<?php echo htmlspecialchars($datos_medicos['enfermedad_corazon_cual'] ?? ''); ?>">


                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="asma" id="asma" <?php echo ($datos_medicos['asma']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="asma">Asma</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="epilepsia" id="epilepsia" <?php echo ($datos_medicos['epilepsia']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="epilepsia">Epilepsia</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="alergia" id="alergia" <?php echo (isset($datos_medicos['alergia']) && $datos_medicos['alergia']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="alergia">Alergia</label>
                </div>
                <input class="form-control mb-3" type="text" name="alergia_especifique" placeholder="Especifique" value="<?php echo htmlspecialchars($datos_medicos['alergia_especifique'] ?? ''); ?>">
                <!-- ¿Es celiaco? -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="es_celiaco" id="es_celiaco" <?php echo (isset($datos_medicos['es_celiaco']) && $datos_medicos['es_celiaco']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="alergia">Es celiaco?</label>
                </div>
                <!-- bulimia_anorexia -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="bulimia_anorexia" id="bulimia_anorexia" <?php echo (isset($datos_medicos['bulimia_anorexia']) && $datos_medicos['bulimia_anorexia']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="bulimia_anorexia">¿Padece bulimia o anorexia?</label>
                </div>
                <!-- ¿Toma alguna medicación? -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="medicacion" id="medicacion" <?php echo (isset($datos_medicos['medicacion']) && $datos_medicos['medicacion']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="medicacion">¿Toma alguna medicación?</label>
                </div>
                <input class="form-control mb-3" type="text" name="enfermedad_corazon_cual" placeholder="Especifique" value="<?php echo htmlspecialchars($datos_medicos['enfermedad_corazon_cual'] ?? ''); ?>">
                <!-- ¿Sufre metabolismo? -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="metabolismo" id="metabolismo" <?php echo (isset($datos_medicos['metabolismo']) && $datos_medicos['metabolismo']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="metabolismo">¿Sufre metabolismo?</label>
                </div>
                <!-- Embarazo -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="embarazo" id="embarazo" <?php echo (isset($datos_medicos['embarazo']) && $datos_medicos['embarazo']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="embarazo">¿Embarazo?</label>
                </div>
                <!-- Hepatitis -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="hepatitis" id="hepatitis" <?php echo (isset($datos_medicos['hepatitis']) && $datos_medicos['hepatitis']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="hepatitis">Hepatitis</label>
                </div>
                <!-- Mononucleosis infecciosa -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="mononucleosis" id="mononucleosis" <?php echo (isset($datos_medicos['mononucleosis']) && $datos_medicos['mononucleosis']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="mononucleosis">Mononucleosis</label>
                </div>

                <!-- Otras enfermedades -->
                <div class="form-group">
                    <label for="otras_enfermedades" class="form-label">Otras enfermedades, luxaciones, etc.:</label>
                    <input class="form-control" type="text" name="otras_enfermedades" id="otras_enfermedades" value="<?php echo htmlspecialchars($datos_medicos['otras_enfermedades']); ?>">
                </div>
            </div>

            <!-- Datos antropométricos -->
            <h3 class="mb-4">Datos Antropométricos</h3>

            <div class="mb-3">
                <label for="peso_actual" class="form-label">Peso actual (kg):</label>
                <input class="form-control" type="text" name="peso_actual" id="peso_actual" value="<?php echo htmlspecialchars($datos_medicos['peso_actual']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="talla" class="form-label">Talla (cm):</label>
                <input class="form-control" type="text" name="talla" id="talla" value="<?php echo htmlspecialchars($datos_medicos['talla']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="imc" class="form-label">IMC:</label>
                <input class="form-control" type="text" name="imc" id="imc" value="<?php echo htmlspecialchars($datos_medicos['imc']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="diagnostico" class="form-label">Diagnóstico:</label>
                <input class="form-control" type="text" name="diagnostico" id="diagnostico" value="<?php echo htmlspecialchars($datos_medicos['diagnostico']); ?>">
            </div>

            <div class="mb-3">
                <label for="tipificacion_dieta" class="form-label">Tipificación de dieta:</label>
                <input class="form-control" type="text" name="tipificacion_dieta" id="tipificacion_dieta" value="<?php echo htmlspecialchars($datos_medicos['tipificacion_dieta']); ?>">
            </div>

            <button name="guardar" type="submit" class="btn btn-primary w-100">Guardar Información</button>
        </form>
    </div>

<?php else: ?>
    <p><b>No hay datos registrados para este paciente.</b></p>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pesoInput = document.querySelector('input[name="peso_actual"]');
        const tallaInput = document.querySelector('input[name="talla"]');
        const imcInput = document.querySelector('input[name="imc"]');

        pesoInput.addEventListener('input', function() {
            const peso = parseFloat(this.value);
            const talla = parseFloat(tallaInput.value);
            const imc = calcularIMC(peso, talla);
            imcInput.value = imc;
        });

        tallaInput.addEventListener('input', function() {
            const peso = parseFloat(pesoInput.value);
            const talla = parseFloat(this.value);
            const imc = calcularIMC(peso, talla);
            imcInput.value = imc;
        });
    });

    function calcularIMC(peso, talla) {
        if (talla > 0) {
            return (peso / ((talla / 100) * (talla / 100))).toFixed(2);
        } else {
            return 0;
        }
    }
</script>