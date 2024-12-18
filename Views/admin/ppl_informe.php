<?php require 'navbar.php'; ?>
<?php $idppl = isset($_GET['id']) ? $_GET['id'] : '';?>
<!-- --------------------- -->
<?php
    $query = "SELECT dni FROM persona WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $idppl, PDO::PARAM_INT);
    $stmt->execute();
    $obtendni = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    if ($obtendni) {
        foreach ($obtendni as $row) {
            
        }
    } 
?>
<section class="container mt-3">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0 d-flex justify-content-between pb-2 ">
            <h5 class="d-inline-block ">Informe de Evaluación Integral Interdiciplinario (IEII)</h5>
            <a href="prontuario_index.php?dni=<?php echo $row['dni']; ?>" data-toggle="modal" data-backdrop="false" class="btn btn-info btn-sm" type="button" title="ver">
                <i class="fa-solid fa-eye" style="color: #000000;"></i> Prontuario PPL
            </a>
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
                                Sanitario
                            </a>
                            <a href="?seccion=informe-psicologico&id=<?php echo $idppl; ?>" class="btn <?php echo $seccion == 'informe-psicologico' ? 'btn-primary' : 'btn-secondary'; ?>">
                                Psiquiátrico y Psicológico 
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
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2 || $_SESSION['id_rol'] === 3) {
                                include 'persona_lista.php';
                            }
                            break;
                            // ----------------------------
                        case 'educacion':
                            $sql_educappl = "SELECT estado FROM educacion WHERE id_ppl = $idppl";
                            $result_educappl = $conexion->query($sql_educappl);
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {                                
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
                                if ($result_educappl && $result_educappl->num_rows > 0) {
                                    $educappl = $result_educappl->fetch_assoc();
                                    if ($educappl['estado'] === 'Activo') {
                                        include 'educacion_lista.php';
                                    } else {
                                        include 'no_dato.php';
                                    }
                                } else {
                                    include 'no_dato.php';
                                }
                            }
                            break;
                            // ----------------------------
                        case 'situacion-social':
                            $sql_sociofamilia = "SELECT estado FROM ppl_familiar_info WHERE idppl = $idppl";
                            $result_sociofamilia = $conexion->query($sql_sociofamilia);
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
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
                                if ($result_sociofamilia && $result_sociofamilia->num_rows > 0) {
                                    $sociofamilia = $result_sociofamilia->fetch_assoc();
                                    if ($sociofamilia['estado'] === 'Activo') {
                                        include 'socio-familiar_lista.php';
                                    } else {
                                        include 'no_dato.php';
                                    }
                                } else {
                                    include 'no_dato.php';
                                }
                            }
                            break;
                            // ----------------------------
                        case 'situacion-laboral':
                            $sql_laboral = "SELECT estado FROM laboral WHERE id_ppl = $idppl";
                            $result_laboral = $conexion->query($sql_laboral);
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {                                
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
                                if ($result_laboral && $result_laboral->num_rows > 0) {
                                    $laboral = $result_laboral->fetch_assoc();
                                    if ($laboral['estado'] === 'Activo') {
                                        include 'laboral_espiritual_lista.php';
                                    } else {
                                        include 'no_dato.php';
                                    }
                                } else {
                                    include 'no_dato.php';
                                }
                            }
                            break;
                            // ----------------------------
                        case 'marcas':
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) {
                                include 'marcas_cuerpo.php';
                            } else {
                                include 'marcas_cuerpo_lista.php';
                            }
                            break;
                            // ---------------------------------
                            case 'informe-psicologico':
                                $sql_psicologico = "SELECT estado FROM psiquiatrico_psicologico WHERE id_ppl = $idppl";
                                $result_psicologico = $conexion->query($sql_psicologico);
                                if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) {                                    
                                    if ($result_psicologico && $result_psicologico->num_rows > 0) {
                                        $psicologico = $result_psicologico->fetch_assoc();
                                        if ($psicologico['estado'] === 'Activo') {
                                            include 'psiquia_psico_lista.php';
                                        } else {
                                            include 'psiquia_psico_crea.php';
                                        }
                                    } else {
                                        include 'psiquia_psico_crea.php';
                                    }
                                } else {
                                    if ($result_psicologico && $result_psicologico->num_rows > 0) {                                  
                                        include 'psiquia_psico_lista.php';
                                    } else {                                    
                                        include 'no_dato.php';
                                    }
                                }
                                break;   
                            // ----------------------------
                        case 'informe-sanitario':
                            $sql_sanitario = "SELECT id_ppl FROM datos_medicos WHERE id_ppl = $idppl AND peso_actual IS NOT NULL AND talla IS NOT NULL";
                            $result_sanitario = $conexion->query($sql_sanitario);
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) {       
                                if ($result_sanitario && $result_sanitario->num_rows > 0) {                                  
                                    include 'sanitario_lista.php';
                                } else {                                    
                                    include 'sanitario_crea.php';
                                }
                            } else {
                                if ($result_sanitario && $result_sanitario->num_rows > 0) {                                  
                                    include 'sanitario_lista.php';
                                } else {                                    
                                    include 'no_dato.php';
                                }                               
                            }
                            break;
                            // ----------------------------
                        case 'observaciones':
                            $sql_observaciones = "SELECT estado FROM observaciones WHERE id_ppl = $idppl";
                            $result_observaciones = $conexion->query($sql_observaciones);
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                
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
                                if ($result_observaciones && $result_observaciones->num_rows > 0) {
                                    $observaciones = $result_observaciones->fetch_assoc();
                                    if ($observaciones['estado'] === 'Activo') {
                                        include 'observaciones_lista.php';
                                    } else {
                                        include 'no_dato.php';
                                    }
                                } else {
                                    include 'no_dato.php';
                                }
                            }
                            break;
                            // ----------------------------
                        case 'clasificacion':
                            $sql_clasificacion = "SELECT estado FROM clasificacion WHERE id_ppl = $idppl";
                            $result_clasificacion = $conexion->query($sql_clasificacion);
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {                                
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
                                if ($result_clasificacion && $result_clasificacion->num_rows > 0) {
                                    $clasificacion = $result_clasificacion->fetch_assoc();
                                    if ($clasificacion['estado'] === 'Activo') {
                                        include 'clasificacion_lista.php';
                                    } else {
                                        include 'no_dato.php';
                                    }
                                } else {
                                    include 'no_dato.php';
                                }
                            }
                            break;
                        default:
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2 || $_SESSION['id_rol'] === 3) {
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