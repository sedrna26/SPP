<?php
require 'navbar.php';
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

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

$observacion = '';
if ($idppl > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM observaciones WHERE id_ppl = ? LIMIT 1");
        $stmt->execute([$idppl]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            $observacion = $resultado['observacion'];  // Asumiendo que la columna se llama 'observacion'
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al obtener la observación: " . $e->getMessage() . "</div>";
    }
}

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_8'])) {
    try {
        // Actualizar la observación en la base de datos
        $stmt = $db->prepare("UPDATE observaciones SET observacion = ? WHERE id_ppl = ?");
        $stmt->execute([$_POST['observacion'], $_POST['id_ppl']]);

        // Registrar acción en la auditoría
        $accion = 'Actualizacion en  Observación';
        $tabla_afectada = 'observaciones';
        $detalles = "Se actualizó la observación para el PPL con ID: $idppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);
        header("Location: ppl_informe.php?seccion=situacion-laboral&id=" . $idppl);
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
        $db->rollBack();
    }
}
?>
<div class="container mt-5">
    <form action="" method="POST" class="shadow p-4 rounded">
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

        <!-- Sección de Observaciones -->
        <div class="mb-4">
            <h3 id="titulo" class="mb-3">Editar Observaciones del PPL</h3>
            <div class="form-group">
                <label for="observacion" class="form-label">Ingrese la observación correspondiente:</label>
                <textarea class="form-control" name="observacion" id="observacion" rows="3" maxlength="255" placeholder="Ingrese la observación"><?php echo htmlspecialchars($observacion); ?></textarea>
            </div>
        </div>

        <!-- Botón de envío -->
        <div class="d-grid">
            <button name="guardar_8" type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>