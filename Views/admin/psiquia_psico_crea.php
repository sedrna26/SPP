<?php
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

$id_ppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO psiquiatrico_psicologico (id_ppl, diagnostico_psiquiatrico, si_no_diagnostico, institucionalizaciones_centros_rehab, orientacion_temporo_espacial, juicio_realidad, ideacion, estado_afectivo, antecedentes_autolesiones, antecedentes_consumo_sustancias, edad_inicio_consumo, datos_interes_intervencion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_ppl'],
            isset($_POST['diagnostico_psiquiatrico']) ? $_POST['diagnostico_psiquiatrico'] : null,
            isset($_POST['si_no_diagnostico']) ? 1 : 0,
            $_POST['institucionalizaciones_centros_rehab'],
            $_POST['orientacion_temporo_espacial'],
            $_POST['juicio_realidad'],
            $_POST['ideacion'],
            $_POST['estado_afectivo'],
            $_POST['antecedentes_autolesiones'],
            $_POST['antecedentes_consumo_sustancias'],
            isset($_POST['edad_inicio_consumo']) ? $_POST['edad_inicio_consumo'] : null,
            $_POST['datos_interes_intervencion']
        ]);
        $db->commit();
        $accion = 'Agregar Datos Psiquiátricos/Psicológicos';
        $tabla_afectada = 'psiquiatrico_psicologico';
        $detalles = "Se insertaron los datos psiquiátricos/psicológicos para el paciente con ID: $id_ppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $id_ppl, $detalles);
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
<form method="POST">
    <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($id_ppl); ?>">

    <div class="form-section">
        <h3 id="titulo">Informe Psiquiátrico</h3>

        <div class="form-group">
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="si_no_diagnostico" onchange="toggleDiagnosticoInput(this)"> ¿Tuvo alguna vez diagnóstico psiquiátrico? Sí/No
                </label>
            </div>
        </div>

        <div class="form-group" id="diagnostico-group" style="display: none;">
            <label>Diagnóstico Psiquiátrico:</label>
            <input type="text" name="diagnostico_psiquiatrico">
        </div>

        <div class="form-group">
            <label>Institucionalizaciones en centros de rehabilitación:</label>
            <input type="text" name="institucionalizaciones_centros_rehab" required>
        </div>

        <h3 id="titulo">Informe Psicológico (Dispositivo de Salud Mental)</h3>

        <div class="form-group">
            <label>Orientación Témporo-Espacial:</label>
            <input type="text" name="orientacion_temporo_espacial" required>
        </div>

        <div class="form-group">
            <label>Juicio de Realidad:</label>
            <input type="text" name="juicio_realidad" required>
        </div>

        <div class="form-group">
            <label>Ideación:</label>
            <input type="text" name="ideacion" required>
        </div>

        <div class="form-group">
            <label>Estado Afectivo:</label>
            <input type="text" name="estado_afectivo" required>
        </div>

        <div class="form-group">
            <label>Antecedentes de Autolesiones:</label>
            <input type="text" name="antecedentes_autolesiones" required>
        </div>

        <div class="form-group">
            <label>Antecedentes de consumo de sustancias psicoactivas (alcohol, estupefacientes, psicofarmacos, inhalantes):</label>
            <input type="text" name="antecedentes_consumo_sustancias" onchange="toggleEdadInicioInput(this)">
        </div>

        <div class="form-group" id="edad-inicio-group" style="display: none;">
            <label>Edad de inicio de consumo:</label>
            <input type="number" name="edad_inicio_consumo">
        </div>

        <div class="form-group">
            <label>Datos de interés y sugerencias de intervención:</label>
            <input type="text" name="datos_interes_intervencion" required>
        </div>
    </div>

    <button name="guardar" type="submit" class="btn btn-primary">Guardar Información</button>
</form>

<script>
    function toggleDiagnosticoInput(checkbox) {
        var diagnosticoGroup = document.getElementById('diagnostico-group');
        if (checkbox.checked) {
            diagnosticoGroup.style.display = 'block';
        } else {
            diagnosticoGroup.style.display = 'none';
        }
    }

    function toggleEdadInicioInput(input) {
        var edadInicioGroup = document.getElementById('edad-inicio-group');
        if (input.value.trim() !== '') {
            edadInicioGroup.style.display = 'block';
        } else {
            edadInicioGroup.style.display = 'none';
        }
    }
</script>