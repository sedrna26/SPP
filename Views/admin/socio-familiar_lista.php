<?php


// Obtener el ID del PPsL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idppl > 0) {
    // Obtener datos de ppl_familiar_info
    $stmt = $db->prepare("SELECT * FROM ppl_familiar_info WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $familiaresInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos de ppl_situacion_sociofamiliar
    $stmt = $db->prepare("SELECT * FROM ppl_situacion_sociofamiliar WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $situacionSociofamiliar = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos de ppl_padres
    $stmt = $db->prepare("SELECT * FROM ppl_padres WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $padres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener datos de ppl_hermanos
    $stmt = $db->prepare("SELECT * FROM ppl_hermanos WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $hermanos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener datos de ppl_pareja
    $stmt = $db->prepare("SELECT * FROM ppl_pareja WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $pareja = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos de ppl_hijos
    $stmt = $db->prepare("SELECT * FROM ppl_hijos WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $hijos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener datos de ppl_otros_visitantes
    $stmt = $db->prepare("SELECT * FROM ppl_otros_visitantes WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $otrosVisitantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="card">
    
        <div class="card-body">
        <div class="d-flex align-items-center">
            <h3 class="">Información Familiar General</h3>
            <a class="btn btn-warning ml-3 btn-sm" href='socio-familia_edit.php?id=<?php echo $idppl; ?>'>Editar Socio-Familia</a>
        </div>

        <div class="row mt-3">
        <?php if ($familiaresInfo): ?>
            <p>Familiares FF.AA: <?= $familiaresInfo['familiares_ffaa'] ? 'Sí' : 'No'; ?></p>
            <p>Detalles FF.AA: <?= $familiaresInfo['ffaa_detalles']; ?></p>
            <p>Familiares Detenidos: <?= $familiaresInfo['familiares_detenidos'] ? 'Sí' : 'No'; ?></p>
            <p>Detalles Detenidos: <?= $familiaresInfo['detenidos_detalles']; ?></p>
            <p>Teléfono Familiar: <?= $familiaresInfo['telefono_familiar']; ?></p>
            <p>Posee DNI: <?= $familiaresInfo['posee_dni'] ? 'Sí' : 'No'; ?></p>
            <p>Motivo No DNI: <?= $familiaresInfo['motivo_no_dni']; ?></p>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Situación Sociofamiliar</h3>
        <?php if ($situacionSociofamiliar): ?>
            <p>Edad de Inicio Laboral: <?= $situacionSociofamiliar['edad_inicio_laboral']; ?></p>
            <p>Situación Económica Precaria: <?= $situacionSociofamiliar['situacion_economica_precaria'] ? 'Sí' : 'No'; ?></p>
            <p>Mendicidad en la Calle: <?= $situacionSociofamiliar['mendicidad_calle'] ? 'Sí' : 'No'; ?></p>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Padres</h3>
        <?php if ($padres): ?>
            <?php foreach ($padres as $padre): ?>
                <h4><?= $padre['tipo'] == 'PADRE' ? 'Padre' : 'Madre'; ?></h4>
                <p>Nombre: <?= $padre['nombre']; ?></p>
                <p>Apellido: <?= $padre['apellido']; ?></p>
                <p>Edad: <?= $padre['edad']; ?></p>
                <p>Nacionalidad: <?= $padre['nacionalidad']; ?></p>
                <p>Estado Civil: <?= $padre['estado_civil']; ?></p>
                <p>Grado de Instrucción: <?= $padre['instruccion']; ?></p>
                <p>Visita: <?= $padre['visita'] ? 'Sí' : 'No'; ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Hermanos</h3>
        <?php if ($hermanos): ?>
            <?php foreach ($hermanos as $hermano): ?>
                <p>Nombre: <?= $hermano['nombre']; ?></p>
                <p>Apellido: <?= $hermano['apellido']; ?></p>
                <p>Edad: <?= $hermano['edad']; ?></p>
                <p>Visita: <?= $hermano['visita'] ? 'Sí' : 'No'; ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Pareja</h3>
        <?php if ($pareja): ?>
            <p>Nombre: <?= ($pareja['nombre']); ?></p>
            <p>Apellido: <?= ($pareja['apellido']); ?></p>
            <p>Edad: <?= ($pareja['edad']); ?></p>
            <p>Nacionalidad: <?= ($pareja['nacionalidad']); ?></p>
            <p>Grado de Instrucción: <?= ($pareja['instruccion']); ?></p>
            <p>Tipo de Unión: <?= ($pareja['tipo_union']); ?></p>
            <p>Visita: <?= $pareja['visita'] ? 'Sí' : 'No'; ?></p>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Hijos</h3>
        <?php if ($hijos): ?>
            <?php foreach ($hijos as $hijo): ?>
                <p>Nombre: <?= ($hijo['nombre']); ?></p>
                <p>Apellido: <?= ($hijo['apellido']); ?></p>
                <p>Edad: <?= ($hijo['edad']); ?></p>
                <p>Fallecido: <?= $hijo['fallecido'] ? 'Sí' : 'No'; ?></p>
                <p>Visita: <?= $hijo['visita'] ? 'Sí' : 'No'; ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Otros Visitantes</h3>
        <?php if ($otrosVisitantes): ?>
            <?php foreach ($otrosVisitantes as $visitante): ?>
                <p>Nombre: <?= ($visitante['nombre']); ?></p>
                <p>Apellido: <?= ($visitante['apellido']); ?></p>
                <p>Teléfono: <?= ($visitante['telefono']); ?></p>
                <p>Domicilio: <?= ($visitante['domicilio']); ?></p>
                <p>Vínculo Filial: <?= ($visitante['vinculo_filial']); ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>
        </div>     
    </div>
</div>