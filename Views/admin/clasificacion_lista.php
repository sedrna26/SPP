<?php


// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos de clasificación
$stmt = $db->prepare("SELECT * FROM clasificacion WHERE id_ppl = ?");
$stmt->execute([$idppl]);
$clasificacion = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clasificación del PPL</title>
    <style>
        

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
        <h3>Clasificación del PPL</h3>
        <?php if ($clasificacion): ?>
            <p>Clasificación: <?= htmlspecialchars($clasificacion['clasificacion']); ?></p>
            <p>Sugerencia de Ubicación: <?= htmlspecialchars($clasificacion['sugerencia']); ?></p>
            <p>Número de Sector: <?= htmlspecialchars($clasificacion['sector_nro']); ?></p>
            <p>Número de Pabellón: <?= htmlspecialchars($clasificacion['pabellon_nro']); ?></p>
        <?php else: ?>
            <p>No hay información de clasificación disponible.</p>
        <?php endif; ?>
    </div>
</body>

</html>