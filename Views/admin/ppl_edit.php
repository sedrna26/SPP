<?php require 'navbar.php'; ?>
<?php

$id = isset($_GET['id']) ? $_GET['id'] : null;

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

try {
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
                              WHERE persona.id = :id
    ");
    $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

    $stmt_ppl = $db->prepare("SELECT id, apodo, profesion, trabaja, foto, huella
        FROM ppl
        WHERE idpersona = :id
    ");
    $stmt_ppl->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);

    if (!$ppl) {
        die("PPL no encontrado.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $apodo = $_POST['apodo'] ?? '';
        $profesion = $_POST['profesion'] ?? '';
        $trabaja = $_POST['trabaja'] === 'Si' ? 1 : 0;
        $foto = $_FILES['foto']['name'];
        $fotoTmp = $_FILES['foto']['tmp_name'];

        $target_dir_foto = "imagenes_p";
        $target_file = $target_dir_foto . basename($foto);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($fotoTmp);
        if ($check !== false) {
            if (move_uploaded_file($fotoTmp, $target_file)) {
            } else {
                die("Error al subir la imagen.");
            }
        } else {
            die("El archivo no es una imagen válida.");
        }

        if (isset($_FILES['huella']) && $_FILES['huella']['error'] === UPLOAD_ERR_OK) {
            $huella = file_get_contents($_FILES['huella']['tmp_name']);
        } else {
            $huella = null;
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
        $stmt_update->bindParam(':foto', $foto, ['foto']['name']);
        $stmt_update->bindParam(':huella', $huella);
        $stmt_update->bindParam(':id', $id);
        $stmt_update->execute();

        if ($huella !== null) {
            $stmt_update_huella = $db->prepare("
            UPDATE ppl 
            SET huella = :huella
            WHERE id = :id
            ");
            $stmt_update_huella->bindParam(':huella', $huella, PDO::PARAM_LOB);
            $stmt_update_huella->bindParam(':id', $id);
            $stmt_update_huella->execute();
            $idhuella = $db->lastInsertId();
        }

        if ($check !== null) {
            $stmt_update_huella = $db->prepare("
            UPDATE ppl 
            SET foto = :foto
            WHERE id = :id
            ");
            $stmt_update_huella->bindParam(':foto', $foto, PDO::PARAM_LOB);
            $stmt_update_huella->bindParam(':id', $id);
            $stmt_update_huella->execute();
            $idhuella = $db->lastInsertId();
        }


        $accion = 'Editar PPL - PPL';
        $tabla_afectada = 'ppl';
        $detalles = "Se editó el PPL con ID: $id";
        registrarAuditoria($db, $accion, $tabla_afectada, $id, $detalles);

        header("Location: ppl_informe.php?id=" . urlencode($id) . "&mensaje=" . urlencode("PPL - PPL Editado con éxito."));
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
                        <label for="apodo" class="form-label">Apodo:</label>
                        <input type="text" id="apodo" name="apodo" class="form-control" value="<?php echo htmlspecialchars($ppl['apodo'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="profesion" class="form-label">Profesion:</label>
                        <input type="text" id="profesion" name="profesion" class="form-control" value="<?php echo htmlspecialchars($ppl['profesion'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="trabaja" class="form-label">¿Trabajaba en el momento de la detención?:</label>
                        <select class="form-select" id="trabaja" name="trabaja" value="<?php echo htmlspecialchars($ppl['trabaja'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <option value="1" <?php echo ($ppl['trabaja'] == '1' ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($ppl['trabaja'] == '0' ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="foto" class="form-label">Foto:</label>
                        <input type="file" id="foto" name="foto" class="form-control" value="<?php echo htmlspecialchars($ppl['foto'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="huella" class="form-label">Huella:</label>
                        <input type="file" id="huella" name="huella" class="form-control" value="<?php echo htmlspecialchars($ppl['huella'], ENT_QUOTES, 'UTF-8'); ?>" required>
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