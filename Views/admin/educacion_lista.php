<?php
// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos de educación
$stmt = $db->prepare("SELECT * FROM educacion WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$educacion = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-graduation-cap me-2"></i>Informe Educacional
            </h4>
            <a href='educacion_edit.php?id=<?php echo $idppl; ?>' class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
        </div>

        <?php if ($educacion): ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-primary">Alfabetización</h5>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Sabe Leer y Escribir
                                    <span class="badge py-2 px-3 fs-6 <?= $educacion['sabe_leer_escrib'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?= $educacion['sabe_leer_escrib'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5 class="text-primary">Educación Formal</h5>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Nivel Primaria
                                    <span class="badge py-2 px-3 fs-6 bg-dark">
                                        <?= htmlspecialchars($educacion['primaria']); ?>
                                    </span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Nivel Secundaria
                                    <span class="badge py-2 px-3 fs-6 bg-dark">
                                        <?= htmlspecialchars($educacion['secundaria']); ?>
                                    </span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Tiene Educación Formal
                                    <span class="badge py-2 px-3 fs-6 <?= $educacion['tiene_educ_formal'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?= $educacion['tiene_educ_formal'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </div>
                                <!-- ... rest of the code remains similar ... -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Similar modifications for other sections -->
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Tiene Educación No Formal
                            <span class="badge py-2 px-3 fs-6 <?= $educacion['tiene_educ_no_formal'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?= $educacion['tiene_educ_no_formal'] ? 'Sí' : 'No'; ?>
                            </span>
                        </div>

                        <!-- For sports and artistic activities -->
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                            Quiere Participar en Deportes
                            <span class="badge py-2 px-3 fs-6 <?= $educacion['quiere_deporte'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?= $educacion['quiere_deporte'] ? 'Sí' : 'No'; ?>
                            </span>
                        </div>

                        <!-- Similar modification for artistic activities -->
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- ... rest of the code remains the same ... -->
        <?php endif; ?>
    </div>
</div>