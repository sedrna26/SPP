<?php
require 'navbar.php';

//-------------BORRADO------------------ 
if (isset($_GET['txtID'])) {
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    
    try {
        $sentencia = $db->prepare("DELETE FROM educacion WHERE id = :id");
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();
        
        $mensaje = "Registro eliminado con Éxito";
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar el registro: " . $e->getMessage();
    }

    header("Location: educacion_index.php?mensaje=" . urlencode($mensaje));
    exit();
}
?>

<!-- ------------------------------------------- -->
<section class="content mt-3">
    <div class="row m-auto">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Listado de Educaciones</h5>
                    <a class="btn btn-primary float-right mb-2" href="educacion_crea.php">Registro de Educación</a>
                </div>
                <div class="card-body table-responsive">
                    <table id="example" class="table table-striped table-sm" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>ID Persona</th>
                                <th>ID Familiar</th>
                                <th>Establecimientos</th>
                                <th>Grado</th>
                                <th>Año</th>
                                <th>Motivo de Abandono</th>
                                <th>Oferta Educativa</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = "SELECT * FROM educacion";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $educaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($educaciones as $educacion) {
                            ?>
                            <tr>    
                                <td><?php echo htmlspecialchars($educacion['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['id_ppl'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['id_familiar'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['establecimiento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['grado'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['año'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['motivo_abandono'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($educacion['oferta_educ'], ENT_QUOTES, 'UTF-8'); ?></td>
                                
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="educacion_edit.php?id=<?php echo htmlspecialchars($educacion['id'], ENT_QUOTES, 'UTF-8'); ?>" 
                                           class="btn btn-warning btn-sm" role="button">
                                           <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:eliminar1(<?php echo htmlspecialchars($educacion['id'], ENT_QUOTES, 'UTF-8'); ?>)" 
                                           class="btn btn-danger btn-sm" 
                                           title="Dar de baja" 
                                           role="button">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            } catch (PDOException $e) {
                                error_log("Error al obtener las educaciones: " . $e->getMessage());
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
<script>
    function eliminar1(educacion) {
        Swal.fire({
            icon: "warning",
            title: "¿Borrar?",        
            showCancelButton: true,
            confirmButtonText: "Si",   
            confirmButtonColor: "#007bff",
            cancelButtonColor: '#dc3545',     
        }).then((result) => {  
            if (result.isConfirmed) { 
                window.location = "educacion_index.php?txtID=" + educacion;
            }          
        });
    }
</script>
<script src="js/ocultarMensaje.js"></script>
<?php 
require 'footer.php'; 
?>
