<?php
require 'navbar.php';
// Función de auditoría
function registrarAuditoria($db, $accion, $tabla_afectada, $registro_id, $detalles) {
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
        error_log("Error en el registro de auditoría: " . $e->getMessage());
    }
}

// Verificar si se ha proporcionado un ID
if (!isset($_GET['id'])) {
    header("Location: ppl_index.php?error=id_no_proporcionado");
    exit();
}

$id_ppl = $_GET['id'];
try {
    // Obtener datos actuales del PPL
    $stmt = $db->prepare(" SELECT p.*, ppl.*, d.* 
        FROM persona p 
        JOIN ppl ON p.id = ppl.idpersona 
        LEFT JOIN domicilio d ON p.direccion = d.id 
        WHERE ppl.idpersona = :id_ppl
    ");
    $stmt->bindParam(':id_ppl', $id_ppl);
    $stmt->execute();
    $ppl = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ppl) {
        header("Location: ppl_index.php?error=ppl_no_encontrado");
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['id'];
    try {
        $db->beginTransaction();

        // Actualizar datos de PPL
        $stmt = $db->prepare("UPDATE ppl 
        SET trabaja = :trabaja,
            profesion = :profesion,
            huella = CASE 
                WHEN :nueva_huella = 1 THEN :huella_data
                ELSE huella
            END
        WHERE idpersona = :id_ppl
        ");

        $trabaja = ($_POST['trabaja'] === 'Si') ? 1 : 0;
        $nueva_huella = isset($_FILES['huella']) && $_FILES['huella']['error'] === UPLOAD_ERR_OK ? 1 : 0;
        $huella_data = null;
    
        if ($nueva_huella) {
            $huella_data = file_get_contents($_FILES['huella']['tmp_name']);
        }
    
        $stmt->bindParam(':trabaja', $trabaja);
        $stmt->bindParam(':profesion', $_POST['profesion']);
        $stmt->bindParam(':id_ppl', $post_id);
        $stmt->bindParam(':nueva_huella', $nueva_huella, PDO::PARAM_INT);
        $stmt->bindParam(':huella_data', $huella_data, PDO::PARAM_LOB);
        $stmt->execute();

        // Procesar nueva foto si se ha subido
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../img_ppl';
            $foto_temp = $_FILES['foto']['tmp_name'];
            $original_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $foto_nombre = substr(hash('md5', uniqid('', true)), 0, 10) . '.' . $original_ext;
            $foto_path = $upload_dir . '/' . $foto_nombre;

            if (move_uploaded_file($foto_temp, $foto_path)) {
                // Actualizar nombre de foto en la base de datos
                $stmt = $db->prepare("UPDATE ppl SET foto = :foto WHERE idpersona = :id_ppl");
                $stmt->bindParam(':foto', $foto_nombre);
                $stmt->bindParam(':id_ppl', $id_ppl);
                $stmt->execute();

                // Eliminar foto anterior si existe
                if (!empty($ppl['foto'])) {
                    $foto_anterior = $upload_dir . '/' . $ppl['foto'];
                    if (file_exists($foto_anterior)) {
                        unlink($foto_anterior);
                    }
                }
            }
        }

        // Registrar en auditoría
        $detalles = "Actualización de PPL - Profesión: {$_POST['profesion']}, Trabaja: " . 
                   ($_POST['trabaja'] === 'Si' ? 'Sí' : 'No') .
                   ($nueva_huella ? ", Huella actualizada" : "");
        
        registrarAuditoria(
            $db,
            'Actualizar PPL',
            'ppl',
            $id_ppl,
            $detalles
        );

        $db->commit();
        var_dump($id_ppl); 
        
        header("Location: ppl_informe.php?id=" . $id_ppl);
        
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        $error = "Error en la actualización: " . $e->getMessage();
    }
}
?>

<div class="container mt-3">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Editar Datos de PPL</h5>
        </div>
        <div class="card-body bg-light">
            
            <form method="POST" enctype="multipart/form-data">                
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_ppl); ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="profesion" class="form-label">Profesión</label>
                        <input type="text" 
                               class="form-control" 
                               id="profesion" 
                               name="profesion" 
                               value="<?php echo htmlspecialchars($ppl['profesion'] ?? ''); ?>"
                               pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" 
                               >
                        <div class="invalid-feedback">
                            Por favor ingrese una profesión válida (solo letras y espacios).
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="trabaja" class="form-label">¿Trabajaba al momento de la detención?</label>
                        <select class="form-select" id="trabaja" name="trabaja" required>
                            <option value="Si" <?php echo ($ppl['trabaja'] == 1) ? 'selected' : ''; ?>>Sí</option>
                            <option value="No" <?php echo ($ppl['trabaja'] == 0) ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>

                <!-- Sección de fotos -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="foto" class="form-label">Nueva Foto</label>
                        <input type="file" 
                               class="form-control" 
                               id="foto" 
                               name="foto" 
                               accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.tiff,.heic,.heif">
                        <div class="form-text">Formatos permitidos: JPG, JPEG, PNG</div>
                        
                    </div>
                    <div class="col-md-6">
                        <div class="row">                            
                            <div class="col">
                                <label class="form-label">Foto Actual</label>
                                    <?php if (!empty($ppl['foto'])): ?>
                                        <div>
                                            <img src="../../img_ppl/<?php echo htmlspecialchars($ppl['foto']); ?>" 
                                                class="img-thumbnail" 
                                                style="max-width: 200px;" 
                                                alt="Foto actual">
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted">No hay foto actual</div>
                                    <?php endif; ?>
                            </div>
                            <div class="col">
                                <div id="nuevaFotoPreview" class="mt-2"></div>
                            </div>
                        </div>               
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="huella" class="form-label">Huella Dactilar</label>
                    <input type="file" 
                           class="form-control" 
                           id="huella" 
                           name="huella" 
                           accept=".dat,.raw">
                    <div class="form-text">
                        Archivo de huella dactilar (opcional)
                        <?php if (!empty($ppl['huella'])): ?>
                            - Ya existe una huella registrada
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="ppl_informe.php?&id=<?php echo $id_ppl; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Validación del formulario
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()

    // Preview de imagen
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('nuevaFotoPreview');
        
        // Clear existing preview
        previewContainer.innerHTML = '';
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.className = 'img-thumbnail';
                preview.style.maxWidth = '200px';
                previewContainer.appendChild(preview);
            }
            reader.readAsDataURL(file);
        }
    });
</script>