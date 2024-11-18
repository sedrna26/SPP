<?php require 'navbar.php'; ?>
<?php
$idppl = isset($_GET['id']) ? $_GET['id'] : '';

?>
<section class="container mt-3">
    <div class="card rounded-2 border-0">

        <!-- <?php echo "Lleva el ID a todos los archivos id=" . $idppl; ?> -->

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
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2|| $_SESSION['id_rol'] === 3) {
                                include 'persona_lista.php';
                            }
                            break;
                        case 'educacion':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
                                $sql_educappl = "SELECT estado FROM educacion WHERE id_ppl = $idppl";                                           
                                $result_educappl = $conexion->query($sql_educappl);                            
                                if ($result_educappl && $result_educappl->num_rows > 0) { 
                                    $educappl = $result_educappl->fetch_assoc();
                            
                                    if ($educappl['estado'] === 'Activo') {
                                        include 'educacion_lista.php';
                                    } else {
                                        include 'educacion_crea.php';
                                    }
                                } else {
                                    include 'educacion_crea.php'; 
                                }
                            } else {
                                include 'educacion_lista.php';
                            }
                            
                            break;

                        case 'situacion-social':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
                                $sql_sociofamilia = "SELECT estado FROM ppl_familiar_info WHERE idppl = $idppl";                                           
                                $result_sociofamilia = $conexion->query($sql_sociofamilia);    
                            
                                if ($result_sociofamilia && $result_sociofamilia->num_rows > 0) { 
                                    $sociofamilia = $result_sociofamilia->fetch_assoc();
                            
                                    if ($sociofamilia['estado'] === 'Activo') {
                                        include 'socio-familiar_lista.php';
                                    } else {
                                        include 'socio-familiar_crea.php';
                                    }
                                } else {
                                    include 'socio-familiar_crea.php'; 
                                }
                            } else {
                                include 'socio-familiar_lista.php';
                            }                            
                            break;
                        case 'situacion-laboral':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
                                $sql_laboral = "SELECT estado FROM laboral WHERE id_ppl = $idppl";                                           
                                $result_laboral = $conexion->query($sql_laboral);                                    
                                if ($result_laboral && $result_laboral->num_rows > 0) { 
                                    $laboral = $result_laboral->fetch_assoc();                                    
                                    if ($laboral['estado'] === 'Activo') {
                                        include 'laboral_espiritual_lista.php';
                                    } else {
                                        include 'laboral_espiritual_crea.php';
                                    }
                                } else {
                                    include 'laboral_espiritual_crea.php'; 
                                }
                            } else {
                                include 'laboral_espiritual_lista.php';
                            }
                            break;
                        case 'marcas':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) {                                
                                include 'marcas_cuerpo.php';
                            }else{
                                include 'marcas_cuerpo_lista.php';
                            }
                            break;

                        case 'informe-sanitario':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) {
                                include 'sanitario_crea.php';
                            }else{

                            }

                            break;

                        case 'observaciones':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
                                $sql_observaciones = "SELECT estado FROM observaciones WHERE id_ppl = $idppl";                                           
                                $result_observaciones = $conexion->query($sql_observaciones);                                    
                                if ($result_observaciones && $result_observaciones->num_rows > 0) { 
                                    $observaciones = $result_observaciones->fetch_assoc();                                    
                                    if ($observaciones['estado'] === 'Activo') {
                                        include 'observaciones_lista.php';
                                    } else {
                                        include 'observaciones_crea.php';
                                    }
                                } else {
                                    include 'observaciones_crea.php'; 
                                }
                            } else {
                                include 'observaciones_lista.php';
                            }
                            break;

                        case 'clasificacion':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) { 
                                $sql_clasificacion = "SELECT estado FROM clasificacion WHERE id_ppl = $idppl";                                           
                                $result_clasificacion = $conexion->query($sql_clasificacion);                                    
                                if ($result_clasificacion && $result_clasificacion->num_rows > 0) { 
                                    $clasificacion = $result_clasificacion->fetch_assoc();                                    
                                    if ($clasificacion['estado'] === 'Activo') {
                                        include 'clasificacion_lista.php';
                                    } else {
                                        include 'clasificacion_crea.php';
                                    }
                                } else {
                                    include 'clasificacion_crea.php'; 
                                }
                            } else {
                                include 'clasificacion_lista.php';
                            }
                            break;



                        default:
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2|| $_SESSION['id_rol'] === 3) {
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