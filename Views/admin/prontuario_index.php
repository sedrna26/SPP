<?php require 'navbar.php'; ?>

<?php
if (isset($_GET['dni']) && ctype_digit($_GET['dni'])) {
    $dni = $_GET['dni'];
    $stmt = $conexion->prepare("SELECT ppl.*, per.*
        FROM ppl AS ppl
        LEFT JOIN persona AS per 
        ON ppl.idpersona = per.id
        WHERE per.dni = ?
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

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

?>

<!-- ------------------------ -->
<style>
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
<!-- ------------------------------------ -->
<?php $dni = isset($_GET['dni']) ? $_GET['dni'] : '';?>
<section class="container mt-3">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="d-inline-block "><?php
                                        echo !empty($persona['nombres']) && !empty($persona['apellidos']) ?
                                            htmlspecialchars($persona['nombres'] . ' ' . $persona['apellidos'], ENT_QUOTES, 'UTF-8') :
                                            'No hay dato';
                                        ?></h5>
        </div>
        <div class="card-body  table-responsive">
            <div class="container mb-4">
                <div class="row mt-2">
                    <div class="col-md-7">
                        <h5 class="section-title mb-3 ">A) DATOS PERSONALES</h5>
                        <p>
                            <label class="h6 ">Apellidos y Nombres:</label>
                            <span class="form-control-static" id="nombres-apellidos">
                                <?php
                                echo !empty($persona['nombres']) && !empty($persona['apellidos']) ?
                                    htmlspecialchars($persona['nombres'] . ' ' . $persona['apellidos'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                            <label class="h6 ml-5">D.N.I.:</label>
                            <span>
                                <?php
                                echo !empty($persona['dni']) ?
                                    htmlspecialchars($persona['dni'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                        </p>
                        <p>
                            <label class="h6 ">Fecha de nacimiento:</label>
                            <span>
                                <?php
                                if (!empty($persona['fechaNacimiento'])) {
                                    echo date('d-m-Y', strtotime($persona['fechaNacimiento']));
                                } else {
                                    echo 'No hay dato';
                                }
                                ?>
                            </span>

                            <label class="h6 ml-5">Edad:</label>
                            <span>
                                <?php
                                echo !empty($persona['edad']) ?
                                    htmlspecialchars($persona['edad'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>

                            <!-- <label class="h6 ml-5">Apodo:</label>
                            <span>
                                <?php
                                echo !empty($ppl['apodo']) ?
                                    htmlspecialchars($ppl['apodo'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span> -->

                        </p>
                        <p>
                            <label class="h6 ">Lugar:</label>
                            <span>
                                <?php
                                echo !empty($persona['ciudad']) ?
                                    htmlspecialchars($persona['ciudad'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                            <label class="h6 ml-5">Nacionalidad:</label>
                            <span>
                                <?php
                                echo !empty($persona['pais']) ?
                                    htmlspecialchars($persona['pais'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                        </p>
                        <p>
                            <label class="h6 ">Domicilio:</label>
                            <span>
                                <?php
                                echo !empty($persona['direccion']) ?
                                    htmlspecialchars($persona['direccion'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                        </p>
                        <p>
                            <label class="h6 ">Departamento / Localidad:</label>
                            <span>
                                <?php
                                echo !empty($persona['localidad']) ?
                                    htmlspecialchars($persona['localidad'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                            <label class="h6 ml-5">Provincia:</label>
                            <span>
                                <?php
                                echo !empty($persona['provincia']) ?
                                    htmlspecialchars($persona['provincia'], ENT_QUOTES, 'UTF-8') :
                                    'No hay dato';
                                ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4 text-center ">
                        <div class="foto">
                            <span>
                                <?php if (!empty($ppl['foto'])): ?>
                                    <img src="imagenes_p<?php echo !empty($ppl['foto']) ? htmlspecialchars($ppl['foto'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>" alt="Foto de la persona" style="max-width: 200px; max-height: 200px;">
                                <?php else: ?>
                                    No se encontró foto.
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <!-- ------------------------- -->
            <table id="" class="table table-striped table-sm" >
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Fecha de Ingreso</th>
                        <th>Fecha de Egreso</th>
                        <th>Cargo/Delito</th>
                        <th>informe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = "SELECT ppl.*, per.*
                                FROM ppl AS ppl
                                LEFT JOIN 
                                persona AS per 
                                ON ppl.idpersona = per.id
                                WHERE per.dni = $dni;";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $pples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($pples as $ppl) {
                    ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>15/04/2020</td>
                                <td>15/04/2050</td>
                                <td>Cargo/Delito</td>
                                <td>
                                  <a class="btn btn-info" href='ppl_informe.php?id=<?php echo $ppl['id']; ?>'>Informe(IEII)</a>
                                <td>
                            </tr>
                    <?php
                        }
                    } catch (PDOException $e) {
                        error_log("Error al obtener el ppl: " . $e->getMessage());
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</section>