<?php
require 'navbar.php';
require '../../conn/connection.php';


//-------------DAR DE BAJA------------------
if (isset($_GET['situacionlegal_id'])) {
    $id = $_GET['situacionlegal_id'] ?? '';

    // Preparar la consulta para eliminar el registro del usuario
    $sentencia = $db->prepare("DELETE FROM situacionlegal WHERE id = :situacionlegal_id");

    // Vincular el parámetro con el valor correspondiente
    $sentencia->bindParam(':situacionlegal_id', $id, PDO::PARAM_INT);

    // Ejecutar la consulta
    $sentencia->execute();

    // Redirigir con mensaje de éxito
    echo '<script>
            var msj = "La situacion legal ha sido eliminado exitosamente";
            window.location="persona_index.php?mensaje=" + encodeURIComponent(msj);
          </script>';
    exit();
}
?>
<!-- ------------------------------------------- -->

<section class="content mt-3">
    <div class="row m-auto">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <center>
                        <h1 style="font-size: larger; width:100%; height:100%;" class="d-inline-block">Listado de Situacion Legal</h1>
                    </center>
                    <ul>
                        <a class="btn btn-primary float-right mb-2" href="situacionlegal_crea.php">Agregar Sitacion Legal</a>
                        <li><a class="btn btn-primary float-left mb-2" href="persona_index.php">Listado de Personas</a></li>
                        <br>
                        <li><a class="btn btn-primary float-left mb-2" href="ubicacion_index.php">Listado de Ubicacion</a></li>
                    </ul>

                </div>
                <div class="card-body table-responsive">
                    <table id="example" class="table table-striped table-sm" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>FOTO</th>
                                <th>PRONTUARIO</th>
                                <th>PPL</th>
                                <th>MOTIVO</th>
                                <th>SITUACION LEGAL</th>
                                <th>REINCIDENCIA</th>
                                <th>SALIDA TRANSITORIA</th>
                                <th>LIBERTAD ASISTIDA</th>
                                <th>LIBERTAD CONDICIONAL</th>
                                <th>DELITO</th>
                                <th>FECHA VENCIMIENTO</th>
                                <th>JUEZ</th>
                                <th>SEÑAS PARTICULARES</th>
                                <th>ACCIONES</th>
                                <th>MÁS INFORMACION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = $query = "SELECT 
                                    sl.id AS situacionlegal_id, 
                                    persona.foto, 
                                    ppl.apodo, 
                                    sl.motivo_t, 
                                    sl.situacionlegal, 
                                    sl.prontuario, 
                                    sl.reincidencia, 
                                    sl.salida_transitoria, 
                                    sl.libertad_asistida, 
                                    sl.libertad_condicional, 
                                    tipodelito.titulo AS delito_titulo, 
                                    fechappl.fechavenc AS fecha_vencimiento, 
                                    juzgado.nombre AS juzgado_nombre, 
                                    caracteristicas.tipo
                                FROM situacionlegal AS sl 
                                LEFT JOIN ppl ON sl.ppl = ppl.id
                                LEFT JOIN persona ON ppl.idpersona = persona.id
                                LEFT JOIN tipodelito ON sl.delito = tipodelito.id
                                LEFT JOIN fechappl ON sl.fecha = fechappl.id
                                LEFT JOIN juzgado ON sl.juzgado = juzgado.id
                                LEFT JOIN caracteristicas ON sl.señas_partic = caracteristicas.id";
                                $stmt = $db->prepare($query);
                                $stmt->execute();

                                while ($situacionlegal = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $imagePath = "../imagenes/" . htmlspecialchars($situacionlegal['foto']); // Corregido aquí
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($situacionlegal['situacionlegal_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php if ($situacionlegal['foto']) { ?> <!-- Corregido aquí -->
                                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($situacionlegal['motivo_t']); ?>" style="width: 110px; height: 125px">
                                            <?php } else { ?>
                                                No imagen
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($situacionlegal['prontuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['apodo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['motivo_t'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['situacionlegal'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['reincidencia'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['salida_transitoria'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['libertad_asistida'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['libertad_condicional'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['delito_titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['fecha_vencimiento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['juzgado_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($situacionlegal['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="situacionlegal_edit.php?id=<?php echo htmlspecialchars($situacionlegal['situacionlegal_id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-sm" role="button">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </div>

                                            <td class="text-center">
                                            <div class="btn-group">
                                                <a href="persona_index.php?situacionlegal_id=<?php echo htmlspecialchars($situacionlegal['situacionlegal_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    class="btn btn-warning btn-sm" role="button" title="Ver Persona">
                                                    <i class="fas fa-user"></i> Persona
                                                </a>

                                                <a href="ubicacion_index.php?situacionlegal_id=<?php echo htmlspecialchars($situacionlegal['situacionlegal_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    class="btn btn-info btn-sm" role="button" title="Ver Ubicacion de la persona">
                                                    <i class="fas fa-map-marker-alt"></i> Ubicacion
                                                </a>
                                            </div>
                                        </td>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } catch (PDOException $e) {
                                error_log("Error al obtener las personas: " . $e->getMessage());
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