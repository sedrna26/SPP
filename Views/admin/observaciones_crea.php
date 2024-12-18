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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_8'])) {
    try {
        // Insertar datos de clasificación
        $stmt = $db->prepare("INSERT INTO observaciones (id_ppl, observacion) 
            VALUES (?, ?)");

        $stmt->execute([
            $_POST['id_ppl'],
            $_POST['observacion']
        ]);

        // Registrar acción en la auditoría
        $accion = 'Agregar Observación';
        $tabla_afectada = 'observaciones';
        $detalles = "Se insertó una nueva observación para el PPL con ID: $idppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);
            
        header("Location: ppl_informe.php?seccion=observaciones&id=".$idppl);

        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
}
?>

<head>
    <style>
    </style>
</head>

<body>
    <form action="" method="POST">
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

        <!-- Sección de Clasificación -->
        <div class="form-section">
            <h3 id="titulo">Observaciones del PPL</h3>
            <div class="form-group">
                <label>Ingrese la observacion correspondiente:</label>
                <textarea class="form-control" name="observacion" id="observacion" rows="3" maxlength="255" placeholder="Ingrese la obvservación"></textarea>
            </div>


        </div>

        <button name="guardar_8" type="submit" class="btn btn-primary">Guardar</button>
    </form>

</body>