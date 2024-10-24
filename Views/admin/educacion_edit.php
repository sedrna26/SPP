<?php 
//require '../../conn/connection.php'; 
require 'navbar.php';

// Manejo de la actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $id_ppl = $_POST['id_ppl'];
    $id_familiar = $_POST['id_familiar'];
    $establecimiento = $_POST['establecimiento'];
    $grado = $_POST['grado'];
    $anio = $_POST['anio'];
    $motivo_abandono = $_POST['motivo_abandono'];
    $oferta_educ = $_POST['oferta_educ'];

    try {
        $updateQuery = "UPDATE educacion SET 
            id_ppl = :id_ppl, 
            id_familiar = :id_familiar, 
            establecimiento = :establecimiento, 
            grado = :grado, 
            año = :anio, 
            motivo_abandono = :motivo_abandono, 
            oferta_educ = :oferta_educ
            WHERE id = :id";

    
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':id_ppl', $id_ppl);
        $stmt->bindParam(':id_familiar', $id_familiar);
        $stmt->bindParam(':establecimiento', $establecimiento);
        $stmt->bindParam(':grado', $grado);
        $stmt->bindParam(':anio', $anio);
        $stmt->bindParam(':motivo_abandono', $motivo_abandono);
        $stmt->bindParam(':oferta_educ', $oferta_educ);

        if ($stmt->execute()) {
            echo '<script>
                    var msj = "Registro de educación actualizado exitosamente";
                    window.location="educacion_index.php?mensaje="+ encodeURIComponent(msj)
                  </script>';
            exit;
        } else {
            echo "Error: No se pudo actualizar el registro de educación.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Manejo de la carga inicial
$id = isset($_GET['id']) ? $_GET['id'] : null;
if ($id) {
    try {
        $sql = "SELECT * FROM educacion WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $educacion = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$educacion) {
            echo "Registro de educación no encontrado.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "ID de educación no proporcionado.";
    exit;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm rounded-4 border-0 mt-5">
                <h5 class="card-header bg-dark text-white rounded-top-4 text-center">Actualizar Educación</h5>
                <div class="card-body bg-light p-4">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($educacion['id'], ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_ppl">Seleccionar PPL:</label>
                                <select class="form-select" id="id_ppl" name="id_ppl" required>
                                    <option value="">Seleccione un PPL</option>
                                    <?php
                                    $query_ppl = "SELECT * FROM ppl";
                                    $stmt_ppl = $db->prepare($query_ppl);
                                    $stmt_ppl->execute();
                                    $ppls = $stmt_ppl->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($ppls as $ppl) {
                                        $selected = ($ppl['id'] == $educacion['id_ppl']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($ppl['apodo'], ENT_QUOTES, 'UTF-8') . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_familiar">Seleccionar Familiar:</label>
                                <select class="form-select" id="id_familiar" name="id_familiar" required>
                                    <option value="">Seleccione un Familiar</option>
                                    <?php
                                    $query_familiar = "SELECT * FROM familia";
                                    $stmt_familiar = $db->prepare($query_familiar);
                                    $stmt_familiar->execute();
                                    $familiares = $stmt_familiar->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($familiares as $familiar) {
                                        $selected = ($familiar['id'] == $educacion['id_familiar']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($familiar['id'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($familiar['ffaa'], ENT_QUOTES, 'UTF-8') . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="establecimiento">Establecimiento:</label>
                                <input type="text" class="form-control" id="establecimiento" name="establecimiento" placeholder="Ingrese establecimiento" value="<?php echo htmlspecialchars($educacion['establecimiento'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="grado">Grado:</label>
                                <input type="text" class="form-control" id="grado" name="grado" placeholder="Ingrese grado" value="<?php echo htmlspecialchars($educacion['grado'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="anio">Año:</label>
                                <input type="number" class="form-control" id="anio" name="anio" placeholder="Ingrese año" value="<?php echo htmlspecialchars($educacion['año'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="motivo_abandono">Motivo de Abandono:</label>
                                <input type="text" class="form-control" id="motivo_abandono" name="motivo_abandono" placeholder="Ingrese motivo de abandono" value="<?php echo htmlspecialchars($educacion['motivo_abandono'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="oferta_educ">Oferta Educativa:</label>
                                <input type="text" class="form-control" id="oferta_educ" name="oferta_educ" placeholder="Ingrese oferta educativa" value="<?php echo htmlspecialchars($educacion['oferta_educ'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../../js/validacion.js"></script>
<?php require 'footer.php'; ?>
