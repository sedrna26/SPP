<?php require 'navbar.php'; ?>
<?php

$id_ppl = isset($_GET['id']) ? $_GET['id'] : null;

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
} {
    $stmt_persona = $db->prepare("SELECT 
    persona.id,
    persona.dni, 
    persona.nombres, 
    persona.apellidos, 
    DATE_FORMAT(persona.fechanac, '%d-%m-%Y') AS fechaNacimiento, 
    persona.edad, 
    persona.genero, 
    persona.estadocivil, 
    d.id AS id_direccion, 
    p.nombre AS pais, 
    pr.nombre AS provincia, 
    c.nombre AS ciudad, 
    d.localidad, 
    d.direccion
FROM persona
LEFT JOIN domicilio d ON persona.direccion = d.id
LEFT JOIN paises p ON d.id_pais = p.id
LEFT JOIN provincias pr ON d.id_provincia = pr.id
LEFT JOIN ciudades c ON d.id_ciudad = c.id
WHERE persona.id = :id");

    $stmt_persona->bindParam(':id', $id_ppl, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);


    $stmt_ppl = $db->prepare("SELECT * FROM ppl  WHERE id = :id"); // Aquí se usa el campo idpersona en vez de id
    $stmt_ppl->bindParam(':id', $id_ppl, PDO::PARAM_INT); // Pasamos el id correcto de persona
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);



    if (!$ppl) {
        die("PPL no encontrado.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $apodo = $_POST['apodo'] ?? '';
            $profesion = $_POST['profesion'] ?? '';
            $trabaja = ($_POST['trabaja'] ?? '0') == '1' ? 1 : 0;

            $id_ppl = $_POST['id'] ?? null;

            if (!$id_ppl) {
                die("ID de PPL no proporcionado.");
            }

            $stmt = $db->prepare("SELECT foto, huella FROM ppl WHERE id = :id");
            $stmt->bindParam(':id', $id_ppl, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                die("No se encontró el PPL con el ID proporcionado.");
            }

            $fotoExistente = $result['foto'];
            $huellaExistente = $result['huella'];

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto = $_FILES['foto']['name'];
                $fotoTmp = $_FILES['foto']['tmp_name'];

                $target_dir_foto = "imagenes_p/";
                $target_file = $target_dir_foto . basename($foto);

                if (!file_exists($target_dir_foto)) {
                    mkdir($target_dir_foto, 0777, true);
                }

                if (move_uploaded_file($fotoTmp, $target_file)) {
                } else {
                    die("Error al subir la nueva foto.");
                }
            } else {
                $foto = $fotoExistente;
            }

            if (isset($_FILES['huella']) && $_FILES['huella']['error'] === UPLOAD_ERR_OK) {
                $huellaTmp = $_FILES['huella']['tmp_name'];
                if (!empty($huellaTmp) && file_exists($huellaTmp)) {
                    $huella = file_get_contents($huellaTmp);
                } else {
                    die("Error al procesar la nueva huella.");
                }
            } else {
                $huella = $huellaExistente;
            }

            $stmt_update = $db->prepare("
                UPDATE ppl
                SET apodo = :apodo,
                    profesion = :profesion,
                    trabaja = :trabaja,
                    foto = :foto,
                    huella = :huella
                WHERE id = :id
            ");
            $stmt_update->bindParam(':apodo', $apodo);
            $stmt_update->bindParam(':profesion', $profesion);
            $stmt_update->bindParam(':trabaja', $trabaja);
            $stmt_update->bindParam(':foto', $foto);
            $stmt_update->bindParam(':huella', $huella, PDO::PARAM_LOB);
            $stmt_update->bindParam(':id', $id_ppl, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $accion = 'Editar PPL - PPL';
                $tabla_afectada = 'ppl';
                $detalles = "Se editó el PPL con ID: $id_ppl";
                registrarAuditoria($db, $accion, $tabla_afectada, $id_ppl, $detalles);

                header("Location: ppl_informe.php?id=" . urlencode($id_ppl) . "&mensaje=" . urlencode("PPL - PPL Editado con éxito."));
                exit();
            } else {
                die("Error al actualizar el PPL.");
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>


<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Editar PPL</h4>
        </div>
        <div class="card-body">
            <form action="ppl_edit.php?id=<?php echo $ppl['id']; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo ($ppl['id']); ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="profesion" class="form-label">Profesion:</label>
                        <input type="text" id="profesion" name="profesion" class="form-control" value="<?php echo !empty($ppl['profesion']) ? htmlspecialchars($ppl['profesion'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="trabaja" class="form-label">¿Trabajaba en el momento de la detención?:</label>
                        <select class="form-select" id="trabaja" name="trabaja" required>
                            <option value="1" <?php echo ($ppl['trabaja'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($ppl['trabaja'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Foto -->
                    <div class="col-md-6">
                        <label for="foto" class="form-label">Foto:</label>
                        <?php if (!empty($ppl['foto'])): ?>
                            <div class="mb-2">
                                <img src="imagenes_p/<?php echo htmlspecialchars($ppl['foto'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="Foto de la persona"
                                    style="max-width: 200px; max-height: 200px;">
                            </div>
                        <?php else: ?>
                            <p>No se encontró foto.</p>
                        <?php endif; ?>
                        <input type="file" id="foto" name="foto" class="form-control">
                    </div>

                    <!-- Huella -->
                    <div class="col-md-6">
                        <label for="huella" class="form-label">Huella:</label>
                        <?php if (!empty($ppl['huella'])): ?>
                            <div class="mb-2">
                                <img src="data:image/png;base64,<?php echo base64_encode($ppl['huella']); ?>"
                                    alt="Huella"
                                    style="max-width: 200px; max-height: 200px;">
                            </div>
                        <?php else: ?>
                            <p>No se encontró huella.</p>
                        <?php endif; ?>
                        <input type="file" id="huella" name="huella" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">

                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-success btn-lg">Actualizar Datos</button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>