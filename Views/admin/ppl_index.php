<?php
require 'navbar.php';
function obtenerDatosCompletosPpl($db)
{
    $query = "SELECT 
                    ppl.*, 
                    per.*, 
                    pai.nombre AS nombre_pais, 
                    ciu.nombre AS nombre_ciudad, 
                    pro.nombre AS nombre_provincia, 
                    dom.localidad AS localidad_domicilio, 
                    dom.direccion AS direccion_domicilio
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
                WHERE 
                    per.estado = 'Activo'";

    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function obtenerFechasPpl($db, $idPpl)
{
    $query = "SELECT 
                    DATE_FORMAT(fppl.inicio_condena, '%d-%m-%Y') AS inicio_condena, 
                    COALESCE(DATE_FORMAT(fppl.fin_condena, '%d-%m-%Y'), '-') AS fin_condena, 
                    DATE_FORMAT(sl.fecha_detencion, '%d-%m-%Y') AS fecha_detencion
              FROM fechappl AS fppl
              LEFT JOIN situacionlegal AS sl ON sl.id_ppl = fppl.idppl
              WHERE fppl.idppl = :idPpl";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':idPpl', $idPpl, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<section class="content mt-3">
    <div class="row m-auto">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Listado de PPL</h5>
                    <?php
                    if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                    ?>
                        <a class="btn btn-primary float-right mb-2" href="persona_crea.php">Nueva Persona</a>
                    <?php
                    }
                    ?>
                </div>
                <div class="card-body table-responsive">
                    <table id="example" class="table table-bordered table-striped table-hover table-sm" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre y Apellido</th>
                                <th>DNI</th>
                                <th>Domicilio</th>
                                <th>Fecha detención</th>
                                <th>Inicio Condena</th>
                                <th>Fin Condena</th>
                                <th>Carga</th>
                                <th>Prontuario PPL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pples = obtenerDatosCompletosPpl($db);

                            foreach ($pples as $ppl) {
                                $fechas = obtenerFechasPpl($db, $ppl['id']);
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ppl['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($ppl['nombres']  ?? '' . ' ' . $ppl['apellidos'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($ppl['dni']  ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <div>
                                            <?php
                                            echo htmlspecialchars($ppl['nombre_pais']  ?? '-', ENT_QUOTES, 'UTF-8') . ",";
                                            echo htmlspecialchars($ppl['nombre_provincia']  ?? '-', ENT_QUOTES, 'UTF-8') . ",";
                                            echo htmlspecialchars($ppl['nombre_ciudad']  ?? '-', ENT_QUOTES, 'UTF-8') . ",";
                                            ?>
                                        </div>
                                        <div>
                                            <?php
                                            echo htmlspecialchars($ppl['localidad_domicilio']  ?? '-', ENT_QUOTES, 'UTF-8') . ",";
                                            echo htmlspecialchars($ppl['direccion_domicilio']  ?? '-', ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($fechas['fecha_detencion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($fechas['inicio_condena'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($fechas['fin_condena'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <a class="btn btn-info" href='ppl_informe.php?id=<?php echo $ppl['id']; ?>'>Informe(IEII)</a>
                                    </td>
                                    <td>
                                        <a href="prontuario_index.php?dni=<?php echo $ppl['dni']; ?>" data-toggle="modal" data-backdrop="false" class="btn btn-info btn-sm" type="button" title="ver">
                                            <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>