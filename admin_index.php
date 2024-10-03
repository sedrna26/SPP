<?php
require '../../conn/connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//-------------BORRADO Usuario (Actualización a inactivo)------------------ 
if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID']; // Obtiene el ID del usuario a eliminar
    $sentencia = $db->prepare("UPDATE usuarios SET activo = '0' WHERE id_usuario = :id_usuario");
    $sentencia->bindParam(':id_usuario', $txtID);

    if ($sentencia->execute()) {
        $mensaje = "Registro Eliminado con Éxito";
    } else {
        $mensaje = "Error al eliminar el usuario.";
    }

    header("Location:admin_index.php?mensaje=" . urlencode($mensaje));
    exit();
}
?>
<!-- ------------------------------------------ -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recolecta datos del formulario
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];
    $dni = $_POST['dni'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $fechanac = $_POST['fechanac'];
    $direccion = $_POST['direccion'];
    $genero = $_POST['genero'];
    $estadocivil = $_POST['estadocivil'];
    $id_rol = $_POST['permiso']; // Mapeado del select de roles

    // Hashear la contraseña
    $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);

    // Calcular la edad a partir de la fecha de nacimiento
    $fecha_nacimiento = new DateTime($fechanac);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento)->y;

    $error = "";
    try {
        // Verificar si el DNI ya existe
        $sql_check_dni = "SELECT COUNT(*) FROM persona WHERE dni = :dni";
        $stmt_check_dni = $db->prepare($sql_check_dni);
        $stmt_check_dni->bindParam(':dni', $dni);
        $stmt_check_dni->execute();
        $count = $stmt_check_dni->fetchColumn();

        if ($count > 0) {
            $error = "El DNI ya está registrado. Por favor, use uno diferente.";
            $redirect_url = "admin_index.php?error=" . urlencode($error)
                . "&nombre=" . urlencode($nombre_usuario);
            header("Location: " . $redirect_url);
            exit();
        } else {
            // Iniciar transacción
            $db->beginTransaction();

            // Insertar en la tabla persona
            $sql_persona = "INSERT INTO persona (dni, nombres, apellidos, fechanac, direccion, genero, estadocivil, edad) 
                            VALUES (:dni, :nombres, :apellidos, :fechanac, :direccion, :genero, :estadocivil, :edad)";
            $stmt_persona = $db->prepare($sql_persona);
            $stmt_persona->bindParam(':dni', $dni);
            $stmt_persona->bindParam(':nombres', $nombres);
            $stmt_persona->bindParam(':apellidos', $apellidos);
            $stmt_persona->bindParam(':fechanac', $fechanac);
            $stmt_persona->bindParam(':direccion', $direccion);
            $stmt_persona->bindParam(':genero', $genero);
            $stmt_persona->bindParam(':estadocivil', $estadocivil);
            $stmt_persona->bindParam(':edad', $edad, PDO::PARAM_INT);
            $stmt_persona->execute();

            // Obtener el ID de la persona recién creada
            $id_persona = $db->lastInsertId();

            // Insertar el nuevo usuario en la base de datos
            $sql_usuario = "INSERT INTO usuarios (nombre_usuario, contrasena, id_persona, id_rol) 
                            VALUES (:nombre_usuario, :contrasena, :id_persona, :id_rol)";
            $stmt_usuario = $db->prepare($sql_usuario);
            $stmt_usuario->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt_usuario->bindParam(':contrasena', $hashed_password);
            $stmt_usuario->bindParam(':id_persona', $id_persona);
            $stmt_usuario->bindParam(':id_rol', $id_rol);
            $stmt_usuario->execute();

            // Confirmar la transacción
            $db->commit();
            header("Location: admin_index.php?mensaje=" . urlencode("Administrador o usuario creado con éxito."));
            exit();
        }
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $db->rollBack();
        $error = "Error en la base de datos: " . $e->getMessage();
        $redirect_url = "admin_index.php?error=" . urlencode($error);
        header("Location: " . $redirect_url);
        exit();
    }
}
?>
<!-- ------------------------------ -->
<?php require 'navbar.php'; ?>
<!-- --------------------------------------- -->
<section class="content mt-3">
    <link rel="shortcut icon" href="/img/LOGO.ico" type="image/x-icon" />
    <div class="row">
        <div class="col mx-3">
            <div class="row">
                <div class="col">
                    <div class="card rounded-2 border-0">
                        <h5 class="card-header bg-dark text-white">Listado de Usuarios</h5>
                        <div class="card-body bg-light">
                            <form id="inscripcionForm" action="" method="post">
                                <table id="example" class="table table-striped table-sm" style="width:100%">
                                    <thead class="thead-dark">
                                        <th>#</th>
                                        <th>Nombres</th>
                                        <th>DNI</th>
                                        <th>Permiso</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
                                            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                            $query = "SELECT u.*, p.nombres, p.apellidos, p.dni FROM usuarios u
                                            JOIN persona p ON u.id_persona = p.id
                                            WHERE (u.id_rol = 1 OR u.id_rol = 3) AND u.activo = '1'";
                                            $stmt = $db->prepare($query);
                                            $stmt->execute();
                                            $usuarioss = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($usuarioss as $usuarios) {
                                                if ($usuarios['id_rol'] === 1) {
                                                    $rol = 'Administrador';
                                                } elseif ($usuarios['id_rol'] === 3) {
                                                    $rol = 'Preceptor';
                                                }
                                        ?>
                                                <tr>
                                                    <th scope="row"><?php echo $usuarios['id_usuario'] ?></th>
                                                    <td><?php echo $usuarios['nombres'] ?></td>
                                                    <td><?php echo $usuarios['dni'] ?></td>
                                                    <td><?php echo $rol ?></td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <a href="javascript:eliminar4(<?php echo $usuarios['id_usuario']; ?>)" class="btn btn-danger btn-sm" type="button" title="Borrar">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } catch (PDOException $e) {
                                            echo "Error de conexión: " . $e->getMessage();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card rounded-2 border-0">
                        <h5 class="card-header bg-dark text-white">Registro de Usuarios</h5>
                        <div class="card-body bg-light">
                            <?php
                            $error = isset($_GET["error"]) ? $_GET["error"] : "";
                            ?>
                            <form id="formulario" action="" method="post" enctype="multipart/form-data">

                                <div id="seccion_inicial">
                                    <div class="form-group">
                                        <label for="nombres">Nombres:</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Ingrese su nombre(s)" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos:</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ingrese su apellido(s)" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dni">DNI:</label>
                                        <input type="text" class="form-control" id="dni" name="dni" placeholder="Ingrese dni" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contrasena">Contraseña:</label>
                                        <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Ingrese contraseña" required>
                                    </div>


                                    <div class="form-group">
                                        <label for="nombre_usuario">Nombre de Usuario:</label>
                                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" placeholder="Se ingresa automático según nombre y apellido" readonly>
                                    </div>

                                    <button type="button" class="btn btn-dark btn-block mt-3" onclick="mostrarCamposAdicionales()">Continuar</button>
                                </div>

                                <div id="campos_adicionales" style="display:none;">
                                    <div class="form-group">
                                        <label for="fechanac">Fecha de Nacimiento:</label>
                                        <input type="date" class="form-control" name="fechanac">
                                    </div>
                                    <div class="form-group">
                                        <label for="direccion">Dirección:</label>
                                        <input type="text" class="form-control" name="direccion">
                                    </div>
                                    <div class="form-group">
                                        <label for="genero">Género:</label>
                                        <select class="form-control" name="genero">
                                            <option value="">Seleccione una opción</option>
                                            <option value="Masculino">Masculino</option>
                                            <option value="Femenino">Femenino</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="estadocivil">Estado Civil:</label>
                                        <select class="form-control" name="estadocivil">
                                            <option value="">Seleccione una opción</option>
                                            <option value="Soltero">Soltero</option>
                                            <option value="Casado">Casado</option>
                                            <option value="Divorciado">Divorciado</option>
                                            <option value="Viudo">Viudo</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="permiso">Permiso:</label>
                                        <!-- Hay que ver el tema de los roles -->
                                        <select name="permiso" id="permiso" class="form-control">
                                            <option value="">Seleccione una opción</option>
                                            <option value="1">Administrador</option>
                                            <option value="2">Usuario</option>
                                            <option value="3">otro</option>

                                        </select>
                                    </div>

                                    <div class="text-center">
                                        <button class="btn btn-dark btn-block mt-3" type="submit">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('nombres').addEventListener('input', generarNombreUsuario);
    document.getElementById('apellidos').addEventListener('input', generarNombreUsuario);

    function generarNombreUsuario() {
        var nombres = document.getElementById('nombres').value.trim();
        var apellidos = document.getElementById('apellidos').value.trim();

        if (nombres && apellidos) {
            var primerNombre = nombres.split(' ')[0].substring(0, 2).toLowerCase();
            var primerApellido = apellidos.split(' ')[0].substring(0, 2).toLowerCase();

            var numero1 = Math.floor(Math.random() * 100);
            var numero2 = Math.floor(Math.random() * 100);


            var nombreUsuario = primerNombre + primerApellido + numero1 + numero2;
            document.getElementById('nombre_usuario').value = nombreUsuario;
        }
    }

    function mostrarCamposAdicionales() {
        var nombres = document.getElementById('nombres').value.trim();
        var apellidos = document.getElementById('apellidos').value.trim();
        var dni = document.getElementById('dni').value.trim();
        var contrasena = document.getElementById('contrasena').value.trim();
        if (nombres === "" || apellidos === "" || dni === "" || contrasena === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor, complete todos los campos antes de continuar.',
                confirmButtonText: 'OK'
            });
        } else {

            document.getElementById('seccion_inicial').style.display = 'none';
            document.getElementById('campos_adicionales').style.display = 'block';
        }
    }
</script>
<script src="/js/alertas.js"></script>
<?php require 'footer.php'; ?>