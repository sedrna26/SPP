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

// Obtener datos existentes de educación
$educacion = null;
if ($idppl > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM educacion WHERE id_ppl = ? LIMIT 1");
        $stmt->execute([$idppl]);
        $educacion = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al obtener la información educativa: " . $e->getMessage() . "</div>";
    }
}

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $db->prepare("UPDATE educacion SET 
            sabe_leer_escrib = ?, 
            primaria = ?, 
            secundaria = ?, 
            tiene_educ_formal = ?, 
            `educ-formal` = ?, 
            tiene_educ_no_formal = ?, 
            `educ-no-formal` = ?, 
            quiere_deporte = ?, 
            `sec-deporte` = ?, 
            quiere_act_artistica = ?, 
            `act-artistica` = ? 
            WHERE id_ppl = ?");

        $stmt->execute([
            isset($_POST['sabe_leer_escrib']) ? 1 : 0,
            $_POST['primaria'],
            $_POST['secundaria'],
            isset($_POST['tiene_educ_formal']) ? 1 : 0,
            $_POST['educ-formal'] ?? '',
            isset($_POST['tiene_educ_no_formal']) ? 1 : 0,
            $_POST['educ-no-formal'] ?? '',
            isset($_POST['quiere_deporte']) ? 1 : 0,
            $_POST['sec-deporte'] ?? '',
            isset($_POST['quiere_act_artistica']) ? 1 : 0,
            $_POST['act-artistica'] ?? '',
            $idppl
        ]);

        // Registrar acción en la auditoría
        $accion = 'Editar Educación';
        $tabla_afectada = 'educación';
        $detalles = "Se actualizó la información educativa para el PPL con ID: $idppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);

        header("Location: ppl_informe.php?seccion=educacion&id=" . $idppl);
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
}
?>
<style>
    .hidden {
    display: none !important;
}
</style>



<div class="container mt-3">
    <div class="card rounded-2 border-0">
        <h5 class="card-header bg-dark text-white pb-0">Actualizar Educación</h5>
        <div class="card-body bg-light p-4">
        <!-- ------------------- -->
            <a href="ppl_informe.php?seccion=educacion&id=<?php echo $idppl; ?>">
                <div class="btn btn-secondary">Cancelar</div>
            </a>
            <form method="POST" class="shadow p-4 rounded">
                <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">
                <!-- Sección de Educación -->
                <div class="form-section">
                    <h3 id="titulo">Editar Información Educativa del PPL</h3>
                    <div class="checkbox-group">
                        <input type="checkbox" id="sabe_leer_escrib" name="sabe_leer_escrib"
                            <?php echo ($educacion && $educacion['sabe_leer_escrib']) ? 'checked' : ''; ?> >
                        <label for="sabe_leer_escrib" class="checkbox-label">¿Sabe leer y escribir?</label>
                    </div>
                    <div class="form-group">
                        <label>Nivel Primaria:</label>
                        <select class="form-control" name="primaria" required>
                            <option value="">Seleccione nivel</option>
                            <option value="Completa" <?php echo ($educacion && $educacion['primaria'] == 'Completa') ? 'selected' : ''; ?>>Completa</option>
                            <option value="Incompleta" <?php echo ($educacion && $educacion['primaria'] == 'Incompleta') ? 'selected' : ''; ?>>Incompleta</option>
                            <option value="En curso" <?php echo ($educacion && $educacion['primaria'] == 'En curso') ? 'selected' : ''; ?>>En curso</option>
                            <option value="No tiene" <?php echo ($educacion && $educacion['primaria'] == 'No tiene') ? 'selected' : ''; ?>>No tiene</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nivel Secundaria:</label>
                        <select class="form-control" name="secundaria" required>
                            <option value="">Seleccione nivel</option>
                            <option value="Completa" <?php echo ($educacion && $educacion['secundaria'] == 'Completa') ? 'selected' : ''; ?>>Completa</option>
                            <option value="Incompleta" <?php echo ($educacion && $educacion['secundaria'] == 'Incompleta') ? 'selected' : ''; ?>>Incompleta</option>
                            <option value="En curso" <?php echo ($educacion && $educacion['secundaria'] == 'En curso') ? 'selected' : ''; ?>>En curso</option>
                            <option value="No tiene" <?php echo ($educacion && $educacion['secundaria'] == 'No tiene') ? 'selected' : ''; ?>>No tiene</option>
                        </select>
                    </div>
                    <h3>Interés por participar de actividades:</h3>
                    <div class="checkbox-group">
                        <input type="checkbox" id="tiene_educ_formal" name="tiene_educ_formal"
                            <?php echo ($educacion && $educacion['tiene_educ_formal']) ? 'checked' : ''; ?>>
                        <label for="tiene_educ_formal" class="checkbox-label">¿Quiere participar en actividades Educativas Formales?</label>
                    </div>
                    <div class="form-group <?php echo (!$educacion || !$educacion['tiene_educ_formal']) ? 'hidden' : ''; ?>" id="educ_formal_group">
                        <label>Educación Formal:</label>
                        <input class="form-control" type="text" name="educ-formal" id="educ-formal" placeholder="Especifique educación formal..."
                            value="<?php echo htmlspecialchars($educacion['educ-formal'] ?? ''); ?>">
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="tiene_educ_no_formal" name="tiene_educ_no_formal"
                            <?php echo ($educacion && $educacion['tiene_educ_no_formal']) ? 'checked' : ''; ?>>
                        <label for="tiene_educ_no_formal" class="checkbox-label">¿Quiere participar en actividades Educativas No Formales?</label>
                    </div>
                    <div class="form-group <?php echo (!$educacion || !$educacion['tiene_educ_no_formal']) ? 'hidden' : ''; ?>" id="educ_no_formal_group">
                        <label>Educación No Formal:</label>
                        <input class="form-control" type="text" name="educ-no-formal" id="educ-no-formal" placeholder="Especifique educación no formal..."
                            value="<?php echo htmlspecialchars($educacion['educ-no-formal'] ?? ''); ?>">
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="quiere_deporte" name="quiere_deporte"
                            <?php echo ($educacion && $educacion['quiere_deporte']) ? 'checked' : ''; ?>>
                        <label  for="quiere_deporte" class="checkbox-label">¿Quiere participar en actividades Deportivas?</label>
                    </div>
                    <div class="form-group <?php echo (!$educacion || !$educacion['quiere_deporte']) ? 'hidden' : ''; ?>" id="deporte_group">
                        <label>Sección Deportiva:</label>
                        <input class="form-control" type="text" name="sec-deporte" id="sec-deporte" placeholder="Especifique actividad deportiva..."
                            value="<?php echo htmlspecialchars($educacion['sec-deporte'] ?? ''); ?>">
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="quiere_act_artistica" name="quiere_act_artistica"
                            <?php echo ($educacion && $educacion['quiere_act_artistica']) ? 'checked' : ''; ?>>
                        <label for="quiere_act_artistica" class="checkbox-label">¿Quiere participar en actividades Artísticas?</label>
                    </div>
                    <div class="form-group <?php echo (!$educacion || !$educacion['quiere_act_artistica']) ? 'hidden' : ''; ?>" id="artistica_group">
                        <label>Actividad Artística:</label>
                        <input class="form-control" type="text" name="act-artistica" id="act-artistica" placeholder="Especifique actividad artística..."
                            value="<?php echo htmlspecialchars($educacion['act-artistica'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Definimos los pares de checkbox y grupo
    const toggles = [
        { checkbox: 'tiene_educ_formal', group: 'educ_formal_group' },
        { checkbox: 'tiene_educ_no_formal', group: 'educ_no_formal_group' },
        { checkbox: 'quiere_deporte', group: 'deporte_group' },
        { checkbox: 'quiere_act_artistica', group: 'artistica_group' }
    ];

    toggles.forEach(function(toggle) {
        const checkbox = document.getElementById(toggle.checkbox);
        const group = document.getElementById(toggle.group);

        if (checkbox && group) {
            // Función para manejar la visibilidad
            function handleVisibility() {
                if (checkbox.checked) {
                    group.classList.remove('hidden');
                } else {
                    group.classList.add('hidden');
                    // Limpiar el input cuando se oculta
                    const input = group.querySelector('input[type="text"]');
                    if (input) input.value = '';
                }
            }

            // Configurar estado inicial
            handleVisibility();

            // Agregar listener para cambios
            checkbox.addEventListener('change', handleVisibility);
        }
    });
});
</script>

<?php require 'footer.php'; ?>