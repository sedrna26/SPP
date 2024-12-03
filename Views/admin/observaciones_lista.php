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

<body>
    <div class="observaciones-list">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <h3>Observaciones</h3>
                <a class="btn btn-warning ml-3 btn-sm" href='observaciones_edit.php?id=<?php echo $idppl; ?>'>Editar Observaciones</a>
            </div>
            <ul>
                <?php
                if (count($observaciones) > 0):
                    foreach ($observaciones as $observacion):
                ?>
                        <?php echo htmlspecialchars($observacion['observacion']); ?>
                    <?php
                    endforeach;
                else:
                    ?>
                    <li>No hay observaciones registradas.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>