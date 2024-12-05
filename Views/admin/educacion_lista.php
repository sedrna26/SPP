<?php
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos de educación
if ($idppl > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM educacion WHERE id_ppl = ? LIMIT 1");
        $stmt->execute([$idppl]);
        $educacion = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al obtener la información educativa: " . $e->getMessage() . "</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>ID de PPL no válido</div>";
    exit();
}
?>

<div class="container mt-3">
    <div class="card">
        <!-- ------------------------ -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Información Educativa del PPL</h4>
            <?php
                if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
            ?>
                <a href="educacion_edit.php?id=<?php echo $idppl; ?>" class='btn btn-warning btn-sm'>
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
            <?php }?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="">
                    <div class="row">
                        <div class="col">
                            <h5 class="mb-3 ">Alfabetización</h5>
                            <div class="alert <?php echo $educacion['sabe_leer_escrib'] ? 'bg-success' : 'bg-danger'; ?> text-white">
                                <span class="badge fs-5 ">
                                    <?php echo $educacion['sabe_leer_escrib'] ? 'Sabe leer y escribir' : 'No sabe leer y escribir'; ?>
                                </span>
                                
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="mt-3 mb-3">Nivel Educativo</h5>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <strong>Primaria:</strong> 
                                    <span class="badge fs-5 <?php echo $educacion['primaria'] == 'Completa' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo htmlspecialchars($educacion['primaria']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <strong>Secundaria:</strong> 
                                    <span class="badge fs-5 <?php echo $educacion['secundaria'] == 'Completa' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo htmlspecialchars($educacion['secundaria']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-3 mb-3">Interés en Actividades</h5>
                    <?php if ($educacion['tiene_educ_formal']) : ?>
                    <div class="alert bg-success text-white mb-2">
                        <strong>Educación Formal:</strong> 
                        <?php echo htmlspecialchars($educacion['educ-formal']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($educacion['tiene_educ_no_formal']) : ?>
                    <div class="alert bg-success text-white mb-2">
                        <strong>Educación No Formal:</strong> 
                        <?php echo htmlspecialchars($educacion['educ-no-formal']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($educacion['quiere_deporte']) : ?>
                    <div class="alert bg-success text-white mb-2">
                        <strong>Actividad Deportiva:</strong> 
                        <?php echo htmlspecialchars($educacion['sec-deporte']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($educacion['quiere_act_artistica']) : ?>
                    <div class="alert bg-success text-white mb-2">
                        <strong>Actividad Artística:</strong> 
                        <?php echo htmlspecialchars($educacion['act-artistica']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
