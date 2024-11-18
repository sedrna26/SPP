<?php
require '../../conn/connection.php';

$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idppl <= 0) {
    die("ID de persona no válido");
}

// Modificar la tabla para incluir idppl, categoria y estado
$sql = "CREATE TABLE IF NOT EXISTS marcas_cuerpo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idppl INT NOT NULL,
    x FLOAT NOT NULL,
    y FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    categoria VARCHAR(50) DEFAULT 'cuerpo',
    estado VARCHAR(20) DEFAULT 'Activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idppl) REFERENCES ppl(id)
)";

if (!$conexion->query($sql)) {
    die("Error creando tabla: " . $conexion->error);
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $conexion->real_escape_string($_POST['id']);
        // Modificar para hacer borrado lógico en lugar de físico
        $sql = "UPDATE marcas_cuerpo SET estado = 'Inactivo' WHERE id = '$id' AND idppl = '$idppl'";
        if ($conexion->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
    } else {
        $x = $conexion->real_escape_string($_POST['x']);
        $y = $conexion->real_escape_string($_POST['y']);
        $description = $conexion->real_escape_string($_POST['description']);
        $categoria = 'cuerpo';

        $sql = "INSERT INTO marcas_cuerpo (idppl, x, y, description, categoria, estado) 
                VALUES ('$idppl', '$x', '$y', '$description', '$categoria', 'Activo')";

        if ($conexion->query($sql) === TRUE) {
            $id = $conexion->insert_id;
            echo json_encode([
                'success' => true,
                'mark' => [
                    'id' => $id,
                    'x' => $x,
                    'y' => $y,
                    'description' => $description,
                    'categoria' => $categoria,
                    'estado' => 'Activo'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_marks') {
    // Modificar para solo obtener marcas activas
    $sql = "SELECT id, x, y, description, categoria, estado FROM marcas_cuerpo WHERE idppl = '$idppl' AND estado = 'Activo' ORDER BY id";
    $result = $conexion->query($sql);

    $marks = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $marks[] = $row;
        }
    }

    echo json_encode($marks);
    exit;
}
?>

<div class="content mt-2">
    <div class="row px-4">
        <div class="col-md-6">
            <div class="card rounded-2 border-0 ">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Formulario de Marcas Corporales - PPL #<?php echo htmlspecialchars($idppl); ?></h5>
                </div>
                <div class="card-body table-responsive px-0 mx-0" style="text-align: center;">
                    <div class="body-image position-relative">
                        <img src="../../img/tu-imagen.png" alt="Silueta del cuerpo humano" id="bodyImage" class="border border-gray-300"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Cicatrices registradas:</h5>
                </div>
                <div class="card-body table-responsive" style="height: 630px;">
                    <div class="body-image">
                        <ul id="marksList" class="list-group list-group-flush" style="list-style-type: none;"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dialogOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="display: none; z-index: 1000;"></div>
<div id="dialog" class="position-fixed top-50 start-50 translate-middle bg-white p-4 rounded shadow" style="display: none; z-index: 1001; min-width: 300px;">
    <h2 id="dialogTitle" class="text-xl font-bold mb-2"></h2>
    <p class="mb-2">Por favor, introduce una descripción de la cicatriz (máximo 255 caracteres).</p>
    <textarea
        id="description"
        maxlength="255"
        placeholder="Describe la cicatriz aquí..."
        class="w-100 p-2 border border-gray-300 rounded mb-4"
        rows="4"
    ></textarea>
    <div class="text-end">
        <button id="cancelButton" class="btn btn-secondary me-2">Cancelar</button>
        <button id="saveButton" class="btn btn-primary">Guardar</button>
    </div>
</div>

<style>
.mark {
    position: absolute;
    width: 24px;
    height: 24px;
    background-color: red;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transform: translate(-50%, -50%);
}

.body-image {
    position: relative;
    display: inline-block;
}

.list-group-item {
    border-left: 4px solid #007bff;
    margin-bottom: 8px;
}

.btn-danger {
    margin-left: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bodyImage = document.getElementById('bodyImage');
    const marksList = document.getElementById('marksList');
    const dialog = document.getElementById('dialog');
    const dialogOverlay = document.getElementById('dialogOverlay');
    const dialogTitle = document.getElementById('dialogTitle');
    const description = document.getElementById('description');
    const saveButton = document.getElementById('saveButton');
    const cancelButton = document.getElementById('cancelButton');
    let marks = [];
    let currentMark = null;

    bodyImage.addEventListener('click', function(e) {
        const rect = e.target.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        currentMark = { x, y, number: marks.length + 1 };
        dialogTitle.textContent = `Añadir descripción de la cicatriz #${currentMark.number}`;
        showDialog();
    });

    function showDialog() {
        dialog.style.display = 'block';
        dialogOverlay.style.display = 'block';
        description.value = '';
        description.focus();
    }

    function hideDialog() {
        dialog.style.display = 'none';
        dialogOverlay.style.display = 'none';
        description.value = '';
        currentMark = null;
    }

    saveButton.addEventListener('click', function() {
        if (currentMark && description.value.trim() !== '') {
            saveMark(currentMark.x, currentMark.y, description.value);
            hideDialog();
        }
    });

    cancelButton.addEventListener('click', hideDialog);
    dialogOverlay.addEventListener('click', hideDialog);

    // Permitir guardar con Enter y cancelar con Escape
    description.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            saveButton.click();
        } else if (e.key === 'Escape') {
            hideDialog();
        }
    });

    function saveMark(x, y, description) {
        fetch('marcas_cuerpo.php?id=<?php echo $idppl; ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `x=${x}&y=${y}&description=${encodeURIComponent(description)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMarks();
            } else {
                alert('Error al guardar la marca');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la marca');
        });
    }

    function deleteMark(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta marca?')) {
            fetch('marcas_cuerpo.php?id=<?php echo $idppl; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadMarks();
                } else {
                    alert('Error al eliminar la marca');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la marca');
            });
        }
    }

    function renderMarks() {
        bodyImage.parentElement.querySelectorAll('.mark').forEach(el => el.remove());
        marksList.innerHTML = '';
        
        marks.forEach((mark, index) => {
            const markEl = document.createElement('div');
            markEl.className = 'mark';
            markEl.style.left = `${mark.x}px`;
            markEl.style.top = `${mark.y}px`;
            markEl.textContent = index + 1;
            bodyImage.parentElement.appendChild(markEl);

            const listItem = document.createElement('li');
            listItem.innerHTML = `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="mb-0">Marca #${index + 1}</h5>
                        <button class="btn btn-danger btn-sm" onclick="deleteMark(${mark.id})">
                            <i class="fas fa-trash"></i> 
                        </button>
                    </div>
                    <p class="text-break mb-0">${mark.description}</p>
                </div>
            `;
            marksList.appendChild(listItem);
        });
    }

    function loadMarks() {
        fetch('marcas_cuerpo.php?action=get_marks&id=<?php echo $idppl; ?>')
            .then(response => response.json())
            .then(data => {
                marks = data;
                renderMarks();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar las marcas');
            });
    }

    // Load existing marks on start
    loadMarks();

    // Make deleteMark function available globally
    window.deleteMark = deleteMark;
});
</script>