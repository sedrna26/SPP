<?php require 'navbar.php'; ?>
<?php
$idppl = isset($_GET['id']) ? $_GET['id'] : '';

?>
<section class="container mt-3">
    <div class="card rounded-2 border-0">

        <?php echo "Lleva el ID a todos los archivos id=" . $idppl; ?>

        <div class="card-header bg-dark text-white pb-0">
            <h5 class="d-inline-block">Informe de Evaluación Integral Interdiciplinario (IEII)</h5>
        </div>
        <div class="card-body">
            <div class="container">
                <?php $seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'datos-personales'; ?>
                <div class="row mb-4">
                    <div class="col">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="?seccion=datos-personales&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'datos-personales' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Datos Personales
                            </a>
                            <a href="?seccion=educacion&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'educacion' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Educación
                            </a>
                            <a href="?seccion=situacion-social&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'situacion-social' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Situación Social y Familiar
                            </a>
                            <a href="?seccion=situacion-laboral&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'situacion-laboral' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Situación Laboral y Espiritual
                            </a>
                            <a href="?seccion=marcas&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'marcas' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Marcas del Cuerpo
                            </a>
                            <a href="?seccion=informe-sanitario&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'informe-sanitario' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Informe Sanitario
                            </a>
                            <a href="?seccion=observaciones&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'observaciones' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Observaciones
                            </a>
                            <a href="?seccion=clasificacion&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'clasificacion' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Clasificación
                            </a>

                        </div>
                    </div>
                </div>

                <div class="content-container">
                    <?php
                    $_GET['id'] = $idppl;

                    switch ($seccion) {
                        case 'datos-personales':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'persona_lista.php';
                            }
                            break;
                        case 'educacion':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'educacion_crea.php';
                            }
                            break;

                        case 'situacion-social':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {

                                include 'socio-familiar_crea.php';
                            }
                            break;

                        case 'situacion-laboral':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'laboral_espiritual.php';
                            }
                            break;
                        case 'marcas':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'app.php';
                            }
                            break;

                        case 'informe-sanitario':
                            include 'sanitario_crea.php';
                            break;

                        case 'observaciones':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'observaciones.php';
                            }
                            break;

                        case 'clasificacion':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                require_once 'clasificacion.php';
                            }
                            break;



                        default:
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'persona_lista.php';
                            }
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>