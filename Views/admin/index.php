<?php require 'navbar.php'; ?>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 300px;
            margin: 0 auto;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #1976D2;
        }

        .body {
            display: flex;
            justify-content: center;
            align-items: center;

        }
    </style>
</head>



</html>
</head>
<div class="body">
    <div class="panel">
        <?php
        if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
            // Usuario autenticado, mostrar el contenido protegido
            echo "Bienvenido, usuario autenticado.  <br>";
            // Puedes agregar más contenido aquí.
        } else {
            // Si el usuario no ha iniciado sesión, mostrar el formulario de inicio de sesión y mensajes de error si las credenciales son incorrectas
            if (isset($_GET['err']) && $_GET['err'] == 1) {
                echo " Credenciales incorrectas. Por favor, inténtalo de nuevo.<br>";
            }
        }
        ?>
        <h1 class="text-center m-2"></h1>
        <div class="container">
            <div class="header">
                <h2>Área Admisión</h2>
            </div>
            <div class="content">
                <button class="btn">Nueva Admisión</button>
                <button class="btn">Listado PPL</button>
                <button class="btn">Administrador</button>
            </div>
        </div>
    </div>
    <?php require 'footer.php'; ?>