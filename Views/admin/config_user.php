<?php
require 'navbar.php'; 
require '../../conn/connection.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";
$error = "";

// Function to register audit trail
function registrarAuditoria($db, $accion, $tabla_afectada, $registro_id, $detalles) {
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

// Fetch current user and personal information
try {
    $stmt = $db->prepare("SELECT p.*, u.nombre_usuario, u.contrasena, r.nombre_rol 
                          FROM usuarios u 
                          JOIN persona p ON u.id_persona = p.id 
                          JOIN rol r ON u.id_rol = r.id_rol 
                          WHERE u.id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $error = "Usuario no encontrado.";
    }
} catch (PDOException $e) {
    $error = "Error al recuperar información del usuario: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $fechanac = $_POST['fechanac'];
    $direccion = $_POST['direccion'];
    $genero = $_POST['genero'];
    $estadocivil = $_POST['estadocivil'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena']; // Nuevo campo de contraseña

    // Calcular la edad a partir de la fecha de nacimiento
    $fecha_nacimiento = new DateTime($fechanac);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento)->y;

    try {
        $db->beginTransaction();

        // Update persona table
        $stmt_persona = $db->prepare("UPDATE persona 
                               SET nombres = :nombres, 
                                   apellidos = :apellidos, 
                                   fechanac = :fechanac, 
                                   direccion = :direccion, 
                                   genero = :genero, 
                                   estadocivil = :estadocivil,
                                   edad = :edad 
                               WHERE id = :id_persona");
        
        $stmt_persona->bindParam(':nombres', $nombres);
        $stmt_persona->bindParam(':apellidos', $apellidos);
        $stmt_persona->bindParam(':fechanac', $fechanac);
        $stmt_persona->bindParam(':direccion', $direccion);
        $stmt_persona->bindParam(':genero', $genero);
        $stmt_persona->bindParam(':estadocivil', $estadocivil);
        $stmt_persona->bindParam(':edad', $edad, PDO::PARAM_INT);
        $stmt_persona->bindParam(':id_persona', $usuario['id']);

        // Update usuarios table
        $stmt_usuario = $db->prepare("UPDATE usuarios 
                               SET nombre_usuario = :nombre_usuario, 
                                   contrasena = :contrasena
                               WHERE id_usuario = :id_usuario");
        
        $stmt_usuario->bindParam(':nombre_usuario', $nombre_usuario);
        $stmt_usuario->bindParam(':contrasena', $contrasena);
        $stmt_usuario->bindParam(':id_usuario', $id_usuario);

        // Execute both updates
        $persona_updated = $stmt_persona->execute();
        $usuario_updated = $stmt_usuario->execute();

        if ($persona_updated && $usuario_updated) {
            // Log audit trail
            $accion = 'Actualización';
            $tabla_afectada = 'persona, usuarios';
            $descripcion = "Actualización de datos personales y usuario para ID de usuario: $id_usuario";
            registrarAuditoria($db, $accion, $tabla_afectada, $id_usuario, $descripcion);

            $db->commit();

            $mensaje = "Información actualizada con éxito.";
            
            // Refresh user data after update
            $stmt = $db->prepare("SELECT p.*, u.nombre_usuario, u.contrasena, r.nombre_rol 
                                  FROM usuarios u 
                                  JOIN persona p ON u.id_persona = p.id 
                                  JOIN rol r ON u.id_rol = r.id_rol 
                                  WHERE u.id_usuario = :id_usuario");
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $db->rollBack();
            $error = "Error al actualizar la información.";
        }
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $error = "Error en la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil de Usuario</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4>Editar Perfil de Usuario</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($mensaje): ?>
                            <div class="alert alert-success"><?php echo $mensaje; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Información Personal</h5>
                                    <div class="form-group">
                                        <label for="nombres">Nombres</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" 
                                               value="<?php echo htmlspecialchars($usuario['nombres'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                               value="<?php echo htmlspecialchars($usuario['apellidos'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="fechanac">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fechanac" name="fechanac" 
                                               value="<?php echo htmlspecialchars($usuario['fechanac'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" 
                                               value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="genero">Género</label>
                                        <select class="form-control" id="genero" name="genero">
                                            <option value="Masculino" <?php echo ($usuario['genero'] == 'Masculino' ? 'selected' : ''); ?>>Masculino</option>
                                            <option value="Femenino" <?php echo ($usuario['genero'] == 'Femenino' ? 'selected' : ''); ?>>Femenino</option>
                                            <option value="Otro" <?php echo ($usuario['genero'] == 'Otro' ? 'selected' : ''); ?>>Otro</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="estadocivil">Estado Civil</label>
                                        <select class="form-control" id="estadocivil" name="estadocivil">
                                            <option value="Soltero" <?php echo ($usuario['estadocivil'] == 'Soltero' ? 'selected' : ''); ?>>Soltero</option>
                                            <option value="Casado" <?php echo ($usuario['estadocivil'] == 'Casado' ? 'selected' : ''); ?>>Casado</option>
                                            <option value="Divorciado" <?php echo ($usuario['estadocivil'] == 'Divorciado' ? 'selected' : ''); ?>>Divorciado</option>
                                            <option value="Viudo" <?php echo ($usuario['estadocivil'] == 'Viudo' ? 'selected' : ''); ?>>Viudo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Información de Usuario</h5>
                                    <div class="form-group">
                                        <label for="nombre_usuario">Nombre de Usuario</label>
                                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" 
                                               value="<?php echo htmlspecialchars($usuario['nombre_usuario'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <!-- <div class="form-group">
                                        <label for="contrasena">Contraseña</label>
                                        <input type="text" class="form-control" id="contrasena" name="contrasena" 
                                               value="<?php echo htmlspecialchars($usuario['contrasena'] ?? ''); ?>" required>
                                    </div> -->
                                    
                                    <div class="form-group">
                                        <label>Rol</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_rol'] ?? ''); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>DNI</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['dni'] ?? ''); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Edad</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['edad'] ?? ''); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary">Actualizar Información</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/alertas.js"></script>
</body>
</html>

<?php require 'footer.php'; ?>