<?php
// Incluir el archivo de conexión
// require_once BASE_PATH . '/conn/connection.php';

function registrarAuditoria($db, $accion, $tabla_afectada, $registro_id, $detalles)
{
    try {
        $sql = "INSERT INTO auditoria (id_usuario, accion, detalles, tabla_afectada, registro_id, fecha) 
                VALUES (:id_usuario, :accion, :detalles, :tabla_afectada, :registro_id, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':detalles', $detalles);
        $stmt->bindParam(':tabla_afectada', $tabla_afectada);
        $stmt->bindParam(':registro_id', $registro_id);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error en el registro de auditoría: " . $e->getMessage();
    }
}

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_8'])) {
    try {
        // Insertar datos de clasificación
        $stmt = $db->prepare("INSERT INTO observaciones (id_ppl, observacion) 
            VALUES (?, ?)");

        $stmt->execute([
            $_POST['id_ppl'],
            $_POST['observacion']
        ]);

        // Registrar acción en la auditoría
        $accion = 'Agregar Observación';
        $tabla_afectada = 'observaciones';
        $detalles = "Se insertó una nueva observación para el PPL con ID: $idppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);


        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
}
?>

<head>
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



        /* .btn {
            padding: 10px 15px;
            background-color: #212529;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        } */

        #titulo {
            padding-bottom: 1rem;
        }
    </style>
</head>

<body>
    <form action="" method="POST">
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

        <!-- Sección de Clasificación -->
        <div class="form-section">
            <h3 id="titulo">Observaciones del PPL</h3>

            <div class="form-group">
                <label>Ingrese la observacion correspondiente:</label>
                <textarea class="w-100 p-2 border border-gray-300 rounded mb-4"
                    name="observacion" id="observacion" placeholder="Ingrese la obvservación">

                </textarea>
            </div>


        </div>

        <button name="guardar_8" type="submit" class="btn btn-primary">Guardar</button>
    </form>

</body>