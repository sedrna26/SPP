<?php 
require '../conn/connection.php';
session_start();

// Redirigir si ya existe una sesión activa
if (isset($_SESSION['id_usuario'])) {
    header('Location: admin/index.php');
    exit;
}

if ($_POST) {
    $nombre_usuario = trim($_POST['nombre_usuario']); // Eliminar espacios en blanco
    $contrasena = $_POST['contrasena'];

    if (!empty($nombre_usuario) && !empty($contrasena)) {
        try {
            $sql = " SELECT 
                    u.*, 
                    p.nombres, 
                    p.apellidos, 
                    r.nombre_rol 
                FROM 
                    usuarios u
                JOIN 
                    persona p ON u.id_persona = p.id
                JOIN 
                    rol r ON u.id_rol = r.id_rol
                WHERE 
                    u.nombre_usuario = :nombre_usuario 
                    AND u.activo = 1";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($contrasena, $usuario['contrasena'])) {
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['nombres'] = $usuario['nombres'];
                    $_SESSION['apellidos'] = $usuario['apellidos'];
                    $_SESSION['id_rol'] = $usuario['id_rol'];
                    $sqlInsertRegistro = " INSERT INTO registro_acceso (id_usuario, hora_inicio) 
                        VALUES (:id_usuario, NOW())";
                    $stmtInsertRegistro = $db->prepare($sqlInsertRegistro);
                    $stmtInsertRegistro->bindParam(':id_usuario', $usuario['id_usuario'], PDO::PARAM_INT);
                    $stmtInsertRegistro->execute();
                    if ($usuario['id_rol'] == 1) {
                        header('Location: admin/index.php');
                    } else {
                        header('Location: user/index.php');
                    }
                    exit;
                } else {
                    $_SESSION['message'] = "Contraseña incorrecta";
                }
            } else {
                $_SESSION['message'] = "Usuario no encontrado";
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error de conexión: " . $e->getMessage();
        }
    } else {
        $_SESSION['message'] = "Por favor, complete todos los campos.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <meta name="description" content="Inicio de Sesión" />
    <title>Login</title>
    <link rel="shortcut icon" href="../img/LOGO.ico" type="image/x-icon" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Roboto&display=swap');

        body {
            background: #f9e612;
            /* 
            gris claro: #cccccc 
            gris oscuro: #666666
            amarillo: #f9e612

            */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body class="container d-flex justify-content-center">
    <div class="p-4 rounded-5 text-secondary shadow my-2" style="width: 25rem; background-color: #cccccc;">
        <div class="d-flex justify-content-center">
            <img src="../img/LOGO.ico" alt="login-icon" style="height: 15rem" class="w-75" />
        </div>
        <div class="text-center fs-1 fw-bold">Bienvenid@</div>        
            <div class="row">
                <div class="col">
                    <p class="py-0 my-0"><b>Admin</b></p>
                    <p class="py-0 my-0">demadmin</p>
                    <p class="py-0 my-0"><b>Clave</b></p>
                    <p class="py-0 my-0">demian1234</p>
                </div>
                <div class="col">
                    <p class="py-0 my-0"><b>Correccional</b></p>
                    <p class="py-0 my-0">maol4192</p>
                    <p class="py-0 my-0"><b>Clave</b></p>
                    <p class="py-0 my-0">123</p>
                </div>
            </div>         
        <form method="post" class="form" action="">
            <div class="input-group mt-4">
                <div class="input-group-text bg-info">
                    <img src="../img/username-icon.svg" alt="dni-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="text" placeholder="Nombre de Usuario" name="nombre_usuario" required />
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text bg-info">
                    <img src="../img/padlock-svgrepo-com.svg" alt="password-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="password" placeholder="contraseña" name="contrasena" id="contrasena" required />
            </div>
            <button type="submit" class="btn btn-primary text-white w-100 mt-4 fs-5 fw-semibold shadow-sm">Iniciar Sesión</button>
            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="error text-danger border border-danger w-100 justify-content-center text-center d-inline-block mt-2 rounded-3 p-1" id="myAlert" style="background-color: #f5c2c7">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
            }
            ?>
        </form>
    </div>
</body>

</html>