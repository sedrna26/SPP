<?php 
// -----------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {   
    $id_ppl = $_POST['id_ppl'];
    $id_familiar = $_POST['id_familiar'];
    $establecimiento = $_POST['establecimiento'];
    $grado = $_POST['grado'];
    $anio = $_POST['anio'];
    $motivo_abandono = $_POST['motivo_abandono'];
    $oferta_educ = $_POST['oferta_educ'];
    
    try {
        $sql_insert = "INSERT INTO educacion (id_ppl, id_familiar, establecimiento, grado, año, motivo_abandono, oferta_educ) 
                       VALUES (:id_ppl, :id_familiar, :establecimiento, :grado, :anio, :motivo_abandono, :oferta_educ)";
        $stmt = $db->prepare($sql_insert);
        $stmt->bindParam(':id_ppl', $id_ppl);
        $stmt->bindParam(':id_familiar', $id_familiar);
        $stmt->bindParam(':establecimiento', $establecimiento);
        $stmt->bindParam(':grado', $grado);
        $stmt->bindParam(':anio', $anio);
        $stmt->bindParam(':motivo_abandono', $motivo_abandono);
        $stmt->bindParam(':oferta_educ', $oferta_educ);

        if ($stmt->execute()) {
            echo '<script>
                var msj = "Registro de educación creado exitosamente";
                window.location="educacion_index.php?mensaje=" + encodeURIComponent(msj);
              </script>';
            exit();
        } else {
            $error = "Error al ingresar el registro de educación.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- -----------------------------------   -->  
<section class="content ">    
    <div class=" ">
        <div class="card ">
            <div class="card-body ">     
                <form action="" method="post" class="">
                    <div class="form-group ">
                        <label for="id_ppl">Seleccionar PPL:</label>
                        <select class="form-select" id="id_ppl" name="id_ppl" required>
                            <option value="">Seleccione un PPL</option>
                            <?php
                            $query_ppl = "SELECT * FROM ppl";
                            $stmt_ppl = $db->prepare($query_ppl);
                            $stmt_ppl->execute();
                            $ppls = $stmt_ppl->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($ppls as $ppl) {
                                echo "<option value='" . htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($ppl['apodo'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_familiar">Seleccionar Familiar:</label>
                        <select class="form-select" id="id_familiar" name="id_familiar" required>
                            <option value="">Seleccione un Familiar</option>
                            <?php
                            $query_familiar = "SELECT * FROM familia";
                            $stmt_familiar = $db->prepare($query_familiar);
                            $stmt_familiar->execute();
                            $familiares = $stmt_familiar->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($familiares as $familiar) {
                                echo "<option value='" . htmlspecialchars($familiar['id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($familiar['ffaa'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                    </div>                    
                    <div class="form-group">
                        <label for="establecimiento">Establecimiento:</label>
                        <input type="text" class="form-control" id="establecimiento" name="establecimiento" placeholder="Ingrese establecimiento" required>
                    </div>                    
                    <div class="form-group">
                        <label for="grado">Grado:</label>
                        <input type="text" class="form-control" id="grado" name="grado" placeholder="Ingrese grado" required>
                    </div>                    
                    <div class="form-group">
                        <label for="anio">Año:</label>
                        <input type="number" class="form-control" id="anio" name="anio" placeholder="Ingrese año" required>
                    </div>                    
                    <div class="form-group">
                        <label for="motivo_abandono">Motivo de Abandono:</label>
                        <input type="text" class="form-control" id="motivo_abandono" name="motivo_abandono" placeholder="Ingrese motivo de abandono">
                    </div>                    
                    <div class="form-group">
                        <label for="oferta_educ">Oferta Educativa:</label>
                        <input type="text" class="form-control" id="oferta_educ" name="oferta_educ" placeholder="Ingrese oferta educativa">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>   
</section>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../../js/validacion.js"></script>
