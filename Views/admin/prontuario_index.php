<?php require 'navbar.php'; ?>
<?php
if (!function_exists('finfo_open')) {
    die('fileinfo extension is not available');
}

if (isset($_GET['dni']) && ctype_digit($_GET['dni'])) {
    $dni = $_GET['dni'];
    // Modified query to get the latest record for this DNI
    $stmt = $conexion->prepare("SELECT ppl.*, per.*
        FROM ppl AS ppl
        LEFT JOIN persona AS per 
        ON ppl.idpersona = per.id
        WHERE per.dni = ?
        ORDER BY ppl.id DESC LIMIT 1
    ");
    $stmt->bind_param("i", $dni);
    $stmt->execute();
    $result = $stmt->get_result();
    $pplget = $result->fetch_assoc();
    if ($pplget) {
        $id = $pplget['idpersona'];
    } else {
        echo "No se encontró ningún resultado para el DNI proporcionado.";
    }
    $stmt->close();
} else {
    echo "DNI no válido.";
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
                              ORDER BY persona.id DESC LIMIT 1
    ");
    $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);
    // Modified PPL query to get latest record
    $stmt_ppl = $db->prepare("SELECT id, apodo, profesion, trabaja, foto, huella
        FROM ppl
        WHERE idpersona = :id
        ORDER BY id DESC LIMIT 1
    ");
    $stmt_ppl->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);

    // Modified situacion legal query to get latest record
    $stmt_situacion = $db->prepare("SELECT situacionlegal.*,
               juzgado.nombre AS nombre_juzgado, 
               juzgado.nombre_juez AS nombre_juez,
               GROUP_CONCAT(delitos.nombre SEPARATOR '\n') AS nombres_causas
        FROM situacionlegal
        LEFT JOIN juzgado ON situacionlegal.id_juzgado = juzgado.id
        LEFT JOIN ppl_causas ON situacionlegal.id_ppl = ppl_causas.id_ppl
        LEFT JOIN delitos ON ppl_causas.id_causa = delitos.id_delito
        WHERE situacionlegal.id_ppl = (SELECT id FROM ppl WHERE idpersona = :id ORDER BY id DESC LIMIT 1)
        GROUP BY situacionlegal.id_ppl
        ORDER BY situacionlegal.id_ppl DESC LIMIT 1
    ");
    $stmt_situacion->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_situacion->execute();
    $situacion_legal = $stmt_situacion->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

// Modified the table query to show latest records
try {
    $query = "SELECT ppl.*, per.*
            FROM ppl AS ppl
            LEFT JOIN persona AS per ON ppl.idpersona = per.id
            WHERE per.dni = :dni
            ORDER BY ppl.id DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
    $stmt->execute();
    $pples = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener el ppl: " . $e->getMessage());
}
// Función para validar y obtener la foto
function obtenerUltimaFoto($dni, $db) {
    try {
        // Query para obtener la última foto
        $query = "SELECT ppl.foto, ppl.id as ppl_id, persona.id as persona_id, persona.dni 
                 FROM ppl 
                 JOIN persona ON ppl.idpersona = persona.id 
                 WHERE persona.dni = :dni 
                 ORDER BY ppl.id DESC 
                 LIMIT 1";
                 
        $stmt = $db->prepare($query);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
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
<!-- ------------------------ -->
<style>
    
    .foto {
        width: 250px;
        height: 250px;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid #ddd;
    }   
</style>
<!-- ------------------------------------ -->

<!-- Mostrar el valor de $ultima_fecha aquí -->



<?php $dni = isset($_GET['dni']) ? $_GET['dni'] : '';?>
<div class="container-fluid pt-3">
    <!-- Main Card -->
    <div class="card shadow-sm border-0 rounded-0">
        <div class="card-header bg-dark text-white ">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="my-0 py-0">Ultimo Ingreso : <span id="fechaDisplay"></span></h5>
            </div>
        </div>
        
        <div class="card-body py-2 my-0">
            <div class="row g-2 ">
                <!-- Personal Information Column -->
                <div class="col-md-8 ">
                    <div class="row g-2">
                        <!-- Full Name -->
                        <div class="col-md-6 ">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-1">Apellidos y Nombres</h6>
                                    <p class="h5 mb-1">
                                        <?php echo !empty($persona['nombres']) && !empty($persona['apellidos']) ?
                                            htmlspecialchars($persona['nombres'] . ' ' . $persona['apellidos'], ENT_QUOTES, 'UTF-8') :
                                            '<span class="text-muted">No hay dato</span>'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-1">DNI</h6>
                                    <p class="h5 mb-1">
                                    <?php echo htmlspecialchars($dni, ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Birth Info -->
                        <div class="col-md-6 ">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-1">Fecha de Nacimiento</h6>
                                    <p class="mb-1"><?php echo !empty($persona['fechaNacimiento']) ? 
                                        date('d-m-Y', strtotime($persona['fechaNacimiento'])) : 
                                        '<span class="text-muted">No hay dato</span>'; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Age -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-1">Edad</h6>
                                    <p class="mb-1"><?php echo !empty($persona['edad']) ? 
                                        htmlspecialchars($persona['edad'], ENT_QUOTES, 'UTF-8') : 
                                        '<span class="text-muted">No hay dato</span>'; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-2">Ubicación</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block py-0 my-0">País</small>
                                            <span class="py-0 my-0"><?php echo !empty($persona['pais']) ? 
                                                htmlspecialchars($persona['pais'], ENT_QUOTES, 'UTF-8') : 
                                                '<span class="text-muted">No hay dato</span>'; ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block py-0 my-0">Provincia</small>
                                            <span class="py-0 my-0"><?php echo !empty($persona['provincia']) ? 
                                                htmlspecialchars($persona['provincia'], ENT_QUOTES, 'UTF-8') : 
                                                '<span class="text-muted">No hay dato</span>'; ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block py-0 my-0">Ciudad</small>
                                            <span class="py-0 my-0"><?php echo !empty($persona['ciudad']) ? 
                                                htmlspecialchars($persona['ciudad'], ENT_QUOTES, 'UTF-8') : 
                                                '<span class="text-muted">No hay dato</span>'; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-1">Localidad</h6>
                                    <p class="mb-1"><?php echo !empty($persona['localidad']) ? 
                                        htmlspecialchars($persona['localidad'], ENT_QUOTES, 'UTF-8') : 
                                        '<span class="text-muted">No hay dato</span>'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-0 my-0">
                                    <h6 class="text-muted mb-1">Dirección Completa</h6>
                                    <p class="mb-1"><?php echo !empty($persona['direccion']) ? 
                                        htmlspecialchars($persona['direccion'], ENT_QUOTES, 'UTF-8') : 
                                        '<span class="text-muted">No hay dato</span>'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photo Column -->
                <div class="col-md-4 py-0 my-0">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="position-relative">
                                <?php
                                if (!isset($dni)) {
                                    echo '<div class="alert alert-danger">Error: DNI no definido</div>';
                                } else {
                                    $rutaFoto = obtenerUltimaFoto($dni, $db);
                                ?>
                                    <img src="<?php echo htmlspecialchars($rutaFoto, ENT_QUOTES, 'UTF-8'); ?>" 
                                         class="img-fluid rounded-3 shadow-sm"
                                         alt="<?php echo ($rutaFoto !== '../../img_ppl/default.jpg') ? 'Foto de la persona' : 'Foto no disponible'; ?>" 
                                         style="max-width: 400px; max-height: 400px; object-fit: cover;">
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table Card -->
    <div class="card shadow-sm border-0 py-0 my-0 rounded-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-hover table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre y Apellido</th>
                        <th>DNI</th>
                        <th>Domicilio</th>
                        <th>Fecha detención</th>
                        <th>Inicio Condena</th>
                        <th>Fin Condena</th>
                        <th>Carga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = "SELECT 
                            ppl.*,
                            per.*,
                            pai.nombre AS nombre_pais,
                            ciu.nombre AS nombre_ciudad,
                            pro.nombre AS nombre_provincia,
                            dom.localidad AS localidad_domicilio,
                            dom.direccion AS direccion_domicilio,
                            DATE_FORMAT(sl.fecha_detencion, '%d-%m-%Y') AS fecha_detencion,
                            DATE_FORMAT(fppl.inicio_condena, '%d-%m-%Y') AS inicio_condena,
                            COALESCE(DATE_FORMAT(fppl.fin_condena, '%d-%m-%Y'), '-') AS fin_condena
                        FROM 
                            ppl AS ppl
                        LEFT JOIN 
                            persona AS per ON ppl.idpersona = per.id
                        LEFT JOIN 
                            domicilio AS dom ON per.id = dom.id_persona
                        LEFT JOIN 
                            paises AS pai ON dom.id_pais = pai.id
                        LEFT JOIN 
                            ciudades AS ciu ON dom.id_ciudad = ciu.id
                        LEFT JOIN 
                            provincias AS pro ON dom.id_provincia = pro.id
                        LEFT JOIN
                            situacionlegal AS sl ON ppl.idpersona = sl.id_ppl
                        LEFT JOIN
                            fechappl AS fppl ON ppl.idpersona = fppl.idppl
                        WHERE 
                            per.dni = :dni
                        ORDER BY 
                            ppl.id DESC";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
                        $stmt->execute();
                        $pples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($pples as $ppl) {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ppl['nombres'] . ' ' . $ppl['apellidos'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ppl['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php 
                                $domicilio = [];
                                if (!empty($ppl['nombre_pais'])) $domicilio[] = htmlspecialchars($ppl['nombre_pais'], ENT_QUOTES, 'UTF-8');
                                if (!empty($ppl['nombre_provincia'])) $domicilio[] = htmlspecialchars($ppl['nombre_provincia'], ENT_QUOTES, 'UTF-8');
                                if (!empty($ppl['nombre_ciudad'])) $domicilio[] = htmlspecialchars($ppl['nombre_ciudad'], ENT_QUOTES, 'UTF-8');
                                if (!empty($ppl['localidad_domicilio'])) $domicilio[] = htmlspecialchars($ppl['localidad_domicilio'], ENT_QUOTES, 'UTF-8');
                                if (!empty($ppl['direccion_domicilio'])) $domicilio[] = htmlspecialchars($ppl['direccion_domicilio'], ENT_QUOTES, 'UTF-8');
                                echo implode(', ', $domicilio); 
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($ppl['fecha_detencion'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ppl['inicio_condena'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php                             
                                if (!isset($ultima_fecha)){
                                    $ultima_fecha= $ppl['inicio_condena'];
                                }
                            ?>
                            <td><?php echo htmlspecialchars($ppl['fin_condena'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a class="btn btn-info" href='ppl_informe.php?id=<?php echo $ppl['id']; ?>'>Informe(IEII)</a>
                            </td>
                        </tr>
                    <?php
                        }
                    } catch (PDOException $e) {
                        error_log("Error al obtener los pples: " . $e->getMessage());
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
<script>
    var ultimaFecha = "<?php echo htmlspecialchars($ultima_fecha, ENT_QUOTES, 'UTF-8'); ?>";
    document.getElementById("fechaDisplay").textContent = ultimaFecha;
    document.getElementById("reflectDateButton").addEventListener("click", function() {
        document.getElementById("reflectedDate").textContent = ultimaFecha;
    });
</script>

<?php require 'footer.php'; ?>