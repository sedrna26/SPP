<?php
ob_start(); 
session_start();
require '../../conn/connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['id_usuario'])) {
  header('Location: home.php');
  exit;
}
$id_rol = $_SESSION['id_rol'];
$id_usuario= $_SESSION['id_usuario'];


$query = "SELECT * FROM rol WHERE id_rol = ?";
$stmt = $conexion->prepare($query);

if (!$stmt) {
  die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id_rol);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
  $rol = $result->fetch_assoc();
  $nombre_rol = $rol['nombre_rol'];
} else {
  $nombre_rol = "Usuario Desconocido";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Servicio Penitenciario Provincial San Juan</title>
  <link rel="shortcut icon" href="../../img/LOGO.ico" type="image/x-icon" />
  <!--font awesome con CDN para iconos-->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <!-- -----------ARCHIVO CSS----------- -->
  <link rel="stylesheet" href="../../css/style.css">
  <!-- ---------FIN ARCHIVO CSS----------- -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- ------------DATATABLES----- -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <script defer src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script defer src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script defer src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.3/css/buttons.bootstrap5.min.css" />
  <!-- Bootstrap-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />
  <!--Script de Bootstrap-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- JQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- DataTable -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js"></script>
  <!-- Bootstrap-->
  <script defer src="../../js/tabla.js"></script>
  <!-- ------------FIN-DATATABLES----- -->

  <!-- -------------sweetalert2(alertas emergentes)------------------   -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../../js/alertas.js"></script>
  <!-- ------------------------------ -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Roboto&display=swap');
  </style>
  <!-- ----------------------------- -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- ------------------------------- -->
  <script src="../../js/ppl.js"></script>
</head>

<body>
  <style>
    body {
      background: #cccccc;
    }
  </style>

  <script>
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
      window.history.pushState(null, "", window.location.href);
    };
  </script>
  <!-- ---------------MENSAJE REGISTROS-------------- -->
  <?php
  $mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
  $error = isset($_GET['error']) ? $_GET['error'] : '';
  ?>
  <!-- Muestra la alerta para mensajes de éxito -->
  <?php if (!empty($mensaje)) { ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: "success",
          title: "<?php echo $mensaje; ?>",
          showConfirmButton: false,
          timer: 1700
        }).then(() => {
          // Eliminar parámetro de la URL
          const url = new URL(window.location);
          url.searchParams.delete('mensaje');
          window.history.replaceState(null, null, url);
        });
      });
    </script>
  <?php } ?>

  <!-- Muestra la alerta para errores -->
  <?php if (!empty($error)) { ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "<?php echo $error; ?>",
          showConfirmButton: false,
          timer: 1700
        }).then(() => {
          // Eliminar parámetro de la URL
          const url = new URL(window.location);
          url.searchParams.delete('error');
          window.history.replaceState(null, null, url);
        });
      });
    </script>
  <?php } ?>
  <!-- ------------------------------------- -->
  <div style="height:60px">
    <nav class="navbar navbar-expand-lg fixed-top my-0 py-0" style="background-color: #f9e612;">
      <div class="container-fluid ml-2 ">
        <a href="index.php" class="navbar-brand mb-0 pr-4 ">
          <img class="d-line-block align-top " src="../../img/LOGO2.ico" width="100px" style="margin-right:0px">
        </a>
        <!-- Toggle Btn-->
        <button type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" class="navbar-toggler shadow-none  mr-3" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <!-- ------------------------------------------------------- -->


        <div class="collapse navbar-collapse " id="navbarNav">
          <ul class="navbar-nav mr-auto ">
            <!-- --------------------------- -->
            <li class="nav-item  pr-3">
              <a class="nav-link " href="ppl_index.php"><b>PPL</b></a>
            </li>
            <?php
              if ($_SESSION['id_rol'] === 1) {
            ?>
                  <li class="nav-item  pr-3">
                    <a class="nav-link " href="admin_index.php"><b>Admininstrador</b></a>
                  </li>
            <?php
              }
            ?>
            <!-- --------------- -->
            <?php
              if ($_SESSION['id_rol'] === 1) {
            ?>
              <li class="nav-item  pr-3">
                <a class="nav-link " href="auditoria.php"><b>Auditoria</b></a>
              </li>
            <?php
              }
            ?>
          </ul>
          <!-- ------------------------------------------------------- -->
          <form class="form-inline d-flex justify-content-end">
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user pr-2"></i>
                    <span class="mr-3">
                      <b>
                      <?php if (isset($nombre_rol)) : ?>
                        <?php echo $nombre_rol; ?>
                      <?php else : ?>
                        "Usuario Desconocido"
                      <?php endif; ?>
                      </b>
                      <span>
                        <?php if (isset($_SESSION['nombres'])) : ?>
                          <!-- -------------- -->
                           <?php
                           $query = "SELECT nombre_usuario FROM usuarios WHERE activo = 1 AND id_usuario = :id_usuario";  // Usamos un parámetro para evitar inyecciones SQL

                           $stmt = $db->prepare($query);
                           $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                           $stmt->execute();
                           $usuario = $stmt->fetch(PDO::FETCH_ASSOC);                          
                           ?>
                           <!-- ------------------ -->
                          <?php echo $usuario['nombre_usuario']; ?>
                          <br>
                          <?php echo $_SESSION['nombres']; ?>
                          <?php echo $_SESSION['apellidos']; ?>
                        <?php endif; ?>
                      </span>
                      
                    </span>
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                  <?php
                      if ($_SESSION['id_rol'] === 1) {
                    ?>
                    <li><a class="dropdown-item" href="config_user.php"> <i class="fas fa-cog pe-2"></i>Configuración</a></li>
                    <?php }?>
                    <li><a class="dropdown-item" href="javascript:cerrar()"> <i class="fa fa-power-off pe-2"></i>Cerrar Sesión</a></li>
                  </ul>
                </li>
            </div>
            </ul>
          </form>
          <!-- ------------------------------------------------------- -->
        </div>
      </div>
    </nav>
  </div>
  <? ob_end_flush();?>