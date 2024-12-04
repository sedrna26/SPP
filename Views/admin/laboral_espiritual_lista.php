<?php
// Assuming database connection is established
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idppl > 0) {
    // Obtener datos de laboral
    $stmt = $db->prepare("SELECT * FROM laboral WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $laboral = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos de asistencia_espiritual
    $stmt = $db->prepare("SELECT * FROM asistencia_espiritual WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $asistenciaEspiritual = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Informe Laboral y Espiritual</h4>
                <a href='laboral_espiritual_edit.php?id=<?php echo $idppl; ?>' class='btn btn-warning btn-sm'>
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-4 text-primary">Información Laboral</h5>
                        <?php if ($laboral): ?>
                            <div class="mb-3">
                                <span class="fw-bold  me-2">Tiene Experiencia Previa:</span>
                                <span class="badge fs-6 <?= $laboral['tiene_exp'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $laboral['tiene_exp'] ? 'Sí' : 'No'; ?>
                                </span>
                            </div>
                            <?php if ($laboral['tiene_exp']): ?>
                                <div class="mb-3">
                                    <span class="fw-bold  me-2">Experiencia:</span>
                                    <span class="text-dark"><?= htmlspecialchars($laboral['experiencia']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <span class="fw-bold  me-2">Se Capacitó:</span>
                                <span class="badge fs-6 <?= $laboral['se_capacito'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $laboral['se_capacito'] ? 'Sí' : 'No'; ?>
                                </span>
                            </div>
                            <?php if ($laboral['se_capacito']): ?>
                                <div class="mb-3">
                                    <span class="fw-bold  me-2">En Qué se Capacitó:</span>
                                    <span class="text-dark"><?= htmlspecialchars($laboral['en_que_se_capacito']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <span class="fw-bold  me-2">Posee Certificación:</span>
                                <span class="badge fs-6 <?= $laboral['posee_certific'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $laboral['posee_certific'] ? 'Sí' : 'No'; ?>
                                </span>
                            </div>

                            <div class="mb-3">
                                <span class="fw-bold  me-2">Formación de Interés:</span>
                                <span class="text-dark"><?= htmlspecialchars($laboral['formac_interes']); ?></span>
                            </div>

                            <div class="mb-3">
                                <span class="fw-bold  me-2">Tiene Inclusión Laboral:</span>
                                <span class="badge fs-6 <?= $laboral['tiene_incl_lab'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $laboral['tiene_incl_lab'] ? 'Sí' : 'No'; ?>
                                </span>
                            </div>
                            <?php if ($laboral['tiene_incl_lab']): ?>
                                <div class="mb-3">
                                    <span class="fw-bold  me-2">Lugar de Inclusión:</span>
                                    <span class="text-dark"><?= htmlspecialchars($laboral['lugar_inclusion']); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                No hay información laboral disponible.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-4 text-primary">Asistencia Espiritual</h5>
                        <?php if ($asistenciaEspiritual): ?>
                            <div class="mb-3">
                                <span class="fw-bold  me-2">Practica Culto:</span>
                                <span class="badge fs-6 <?= $asistenciaEspiritual['practica_culto'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $asistenciaEspiritual['practica_culto'] ? 'Sí' : 'No'; ?>
                                </span>
                            </div>
                            <?php if ($asistenciaEspiritual['practica_culto']): ?>
                                <div class="mb-3">
                                    <span class="fw-bold  me-2">Culto:</span>
                                    <span class="text-dark"><?= htmlspecialchars($asistenciaEspiritual['culto']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <span class="fw-bold  me-2">Desea Participar:</span>
                                <span class="badge fs-6 <?= $asistenciaEspiritual['desea_participar'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $asistenciaEspiritual['desea_participar'] ? 'Sí' : 'No'; ?>
                                </span>
                            </div>
                            <?php if ($asistenciaEspiritual['desea_participar']): ?>
                                <div class="mb-3">
                                    <span class="fw-bold  me-2">Elección de Actividad:</span>
                                    <span class="text-dark"><?= htmlspecialchars($asistenciaEspiritual['eleccion_actividad']); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                No hay información de asistencia espiritual disponible.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>