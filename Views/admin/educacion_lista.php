<?php

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos de educación
$stmt = $db->prepare("SELECT * FROM educacion WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$educacion = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="card">

    <div class="card-body">
        <div class="d-flex align-items-center">
            <h3>Información Educativa</h3>
            <a class="btn btn-warning ml-3 btn-sm" href='educacion_edit.php?id=<?php echo $idppl; ?>'>Editar educacion</a>
        </div>

        <div class="row mt-3">
            <?php if ($educacion): ?>
                <p>Sabe Leer y Escribir: <?= $educacion['sabe_leer_escrib'] ? 'Sí' : 'No'; ?></p>
                <p>Nivel Primaria: <?= htmlspecialchars($educacion['primaria']); ?></p>
                <p>Nivel Secundaria: <?= htmlspecialchars($educacion['secundaria']); ?></p>
                <p>Tiene Educación Formal: <?= $educacion['tiene_educ_formal'] ? 'Sí' : 'No'; ?></p>
                <p>Educación Formal: <?= htmlspecialchars($educacion['educ-formal']); ?></p>
                <p>Tiene Educación No Formal: <?= $educacion['tiene_educ_no_formal'] ? 'Sí' : 'No'; ?></p>
                <p>Educación No Formal: <?= htmlspecialchars($educacion['educ-no-formal']); ?></p>
                <p>Quiere Participar en Deportes: <?= $educacion['quiere_deporte'] ? 'Sí' : 'No'; ?></p>
                <p>Sección Deportiva: <?= htmlspecialchars($educacion['sec-deporte']); ?></p>
                <p>Quiere Participar en Actividades Artísticas: <?= $educacion['quiere_act_artistica'] ? 'Sí' : 'No'; ?></p>
                <p>Actividad Artística: <?= htmlspecialchars($educacion['act-artistica']); ?></p>
            <?php else: ?>
                <p>No hay información educativa disponible.</p>
            <?php endif; ?>
        </div>
    </div>
</div>