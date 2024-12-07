<?php
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    die("ID inválido.");
}

function obtenerSituacionLegal($id_ppl, $db)
{
    try {

        $query = "SELECT 
            situacionlegal.id_ppl, 
            situacionlegal.fecha_detencion, 
            situacionlegal.dependencia, 
            situacionlegal.motivo_t, 
            situacionlegal.situacionlegal, 
            situacionlegal.id_juzgado, 
            situacionlegal.en_prejucio, 
            situacionlegal.condena, 
            situacionlegal.categoria, 
            situacionlegal.reingreso_falta, 
            situacionlegal.causas_pend, 
            situacionlegal.cumplio_medida, 
            situacionlegal.asistio_rehabi, 
            situacionlegal.causa_nino, 
            situacionlegal.tiene_defensor, 
            situacionlegal.nombre_defensor, 
            situacionlegal.tiene_com_defensor,
            juzgado.nombre AS nombre_juzgado, 
            juzgado.nombre_juez AS nombre_juez,
            GROUP_CONCAT(delitos.nombre SEPARATOR '\n') AS nombres_causas
        FROM situacionlegal
        LEFT JOIN juzgado ON situacionlegal.id_juzgado = juzgado.id
        LEFT JOIN ppl_causas ON situacionlegal.id_ppl = ppl_causas.id_ppl
        LEFT JOIN delitos ON ppl_causas.id_causa = delitos.id_delito
        WHERE situacionlegal.id_ppl = :id_ppl
        GROUP BY situacionlegal.id_ppl";

      
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_ppl', $id_ppl, PDO::PARAM_INT);
        $stmt->execute();

        
        $situacionLegal = $stmt->fetch(PDO::FETCH_ASSOC);
        return $situacionLegal; 
    } catch (PDOException $e) {
        
        return null;
    }
}
$situacion_legal = obtenerSituacionLegal($id, $db);  

try {
    
    $stmt_persona = $db->prepare("SELECT persona.id, persona.dni, persona.nombres, persona.apellidos, 
        DATE_FORMAT(persona.fechanac, '%d-%m-%Y') AS fechaNacimiento, persona.edad, persona.genero, 
        persona.estadocivil, d.id AS id_direccion, p.nombre AS pais, pr.nombre AS provincia, c.nombre AS ciudad, 
        d.localidad, d.direccion, 
        CONCAT(d.localidad, ', ', d.direccion, ', ', c.nombre, ', ', pr.nombre, ', ', p.nombre) AS direccion_completa 
        FROM persona 
        LEFT JOIN domicilio d ON persona.id = d.id_persona 
        LEFT JOIN paises p ON d.id_pais = p.id 
        LEFT JOIN provincias pr ON d.id_provincia = pr.id 
        LEFT JOIN ciudades c ON d.id_ciudad = c.id 
        WHERE persona.id = :id LIMIT 0, 25;");
    $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

   
    $stmt_ppl = $db->prepare("SELECT id, apodo, profesion, trabaja, foto, huella
        FROM ppl
        WHERE idpersona = :id");
    $stmt_ppl->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

function mostrarCausas($id, $db)
{
    try {
       
        $query = "SELECT delitos.nombre 
                  FROM ppl_causas
                  JOIN delitos ON ppl_causas.id_causa = delitos.id_delito
                  WHERE ppl_causas.id_ppl = :id_ppl";

      
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_ppl', $id, PDO::PARAM_INT);
        $stmt->execute();

        $causas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($causas) {
            echo "<ul>";
            foreach ($causas as $causa) {
                echo "<li>" . htmlspecialchars($causa['nombre'], ENT_QUOTES, 'UTF-8') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='text-muted'>No hay causas registradas.</p>";
        }
    } catch (PDOException $e) {
        echo "Error al obtener las causas: " . $e->getMessage();
    }
}

function obtenerUltimaFoto($id, $db)
{
    try {
       
        $query = "SELECT ppl.foto, 
                    ppl.id AS ppl_id, 
                    persona.id AS persona_id, 
                    persona.dni
                    FROM ppl
                    JOIN persona ON ppl.idpersona = persona.id
                    WHERE persona.id = :id
                    ORDER BY ppl.id DESC
                    LIMIT 1;";

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

function mostrarDireccion($persona)
{
    // Arreglo para almacenar la dirección
    $direccionCompleta = [];

    // Verificar y agregar cada campo si tiene datos
    if (!empty($persona['pais'])) $direccionCompleta[] = "<strong>País:</strong> " . htmlspecialchars($persona['pais'], ENT_QUOTES, 'UTF-8');
    if (!empty($persona['provincia'])) $direccionCompleta[] = "<strong>Provincia:</strong> " . htmlspecialchars($persona['provincia'], ENT_QUOTES, 'UTF-8');
    if (!empty($persona['ciudad'])) $direccionCompleta[] = "<strong>Ciudad:</strong> " . htmlspecialchars($persona['ciudad'], ENT_QUOTES, 'UTF-8');
    if (!empty($persona['localidad'])) $direccionCompleta[] = "<strong>Localidad:</strong> " . htmlspecialchars($persona['localidad'], ENT_QUOTES, 'UTF-8');
    if (!empty($persona['direccion'])) $direccionCompleta[] = "<strong>Dirección:</strong> " . htmlspecialchars($persona['direccion'], ENT_QUOTES, 'UTF-8');

    // Si hay alguna dirección, mostrarla
    if (!empty($direccionCompleta)) {
        echo "<p>" . implode('<br>', $direccionCompleta) . "</p>";
    } else {
        echo "<p class='text-muted'>No hay dato</p>";
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
<div class="container-fluid ">
    <!-- Action Buttons -->
    <?php if (isset($_SESSION['id_rol']) && ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2)): ?>
        <div class="mb-4">
            <?php if ($persona && isset($ppl['id'])): ?>
                <a class="btn btn-warning me-2 btn-sm" href="persona_edit.php?id=<?php echo $persona['id']; ?>">
                    <i class="fas fa-user-edit me-1"></i> Editar Persona
                </a>
            <?php endif; ?>

            <?php if ($ppl && isset($ppl['id'])): ?>
                <a class="btn btn-warning me-2 btn-sm" href="ppl_edit.php?id=<?php echo $persona['id']; ?>">
                    <i class="fas fa-edit me-1"></i> Editar PPL
                </a>
            <?php endif; ?>

            <?php if ($situacion_legal && isset($situacion_legal['id_ppl'])): ?>
                <a class="btn btn-warning btn-sm" href="situacionlegal_edit.php?id=<?php echo $persona['id']; ?>">
                    <i class="fas fa-balance-scale me-1"></i> Editar Situación Legal
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Profile Header -->
    <div class="row bg-primary text-white p-4 rounded">
        <div class="col-md-3 text-center">
            <?php if (!isset($id)): ?>
                <p>Error: DNI no definido</p>
            <?php else:
                $rutaFoto = obtenerUltimaFoto($id, $db); ?>
                <img src="<?php echo htmlspecialchars($rutaFoto, ENT_QUOTES, 'UTF-8'); ?>"
                    alt="<?php echo ($rutaFoto !== '../../img_ppl/default.jpg') ? 'Foto de la persona' : 'Foto no disponible'; ?>"
                    class="" style="width: 200px; height: 200px; object-fit: cover;">
            <?php endif; ?>
        </div>
        <div class="col-md-9">
            <h2><?php echo htmlspecialchars($persona['nombres'] . ' ' . $persona['apellidos'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="lead mb-0">DNI: <?php echo htmlspecialchars($persona['dni'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Datos Personales -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm ">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Datos Personales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="mb-1 fw-bold">Fecha de Nacimiento</p>
                            <p><?php echo !empty($persona['fechaNacimiento']) ? date('d-m-Y', strtotime($persona['fechaNacimiento'])) : 'No hay dato'; ?></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1 fw-bold">Edad</p>
                            <p><?php echo !empty($persona['edad']) ? htmlspecialchars($persona['edad'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1 fw-bold">Género</p>
                            <p><?php echo !empty($persona['genero']) ? htmlspecialchars($persona['genero'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1 fw-bold">Estado Civil</p>
                            <p><?php echo !empty($persona['estadocivil']) ? htmlspecialchars($persona['estadocivil'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1 fw-bold">Dirección Completa</p>
                            <?php mostrarDireccion($persona); ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Información PPL -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Información PPL</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <p class="mb-1 fw-bold">¿Trabajaba en el momento de la detención?</p>
                            <p><?php echo isset($ppl['trabaja']) ? ($ppl['trabaja'] ? 'Sí' : 'No') : 'No hay dato'; ?></p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1 fw-bold">Profesión</p>
                            <p><?php echo !empty($ppl['profesion']) ? htmlspecialchars($ppl['profesion'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                        </div>
                        <?php if (!empty($ppl['huella'])): ?>
                            <div class="col-12">
                                <p class="mb-1 fw-bold">Huella</p>
                                <img src="data:image/png;base64,<?php echo base64_encode($ppl['huella']); ?>"
                                    alt="Huella" class="img-fluid" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Situación Legal -->
        <?php if ($situacion_legal): ?>
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-balance-scale me-2"></i>Situación Legal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <p class="mb-1 fw-bold">Fecha de detención</p>
                                <p><?php echo !empty($situacion_legal['fecha_detencion']) ? date('d-m-Y', strtotime($situacion_legal['fecha_detencion'])) : 'No hay dato'; ?></p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 fw-bold">Dependencia</p>
                                <p><?php echo !empty($situacion_legal['dependencia']) ? htmlspecialchars($situacion_legal['dependencia'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 fw-bold">Situación legal</p>
                                <p><?php echo !empty($situacion_legal['situacionlegal']) ? htmlspecialchars($situacion_legal['situacionlegal'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                            </div>
                            <div class="col-12">
                                <p class="mb-1 fw-bold">Causa/s</p>
                                <?php
                              
                                mostrarCausas($id, $db);
                                ?>
                            </div>

                            <div class="col-md-6">
                                <p class="mb-1 fw-bold">Juzgado y Juez</p>
                                <p><?php
                                    if (!empty($situacion_legal['nombre_juzgado']) && !empty($situacion_legal['nombre_juez'])) {
                                        echo nl2br(htmlspecialchars($situacion_legal['nombre_juzgado'], ENT_QUOTES, 'UTF-8')) . '<br>Juez: ' .
                                            nl2br(htmlspecialchars($situacion_legal['nombre_juez'], ENT_QUOTES, 'UTF-8'));
                                    } else {
                                        echo 'No hay dato';
                                    } ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 fw-bold">Condena</p>
                                <p><?php echo !empty($situacion_legal['condena']) ? htmlspecialchars($situacion_legal['condena'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></p>
                            </div>

                            <!-- Información adicional -->
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">Reingreso por quebrantamiento</td>
                                                <td><?php echo isset($situacion_legal['reingreso_falta']) ? ($situacion_legal['reingreso_falta'] ? 'Sí' : 'No') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Causas en la niñez/adolescencia</td>
                                                <td><?php echo isset($situacion_legal['causa_nino']) ? ($situacion_legal['causa_nino'] ? 'Sí' : 'No') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Cumplió medidas socioeducativas</td>
                                                <td><?php echo isset($situacion_legal['cumplio_medida']) ? ($situacion_legal['cumplio_medida'] ? 'Sí' : 'No') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Asistió a rehabilitación</td>
                                                <td><?php echo isset($situacion_legal['asistio_rehabi']) ? ($situacion_legal['asistio_rehabi'] ? 'Sí' : 'No') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Categoría</td>
                                                <td><?php echo !empty($situacion_legal['categoria']) ? htmlspecialchars($situacion_legal['categoria'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Motivo de traslado</td>
                                                <td><?php echo !empty($situacion_legal['motivo_t']) ? htmlspecialchars($situacion_legal['motivo_t'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">En perjuicio de quien (si es intrafamiliar)</td>
                                                <td><?php
                                                    if (isset($situacion_legal['en_prejucio']) && $situacion_legal['en_prejucio']) {
                                                        echo !empty($situacion_legal['en_prejucio'])
                                                            ? htmlspecialchars($situacion_legal['en_prejucio'], ENT_QUOTES, 'UTF-8')
                                                            : 'No especificado';
                                                    } else {
                                                        echo 'No aplica';
                                                    }
                                                    ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Causas pendientes</td>
                                                <td><?php echo !empty($situacion_legal['causas_pend']) ? htmlspecialchars($situacion_legal['causas_pend'], ENT_QUOTES, 'UTF-8') : 'No hay dato'; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tiene defensor oficial</td>
                                                <td>
                                                    <?php echo isset($situacion_legal['tiene_defensor']) ? ($situacion_legal['tiene_defensor'] ? 'Sí' : 'No') : 'No hay dato'; ?>
                                                    <?php if (isset($situacion_legal['tiene_defensor']) && $situacion_legal['tiene_defensor']): ?>
                                                        <br>Nombre: <?php echo htmlspecialchars($situacion_legal['nombre_defensor'], ENT_QUOTES, 'UTF-8'); ?>
                                                        <br>Tiene comunicación: <?php echo isset($situacion_legal['tiene_com_defensor']) ? ($situacion_legal['tiene_com_defensor'] ? 'Sí' : 'No') : 'No hay dato'; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>


    </div>
</div>