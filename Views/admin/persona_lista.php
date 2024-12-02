<?php

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    die("ID inválido.");
}

try {
    $stmt_persona = $db->prepare("SELECT 
                                  persona.id,
                                  persona.dni, 
                                  persona.nombres, 
                                  persona.apellidos, 
                                  DATE_FORMAT(persona.fechanac, '%d-%m-%Y') AS fechaNacimiento, 
                                  persona.edad, 
                                  persona.genero, 
                                  persona.estadocivil, 
                                  d.id AS id_direccion, 
                                  p.nombre AS pais, 
                                  pr.nombre AS provincia, 
                                  c.nombre AS ciudad, 
                                  d.localidad, 
                                  d.direccion
                              FROM persona
                              LEFT JOIN domicilio d ON persona.direccion = d.id
                              LEFT JOIN paises p ON d.id_pais = p.id
                              LEFT JOIN provincias pr ON d.id_provincia = pr.id
                              LEFT JOIN ciudades c ON d.id_ciudad = c.id
                              WHERE persona.id = :id
    ");
    $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

    $stmt_ppl = $db->prepare("SELECT id, apodo, profesion, trabaja, foto, huella
        FROM ppl
        WHERE idpersona = :id
    ");
    $stmt_ppl->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);

    $stmt_situacion = $db->prepare("SELECT situacionlegal.id_ppl, situacionlegal.fecha_detencion, situacionlegal.dependencia, situacionlegal.motivo_t, 
               situacionlegal.situacionlegal, situacionlegal.causas, situacionlegal.id_juzgado, situacionlegal.en_prejucio, 
               situacionlegal.condena, situacionlegal.categoria, situacionlegal.reingreso_falta, situacionlegal.causas_pend, 
               situacionlegal.cumplio_medida, situacionlegal.asistio_rehabi, situacionlegal.causa_nino, 
               situacionlegal.tiene_defensor, situacionlegal.nombre_defensor, situacionlegal.tiene_com_defensor,
               juzgado.nombre AS nombre_juzgado, 
               juzgado.nombre_juez AS nombre_juez,
               GROUP_CONCAT(delitos.nombre SEPARATOR '\n') AS nombres_causas
        FROM situacionlegal
        LEFT JOIN juzgado ON situacionlegal.id_juzgado = juzgado.id
        LEFT JOIN ppl_causas ON situacionlegal.id_ppl = ppl_causas.id_ppl
        LEFT JOIN delitos ON ppl_causas.id_causa = delitos.id_delito
        WHERE situacionlegal.id_ppl = (SELECT id FROM ppl WHERE idpersona = :id)
        GROUP BY situacionlegal.id_ppl
    ");
    $stmt_situacion->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_situacion->execute();
    $situacion_legal = $stmt_situacion->fetch(PDO::FETCH_ASSOC);

    if (!$situacion_legal && $ppl && $persona) {
        echo "No se encontraron datos para este ID.";
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}
function obtenerUltimaFoto($id, $db) {
    try {
        // Query to get the latest photo using person ID
        $query = "SELECT ppl.foto, ppl.id as ppl_id, persona.id as persona_id, persona.dni 
                 FROM ppl 
                 JOIN persona ON ppl.idpersona = persona.id 
                 WHERE persona.id = :id 
                 ORDER BY ppl.id DESC 
                 LIMIT 1";
                 
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $rutaBase = '../../img_ppl/';
        $fotoDefault = 'default.jpg';
        
        if ($resultado && !empty($resultado['foto'])) {
            $rutaFoto = $rutaBase . htmlspecialchars($resultado['foto'], ENT_QUOTES, 'UTF-8');
            
            if (file_exists($rutaFoto)) {
                return $rutaFoto;
            }
        }
        
        return $rutaBase . $fotoDefault;
    } catch (PDOException $e) {
        return $rutaBase . $fotoDefault;
    }
}

?>


<style>
    .form-group {
        margin-bottom:
            1rem;
        display: flex;
        align-items: center;
    }

    .form-group label {
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .form-control-static {
        padding-top: calc(0.375rem + 1px);
        padding-bottom: calc(0.375rem + 1px);
        margin-bottom: 0;
        line-height: 1.5;
        flex-grow: 1;
    }
    .form-container {
        margin: 20px;
    }

    .foto {
        width: 250px;
        height: 250px;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid #ddd;
    }

    .line {
        display: inline-block;
        width: 150px;
        border-bottom: 1px solid #000;
        margin-left: 5px;
    }

    .section-title {
        font-weight: bold;
    }
</style>
<!-- ----------------------------------- -->
<div class="container mt-4">
    <?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        die("ID inválido.");
    }

    if (isset($_SESSION['id_rol']) && ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2)) {
        echo '<div style="display: inline-block; margin-right: 10px;">';
        if ($persona && isset($ppl['id'])) {
            echo '<a class="btn btn-warning btn-sm" href="persona_edit.php?id=' . $persona['id'] . '" type="button" title="Editar Persona">Editar Persona</a>';
        }
        echo '</div>';

        if ($ppl && isset($ppl['id'])) {
            echo '<div style="display: inline-block; margin-right: 10px;">';
            echo '<a class="btn btn-warning btn-sm" href="ppl_edit.php?id=' . $ppl['id'] . '" type="button" title="Editar PPL">Editar PPL</a>';
            echo '</div>';
        }

        if ($situacion_legal && isset($situacion_legal['id_ppl'])) {
            echo '<div style="display: inline-block; margin-right: 10px;">';
            echo '<a class="btn btn-warning btn-sm" href="situacionlegal_edit.php?id=' . $situacion_legal['id_ppl'] . '" type="button" title="Editar Situación Legal">Editar Situación Legal</a>';
            echo '</div>';
        }
    }
    ?>

    <!-- ----------------------------------------- -->
    <div class="card">
        <div class="card-body">
                <h4 class="card-title">Datos Personales</h4>
                <div class="row mt-3"> 
                    <div class="col-md-8">
                        <div>
                            <label class="h6">D.N.I.:</label>                        
                            <?php echo !empty($persona['dni']) ? htmlspecialchars($persona['dni'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?>                        
                        </div>
                        <div>                            
                            <label class="h6">Nombres:</label>
                            <?php echo !empty($persona['nombres']) ? htmlspecialchars($persona['nombres'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                        </div>                
                        <div>
                            <label class="h6">Apellidos:</label>
                            <?php echo !empty($persona['apellidos']) ? htmlspecialchars($persona['apellidos'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                        </div>                              
                        <div>
                            <label class="h6">Fecha de Nacimiento:</label>
                            <?php if (!empty($persona['fechaNacimiento'])) {
                                echo date('d-m-Y', strtotime($persona['fechaNacimiento']));
                                } else {echo 'No hay dato';}?>
                        </div>
                        <div>
                            <label class="h6">Edad:</label>
                            <?php echo !empty($persona['edad']) ? htmlspecialchars($persona['edad'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                        </div>
                        <div>
                            <label class="h6">Género:</label>
                            <?php echo !empty($persona['genero']) ? htmlspecialchars($persona['genero'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                        </div>
                        <div>
                            <label class="h6">Estado Civil:</label>
                            <?php echo !empty($persona['estadocivil']) ? htmlspecialchars($persona['estadocivil'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                        </div>
                        <div>
                            <label class="h6">Dirección:</label>                   
                            <?php if (!empty($persona['direccion'])) {
                                echo htmlspecialchars(
                                    (!empty($persona['pais']) ? $persona['pais'] . ', ' : '') . 
                                        (!empty($persona['provincia']) ? $persona['provincia'] . ', ' : '') . 
                                        (!empty($persona['ciudad']) ? $persona['ciudad'] . ', ' : '') . 
                                        (!empty($persona['localidad']) ? $persona['localidad'] . ', ' : '') . 
                                        (!empty($persona['direccion']) ? $persona['direccion'] : ''), 
                                ENT_QUOTES, 'UTF-8');} else {echo 'No hay dato';}?>
                        </div>                        
                    </div>
                    <div class="col-md-4 text-center">                            
                        <div class="foto">
                            <?php if (!isset($id)) {
                                echo "<p>Error: DNI no definido</p>";
                            } else { $rutaFoto = obtenerUltimaFoto($id, $db); ?>
                                <img src="<?php echo htmlspecialchars($rutaFoto, ENT_QUOTES, 'UTF-8'); ?>" 
                                    alt="<?php echo ($rutaFoto !== '../../img_ppl/default.jpg') ? 'Foto de la persona' : 'Foto no disponible'; ?>" 
                                    style="max-width: 250px; max-height: 250px; object-fit: cover;">
                            <?php } ?>
                        </div>                    
                    </div> 
                </div>            
            <!-- ------------------------- -->
            <h4 class="mt-3">Información de PPL</h4>
            <div class="row">
                <div>
                    <label class="h6 ">¿Trabajaba en el momento de la detención?:</label>
                    <span class="fs-5"><?php echo isset($ppl['trabaja']) ? ($ppl['trabaja'] ? 'Sí' : 'No') : 'No hay dato';?></span>                    
                </div>
                <div>
                    <label class="h6">Profesión:</label>
                    <?php echo !empty($ppl['profesion']) ? htmlspecialchars($ppl['profesion'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?>                    
                </div>      
                <div>
                    <label class="h6">Huella:</label>                
                    <?php if (!empty($ppl['huella'])): ?>
                        <img src="data:image/png;base64,<?php echo !empty($ppl['huella']) ? base64_encode($ppl['huella']) : 'No hay dato' ?>" alt="Huella" style="max-width: 200px; max-height: 200px;">
                    <?php else: ?> No se encontró huella. <?php endif; ?>
                </div>          
            </div>       
            <!-- ------------------------- -->  
            <h4 class="mt-3 mb-3">Situación Legal</h4>
            <div class="row">
                <div>
                    <?php if ($situacion_legal): ?>                
                    <label class="h6">Fecha de detención:</label>                    
                    <?php if (!empty($situacion_legal['fecha_detencion'])) {
                        echo date('d-m-Y', strtotime($situacion_legal['fecha_detencion']));
                    } else { echo 'No hay dato'; } ?>
                </div>
                <div>
                    <label class="h6">Dependencia:</label>
                    <?php echo !empty($situacion_legal['dependencia']) ? htmlspecialchars($situacion_legal['dependencia'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                </div>
                <div>
                    <label class="h6">Motivo de traslado:</label>
                    <?php echo !empty($situacion_legal['motivo_t']) ? htmlspecialchars($situacion_legal['motivo_t'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                </div>
                <div>
                    <label class="h6">Situación legal:</label>
                    <?php echo !empty($situacion_legal['situacionlegal']) ? htmlspecialchars($situacion_legal['situacionlegal'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                </div>
                <div>
                    <label class="h6">Causa/s:</label>
                    <?php echo !empty($situacion_legal['nombres_causas'])
                    ? nl2br(htmlspecialchars($situacion_legal['nombres_causas'], ENT_QUOTES, 'UTF-8'))
                    : 'No hay dato'; ?>
                </div>
                <div>
                    <label class="h6">Juzgado:</label>                    
                    <?php
                    if (!empty($situacion_legal['nombre_juzgado']) && !empty($situacion_legal['nombre_juez'])) {
                        echo nl2br(htmlspecialchars($situacion_legal['nombre_juzgado'], ENT_QUOTES, 'UTF-8')) . ' - Juez: ' .
                            nl2br(htmlspecialchars($situacion_legal['nombre_juez'], ENT_QUOTES, 'UTF-8'));
                    } else { echo 'No hay dato'; } ?>
                </div>
                <div>
                    <label class="h6">En perjuicio de quien:</label>
                    <?php echo !empty($situacion_legal['en_prejucio']) ? htmlspecialchars($situacion_legal['en_prejucio'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?> 
                </div>
                <div>
                    <label class="h6">Condena:</label>
                    <?php echo !empty($situacion_legal['condena']) ? htmlspecialchars($situacion_legal['condena'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                </div>
                <div>
                    <label class="h6">Categoria:</label>
                    <?php echo !empty($situacion_legal['categoria']) ? htmlspecialchars($situacion_legal['categoria'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                </div>
                <div>
                    <label class="h6">Causas pendientes de resolución:</label>
                    <?php echo !empty($situacion_legal['causas_pend']) ? htmlspecialchars($situacion_legal['causas_pend'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>
                </div>
                <div>
                    <label class="h6">Reingreso en caso de quebrantamiento de beneficio y/o libertad:</label>
                    <span class="fs-5"><?php echo isset($situacion_legal['reingreso_falta']) ? ($situacion_legal['reingreso_falta'] ? 'Sí' : 'No') : 'No hay dato'; ?></span> 
                </div>
                <div>
                    <label class="h6">¿Causas judicializadas durante la niñez o adolescencia?:</label>
                    <span class="fs-5"><?php echo isset($situacion_legal['causa_nino']) ? ($situacion_legal['causa_nino'] ? 'Sí' : 'No') : 'No hay dato'; ?></span> 
                </div>
                <div>
                    <label class="h6">¿Cumplió medidas socioeducativas?:</label>
                    <span class="fs-5"><?php echo isset($situacion_legal['cumplio_medida']) ? ($situacion_legal['cumplio_medida'] ? 'Sí' : 'No') : 'No hay dato'; ?></span> 
                </div>
                <div>
                    <label class="h6">Institucionalizaciones en centros de rehabilitación por conflictos con la ley:</label>
                    <span class="fs-5"><?php echo isset($situacion_legal['asistio_rehabi']) ? ($situacion_legal['asistio_rehabi'] ? 'Sí' : 'No') : 'No hay dato'; ?></span> 
                </div>
                <div>
                    <label class="h6">¿Cuenta con un defensor oficial?:</label>
                    <span class="fs-5"><?php echo isset($situacion_legal['tiene_defensor']) ? ($situacion_legal['tiene_defensor'] ? 'Sí' : 'No') : 'No hay dato'; ?></span> 
                </div>
                <?php if (isset($situacion_legal['tiene_defensor']) && $situacion_legal['tiene_defensor']) : ?>
                    <div>
                        <label class="h6">¿Quién?:</label>
                        <?php echo !empty($situacion_legal['nombre_defensor']) ? htmlspecialchars($situacion_legal['nombre_defensor'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?>
                    </div>
                    <div>
                        <label class="h6">¿Tiene comunicación con él?:</label>
                        <span class="fs-5"><?php echo isset($situacion_legal['tiene_com_defensor']) ? ($situacion_legal['tiene_com_defensor'] ? 'Sí' : 'No') : 'No hay dato'; ?></span> 
                    </div>
                <?php endif; ?>
                

            </div>

                    
                
                    
                    
                    
                    
                        
                
        </div>

    <?php else: ?>
        <p>No se encontraron datos de situación legal para este ID.</p>
    <?php endif; ?>
    </div>
</div>