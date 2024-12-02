<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $db->prepare("SELECT * FROM clasificacion WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$clasificacion = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <h4>Clasificación del PPL</h4>
        <a class="btn btn-warning ml-3 btn-sm" href='clasificacion_edit.php?id=<?php echo $idppl; ?>'>Editar Clasificacion</a>
    </div>

    <div class="row mt-3">   
        
        <?php if ($clasificacion): ?>
            <p>Clasificación: <?= htmlspecialchars($clasificacion['clasificacion']); ?></p>
            <p>Sugerencia de Ubicación: <?= htmlspecialchars($clasificacion['sugerencia']); ?></p>
            <p>Número de Sector: <?= htmlspecialchars($clasificacion['sector_nro']); ?></p>
            <p>Número de Pabellón: <?= htmlspecialchars($clasificacion['pabellon_nro']); ?></p>
        <?php else: ?>
            <p>No hay información de clasificación disponible.</p>
        <?php endif; ?>
        </div>
    </div>
</div>