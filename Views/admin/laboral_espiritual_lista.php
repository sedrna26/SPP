<?php $idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

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

    <style>
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
    </style>
    <div class="container ">
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
                                <span class="info-label">Tiene Experiencia Previa:</span>
                                <span class="info-value"><?= $laboral['tiene_exp'] ? 'Sí' : 'No'; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Experiencia:</span>
                                <span class="info-value"><?= htmlspecialchars($laboral['experiencia']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Se Capacitó:</span>
                                <span class="info-value"><?= $laboral['se_capacito'] ? 'Sí' : 'No'; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">En Qué se Capacitó:</span>
                                <span class="info-value"><?= htmlspecialchars($laboral['en_que_se_capacito']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Posee Certificación:</span>
                                <span class="info-value"><?= $laboral['posee_certific'] ? 'Sí' : 'No'; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Formación de Interés:</span>
                                <span class="info-value"><?= htmlspecialchars($laboral['formac_interes']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Tiene Inclusión Laboral:</span>
                                <span class="info-value"><?= $laboral['tiene_incl_lab'] ? 'Sí' : 'No'; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Lugar de Inclusión:</span>
                                <span class="info-value"><?= htmlspecialchars($laboral['lugar_inclusion']); ?></span>
                            </div>
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
                                <span class="info-label">Practica Culto:</span>
                                <span class="info-value"><?= $asistenciaEspiritual['practica_culto'] ? 'Sí' : 'No'; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Culto:</span>
                                <span class="info-value"><?= htmlspecialchars($asistenciaEspiritual['culto']); ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Desea Participar:</span>
                                <span class="info-value"><?= $asistenciaEspiritual['desea_participar'] ? 'Sí' : 'No'; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="info-label">Elección de Actividad:</span>
                                <span class="info-value"><?= htmlspecialchars($asistenciaEspiritual['eleccion_actividad']); ?></span>
                            </div>
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