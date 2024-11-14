<?php

// Incluir el archivo de conexión
require_once BASE_PATH . '/conn/connection.php';

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idppl > 0) {
    // Obtener datos de laboral
    $stmt = $db->prepare("SELECT * FROM laboral WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $laboral = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener datos de asistencia_espiritual
    $stmt = $db->prepare("SELECT * FROM asistencia_espiritual WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $asistenciaEspiritual = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboral y Espiritual</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h3 {
            margin-top: 30px;
        }

        p {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Informe Laboral</h3>
        <?php if ($laboral): ?>
            <p>Tiene Experiencia Previa: <?= $laboral['tiene_exp'] ? 'Sí' : 'No'; ?></p>
            <p>Experiencia: <?= htmlspecialchars($laboral['experiencia']); ?></p>
            <p>Se Capacitó: <?= $laboral['se_capacito'] ? 'Sí' : 'No'; ?></p>
            <p>En Qué se Capacitó: <?= htmlspecialchars($laboral['en_que_se_capacito']); ?></p>
            <p>Posee Certificación: <?= $laboral['posee_certific'] ? 'Sí' : 'No'; ?></p>
            <p>Formación de Interés: <?= htmlspecialchars($laboral['formac_interes']); ?></p>
            <p>Tiene Inclusión Laboral: <?= $laboral['tiene_incl_lab'] ? 'Sí' : 'No'; ?></p>
            <p>Lugar de Inclusión: <?= htmlspecialchars($laboral['lugar_inclusion']); ?></p>
        <?php else: ?>
            <p>No hay información laboral disponible.</p>
        <?php endif; ?>
        <h3>Asistencia Espiritual</h3>
        <?php if ($asistenciaEspiritual): ?>
            <p>Practica Culto: <?= $asistenciaEspiritual['practica_culto'] ? 'Sí' : 'No'; ?></p>
            <p>Culto: <?= htmlspecialchars($asistenciaEspiritual['culto']); ?></p>
            <p>Desea Participar: <?= $asistenciaEspiritual['desea_participar'] ? 'Sí' : 'No'; ?></p>
            <p>Elección de Actividad: <?= htmlspecialchars($asistenciaEspiritual['eleccion_actividad']); ?></p>
        <?php else: ?>
            <p>No hay información de asistencia espiritual disponible.</p>
        <?php endif; ?>
    </div>
</body>

</html>