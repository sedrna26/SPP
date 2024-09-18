<?php 
require 'navbar.php'; 
require '../../conn/connection.php';

if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    echo '<script>
            alert("No tienes permiso para realizar esta acción.");
            window.location="profe_index.php";
          </script>';
    exit();
}

// -----------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $fechadeingreso = $_POST['fechadeingreso'];
    $foto = $_FILES['foto']['name'];
    $cv = $_FILES['cv']['name'];
    $etapa = "Activo";
    $id_rol = 2; // Rol para profesores
    $target_dir_foto = "../../profesores/uploads/";
    $target_file_foto = $target_dir_foto . basename($foto);
    $target_dir_cv = "../../profesores/cv/";
    $target_file_cv = $target_dir_cv . basename($cv);

    if (!is_dir($target_dir_foto)) {
        mkdir($target_dir_foto, 0777, true);
    }
    if (!is_dir($target_dir_cv)) {
        mkdir($target_dir_cv, 0777, true);
    }

    try {
        $sql_check_dni = "SELECT * FROM usuarios WHERE dni = :dni";
        $stmt = $db->prepare($sql_check_dni);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {            
            echo '<script>
                    var msj = "Error: Ya existe un usuario con el mismo DNI.";
                    window.location="profe_crea.php?error=" + encodeURIComponent(msj);
                  </script>';
        } else {
            move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file_foto);
            move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file_cv);
            $sql_insert = "INSERT INTO usuarios (nombre, apellido, dni, celular, correo, direccion, foto, cv, fechadeingreso, etapa, id_rol) 
                           VALUES (:nombre, :apellido, :dni, :celular, :correo, :direccion, :foto, :cv, :fechadeingreso, :etapa, :id_rol)";
            $stmt = $db->prepare($sql_insert);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':dni', $dni);
            $stmt->bindParam(':celular', $celular);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':foto', $foto);
            $stmt->bindParam(':cv', $cv);
            $stmt->bindParam(':fechadeingreso', $fechadeingreso);
            $stmt->bindParam(':etapa', $etapa);
            $stmt->bindParam(':id_rol', $id_rol);
            if ($stmt->execute()) {
                echo '<script>
                    var msj = "Profesor creado exitosamente";
                    window.location="profe_index.php?mensaje=" + encodeURIComponent(msj);
                  </script>';
                exit();
            } else {
                $error = "Error al ingresar el profesor.";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- -----------------------------------   -->  
<div class="container mt-3">
    <div class="col d-flex align-items-center justify-content-center">
        <div class="card rounded-2 border-0 w-75 ">
            <h5 class="card-header bg-dark text-white">Agregar Profesor</h5>
            <div class="card-body bg-light">          
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese nombre" required>
                    </div>                    
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese apellido" required>
                    </div>                    
                    <div class="form-group">
                        <label for="dni">DNI:</label>
                        <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese DNI" required>
                    </div>                    
                    <div class="form-group">
                        <label for="celular">Celular:</label>
                        <input type="text" class="form-control" id="celular" name="celular" placeholder="Ingrese celular" required>
                    </div>                    
                    <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese correo" required>
                    </div>                    
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese dirección">
                    </div>                    
                    <div class="form-group">
                        <label for="foto">Foto:</label>
                        <input type="file" class="form-control" id="foto" name="foto">
                    </div>                    
                    <div class="form-group">
                        <label for="cv">CV:</label>
                        <input type="file" class="form-control" id="cv" name="cv">
                    </div>                    
                    <div class="form-group">
                        <label for="fechadeingreso">Fecha de ingreso:</label>
                        <input type="date" class="form-control" id="fechadeingreso" name="fechadeingreso" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>             
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../../js/validacion.js"></script>
<?php require 'footer.php'; ?>
