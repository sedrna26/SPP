<?php
require 'navbar.php';
require '../../conn/connection.php';

//-------------DAR DE BAJA------------------
if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];

    try {
        $db->beginTransaction();

        // Obtener el dni persona correspondiente al DNI
        $sentenciaPersona = $db->prepare("SELECT id FROM persona WHERE dni = :dni");
        $sentenciaPersona->bindParam(':dni', $dni, PDO::PARAM_INT);
        $sentenciaPersona->execute();
        $persona = $sentenciaPersona->fetch(PDO::FETCH_ASSOC);

        if ($persona) {
            $idpersona = $persona['id'];

            // Obtener el id de la tabla `ppl`
            $sentenciaPpl = $db->prepare("SELECT id FROM ubicacion WHERE id = :id");
            $sentenciaPpl->bindParam(':id', $idpersona, PDO::PARAM_INT);
            $sentenciaPpl->execute();
            $ppl = $sentenciaPpl->fetch(PDO::FETCH_ASSOC);

            // Luego eliminar en la tabla 'persona'
            $sentenciaEliminarPersona = $db->prepare("DELETE FROM persona WHERE dni = :dni");
            $sentenciaEliminarPersona->bindParam(':dni', $dni, PDO::PARAM_STR);
            $sentenciaEliminarPersona->execute();
            echo "Persona eliminada<br>";

            // Confirmar la transacción
            $db->commit();

            echo '<script>
                    var msj = "El usuario ha sido eliminado exitosamente";
                    window.location="persona_index.php?mensaje=" + encodeURIComponent(msj);
                  </script>';
        } else {
            echo "No se encontró la persona con el DNI proporcionado.<br>";
        }
    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Error al eliminar el usuario: " . $e->getMessage());
        echo "Error: " . $e->getMessage() . "<br>";
        exit();
    }
}
?>
<!-- ------------------------------------------- -->

<section class="content mt-3">
    <div class="row m-auto">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <center><h5 class="d-inline-block">Listado de Ubicación</h5></center>
                    <ul>
                        <a class="btn btn-primary float-right mb-2" href="ubicacion_crea.php">Agregar Ubicación</a>
                        <li><a class="btn btn-primary float-left mb-2" href="situacionlegal_index.php">Listado de Situacion Legal</a></li>
                        <br>
                        <li><a class="btn btn-primary float-left mb-2" href="persona_index.php">Listado de Personas</a></li>
                    </ul>
                    
                </div>
                <div class="card-body table-responsive">
                    <table id="example" class="table table-striped table-sm" style="width:100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>FOTO</th>
                                <th>DNI</th>
                                <th>NOMBRE</th>
                                <th>APELLIDO</th>
                                <th>PAIS</th>
                                <th>PROVINCIA</th>
                                <th>DEPARTAMENTO</th>
                                <th>ACCIONES</th>
                                <th>MÁS INFORMACION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = "SELECT u.id, u.pais, u.provincia, u.departamento, u.direccion, p.foto, p.id, p.dni, p.nombres, p.apellidos, p.direccion
                                FROM persona AS p 
                                JOIN ubicacion AS u ON p.direccion = u.id";
                                $stmt = $db->prepare($query);
                                $stmt->execute();

                                while ($ubicacion = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $imagePath = "../imagenes/" . htmlspecialchars($ubicacion['foto']); // Corregido aquí
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ubicacion['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php if ($ubicacion['foto']) { ?> <!-- Corregido aquí -->
                                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($ubicacion['pais']); ?>" style="width: 110px; height: 125px">
                                            <?php } else { ?>
                                                No imagen
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($ubicacion['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ubicacion['nombres'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ubicacion['apellidos'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ubicacion['pais'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ubicacion['provincia'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($ubicacion['departamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="ubicacion_edit.php?id=<?php echo htmlspecialchars($ubicacion['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-sm" role="button">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="situacionlegal_index.php?id=<?php echo htmlspecialchars($ubicacion['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    class="btn btn-warning btn-sm" role="button" title="Ver Situacion Legas de la persona">
                                                    <i class="fas fa-balance-scale"></i> Sitacion Legal
                                                </a>

                                                <a href="persona_index.php?id=<?php echo htmlspecialchars($ubicacion['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    class="btn btn-info btn-sm" role="button" title="Ver Persona">
                                                    <i class="fas fa-user"></i> Persona
                                                </a>
                                            </div>
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