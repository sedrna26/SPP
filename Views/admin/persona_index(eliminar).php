<?php
require 'navbar.php';
require '../../conn/connection.php';

//-------------DAR DE BAJA------------------
if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];

    try {
        $db->beginTransaction();

        // Obtener el idpersona correspondiente al DNI
        $sentenciaPersona = $db->prepare("SELECT id FROM persona WHERE dni = :dni");
        $sentenciaPersona->bindParam(':dni', $dni, PDO::PARAM_INT);
        $sentenciaPersona->execute();
        $persona = $sentenciaPersona->fetch(PDO::FETCH_ASSOC);

        if ($persona) {
            $idpersona = $persona['id'];

            // Obtener el id de la tabla `ppl`
            $sentenciaPpl = $db->prepare("SELECT id FROM ppl WHERE idpersona = :idpersona");
            $sentenciaPpl->bindParam(':idpersona', $idpersona, PDO::PARAM_INT);
            $sentenciaPpl->execute();
            $ppl = $sentenciaPpl->fetch(PDO::FETCH_ASSOC);

            if ($ppl) {
                $idPpl = $ppl['id'];

                // Eliminar en la tabla 'situacionlegal'
                $sentenciaSituacionLegal = $db->prepare("DELETE FROM situacionlegal WHERE ppl = :ppl");
                $sentenciaSituacionLegal->bindParam(':ppl', $idPpl, PDO::PARAM_INT);
                $sentenciaSituacionLegal->execute();
                echo "Situacion legal eliminada<br>";

                // Eliminar en la tabla 'ppl'
                $sentenciaPpl = $db->prepare("DELETE FROM ppl WHERE idpersona = :idpersona");
                $sentenciaPpl->bindParam(':idpersona', $idpersona, PDO::PARAM_INT);
                $sentenciaPpl->execute();
                echo "PPL eliminado<br>";
            }

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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        // Función para abrir el modal y mostrar la imagen y el ID
        function mostrarImagenGrande(imgSrc, id) {
            // Mostrar el modal
            var modal = document.getElementById('modalImagen');
            modal.style.display = "block";

            // Colocar la imagen seleccionada en el modal
            var modalImg = document.getElementById('imagenGrande');
            modalImg.src = imgSrc;

            // Colocar el ID en el modal
            var modalId = document.getElementById('modalId');
            modalId.innerText = "ID: " + id;
        }

        // Función para cerrar el modal
        function cerrarModal() {
            var modal = document.getElementById('modalImagen');
            modal.style.display = "none";
        }
    </script>
</head>

<body>

    <section class="content mt-3">
        <div class="row m-auto">
            <div class="col-sm">
                <div class="card rounded-2 border-0">
                    <div class="card-header bg-dark text-white pb-0">
                        <center>
                            <h1 class="d-inline-block">Listado PPL</h1>
                        </center>
                        <ul>
                            <a class="btn btn-primary float-right mb-2" href="persona_crea.php">Agregar PPL</a>
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
                                    <th>APODO</th>
                                    <th>PROFESION</th>
                                    <th>TRABAJA</th>
                                    <th>HUELLA</th>
                                    <th>FECHA ENTREVISTA</th>
                                    <th>ACCIONES</th>
                                    <th>MÁS INFORMACIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $query = "SELECT p.id, p.dni, p.nombres, p.apellidos, p.foto, ppl.apodo, ppl.huella, ppl.trabaja, ppl.profesion, ppl.fechaentrevista
                                          FROM persona AS p
                                          LEFT JOIN ppl ON p.id = ppl.idpersona
                                          ORDER BY p.id ASC";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();

                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $imagePath = "../imagenes/" . htmlspecialchars($row['foto']);
                                ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <?php if ($row['foto']) { ?>
                                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['nombres']); ?>" style="width: 110px; height: 125px; cursor: pointer;" onclick="mostrarImagenGrande('<?php echo $imagePath; ?>', '<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>')">
                                                <?php } else { ?>
                                                    No imagen
                                                <?php } ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['nombres'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['apellidos'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['apodo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['profesion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <?php echo $row['trabaja'] == 1 ? 'Sí' : 'No'; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['huella']) { ?>
                                                    <img src="huella_image.php?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" alt="Huella dactilar" style="width: 110px; height: 125px">
                                                <?php } else { ?>
                                                    No huella
                                                <?php } ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['fechaentrevista'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="persona_edit.php?dni=<?php echo htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-sm" title="Editar" role="button">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <a href="javascript:eliminar2('<?php echo htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8'); ?>')" class="btn btn-danger btn-sm" title="Dar de baja" role="button">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="ubicacion_index.php?dni=<?php echo htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-sm" role="button" title="Ver ubicación de la persona">
                                                        <i class="fas fa-map-marker-alt"></i> Ubicación
                                                    </a>
                                                    <a href="situacionlegal_index.php?dni=<?php echo htmlspecialchars($row['dni'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-info btn-sm" role="button" title="Ver Situación Legal de la persona">
                                                        <i class="fas fa-balance-scale"></i> Situación Legal
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } catch (PDOException $e) {
                                    error_log("Error al obtener las personas y PPL: " . $e->getMessage());
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal para mostrar la imagen en grande -->
    <!-- Modal para mostrar la imagen en grande con el ID -->
    <div id="modalImagen" style="display:none; position: fixed; z-index: 9999; padding-top: 100px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9);">
        <span style="position: absolute; top: 15px; right: 35px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer;" onclick="cerrarModal()">&times;</span>
        <img id="imagenGrande" style="margin: auto; display: block; max-width: 80%; max-height: 80%;">
        <div id="modalId" style="color: white; text-align: center; margin-top: 10px; font-size: 20px;"></div>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/ocultarMensaje.js"></script>
<?php require 'footer.php'; ?>