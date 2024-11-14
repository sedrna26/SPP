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
<!-- ------------------------------------------- -->
<section class="content mt-3">
    <div class="row m-auto">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Listado de PPL</h5>
                    <a class="btn btn-primary float-right mb-2" href="persona_crea.php">Nueva Persona</a>
                </div>
                <!-- -------------------- -->
                <div class="card-body table-responsive">
                    <table id="example" class="table table-striped table-sm" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre y Apellido</th>
                                <th>DNI</th>
                                <th>Domicilio</th>
                                <th>Carga</th>
                                <th>Prontuario PPL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = "
                                    SELECT `ppl`.*, `persona`.*
FROM `ppl` 
	LEFT JOIN `persona` ON `ppl`.`idpersona` = `persona`.`id`;
                                ";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $pples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($pples as $ppl) {
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ppl['nombres'] . ' ' . $ppl['apellidos'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ppl['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ppl['direccion'], ENT_QUOTES, 'UTF-8'); ?></td>

                                        <td>
                                            <a class="btn btn-info" href='ppl_informe.php?id=<?php echo $ppl['id']; ?>'>Informe(IEII)</a>
                                        <td>
                                            <a href="prontuario_index.php?id=<?php echo $ppl['id']; ?>" data-toggle="modal" data-backdrop="false" class="btn btn-info btn-sm" type="button" title="ver">
                                                <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                            </a>
                                        </td>

                                        <!-- <div class="btn-group">
                                        <a href="profe_edit.php?id_usuario=<?php echo htmlspecialchars($ppl['id_usuario'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        class="btn btn-warning btn-sm" role="button">
                                        <i class="fas fa-edit"></i></a>
                                        <a href="javascript:eliminar2(<?php echo $ppl['id_usuario']; ?>)" 
                                                 class="btn btn-danger btn-sm" 
                                                 title="Dar de baja" 
                                                 role="button">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                    </div> -->
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