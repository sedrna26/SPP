<?php
// require '../../conn/connection.php';


$sql = "CREATE TABLE IF NOT EXISTS body_marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    x FLOAT NOT NULL,
    y FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $conexion->real_escape_string($_POST['id']);
        $sql = "DELETE FROM body_marks WHERE id = '$id'";
        if ($conexion->query($sql) === TRUE) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
    } else {
        $x = $conexion->real_escape_string($_POST['x']);
        $y = $conexion->real_escape_string($_POST['y']);
        $description = $conexion->real_escape_string($_POST['description']);

        $sql = "INSERT INTO body_marks (x, y, description) VALUES ('$x', '$y', '$description')";

        if ($conexion->query($sql) === TRUE) {
            $id = $conexion->insert_id;
            echo json_encode([
                'success' => true,
                'mark' => [
                    'id' => $id,
                    'x' => $x,
                    'y' => $y,
                    'description' => $description
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => $conexion->error]);
        }
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_marks') {
    $sql = "SELECT id, x, y, description FROM body_marks ORDER BY id";
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
<div>
<?php echo "ID traido desde ppl_informe.php=".$idppl."(eliminar despues)";?>
<!-- ----------------------------------------------------- -->
<div class="content mt-2">
    <div class="row px-4">
        
        <div class="col-md-6">
            <div class="card rounded-2 border-0 ">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Formulario de Marcas Corporales</h5>
                </div>
                <div class="card-body table-responsive px-0 mx-0"style="text-align: center;">
                    <div class="body-image " >
                        <img src="../../img/tu-imagen.png" alt="Silueta del cuerpo humano" id="bodyImage" class="border border-gray-300 "/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block">Cicatrices registradas:</h5>
                </div>
                <div class="card-body table-responsive"style="height: 630px;">
                    <div class="body-image">
                    <ul id="marksList" class="list-group list-group-flush" style="list-style-type: none;"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ----------------------------------------------------- -->
    <div id="dialogOverlay"></div>
    <div id="dialog">
        <h2 id="dialogTitle" class="text-xl font-bold mb-2"></h2>
        <p class="mb-2">Por favor, introduce una descripción de la cicatriz (máximo 255 caracteres).</p>
        <textarea
            id="description"
            maxlength="255"
            placeholder="Describe la cicatriz aquí..."
            class="w-100 p-2 border border-gray-300 rounded mb-4"
            rows="4"
        ></textarea>
        <button id="saveButton" class="px-4 py-2 btn btn-primary rounded">Guardar</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bodyImage = document.getElementById('bodyImage');
        const marksList = document.getElementById('marksList');
        const dialog = document.getElementById('dialog');
        const dialogOverlay = document.getElementById('dialogOverlay');
        const dialogTitle = document.getElementById('dialogTitle');
        const description = document.getElementById('description');
        const saveButton = document.getElementById('saveButton');
        let marks = [];
        let currentMark = null;

        bodyImage.addEventListener('click', function(e) {
            const rect = e.target.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            currentMark = { x, y, number: marks.length + 1 };
            dialogTitle.textContent = `Añadir descripción de la cicatriz #${currentMark.number}`;
            dialog.style.display = 'block';
            dialogOverlay.style.display = 'block';
        });

        saveButton.addEventListener('click', function() {
            if (currentMark && description.value.trim() !== '') {
                saveMark(currentMark.x, currentMark.y, description.value);
                dialog.style.display = 'none';
                dialogOverlay.style.display = 'none';
                description.value = '';
            }
        });

        dialogOverlay.addEventListener('click', function() {
            dialog.style.display = 'none';
            dialogOverlay.style.display = 'none';
            description.value = '';
        });

        function saveMark(x, y, description) {
            fetch('app.php', {
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
            });
        }

        function deleteMark(id) {
            fetch('app.php', {
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
            });
        }

        function renderMarks() {
            bodyImage.parentElement.querySelectorAll('.mark').forEach(el => el.remove());
            marksList.innerHTML = '';
            
            marks.forEach((mark, index) => {
                const markEl = document.createElement('div');
                markEl.className = 'mark';
                markEl.style.left = `${mark.x - 12}px`;
                markEl.style.top = `${mark.y - 12}px`;
                markEl.textContent = index + 1;
                bodyImage.parentElement.appendChild(markEl);

                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    
                    <div class="list-group-item">
                        <div class="d-flex  align-items-center mb-1">
                            <h5 class="mb-0">Marca #${index + 1}</h5>
                            <button class="btn btn-danger btn-sm ml-3" onclick="deleteMark(${mark.id})">Eliminar</button>
                        </div>
                        <p class="text-break">${mark.description}</p>
                    </div>
                    `;
                marksList.appendChild(listItem);
            });
        }

        function loadMarks() {
            fetch('app.php?action=get_marks')
                .then(response => response.json())
                .then(data => {
                    marks = data;
                    renderMarks();
                });
        }

        // Load existing marks on start
        loadMarks();

        // Make deleteMark function available globally
        window.deleteMark = deleteMark;
    });
    </script>
</div>

