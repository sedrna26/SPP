<?php
require '../conn/connection.php';
session_start();

if (isset($_SESSION['id_usuario'])) {
    header('Location: admin/index.php');
    exit;
}

if ($_POST) {
    $messages = [
        "1" => "Credenciales incorrectas",
        "2" => "No ha iniciado sesión",
        "3" => "No tienes permisos"
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dni = $_POST['dni'];
        $clave = $_POST['clave']; 

        if (!empty($dni) && !empty($clave)) {
            
            $sql = "SELECT persona.*, rol.nombre_rol FROM persona 
                    JOIN rol ON persona.id_rol = rol.id_rol 
                    WHERE dni = :dni";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->execute();

            // Verifica si el usuario existe
            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                // Aca se compara la clave
                if ($clave === $usuario['clave']) {
                 
                    $_SESSION['id_usuario'] = $usuario['id'];
                    $_SESSION['nombres'] = $usuario['nombres'];
                    $_SESSION['apellidos'] = $usuario['apellidos'];
                    $_SESSION['id_rol'] = $usuario['id_rol'];

                    // Se va a dirigir basado en el rol , actualmente hay 1(admin).
                    if ($usuario['id_rol'] == 1) {
                        header('Location: admin/index.php');
                        exit;
                    } elseif ($usuario['id_rol'] == 2) {
                        header('Location: /index.php');
                        exit;
                    } elseif ($usuario['id_rol'] == 3) {
                        header('Location: /index.php');
                        exit;
                    } else {
                        $_SESSION['message'] = $messages[3];
                    }
                } else {
                    $_SESSION['message'] = $messages[1]; 
                }
            } else {
                $_SESSION['message'] = $messages[1];
            }
        } else {
            $_SESSION['message'] = $messages[1];
        }
    }

    header('Location: home.php');
    exit;
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
    <link rel="shortcut icon" href="../img/LOGO3.ico" type="image/x-icon" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Roboto&display=swap');
        body {
            background: linear-gradient(135deg,#fecb37,#f7e41e);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="container d-flex justify-content-center">
    <div class="p-4 rounded-5 text-secondary shadow my-2" style="width: 25rem; background-color: #fecb37;">
        <div class="d-flex justify-content-center">
            <img src="../img/LOGO.ico" alt="login-icon" style="height: 15rem" class="w-75" />
        </div>
        <div class="text-center fs-1 fw-bold">Bienvenid@</div>
        <form method="post" class="form" action="">
            <div class="input-group mt-4">
                <div class="input-group-text bg-info">
                    <img src="../img/username-icon.svg" alt="dni-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="text" placeholder="DNI" name="dni" required />
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text bg-info">
                    <img src="../img/padlock-svgrepo-com.svg" alt="password-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="password" placeholder="Clave" name="clave" id="clave" required />
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
