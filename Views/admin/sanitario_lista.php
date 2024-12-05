<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
$datos = [];
if ($idppl > 0) {
    $stmt = $db->prepare("SELECT * FROM datos_medicos WHERE id_ppl = :id_ppl");
    $stmt->bindParam(':id_ppl', $idppl);
    $stmt->execute();
    $datos = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}
function obtenerValor($campo, $datos, $default = "No especificado")
{
    return isset($datos[$campo]) && $datos[$campo] ? htmlspecialchars($datos[$campo]) : $default;
}
function mostrarCondicion($campo, $datos, $texto)
{
    if (isset($datos[$campo]) && $datos[$campo]) {
        return "<span class='badge bg-primary'>$texto</span>";
    }
    return "";
}
?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Informe Sanitario</h4>
            <?php
                if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) { 
            ?>
                <a class="btn btn-warning ms-3 btn-sm" href='sanitario_edit.php?id=<?php echo $idppl; ?>'>
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
            <?php }?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-4">
                    <h4 class="border-bottom pb-2">Condiciones Médicas</h4>
                    <div class="d-flex flex-wrap gap-2 mb-3 fs-5">
                        <?php echo mostrarCondicion('hipertension', $datos, 'Hipertensión'); ?>
                        <?php echo mostrarCondicion('diabetes', $datos, 'Diabetes'); ?>
                        <?php echo mostrarCondicion('asma', $datos, 'Asma'); ?>
                        <?php echo mostrarCondicion('epilepsia', $datos, 'Epilepsia'); ?>
                        <?php echo mostrarCondicion('es_celiaco', $datos, 'Celíaco'); ?>
                        <?php echo mostrarCondicion('bulimia_anorexia', $datos, 'Bulimia/Anorexia'); ?>
                        <?php echo mostrarCondicion('metabolismo', $datos, 'Metabolismo'); ?>
                        <?php echo mostrarCondicion('embarazo', $datos, 'Embarazo'); ?>
                        <?php echo mostrarCondicion('hepatitis', $datos, 'Hepatitis'); ?>
                        <?php echo mostrarCondicion('mononucleosis', $datos, 'Mononucleosis'); ?>
                    </div>

                    <?php if (isset($datos['enfermedad_corazon']) && $datos['enfermedad_corazon']): ?>
                    <div class="mb-3">
                        <strong>Enfermedad del Corazón:</strong>
                        <p class="mb-0"><?php echo obtenerValor('enfermedad_corazon_cual', $datos); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($datos['alergia']) && $datos['alergia']): ?>
                    <div class="mb-3">
                        <strong>Alergias:</strong>
                        <p class="mb-0"><?php echo obtenerValor('alergia_especifique', $datos); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($datos['medicacion']): ?>
                    <div class="mb-3">
                        <strong>Medicación:</strong>
                        <p class="mb-0"><?php echo obtenerValor('medicacion', $datos); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($datos['otras_enfermedades']): ?>
                    <div class="mb-3">
                        <strong>Otras condiciones médicas:</strong>
                        <p class="mb-0"><?php echo obtenerValor('otras_enfermedades', $datos); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <h4 class="border-bottom pb-2">Datos Antropométricos</h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Peso</h6>
                                    <p class="card-text"><?php echo obtenerValor('peso_actual', $datos); ?> kg</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Talla</h6>
                                    <p class="card-text"><?php echo obtenerValor('talla', $datos); ?> cm</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">IMC</h6>
                                    <p class="card-text"><?php echo obtenerValor('imc', $datos); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="mb-3">
                            <strong>Diagnóstico:</strong>
                            <p class="mb-0"><?php echo obtenerValor('diagnostico', $datos); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Tipificación de dieta:</strong>
                            <p class="mb-0"><?php echo obtenerValor('tipificacion_dieta', $datos); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>