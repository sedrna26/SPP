<?php

$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

$observaciones = [];
try {
    $stmt = $db->prepare("SELECT * FROM observaciones WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $observaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al obtener las observaciones: " . $e->getMessage() . "</div>";
}

?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Observaciones</h4>
            <?php
                if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
            ?>
                <a href='observaciones_edit.php?id=<?php echo $idppl; ?>' class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
            <?php }?>
        </div>
        <div class="card-body">
            <?php if (count($observaciones) > 0): ?>
                <div class="list-group">
                    <?php foreach ($observaciones as $index => $observacion): ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">                                     
                            </div>
                            <p class="mb-1">
                                <?php echo htmlspecialchars($observacion['observacion']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    No hay observaciones registradas.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>