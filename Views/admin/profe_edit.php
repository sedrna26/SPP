<?php
require '../../conn/connection.php';
require 'navbar.php';

// Manejo de la actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_usuario'];
    $direccion = $_POST['direccion'];
    $celular = $_POST['celular'];
    $email = $_POST['correo'];
    $fechadeingreso = $_POST['fechadeingreso'];
    $fechadebaja = $_POST['fechadebaja'];
    $etapa = $fechadebaja ? 'Inactivo' : 'Activo';

    $foto = $_FILES['foto']['name'];
    $cv = $_FILES['cv']['name'];

    $updateQuery = "UPDATE usuarios SET 
        direccion = :direccion, 
        celular = :celular, 
        correo = :correo, 
        fechadeingreso = :fechadeingreso, 
        fechadebaja = :fechadebaja, 
        etapa = :etapa";
    
    if ($foto) {
        $updateQuery .= ", foto = :foto";
        $target_dir_foto = "../../usuarios/uploads/";
        $target_file_foto = $target_dir_foto . basename($foto);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file_foto);
    }

    if ($cv) {
        $updateQuery .= ", cv = :cv";
        $target_dir_cv = "../../usuarios/cv/";
        $target_file_cv = $target_dir_cv . basename($cv);
        move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file_cv);
    }

    $updateQuery .= " WHERE id_usuario = :id";

    try {
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':correo', $email);
        $stmt->bindParam(':fechadeingreso', $fechadeingreso);
        $stmt->bindParam(':fechadebaja', $fechadebaja);
        $stmt->bindParam(':etapa', $etapa);

        if ($foto) {
            $stmt->bindParam(':foto', $foto);
        }

        if ($cv) {
            $stmt->bindParam(':cv', $cv);
        }

        if ($stmt->execute()) {
            echo '<script>
                    var msj = "Usuario actualizado exitosamente";
                    window.location="profe_index.php?mensaje="+ encodeURIComponent(msj)
                  </script>';
            exit;
        } else {
            echo "Error: No se pudo actualizar el usuario.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Manejo de la carga inicial
$id = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;
if ($id) {
    try {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            echo "Usuario no encontrado.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm rounded-4 border-0 mt-5">
                <h5 class="card-header bg-dark text-white rounded-top-4 text-center">Actualizar Usuario</h5>
                <div class="card-body bg-light p-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id_usuario'], ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="apellido">Apellido:</label>
                                <input type="text" class="form-control" id="apellido" value="<?php echo htmlspecialchars($usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dni">DNI:</label>
                                <input type="number" class="form-control" id="dni" value="<?php echo htmlspecialchars($usuario['dni'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="direccion">Dirección:</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese Dirección" value="<?php echo htmlspecialchars($usuario['direccion'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="celular">Celular:</label>
                                <input type="tel" class="form-control" id="celular" name="celular" placeholder="Ingrese Celular" value="<?php echo htmlspecialchars($usuario['celular'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="correo">Correo:</label>
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese Correo" value="<?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="foto">Foto:</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <?php if ($usuario['foto']): ?>
                                    <div class="mt-2">
                                        <img src="../../usuarios/uploads/<?php echo htmlspecialchars($usuario['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto del Usuario" class="img-thumbnail" style="width: 100px; height: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cv">CV:</label>
                                <input type="file" class="form-control" id="cv" name="cv">
                                <?php if ($usuario['cv']): ?>
                                    <div class="mt-2">
                                        <a href="../../usuarios/cv/<?php echo htmlspecialchars($usuario['cv'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Ver CV</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fechadeingreso">Fecha de Ingreso:</label>
                                <input type="date" class="form-control" id="fechadeingreso" name="fechadeingreso" value="<?php echo htmlspecialchars($usuario['fechadeingreso'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fechadebaja">Fecha de Baja:</label>
                                <input type="date" class="form-control" id="fechadebaja" name="fechadebaja" value="<?php echo htmlspecialchars($usuario['fechadebaja'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../../js/contraseña.js"></script>
<script src="../../js/validacion.js"></script>
<script src="../../js/validacion2.js"></script>
<?php require 'footer.php'; ?>
