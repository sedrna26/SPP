<?php
require 'navbar.php';
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

// Cargar datos existentes
$clasificacion = null;
if ($idppl > 0) {
    $stmt = $db->prepare("SELECT * FROM clasificacion WHERE id_ppl = ?");
    $stmt->execute([$idppl]);
    $clasificacion = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_9'])) {
    try {
        if ($clasificacion) {
            // Actualizar datos existentes
            $stmt = $db->prepare("UPDATE clasificacion 
                SET clasificacion = ?, sugerencia = ?, sector_nro = ?, pabellon_nro = ? 
                WHERE id_ppl = ?");
            $stmt->execute([
                $_POST['clasificacion'],
                $_POST['sugerencia'],
                $_POST['sector_nro'],
                $_POST['pabellon_nro'],
                $_POST['id_ppl']
            ]);

            $accion = 'Editar Clasificación';
            $detalles = "Se actualizó la clasificación para el PPL con ID: $idppl";
        } else {
            // Insertar nuevos datos
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

            $accion = 'Editar Clasificación';
            $detalles = "Se actualizo clasificacion para el PPL con ID: $idppl";
        }

        // Registrar acción en la auditoría
        $tabla_afectada = 'clasificacion';
        registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);

        header("Location: ppl_informe.php?seccion=clasificacion&id=" . $idppl);
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
}
?>

<!-- ---------------- -->
<div class="container mt-3">    
    <div class="card shadow-sm">
        <h5 id="titulo"class="card-header bg-dark text-white ">Editar Clasificación del PPL</h5>
        <div class="card-body ">
            <!-- ------------------- -->
            <a href="ppl_informe.php?seccion=clasificacion&id=<?php echo $idppl; ?>">
                <div class="btn btn-secondary mb-4">Cancelar</div>
            </a>
            <!-- ----------- -->
            <form method="POST">
                <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

                <!-- Sección de Clasificación -->
                <div class="form-section">
                    <div class="form-group">
                        <label >Clasificación:</label>
                        <select class="form-control" name="clasificacion" required>
                            <option value="">Seleccione una clasificación</option>
                            <option value="Adulto Primario" <?php echo isset($clasificacion['clasificacion']) && $clasificacion['clasificacion'] === 'Adulto Primario' ? 'selected' : ''; ?>>Adulto Primario</option>
                            <option value="Adulto Reiterante" <?php echo isset($clasificacion['clasificacion']) && $clasificacion['clasificacion'] === 'Adulto Reiterante' ? 'selected' : ''; ?>>Adulto Reiterante</option>
                            <option value="Adulto Reincidente" <?php echo isset($clasificacion['clasificacion']) && $clasificacion['clasificacion'] === 'Adulto Reincidente' ? 'selected' : ''; ?>>Adulto Reincidente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Sugerencia de Ubicación:</label>
                        <input type="text" class="form-control" name="sugerencia" placeholder="Ingrese sugerencia de ubicación..." required value="<?php echo htmlspecialchars($clasificacion['sugerencia'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Número de Sector:</label>
                        <select class="form-control" name="sector_nro" required>
                            <option value="">Seleccione un sector</option>
                            <option value="1" <?php echo isset($clasificacion['sector_nro']) && $clasificacion['sector_nro'] == 1 ? 'selected' : ''; ?>>Sector 1</option>
                            <option value="2" <?php echo isset($clasificacion['sector_nro']) && $clasificacion['sector_nro'] == 2 ? 'selected' : ''; ?>>Sector 2</option>
                            <option value="3" <?php echo isset($clasificacion['sector_nro']) && $clasificacion['sector_nro'] == 3 ? 'selected' : ''; ?>>Sector 3</option>
                            <option value="4" <?php echo isset($clasificacion['sector_nro']) && $clasificacion['sector_nro'] == 4 ? 'selected' : ''; ?>>Sector 4</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de Pabellón:</label>
                        <input class="form-control" type="number" name="pabellon_nro" min="1" required placeholder="Ingrese el número de pabellón..." value="<?php echo htmlspecialchars($clasificacion['pabellon_nro'] ?? ''); ?>">
                    </div>
                </div>

                <button name="guardar_9" type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
</div>
