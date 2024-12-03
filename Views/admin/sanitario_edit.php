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
        error_log("Error en el registro de auditoría: " . $e->getMessage());
    }
}

// Verificar si existe el registro antes de intentar editarlo
$sql = "SELECT * FROM datos_medicos WHERE id_ppl = :id_ppl";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id_ppl', $idppl);
$stmt->execute();
$datos_medicos = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el registro, crearlo
if (!$datos_medicos) {
    try {
        $sql = "INSERT INTO datos_medicos (id_ppl) VALUES (:id_ppl)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ppl', $idppl);
        $stmt->execute();
        
        // Recuperar el registro recién creado
        $sql = "SELECT * FROM datos_medicos WHERE id_ppl = :id_ppl";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_ppl', $idppl);
        $stmt->execute();
        $datos_medicos = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error al crear el registro: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    try {
        // Validación de campos numéricos
        $peso = filter_var($_POST['peso_actual'], FILTER_VALIDATE_FLOAT);
        $talla = filter_var($_POST['talla'], FILTER_VALIDATE_FLOAT);
        
        if ($peso === false || $talla === false) {
            throw new Exception("El peso y la talla deben ser valores numéricos válidos");
        }

        if ($peso <= 0 || $talla <= 0) {
            throw new Exception("El peso y la talla deben ser valores mayores a 0");
        }

        $db->beginTransaction();

        $sql = "UPDATE datos_medicos SET 
                hipertension = :hipertension,
                diabetes = :diabetes,
                enfermedad_corazon = :enfermedad_corazon,
                enfermedad_corazon_cual = :enfermedad_corazon_cual,
                asma = :asma,
                epilepsia = :epilepsia,
                alergia = :alergia,
                alergia_especifique = :alergia_especifique,
                es_celiaco = :es_celiaco,
                bulimia_anorexia = :bulimia_anorexia,
                medicacion = :medicacion,
                metabolismo = :metabolismo,
                embarazo = :embarazo,
                hepatitis = :hepatitis,
                mononucleosis = :mononucleosis,
                otras_enfermedades = :otras_enfermedades,
                peso_actual = :peso_actual,
                talla = :talla,
                imc = :imc,
                diagnostico = :diagnostico,
                tipificacion_dieta = :tipificacion_dieta
                WHERE id_ppl = :id_ppl";

        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':hipertension', isset($_POST['hipertension']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':diabetes', isset($_POST['diabetes']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':enfermedad_corazon', isset($_POST['enfermedad_corazon']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':enfermedad_corazon_cual', trim($_POST['enfermedad_corazon_cual']));
        $stmt->bindValue(':asma', isset($_POST['asma']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':epilepsia', isset($_POST['epilepsia']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':alergia', isset($_POST['alergia']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':alergia_especifique', trim($_POST['alergia_especifique']));
        $stmt->bindValue(':es_celiaco', isset($_POST['es_celiaco']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':bulimia_anorexia', isset($_POST['bulimia_anorexia']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':medicacion', trim($_POST['medicacion']));
        $stmt->bindValue(':metabolismo', isset($_POST['metabolismo']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':embarazo', isset($_POST['embarazo']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':hepatitis', isset($_POST['hepatitis']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':mononucleosis', isset($_POST['mononucleosis']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':otras_enfermedades', trim($_POST['otras_enfermedades']));
        $stmt->bindValue(':peso_actual', $peso);
        $stmt->bindValue(':talla', $talla);
        $stmt->bindValue(':imc', $_POST['imc']);
        $stmt->bindValue(':diagnostico', trim($_POST['diagnostico']));
        $stmt->bindValue(':tipificacion_dieta', trim($_POST['tipificacion_dieta']));
        $stmt->bindValue(':id_ppl', $idppl, PDO::PARAM_INT);

        $stmt->execute();

        $db->commit();
        
        registrarAuditoria(
            $db, 
            'Editar Datos Médicos/Informe', 
            'datos_medicos', 
            $idppl, 
            "Se editaron los datos médicos para el paciente con ID: " . $idppl
        );
            
        header("Location: ppl_informe.php?seccion=informe-sanitario&id=" . $idppl);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $error_message = $e->getMessage();
    }
}
?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <h5 class="card-header bg-dark text-white">
            <?php echo $datos_medicos ? 'Actualizar' : 'Crear'; ?> Datos Médicos
        </h5>
        <div class="card-body">
            <a href="ppl_informe.php?seccion=informe-sanitario&id=<?php echo $idppl; ?>" 
               class="btn btn-secondary mb-3">
                Cancelar
            </a>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">
                
                <!-- Condiciones Médicas -->
                <div class="mb-4">
                    <h5 class="mb-3">Condiciones Médicas</h5>
                    
                    <?php 
                    $condiciones = [
                        'hipertension' => 'Hipertensión',
                        'diabetes' => 'Diabetes',
                        'enfermedad_corazon' => 'Enfermedad del Corazón',
                        'asma' => 'Asma',
                        'epilepsia' => 'Epilepsia',
                        'alergia' => 'Alergia',
                        'es_celiaco' => 'Es Celíaco',
                        'bulimia_anorexia' => 'Bulimia/Anorexia',
                        'metabolismo' => 'Metabolismo',
                        'embarazo' => 'Embarazo',
                        'hepatitis' => 'Hepatitis',
                        'mononucleosis' => 'Mononucleosis'
                    ];

                    foreach ($condiciones as $campo => $label): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="<?php echo $campo; ?>" 
                                   id="<?php echo $campo; ?>" 
                                   <?php echo ($datos_medicos[$campo] ?? false) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="<?php echo $campo; ?>">
                                <?php echo $label; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <!-- Campos específicos que requieren texto adicional -->
                    <div class="mb-3" id="enfermedad_corazon_detail" 
                         style="display: <?php echo ($datos_medicos['enfermedad_corazon'] ?? false) ? 'block' : 'none'; ?>">
                        <label class="form-label">Especifique enfermedad del corazón:</label>
                        <input class="form-control" type="text" name="enfermedad_corazon_cual" 
                               value="<?php echo htmlspecialchars($datos_medicos['enfermedad_corazon_cual'] ?? ''); ?>">
                    </div>

                    <div class="mb-3" id="alergia_detail" 
                         style="display: <?php echo ($datos_medicos['alergia'] ?? false) ? 'block' : 'none'; ?>">
                        <label class="form-label">Especifique alergia:</label>
                        <input class="form-control" type="text" name="alergia_especifique" 
                               value="<?php echo htmlspecialchars($datos_medicos['alergia_especifique'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Medicación:</label>
                        <input class="form-control" type="text" name="medicacion" 
                               value="<?php echo htmlspecialchars($datos_medicos['medicacion'] ?? ''); ?>"
                               placeholder="Especifique medicación">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Otras enfermedades:</label>
                        <input class="form-control" type="text" name="otras_enfermedades" 
                               value="<?php echo htmlspecialchars($datos_medicos['otras_enfermedades'] ?? ''); ?>"
                               placeholder="Especifique otras enfermedades">
                    </div>
                </div>

                <!-- Datos Antropométricos -->
                <div class="mb-4">
                    <h5 class="mb-3">Datos Antropométricos</h5>
                    
                    <div class="mb-3">
                        <label for="peso_actual" class="form-label">Peso actual (kg):</label>
                        <input class="form-control" type="number" step="0.01" name="peso_actual" id="peso_actual" 
                               value="<?php echo htmlspecialchars($datos_medicos['peso_actual'] ?? ''); ?>" 
                               required>
                        <div class="invalid-feedback">
                            Por favor ingrese un peso válido
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="talla" class="form-label">Talla (cm):</label>
                        <input class="form-control" type="number" step="0.1" name="talla" id="talla" 
                               value="<?php echo htmlspecialchars($datos_medicos['talla'] ?? ''); ?>" 
                               required>
                        <div class="invalid-feedback">
                            Por favor ingrese una talla válida
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imc" class="form-label">IMC:</label>
                        <input class="form-control" type="text" name="imc" id="imc" 
                               value="<?php echo htmlspecialchars($datos_medicos['imc'] ?? ''); ?>" 
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label for="diagnostico" class="form-label">Diagnóstico:</label>
                        <input class="form-control" type="text" name="diagnostico" id="diagnostico" 
                               value="<?php echo htmlspecialchars($datos_medicos['diagnostico'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tipificacion_dieta" class="form-label">Tipificación de dieta:</label>
                        <input class="form-control" type="text" name="tipificacion_dieta" id="tipificacion_dieta" 
                               value="<?php echo htmlspecialchars($datos_medicos['tipificacion_dieta'] ?? ''); ?>">
                    </div>
                </div>

                <button name="guardar" type="submit" class="btn btn-primary">
                    <?php echo $datos_medicos ? 'Actualizar' : 'Guardar'; ?> Información
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pesoInput = document.getElementById('peso_actual');
    const tallaInput = document.getElementById('talla');
    const imcInput = document.getElementById('imc');
    const enfermedadCorazonCheck = document.getElementById('enfermedad_corazon');
    const enfermedadCorazonDetail = document.getElementById('enfermedad_corazon_detail');
    const alergiaCheck = document.getElementById('alergia');
    const alergiaDetail = document.getElementById('alergia_detail');

    function calcularIMC() {
        const peso = parseFloat(pesoInput.value);
        const talla = parseFloat(tallaInput.value);
        
        if (peso > 0 && talla > 0) {
            const imc = (peso / Math.pow(talla / 100, 2)).toFixed(2);
            imcInput.value = imc;
        }
    }

    function toggleDetalles(checkbox, detailDiv) {
        if (checkbox && detailDiv) {
            checkbox.addEventListener('change', function() {
                detailDiv.style.display = this.checked ? 'block' : 'none';
            });
        }
    }

    // Event listeners
    pesoInput.addEventListener('input', calcularIMC);
    tallaInput.addEventListener('input', calcularIMC);
    toggleDetalles(enfermedadCorazonCheck, enfermedadCorazonDetail);
    toggleDetalles(alergiaCheck, alergiaDetail);

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>