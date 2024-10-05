<?php
require '../../conn/connection.php'; 
require 'navbar.php';
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
                    <h5 class="d-inline-block">Nombre del PPL</h5>
                    
                    <a class="btn btn-primary float-right mb-2" href="ppl_crea.php">Nuevo Informe PPL</a>
                    <p class="float-right mb-2">boton para cargar nuevo informe segun id</p>
                </div>
                <!-- ------------------------ -->
                 <p>Informacion del Perfil del PPL segun los datos personales</p>
                 <p>Foto de perfil y datos</p>
                 <p>A continuacion se lista las condenas, con fecha de ingres, salida, dni, nombre,etc.</p>
                <!-- ------------------------ -->
                <div class="card-body table-responsive">
                    <table id="example" class="table table-striped table-sm" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre y Apellido</th>
                                <th>DNI</th>
                                <th>Domicilio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = "SELECT * FROM persona WHERE id_rol = 1  ";
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
                                
                                <td class="text-center">
                                <a class="btn btn-primary float-right mb-2" href="ppl_informe.php">Ver informe</a>

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
