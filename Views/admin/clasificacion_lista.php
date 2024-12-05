<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $db->prepare("SELECT * FROM clasificacion WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$clasificacion = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Clasificación del PPL</h4>
            <?php
                if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
            ?>
                <a class="btn btn-warning btn-sm" href='clasificacion_edit.php?id=<?php echo $idppl; ?>'>
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
            <?php }?>
        </div>
        <div class="card-body">
            <?php if ($clasificacion): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="list-group">
                            <div class="list-group-item">
                                <h6 class="mb-1 text-muted">Clasificación</h6>
                                <p class="mb-0 fw-bold"><?= htmlspecialchars($clasificacion['clasificacion']); ?></p>
                            </div>
                            <div class="list-group-item">
                                <h6 class="mb-1 text-muted">Sugerencia de Ubicación</h6>
                                <p class="mb-0 fw-bold"><?= htmlspecialchars($clasificacion['sugerencia']); ?></p>
                            </div>
                            <div class="list-group-item">
                                <h6 class="mb-1 text-muted">Número de Sector</h6>
                                <p class="mb-0 fw-bold"><?= htmlspecialchars($clasificacion['sector_nro']); ?></p>
                            </div>
                            <div class="list-group-item">
                                <h6 class="mb-1 text-muted">Número de Pabellón</h6>
                                <p class="mb-0 fw-bold"><?= htmlspecialchars($clasificacion['pabellon_nro']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay información de clasificación disponible.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>