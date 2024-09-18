<?php require 'navbar.php'; ?>
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
        </div>
    </div>
<?php require 'footer.php'; ?>