<?php

function registrarAuditoria($db, $accion, $tabla_afectada, $registro_id, $detalles)
{
    try {
        $sql = "INSERT INTO auditoria (id_usuario, accion, detalles, tabla_afectada, registro_id, fecha) 
                VALUES (:id_usuario, :accion, :detalles, :tabla_afectada, :registro_id, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':detalles', $detalles);
        $stmt->bindParam(':tabla_afectada', $tabla_afectada);
        $stmt->bindParam(':registro_id', $registro_id);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error en el registro de auditoría: " . $e->getMessage();
    }
}

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_9'])) {
    try {
        // Insertar datos de clasificación
        $stmt = $db->prepare("INSERT INTO clasificacion (id_ppl, clasificacion, sugerencia, 
            sector_nro, pabellon_nro) 
            VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['id_ppl'],
            $_POST['clasificacion'],
            $_POST['sugerencia'],
            $_POST['sector_nro'],
            $_POST['pabellon_nro']
        ]);
        header("Location: ppl_informe.php?seccion=clasificacion&id=".$idppl);
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }

    // Registrar acción en la auditoría
    $accion = 'Agregar Clasificacion';
    $tabla_afectada = 'clasificacion';
    $detalles = "Se insertó una nueva clasificacion para el PPL con ID: $idppl";
    registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);
}
?>

<head>
    <style>
        .form-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #titulo {
            padding-bottom: 1rem;
        }
    </style>
</head>

<body>
    <form method="POST">
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

        <!-- Sección de Clasificación -->
        <div class="form-section">
            <h3 id="titulo">Clasificación del PPL</h3>

            <div class="form-group">
                <label>Clasificación:</label>
                <select name="clasificacion" required>
                    <option value="">Seleccione una clasificación</option>
                    <option value="Adulto Primario">Adulto Primario</option>
                    <option value="Adulto Reiterante">Adulto Reiterante</option>
                    <option value="Adulto Reincidente">Adulto Reincidente</option>
                </select>
            </div>

            <div class="form-group">
                <label>Sugerencia de Ubicación:</label>
                <input type="text" name="sugerencia" placeholder="Ingrese sugerencia de ubicación..." required>
            </div>

            <div class="form-group">
                <label>Número de Sector:</label>
                <select name="sector_nro" required>
                    <option value="">Seleccione un sector</option>
                    <option value="1">Sector 1</option>
                    <option value="2">Sector 2</option>
                    <option value="3">Sector 3</option>
                    <option value="4">Sector 4</option>
                </select>
            </div>

            <div class="form-group">
                <label>Número de Pabellón:</label>
                <input type="number" name="pabellon_nro" min="1" required placeholder="Ingrese el número de pabellón...">
            </div>
        </div>

        <button name="guardar_9" type="submit" class="btn btn-primary">Guardar</button>
    </form>

</body>