<?php
require '../../conn/connection.php';

$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idppl <= 0) {
    die("ID de persona no válido");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => '', 'redirect' => ''];
    
    if (isset($_POST['action']) && $_POST['action'] === 'eliminar_marca') {
        $id_marca = $conexion->real_escape_string($_POST['id_marca']);
        $sql_eliminar = "UPDATE marcas_cuerpo SET estado = 'Inactivo' WHERE id = '$id_marca' AND idppl = '$idppl'";
        if ($conexion->query($sql_eliminar)) {
            $response = [
                'success' => true,
                'message' => 'Marca eliminada correctamente',
                'redirect' => "marcas_cuerpo.php?id=" . $idppl
            ];
        }
        echo json_encode($response);
        exit;
    }

    if (isset($_POST['x']) && isset($_POST['y']) && isset($_POST['description'])) {
        $x = $conexion->real_escape_string($_POST['x']);
        $y = $conexion->real_escape_string($_POST['y']);
        $description = $conexion->real_escape_string($_POST['description']);

        $sql_insertar = "INSERT INTO marcas_cuerpo (idppl, x, y, description) 
                         VALUES ('$idppl', '$x', '$y', '$description')";
        if ($conexion->query($sql_insertar)) {
            $new_id = $conexion->insert_id;
            $response = [
                'success' => true,
                'message' => 'Marca agregada correctamente',
                'redirect' => "marcas_cuerpo.php?id=" . $idppl,
                'marca' => [
                    'id' => $new_id,
                    'x' => $x,
                    'y' => $y,
                    'description' => $description
                ]
            ];
        }
        echo json_encode($response);
        exit;
    }
}

$sql_marcas = "SELECT id, x, y, description FROM marcas_cuerpo WHERE idppl = '$idppl' AND estado = 'Activo'";
$resultado_marcas = $conexion->query($sql_marcas);
$marcas = $resultado_marcas->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .body-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .body-mark {
        position: absolute;
        width: 24px;
        height: 24px;
        background-color: red;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transform: translate(-50%, -50%);
        font-size: 12px;
        max-width: 5%;
        max-height: 5%;
    }
    #bodyImage {
        width: 100%;
        height: auto;
        display: block;
    }
    .marks-list {
        max-height: 500px;
        overflow-y: auto;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Marcas Corporales - PPL #<?php echo $idppl; ?></h5>
                </div>
                <div class="card-body text-center">
                    <div class="body-container" id="bodyContainer">
                        <img src="../../img/tu-imagen.png" alt="Silueta del cuerpo" id="bodyImage" class="img-fluid">
                        <div id="marks-container">
                            <?php foreach($marcas as $index => $marca): ?>
                                <div class="body-mark" 
                                     data-mark-id="<?php echo $marca['id']; ?>"
                                     style="left: <?php echo $marca['x']; ?>%; top: <?php echo $marca['y']; ?>%;"
                                     title="<?php echo htmlspecialchars($marca['description']); ?>">
                                    <?php echo $index + 1; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Cicatrices Registradas</h5>
                </div>
                <div class="card-body marks-list" id="marks-list">
                    <?php foreach($marcas as $index => $marca): ?>
                        <div class=" justify-content-between align-items-center mb-2 pb-2 border-bottom" data-mark-id="<?php echo $marca['id']; ?>">

                        
                            <strong>Marca #<?php echo $index + 1; ?></strong>
                            
                       
                            <div>                               
                                <p class="mb-0"><?php echo htmlspecialchars($marca['description']); ?></p>
                            </div>                            
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
