<?php
require 'navbar.php';
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

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

// Obtener datos existentes de educación
$educacion = null;
if ($idppl > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM educacion WHERE id_ppl = ? LIMIT 1");
        $stmt->execute([$idppl]);
        $educacion = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al obtener la información educativa: " . $e->getMessage() . "</div>";
    }
}

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $db->prepare("UPDATE educacion SET 
            sabe_leer_escrib = ?, 
            primaria = ?, 
            secundaria = ?, 
            tiene_educ_formal = ?, 
            `educ-formal` = ?, 
            tiene_educ_no_formal = ?, 
            `educ-no-formal` = ?, 
            quiere_deporte = ?, 
            `sec-deporte` = ?, 
            quiere_act_artistica = ?, 
            `act-artistica` = ? 
            WHERE id_ppl = ?");

        $stmt->execute([
            isset($_POST['sabe_leer_escrib']) ? 1 : 0,
            $_POST['primaria'],
            $_POST['secundaria'],
            isset($_POST['tiene_educ_formal']) ? 1 : 0,
            $_POST['educ-formal'] ?? '',
            isset($_POST['tiene_educ_no_formal']) ? 1 : 0,
            $_POST['educ-no-formal'] ?? '',
            isset($_POST['quiere_deporte']) ? 1 : 0,
            $_POST['sec-deporte'] ?? '',
            isset($_POST['quiere_act_artistica']) ? 1 : 0,
            $_POST['act-artistica'] ?? '',
            $idppl
        ]);

        // Registrar acción en la auditoría
        $accion = 'Editar Educación';
        $tabla_afectada = 'educación';
        $detalles = "Se actualizó la información educativa para el PPL con ID: $idppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $idppl, $detalles);

        header("Location: ppl_informe.php?seccion=educacion&id=" . $idppl);
        exit();
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

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .checkbox-group {
            margin-bottom: 15px;
        }

        .checkbox-label {
            font-weight: normal;
            display: inline;
            margin-left: 5px;
        }

        #titulo {
            padding-bottom: 1rem;
        }

        /* Estilo para campos ocultos */
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <form method="POST" class="shadow p-4 rounded">
            <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

            <!-- Sección de Educación -->
            <div class="form-section">
                <h3 id="titulo">Editar Información Educativa del PPL</h3>

                <div class="checkbox-group">
                    <input type="checkbox" id="sabe_leer_escrib" name="sabe_leer_escrib"
                        <?php echo ($educacion && $educacion['sabe_leer_escrib']) ? 'checked' : ''; ?> required>
                    <label for="sabe_leer_escrib" class="checkbox-label">¿Sabe leer y escribir?</label>
                </div>

                <div class="form-group">
                    <label>Nivel Primaria:</label>
                    <select name="primaria" required>
                        <option value="">Seleccione nivel</option>
                        <option value="Completa" <?php echo ($educacion && $educacion['primaria'] == 'Completa') ? 'selected' : ''; ?>>Completa</option>
                        <option value="Incompleta" <?php echo ($educacion && $educacion['primaria'] == 'Incompleta') ? 'selected' : ''; ?>>Incompleta</option>
                        <option value="En curso" <?php echo ($educacion && $educacion['primaria'] == 'En curso') ? 'selected' : ''; ?>>En curso</option>
                        <option value="No tiene" <?php echo ($educacion && $educacion['primaria'] == 'No tiene') ? 'selected' : ''; ?>>No tiene</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nivel Secundaria:</label>
                    <select name="secundaria" required>
                        <option value="">Seleccione nivel</option>
                        <option value="Completa" <?php echo ($educacion && $educacion['secundaria'] == 'Completa') ? 'selected' : ''; ?>>Completa</option>
                        <option value="Incompleta" <?php echo ($educacion && $educacion['secundaria'] == 'Incompleta') ? 'selected' : ''; ?>>Incompleta</option>
                        <option value="En curso" <?php echo ($educacion && $educacion['secundaria'] == 'En curso') ? 'selected' : ''; ?>>En curso</option>
                        <option value="No tiene" <?php echo ($educacion && $educacion['secundaria'] == 'No tiene') ? 'selected' : ''; ?>>No tiene</option>
                    </select>
                </div>

                <h3>Interés por participar de actividades:</h3>

                <div class="checkbox-group">
                    <input type="checkbox" id="tiene_educ_formal" name="tiene_educ_formal"
                        <?php echo ($educacion && $educacion['tiene_educ_formal']) ? 'checked' : ''; ?>>
                    <label for="tiene_educ_formal" class="checkbox-label">¿Quiere participar en actividades Educativas Formales?</label>
                </div>

                <div class="form-group <?php echo (!$educacion || !$educacion['tiene_educ_formal']) ? 'hidden' : ''; ?>" id="educ_formal_group">
                    <label>Educación Formal:</label>
                    <input type="text" name="educ-formal" id="educ-formal" placeholder="Especifique educación formal..."
                        value="<?php echo htmlspecialchars($educacion['educ-formal'] ?? ''); ?>">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="tiene_educ_no_formal" name="tiene_educ_no_formal"
                        <?php echo ($educacion && $educacion['tiene_educ_no_formal']) ? 'checked' : ''; ?>>
                    <label for="tiene_educ_no_formal" class="checkbox-label">¿Quiere participar en actividades Educativas No Formales?</label>
                </div>

                <div class="form-group <?php echo (!$educacion || !$educacion['tiene_educ_no_formal']) ? 'hidden' : ''; ?>" id="educ_no_formal_group">
                    <label>Educación No Formal:</label>
                    <input type="text" name="educ-no-formal" id="educ-no-formal" placeholder="Especifique educación no formal..."
                        value="<?php echo htmlspecialchars($educacion['educ-no-formal'] ?? ''); ?>">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="quiere_deporte" name="quiere_deporte"
                        <?php echo ($educacion && $educacion['quiere_deporte']) ? 'checked' : ''; ?>>
                    <label for="quiere_deporte" class="checkbox-label">¿Quiere participar en actividades Deportivas?</label>
                </div>

                <div class="form-group <?php echo (!$educacion || !$educacion['quiere_deporte']) ? 'hidden' : ''; ?>" id="deporte_group">
                    <label>Sección Deportiva:</label>
                    <input type="text" name="sec-deporte" id="sec-deporte" placeholder="Especifique actividad deportiva..."
                        value="<?php echo htmlspecialchars($educacion['sec-deporte'] ?? ''); ?>">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="quiere_act_artistica" name="quiere_act_artistica"
                        <?php echo ($educacion && $educacion['quiere_act_artistica']) ? 'checked' : ''; ?>>
                    <label for="quiere_act_artistica" class="checkbox-label">¿Quiere participar en actividades Artísticas?</label>
                </div>

                <div class="form-group <?php echo (!$educacion || !$educacion['quiere_act_artistica']) ? 'hidden' : ''; ?>" id="artistica_group">
                    <label>Actividad Artística:</label>
                    <input type="text" name="act-artistica" id="act-artistica" placeholder="Especifique actividad artística..."
                        value="<?php echo htmlspecialchars($educacion['act-artistica'] ?? ''); ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>

    <script>
        // Función para manejar la visibilidad de los campos
        function toggleFormGroup(checkboxId, groupId) {
            const checkbox = document.getElementById(checkboxId);
            const group = document.getElementById(groupId);

            if (!checkbox || !group) return; // Validación de elementos

            const input = group.querySelector('input[type="text"]');

            // Configurar estado inicial
            if (checkbox.checked) {
                group.classList.remove('hidden');
                if (input) input.required = true;
            } else {
                group.classList.add('hidden');
                if (input) {
                    input.required = false;
                    input.value = ''; // Limpiar el campo cuando se oculta
                }
            }

            // Agregar listener para cambios
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    group.classList.remove('hidden');
                    if (input) input.required = true;
                } else {
                    group.classList.add('hidden');
                    if (input) {
                        input.required = false;
                        input.value = ''; // Limpiar el campo cuando se oculta
                    }
                }
            });
        }

        // Configurar los listeners para todos los checkboxes cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            toggleFormGroup('tiene_educ_formal', 'educ_formal_group');
            toggleFormGroup('tiene_educ_no_formal', 'educ_no_formal_group');
            toggleFormGroup('quiere_deporte', 'deporte_group');
            toggleFormGroup('quiere_act_artistica', 'artistica_group');
        });
    </script>
</body>