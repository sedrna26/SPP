<?php
// Incluir el archivo de conexión
// require_once BASE_PATH . '/conn/connection.php';

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_8'])) {
    try {
        // Insertar datos de clasificación
        $stmt = $db->prepare("INSERT INTO observaciones (id_ppl, observacion,estado) 
            VALUES (?, ?,?)");

        $stmt->execute([
            $_POST['id_ppl'],
            $_POST['observacion'],
            'Activo'
        ]);

        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
}
?>
    <style>
        .form-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        #titulo {
            padding-bottom: 1rem;
        }
    </style>
    <form method="POST">
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

        <!-- Sección de Clasificación -->
        <div class="form-section">
            <h3 id="titulo">Observaciones del PPL</h3>

            <div class="form-group">
                <label>Ingrese la obvservacion correspondiente:</label>
                <textarea class="w-100 p-2 border border-gray-300 rounded mb-4"
                    name="observacion" id="observacion" placeholder="Ingrese la obvservación">

                </textarea>
            </div>


        </div>

        <button name="guardar_8" type="button" class="btn btn-primary">Guardar</button>
    </form>