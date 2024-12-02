<?php require 'navbar.php';
//-------------DAR DE BAJA------------------
if (isset($_GET['txtID'])) {
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";
    $fechaBaja = date('Y-m-d');
    $sentencia = $db->prepare("UPDATE persona SET etapa = 'Inactivo', fechadebaja = :fechadebaja WHERE id_usuario = :id");
    $sentencia->bindParam(':id', $txtID);
    $sentencia->bindParam(':fechadebaja', $fechaBaja);
    $sentencia->execute();
    echo '<script>
            var msj = "El usuario ha sido dado de baja exitosamente";
            window.location="profe_index.php?mensaje=" + encodeURIComponent(msj);
          </script>';
    exit();
}
?>
<section class="content mt-3">
    <div class="row m-auto">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Listado de PPL</h5>
                    <a class="btn btn-primary float-right mb-2" href="persona_crea.php">Nueva Persona</a>
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
                                        situacionlegal AS sl ON ppl.id = sl.id_ppl
                                    LEFT JOIN
                                        fechappl AS fppl ON ppl.id = fppl.idppl
                                    WHERE 
                                        per.estado = 'Activo'";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    $pples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($pples as $ppl) {
                                ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($ppl['nombres'] . ' ' . $ppl['apellidos'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($ppl['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <div>                                                        
                                                    <?php echo htmlspecialchars($ppl['nombre_pais'], ENT_QUOTES, 'UTF-8').",";?>
                                                    <?php echo htmlspecialchars($ppl['nombre_provincia'], ENT_QUOTES, 'UTF-8').",";?>
                                                    <?php echo htmlspecialchars($ppl['nombre_ciudad'], ENT_QUOTES, 'UTF-8').","; ?>
                                                </div>
                                                <div>                                                    
                                                    <?php echo htmlspecialchars($ppl['localidad_domicilio'], ENT_QUOTES, 'UTF-8').",";?>
                                                    <?php echo htmlspecialchars($ppl['direccion_domicilio'], ENT_QUOTES, 'UTF-8');?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($ppl['fecha_detencion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($ppl['inicio_condena'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($ppl['fin_condena'], ENT_QUOTES, 'UTF-8'); ?></td>
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
</section>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/ocultarMensaje.js"></script>
<?php require 'footer.php'; ?>