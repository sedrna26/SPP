<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $db->prepare("SELECT * FROM psiquiatrico_psicologico WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$psiquia_psico = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<style>
    h3 {
        margin-top: 30px;
    }

    p {
        margin-bottom: 10px;
    }
</style>
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <h4>Informe Psiquiátrico y Psicológico</h4>
            <a class="btn btn-warning ml-3 btn-sm" href='psiquia_psico_edit.php?id=<?php echo $idppl; ?>'>Editar Informe</a>
        </div>

        <div class="row mt-3">
            <?php if ($psiquia_psico): ?>
                <div class="col-12">
                    <h5>Información Psiquiátrica</h5>
                    <p><strong>Diagnóstico Psiquiátrico:</strong>
                        <?php
                        if ($psiquia_psico['si_no_diagnostico']) {
                            echo htmlspecialchars($psiquia_psico['diagnostico_psiquiatrico'] ?? 'Diagnóstico presente sin detalles');
                        } else {
                            echo 'Sin diagnóstico previo';
                        }
                        ?>
                    </p>
                    <p><strong>Institucionalizaciones en Centros de Rehabilitación:</strong>
                        <?= htmlspecialchars($psiquia_psico['institucionalizaciones_centros_rehab']); ?>
                    </p>

                    <h5>Informe Psicológico</h5>
                    <p><strong>Orientación Témporo-Espacial:</strong>
                        <?= htmlspecialchars($psiquia_psico['orientacion_temporo_espacial']); ?>
                    </p>
                    <p><strong>Juicio de Realidad:</strong>
                        <?= htmlspecialchars($psiquia_psico['juicio_realidad']); ?>
                    </p>
                    <p><strong>Ideación:</strong>
                        <?= htmlspecialchars($psiquia_psico['ideacion']); ?>
                    </p>
                    <p><strong>Estado Afectivo:</strong>
                        <?= htmlspecialchars($psiquia_psico['estado_afectivo']); ?>
                    </p>
                    <p><strong>Antecedentes de Autolesiones:</strong>
                        <?= htmlspecialchars($psiquia_psico['antecedentes_autolesiones']); ?>
                    </p>
                    <p><strong>Antecedentes de Consumo de Sustancias:</strong>
                        <?= htmlspecialchars($psiquia_psico['antecedentes_consumo_sustancias']); ?>
                    </p>
                    <?php if ($psiquia_psico['edad_inicio_consumo']): ?>
                        <p><strong>Edad de Inicio de Consumo:</strong>
                            <?= htmlspecialchars($psiquia_psico['edad_inicio_consumo']); ?>
                        </p>
                    <?php endif; ?>
                    <p><strong>Datos de Interés e Intervención:</strong>
                        <?= htmlspecialchars($psiquia_psico['datos_interes_intervencion']); ?>
                    </p>
                <?php else: ?>
                    <p>No hay información psiquiátrica o psicológica disponible.</p>
                <?php endif; ?>
                </div>
        </div>
    </div>
</div>