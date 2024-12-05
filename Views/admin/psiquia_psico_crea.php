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
        echo '<div class="alert alert-danger" role="alert">Error en el registro de auditoría: ' . $e->getMessage() . '</div>';
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
        header("Location: ppl_informe.php?seccion=informe-psicologico&id=".$idppl);
        echo '<div class="alert alert-success" role="alert">Datos guardados correctamente</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al guardar los datos: ' . $e->getMessage() . '</div>';
    }
}
?>

<div class="container mt-4">
    <form method="POST" class="needs-validation" novalidate>
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($id_ppl); ?>">

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Informe Psiquiátrico</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="si_no_diagnostico" name="si_no_diagnostico" onchange="toggleDiagnosticoInput(this)">
                        <label class="form-check-label" for="si_no_diagnostico">¿Tuvo alguna vez diagnóstico psiquiátrico?</label>
                    </div>
                </div>
                <div class="mb-3" id="diagnostico-group" style="display: none;">
                    <label class="form-label">Diagnóstico Psiquiátrico:</label>
                    <input type="text" class="form-control" name="diagnostico_psiquiatrico">
                </div>
                <div class="mb-3">
                    <label class="form-label">Institucionalizaciones en centros de rehabilitación:</label>
                    <input type="text" class="form-control" name="institucionalizaciones_centros_rehab" required>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Informe Psicológico (Dispositivo de Salud Mental)</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Orientación Témporo-Espacial:</label>
                    <input type="text" class="form-control" name="orientacion_temporo_espacial" required>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Juicio de Realidad:</label>
                    <input type="text" class="form-control" name="juicio_realidad" required>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ideación:</label>
                    <input type="text" class="form-control" name="ideacion" required>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado Afectivo:</label>
                    <input type="text" class="form-control" name="estado_afectivo" required>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Antecedentes de Autolesiones:</label>
                    <input type="text" class="form-control" name="antecedentes_autolesiones" required>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Antecedentes de consumo de sustancias psicoactivas:</label>
                    <input type="text" class="form-control" name="antecedentes_consumo_sustancias" oninput="toggleEdadInicioInput(this)">
                </div>
                <div class="mb-3" id="edad-inicio-group" style="display: none;">
                    <label class="form-label">Edad de inicio de consumo:</label>
                    <input type="number" class="form-control" name="edad_inicio_consumo" min="1" max="150">
                </div>
                <div class="mb-3">
                    <label class="form-label">Datos de interés y sugerencias de intervención:</label>
                    <textarea class="form-control" name="datos_interes_intervencion" rows="3" ></textarea>
                    <div class="invalid-feedback">Este campo es requerido</div>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
            <button name="guardar" type="submit" class="btn btn-primary btn-lg">Guardar Información</button>
        </div>
    </form>
</div>

<script>
    function toggleDiagnosticoInput(checkbox) {
        var diagnosticoGroup = document.getElementById('diagnostico-group');
        diagnosticoGroup.style.display = checkbox.checked ? 'block' : 'none';
    }

    function toggleEdadInicioInput(input) {
        var edadInicioGroup = document.getElementById('edad-inicio-group');
        // Show/hide based on whether there's any text in the input
        if (input.value.length > 0) {
            edadInicioGroup.style.display = 'block';
        } else {
            edadInicioGroup.style.display = 'none';
        }
    }
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>