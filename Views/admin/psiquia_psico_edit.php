<?php
require 'navbar.php';
$id_ppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
$psiquia_psico = null;
if ($id_ppl > 0) {
    $stmt = $db->prepare("SELECT * FROM psiquiatrico_psicologico WHERE id_ppl = ?");
    $stmt->execute([$id_ppl]);
    $psiquia_psico = $stmt->fetch(PDO::FETCH_ASSOC);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    try {
        $db->beginTransaction();
        if ($psiquia_psico) {
            // Actualizar datos existentes
            $stmt = $db->prepare("UPDATE psiquiatrico_psicologico 
                SET diagnostico_psiquiatrico = ?, 
                    si_no_diagnostico = ?, 
                    institucionalizaciones_centros_rehab = ?, 
                    orientacion_temporo_espacial = ?, 
                    juicio_realidad = ?, 
                    ideacion = ?, 
                    estado_afectivo = ?, 
                    antecedentes_autolesiones = ?, 
                    antecedentes_consumo_sustancias = ?, 
                    edad_inicio_consumo = ?, 
                    datos_interes_intervencion = ? 
                WHERE id_ppl = ?");
            $stmt->execute([
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
                $_POST['datos_interes_intervencion'],
                $id_ppl
            ]);

            $accion = 'Editar Datos Psiquiátricos/Psicológicos';
            $detalles = "Se actualizaron los datos psiquiátricos/psicológicos para el PPL con ID: $id_ppl";
        } else {
            // Insertar nuevos datos (similar al script de creación)
            $stmt = $db->prepare("INSERT INTO psiquiatrico_psicologico (
                id_ppl, diagnostico_psiquiatrico, si_no_diagnostico, 
                institucionalizaciones_centros_rehab, orientacion_temporo_espacial, 
                juicio_realidad, ideacion, estado_afectivo, antecedentes_autolesiones, 
                antecedentes_consumo_sustancias, edad_inicio_consumo, datos_interes_intervencion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $id_ppl,
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

            $accion = 'Agregar Datos Psiquiátricos/Psicológicos';
            $detalles = "Se insertaron los datos psiquiátricos/psicológicos para el PPL con ID: $id_ppl";
        }

        $db->commit();

        // Registrar acción en la auditoría
        $tabla_afectada = 'psiquiatrico_psicologico';
        registrarAuditoria($db, $accion, $tabla_afectada, $id_ppl, $detalles);

        header("Location: ppl_informe.php?seccion=informe-psicologico&id=".$id_ppl);
        exit();
    } catch (PDOException $e) {
        $db->rollBack();
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
}
?>
<div class="container py-4">    
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Editar Informe Psiquiátrico y Psicológico del PPL</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="ppl_informe.php?seccion=informe-psicologico&id=<?php echo $id_ppl; ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($id_ppl); ?>">

                <div class="mb-4">
                    <h4 class="mb-3">Editar Informe Psiquiátrico</h4>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="si_no_diagnostico" 
                                   name="si_no_diagnostico"
                                   onchange="toggleDiagnosticoInput(this)"
                                   <?php echo ($psiquia_psico && $psiquia_psico['si_no_diagnostico']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="si_no_diagnostico">
                                ¿Tuvo alguna vez diagnóstico psiquiátrico?
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="diagnostico-group" style="display: <?php echo ($psiquia_psico && $psiquia_psico['si_no_diagnostico']) ? 'block' : 'none'; ?>;">
                        <label class="form-label">Diagnóstico Psiquiátrico:</label>
                        <input type="text" 
                               class="form-control" 
                               name="diagnostico_psiquiatrico"
                               value="<?php echo htmlspecialchars($psiquia_psico['diagnostico_psiquiatrico'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Institucionalizaciones en centros de rehabilitación:</label>
                        <input type="text" 
                               class="form-control" 
                               name="institucionalizaciones_centros_rehab" 
                               required
                               value="<?php echo htmlspecialchars($psiquia_psico['institucionalizaciones_centros_rehab'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="mb-3">Informe Psicológico (Dispositivo de Salud Mental)</h4>

                    <div class="mb-3">
                        <label class="form-label">Orientación Témporo-Espacial:</label>
                        <input type="text" 
                               class="form-control" 
                               name="orientacion_temporo_espacial" 
                               required
                               value="<?php echo htmlspecialchars($psiquia_psico['orientacion_temporo_espacial'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Juicio de Realidad:</label>
                        <input type="text" 
                               class="form-control" 
                               name="juicio_realidad" 
                               required
                               value="<?php echo htmlspecialchars($psiquia_psico['juicio_realidad'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ideación:</label>
                        <input type="text" 
                               class="form-control" 
                               name="ideacion" 
                               required
                               value="<?php echo htmlspecialchars($psiquia_psico['ideacion'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado Afectivo:</label>
                        <input type="text" 
                               class="form-control" 
                               name="estado_afectivo" 
                               required
                               value="<?php echo htmlspecialchars($psiquia_psico['estado_afectivo'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Antecedentes de Autolesiones:</label>
                        <input type="text" 
                               class="form-control" 
                               name="antecedentes_autolesiones" 
                               required
                               value="<?php echo htmlspecialchars($psiquia_psico['antecedentes_autolesiones'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Antecedentes de consumo de sustancias psicoactivas:</label>
                        <input type="text" 
                            class="form-control" 
                            name="antecedentes_consumo_sustancias"
                            oninput="toggleEdadInicioInput(this)"
                            value="<?php echo htmlspecialchars($psiquia_psico['antecedentes_consumo_sustancias'] ?? ''); ?>">
                        <div class="form-text">Incluir alcohol, estupefacientes, psicofarmacos, inhalantes</div>
                    </div>

                    <div class="mb-3" id="edad-inicio-group" style="display: <?php echo ($psiquia_psico && $psiquia_psico['edad_inicio_consumo']) ? 'block' : 'none'; ?>;">
                        <label class="form-label">Edad de inicio de consumo:</label>
                        <input type="number" 
                               class="form-control" 
                               name="edad_inicio_consumo"
                               value="<?php echo htmlspecialchars($psiquia_psico['edad_inicio_consumo'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Datos de interés y sugerencias de intervención:</label>
                        <textarea class="form-control" 
                                  name="datos_interes_intervencion" 
                                  required
                                  rows="3"><?php echo htmlspecialchars($psiquia_psico['datos_interes_intervencion'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <button name="guardar" type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDiagnosticoInput(checkbox) {
    const diagnosticoGroup = document.getElementById('diagnostico-group');
    diagnosticoGroup.style.display = checkbox.checked ? 'block' : 'none';
}

function toggleEdadInicioInput(input) {
    const edadInicioGroup = document.getElementById('edad-inicio-group');
    if (input.value.trim().length > 0) {
        edadInicioGroup.style.display = 'block';
    } else {
        edadInicioGroup.style.display = 'none';
        document.querySelector('input[name="edad_inicio_consumo"]').value = '';
    }
}
</script>