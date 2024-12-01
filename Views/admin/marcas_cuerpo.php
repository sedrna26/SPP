<?php
require '../../conn/connection.php';

$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idppl <= 0) {
    die("ID de persona no válido");
}

// Crear tabla si no existe
$sql = "CREATE TABLE IF NOT EXISTS marcas_cuerpo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idppl INT NOT NULL,
    x FLOAT NOT NULL,
    y FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idppl) REFERENCES ppl(id)
)";
$conexion->query($sql);

// Procesar eliminación de marca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar_marca') {
    $id_marca = $conexion->real_escape_string($_POST['id_marca']);
    $sql_eliminar = "UPDATE marcas_cuerpo SET estado = 'Inactivo' WHERE id = '$id_marca' AND idppl = '$idppl'";
    $conexion->query($sql_eliminar);
    header("Location: {$_SERVER['PHP_SELF']}?id={$idppl}");
    exit();
}

// Procesar agregado de marca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x']) && isset($_POST['y']) && isset($_POST['description'])) {
    $x = $conexion->real_escape_string($_POST['x']);
    $y = $conexion->real_escape_string($_POST['y']);
    $description = $conexion->real_escape_string($_POST['description']);

    $sql_insertar = "INSERT INTO marcas_cuerpo (idppl, x, y, description) 
                     VALUES ('$idppl', '$x', '$y', '$description')";
    $conexion->query($sql_insertar);
    header("Location: {$_SERVER['PHP_SELF']}?id={$idppl}");
    exit();
}

// Obtener marcas activas
$sql_marcas = "SELECT id, x, y, description FROM marcas_cuerpo WHERE idppl = '$idppl' AND estado = 'Activo'";
$resultado_marcas = $conexion->query($sql_marcas);
$marcas = $resultado_marcas->fetch_all(MYSQLI_ASSOC);
?>

    <style>
        .body-container {
            position: relative;
            display: inline-block;
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
        }
        #bodyImage {
            max-width: 100%;
            height: auto;
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
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Marcas Corporales - PPL #<?php echo $idppl; ?></h5>
                </div>
                <div class="card-body text-center">
                    <div class="body-container" id="bodyContainer">
                        <img src="../../img/tu-imagen.png" alt="Silueta del cuerpo" id="bodyImage" class="img-fluid">
                        
                        <?php foreach($marcas as $index => $marca): ?>
                            <div class="body-mark" 
                                 style="left: <?php echo $marca['x']; ?>px; top: <?php echo $marca['y']; ?>px;"
                                 title="<?php echo htmlspecialchars($marca['description']); ?>">
                                <?php echo $index + 1; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Cicatrices Registradas</h5>
                </div>
                <div class="card-body marks-list">
                    <?php foreach($marcas as $index => $marca): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <strong>Marca #<?php echo $index + 1; ?></strong>
                                <p class="mb-0"><?php echo htmlspecialchars($marca['description']); ?></p>
                            </div>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="eliminar_marca">
                                <input type="hidden" name="id_marca" value="<?php echo $marca['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Añadir Nueva Marca</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="markForm">
                        <input type="hidden" name="x" id="markX">
                        <input type="hidden" name="y" id="markY">
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción de la Marca</label>
                            <textarea name="description" id="description" class="form-control" rows="3" required maxlength="255" placeholder="Describe la cicatriz aquí..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="saveMarkButton" disabled>Guardar Marca</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bodyImage = document.getElementById('bodyImage');
    const markX = document.getElementById('markX');
    const markY = document.getElementById('markY');
    const description = document.getElementById('description');
    const saveMarkButton = document.getElementById('saveMarkButton');

    bodyImage.addEventListener('click', function(event) {
        const rect = event.target.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        markX.value = x;
        markY.value = y;
        
        description.focus();
        saveMarkButton.disabled = false;
    });

    description.addEventListener('input', function() {
        saveMarkButton.disabled = this.value.trim() === '';
    });
});
</script>