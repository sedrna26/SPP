<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $db->prepare("SELECT * FROM psiquiatrico_psicologico WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$psiquia_psico = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="containermt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 ">Informe Psiquiátrico y Psicológico</h4>
            <a class="btn btn-warning btn-sm" href='psiquia_psico_edit.php?id=<?php echo $idppl; ?>'>
                <i class="fas fa-edit me-1"></i> Editar
            </a>
        </div>

        <div class="card-body">
            <?php if ($psiquia_psico): ?>
                <!-- Información Psiquiátrica -->
                <div class="mb-4">
                    <h5 class="text-primary border-bottom pb-2">Información Psiquiátrica</h5>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Diagnóstico Psiquiátrico:</div>
                                <div class="col-md-8">
                                    <?php
                                    if ($psiquia_psico['si_no_diagnostico']) {
                                        echo htmlspecialchars($psiquia_psico['diagnostico_psiquiatrico'] ?? 'Diagnóstico presente sin detalles');
                                    } else {
                                        echo 'Sin diagnóstico previo';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Institucionalizaciones:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['institucionalizaciones_centros_rehab']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informe Psicológico -->
                <div>
                    <h5 class="text-primary border-bottom pb-2">Informe Psicológico</h5>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Orientación Témporo-Espacial:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['orientacion_temporo_espacial']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Juicio de Realidad:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['juicio_realidad']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Ideación:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['ideacion']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Estado Afectivo:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['estado_afectivo']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Antecedentes de Autolesiones:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['antecedentes_autolesiones']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Antecedentes de Consumo:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['antecedentes_consumo_sustancias']); ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($psiquia_psico['edad_inicio_consumo']): ?>
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Edad de Inicio de Consumo:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['edad_inicio_consumo']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-md-4 fw-bold">Datos de Interés e Intervención:</div>
                                <div class="col-md-8">
                                    <?= htmlspecialchars($psiquia_psico['datos_interes_intervencion']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay información psiquiátrica o psicológica disponible.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>