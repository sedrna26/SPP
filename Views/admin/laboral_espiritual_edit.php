<?php
require 'navbar.php';
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

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consultar datos existentes
$laboral = [];
$espiritual = [];
if ($idppl > 0) {
    $stmt = $db->prepare("SELECT * FROM laboral WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $laboral = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM asistencia_espiritual WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $espiritual = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_4'])) {
    try {
        $db->beginTransaction();

       
        if ($laboral) {
            $stmt = $db->prepare("UPDATE laboral SET tiene_exp = ?, experiencia = ?, se_capacito = ?, 
                en_que_se_capacito = ?, posee_certific = ?, formac_interes = ?, tiene_incl_lab = ?, lugar_inclusion = ? 
                WHERE id_ppl = ?");
            $accion = 'Editar';
        } else {
            $stmt = $db->prepare("INSERT INTO laboral (tiene_exp, experiencia, se_capacito, en_que_se_capacito, 
                posee_certific, formac_interes, tiene_incl_lab, lugar_inclusion, id_ppl) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $accion = 'Agregar';
        }

        $stmt->execute([
            isset($_POST['tiene_exp']) ? 1 : 0,
            $_POST['experiencia'],
            isset($_POST['se_capacito']) ? 1 : 0,
            $_POST['en_que_se_capacito'],
            isset($_POST['posee_certific']) ? 1 : 0,
            $_POST['formac_interes'],
            isset($_POST['tiene_incl_lab']) ? 1 : 0,
            $_POST['lugar_inclusion'],
            $_POST['id_ppl']
        ]);

      
        if ($espiritual) {
            $stmt = $db->prepare("UPDATE asistencia_espiritual SET practica_culto = ?, culto = ?, 
                desea_participar = ?, eleccion_actividad = ? WHERE id_ppl = ?");
            $accion = 'Editar';
        } else {
            $stmt = $db->prepare("INSERT INTO asistencia_espiritual (practica_culto, culto, desea_participar, 
                eleccion_actividad, id_ppl) VALUES (?, ?, ?, ?, ?)");
            $accion = 'Agregar';
        }

        $stmt->execute([
            isset($_POST['practica_culto']) ? 1 : 0,
            $_POST['culto'],
            isset($_POST['desea_participar']) ? 1 : 0,
            $_POST['eleccion_actividad'],
            $_POST['id_ppl']
        ]);

        $db->commit();
        $accion = 'Editar asistencia_espiritual, laboral';
        $tabla_afectada = 'asistencia_espiritual, laboral';
        $detalles = "$accion asistencia_espiritual y laboral para el PPL con ID: $idppl";
        registrarAuditoria($db, "$accion Datos", $tabla_afectada, $idppl, $detalles);

        header("Location: ppl_informe.php?seccion=situacion-laboral&id=".$idppl);
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
        $db->rollBack();
    }
}
?>
<form method="POST" class="container mt-3">
    <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">
    <div class="row">
        <div class="col">
    <!-- Sección Laboral -->
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="mb-0">Informe Laboral</h5>
        </div>
        <div class="card-body table-responsive">
        <!-- ------------------- -->
        <a href="ppl_informe.php?seccion=situacion-laboral&id=<?php echo $idppl; ?>">
            <div class="btn btn-secondary mb-4">Cancelar</div>
        </a>
        <!-- ------------------- -->
        
            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="tiene_exp" 
                    id="tieneExp" 
                    <?php echo isset($laboral['tiene_exp']) && $laboral['tiene_exp'] ? 'checked' : ''; ?>
                >
                <label class="form-check-label" for="tieneExp">¿Tiene experiencia previa a la detención?</label>
            </div>

            <div class="mb-3" id="experienciaContainer" style="display: <?php echo isset($laboral['tiene_exp']) && $laboral['tiene_exp'] ? 'block' : 'none'; ?>">
                <label for="experiencia" class="form-label">Describa su experiencia:</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="experiencia" 
                    name="experiencia" 
                    value="<?php echo htmlspecialchars($laboral['experiencia'] ?? ''); ?>" 
                    placeholder="Ingrese detalles de su experiencia..."
                >
            </div>

            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="se_capacito" 
                    id="seCapacito" 
                    <?php echo isset($laboral['se_capacito']) && $laboral['se_capacito'] ? 'checked' : ''; ?>
                >
                <label class="form-check-label" for="seCapacito">¿Se capacitó en oficios?</label>
            </div>

            <div class="mb-3" id="capacitacionContainer" style="display: <?php echo isset($laboral['se_capacito']) && $laboral['se_capacito'] ? 'block' : 'none'; ?>">
                <label for="enQueSeCapacito" class="form-label">¿En qué se capacitó?</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="enQueSeCapacito" 
                    name="en_que_se_capacito" 
                    value="<?php echo htmlspecialchars($laboral['en_que_se_capacito'] ?? ''); ?>" 
                    placeholder="Ingrese detalles de su capacitación..."
                >
            </div>

            <div class="form-check mb-3">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="posee_certific" 
                id="poseeCertific" 
                <?php echo isset($laboral['posee_certific']) && $laboral['posee_certific'] ? 'checked' : ''; ?>
            >
            <label class="form-check-label" for="poseeCertific">Posee certificación del oficio</label>
        </div>

        <div class="mb-3" id="formacInteresContainer" style="display: <?php echo isset($laboral['posee_certific']) && $laboral['posee_certific'] ? 'block' : 'none'; ?>">
            <label for="formacInteres" class="form-label">Capacitación y/o formación que resulte de interés:</label>
            <input 
                type="text" 
                class="form-control" 
                id="formacInteres" 
                name="formac_interes" 
                value="<?php echo htmlspecialchars($laboral['formac_interes'] ?? ''); ?>" 
                placeholder="Ingrese formación de interés..."
            >
        </div>

            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="tiene_incl_lab" 
                    id="tieneInclLab" 
                    <?php echo isset($laboral['tiene_incl_lab']) && $laboral['tiene_incl_lab'] ? 'checked' : ''; ?>
                >
                <label class="form-check-label" for="tieneInclLab">Presenta posibilidad de inclusión laboral</label>
            </div>

            <div class="mb-3" id="lugarInclusionContainer" style="display: <?php echo isset($laboral['tiene_incl_lab']) && $laboral['tiene_incl_lab'] ? 'block' : 'none'; ?>">
                <label for="lugarInclusion" class="form-label">¿Cuál/es?</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="lugarInclusion" 
                    name="lugar_inclusion" 
                    value="<?php echo htmlspecialchars($laboral['lugar_inclusion'] ?? ''); ?>" 
                    placeholder="Ingrese lugar de inclusión..."
                >
            </div>
        </div>
    </div>
    </div>
    <div class="col">

    <!-- Sección Asistencia Espiritual -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="mb-0">Asistencia Espiritual</h5>
        </div>
        <div class="card-body table-responsive">
            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="practica_culto" 
                    id="practicaCulto" 
                    <?php echo isset($espiritual['practica_culto']) && $espiritual['practica_culto'] ? 'checked' : ''; ?>
                >
                <label class="form-check-label" for="practicaCulto">¿Practica algún culto?</label>
            </div>

            <div class="mb-3" id="cultoContainer" style="display: <?php echo isset($espiritual['practica_culto']) && $espiritual['practica_culto'] ? 'block' : 'none'; ?>">
                <label for="culto" class="form-label">Especifique el culto:</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="culto" 
                    name="culto" 
                    value="<?php echo htmlspecialchars($espiritual['culto'] ?? ''); ?>" 
                    placeholder="Ingrese el nombre del culto..."
                >
            </div>

            <div class="form-check mb-3">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="desea_participar" 
                    id="deseaParticipar" 
                    <?php echo isset($espiritual['desea_participar']) && $espiritual['desea_participar'] ? 'checked' : ''; ?>
                >
                <label class="form-check-label" for="deseaParticipar">¿Desea participar en alguna actividad religiosa?</label>
            </div>

            <div class="mb-3" id="actividadContainer" style="display: <?php echo isset($espiritual['desea_participar']) && $espiritual['desea_participar'] ? 'block' : 'none'; ?>">
                <label for="eleccionActividad" class="form-label">Especifique la actividad:</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="eleccionActividad" 
                    name="eleccion_actividad" 
                    value="<?php echo htmlspecialchars($espiritual['eleccion_actividad'] ?? ''); ?>" 
                    placeholder="Ingrese la actividad deseada..."
                >
            </div>
        </div>
        <div class="text-center">
            <button name="guardar_4" type="submit" class="btn btn-primary mb-3">Guardar Información</button>
        </div>
    </div>
    <!-- --------------------- -->        
    </div>
    </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Laboral Section
    var tieneExpCheckbox = document.getElementById('tieneExp');
    var experienciaContainer = document.getElementById('experienciaContainer');
    tieneExpCheckbox.addEventListener('change', function() {
        experienciaContainer.style.display = this.checked ? 'block' : 'none';
    });

    var seCapacitoCheckbox = document.getElementById('seCapacito');
    var capacitacionContainer = document.getElementById('capacitacionContainer');
    seCapacitoCheckbox.addEventListener('change', function() {
        capacitacionContainer.style.display = this.checked ? 'block' : 'none';
    });

    var poseeCertificCheckbox = document.getElementById('poseeCertific');
    var formacInteresContainer = document.getElementById('formacInteresContainer');
    poseeCertificCheckbox.addEventListener('change', function() {
        formacInteresContainer.style.display = this.checked ? 'block' : 'none';
    });

    var tieneInclLabCheckbox = document.getElementById('tieneInclLab');
    var lugarInclusionContainer = document.getElementById('lugarInclusionContainer');
    tieneInclLabCheckbox.addEventListener('change', function() {
        lugarInclusionContainer.style.display = this.checked ? 'block' : 'none';
    });

    // Spiritual Assistance Section
    var practicaCultoCheckbox = document.getElementById('practicaCulto');
    var cultoContainer = document.getElementById('cultoContainer');
    practicaCultoCheckbox.addEventListener('change', function() {
        cultoContainer.style.display = this.checked ? 'block' : 'none';
    });

    var deseaParticiparCheckbox = document.getElementById('deseaParticipar');
    var actividadContainer = document.getElementById('actividadContainer');
    deseaParticiparCheckbox.addEventListener('change', function() {
        actividadContainer.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
</body>
</html>