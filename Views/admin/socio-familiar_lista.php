<?php
// Definir la ruta base del proyecto
define('BASE_PATH', dirname(__DIR__, 2));

// Incluir el archivo de conexión
require_once BASE_PATH . '/conn/connection.php';

// Obtener el ID del PPL de la URL
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





<body>
    <div class="container mt-3">


        <h3>Información Familiar General</h3>
        <?php if ($familiaresInfo): ?>
            <p>Familiares FF.AA: <?= $familiaresInfo['familiares_ffaa'] ? 'Sí' : 'No'; ?></p>
            <p>Detalles FF.AA: <?= htmlspecialchars($familiaresInfo['ffaa_detalles']); ?></p>
            <p>Familiares Detenidos: <?= $familiaresInfo['familiares_detenidos'] ? 'Sí' : 'No'; ?></p>
            <p>Detalles Detenidos: <?= htmlspecialchars($familiaresInfo['detenidos_detalles']); ?></p>
            <p>Teléfono Familiar: <?= htmlspecialchars($familiaresInfo['telefono_familiar']); ?></p>
            <p>Posee DNI: <?= $familiaresInfo['posee_dni'] ? 'Sí' : 'No'; ?></p>
            <p>Motivo No DNI: <?= htmlspecialchars($familiaresInfo['motivo_no_dni']); ?></p>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Situación Sociofamiliar</h3>
        <?php if ($situacionSociofamiliar): ?>
            <p>Edad de Inicio Laboral: <?= htmlspecialchars($situacionSociofamiliar['edad_inicio_laboral']); ?></p>
            <p>Situación Económica Precaria: <?= $situacionSociofamiliar['situacion_economica_precaria'] ? 'Sí' : 'No'; ?></p>
            <p>Mendicidad en la Calle: <?= $situacionSociofamiliar['mendicidad_calle'] ? 'Sí' : 'No'; ?></p>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Padres</h3>
        <?php if ($padres): ?>
            <?php foreach ($padres as $padre): ?>
                <h4><?= $padre['tipo'] == 'PADRE' ? 'Padre' : 'Madre'; ?></h4>
                <p>Nombre: <?= htmlspecialchars($padre['nombre']); ?></p>
                <p>Apellido: <?= htmlspecialchars($padre['apellido']); ?></p>
                <p>Edad: <?= htmlspecialchars($padre['edad']); ?></p>
                <p>Nacionalidad: <?= htmlspecialchars($padre['nacionalidad']); ?></p>
                <p>Estado Civil: <?= htmlspecialchars($padre['estado_civil']); ?></p>
                <p>Grado de Instrucción: <?= htmlspecialchars($padre['instruccion']); ?></p>
                <p>Visita: <?= $padre['visita'] ? 'Sí' : 'No'; ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Hermanos</h3>
        <?php if ($hermanos): ?>
            <?php foreach ($hermanos as $hermano): ?>
                <p>Nombre: <?= htmlspecialchars($hermano['nombre']); ?></p>
                <p>Apellido: <?= htmlspecialchars($hermano['apellido']); ?></p>
                <p>Edad: <?= htmlspecialchars($hermano['edad']); ?></p>
                <p>Visita: <?= $hermano['visita'] ? 'Sí' : 'No'; ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Pareja</h3>
        <?php if ($pareja): ?>
            <p>Nombre: <?= htmlspecialchars($pareja['nombre']); ?></p>
            <p>Apellido: <?= htmlspecialchars($pareja['apellido']); ?></p>
            <p>Edad: <?= htmlspecialchars($pareja['edad']); ?></p>
            <p>Nacionalidad: <?= htmlspecialchars($pareja['nacionalidad']); ?></p>
            <p>Grado de Instrucción: <?= htmlspecialchars($pareja['instruccion']); ?></p>
            <p>Tipo de Unión: <?= htmlspecialchars($pareja['tipo_union']); ?></p>
            <p>Visita: <?= $pareja['visita'] ? 'Sí' : 'No'; ?></p>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>

        <h3>Hijos</h3>
        <?php if ($hijos): ?>
            <?php foreach ($hijos as $hijo): ?>
                <p>Nombre: <?= htmlspecialchars($hijo['nombre']); ?></p>
                <p>Apellido: <?= htmlspecialchars($hijo['apellido']); ?></p>
                <p>Edad: <?= htmlspecialchars($hijo['edad']); ?></p>
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
                <p>Nombre: <?= htmlspecialchars($visitante['nombre']); ?></p>
                <p>Apellido: <?= htmlspecialchars($visitante['apellido']); ?></p>
                <p>Teléfono: <?= htmlspecialchars($visitante['telefono']); ?></p>
                <p>Domicilio: <?= htmlspecialchars($visitante['domicilio']); ?></p>
                <p>Vínculo Filial: <?= htmlspecialchars($visitante['vinculo_filial']); ?></p>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay información disponible.</p>
        <?php endif; ?>
    </div>
</body>

</html>