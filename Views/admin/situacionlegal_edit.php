<?php 
require 'navbar.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

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

try {
    $db = new PDO("mysql:host=localhost;dbname=spp;charset=utf8mb4", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch persona data
    $stmt_persona = $db->prepare("SELECT 
        persona.id,
        persona.dni, 
        persona.nombres, 
        persona.apellidos, 
        DATE_FORMAT(persona.fechanac, '%d-%m-%Y') AS fechaNacimiento, 
        persona.edad, 
        persona.genero, 
        persona.estadocivil, 
        d.id AS id_direccion, 
        p.nombre AS pais, 
        pr.nombre AS provincia, 
        c.nombre AS ciudad, 
        d.localidad, 
        d.direccion
        FROM persona
        LEFT JOIN domicilio d ON persona.direccion = d.id
        LEFT JOIN paises p ON d.id_pais = p.id
        LEFT JOIN provincias pr ON d.id_provincia = pr.id
        LEFT JOIN ciudades c ON d.id_ciudad = c.id
        WHERE persona.id = :id");
    $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

    // Fetch PPL data
    $stmt_ppl = $db->prepare("SELECT id, apodo, profesion, trabaja, foto, huella
        FROM ppl
        WHERE idpersona = :id");
    $stmt_ppl->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);

    // Fetch situacion legal data
    $stmt_situacion = $db->prepare("SELECT 
        situacionlegal.*, 
        juzgado.nombre AS nombre_juzgado, 
        juzgado.nombre_juez AS nombre_juez,
        GROUP_CONCAT(delitos.nombre SEPARATOR '\n') AS nombres_causas,
        GROUP_CONCAT(delitos.id_delito) AS ids_causas
        FROM situacionlegal
        LEFT JOIN juzgado ON situacionlegal.id_juzgado = juzgado.id
        LEFT JOIN ppl_causas ON situacionlegal.id_ppl = ppl_causas.id_ppl
        LEFT JOIN delitos ON ppl_causas.id_causa = delitos.id_delito
        WHERE situacionlegal.id_ppl = (SELECT id FROM ppl WHERE idpersona = :id)
        GROUP BY situacionlegal.id_ppl");
    $stmt_situacion->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_situacion->execute();
    $situacion_legal = $stmt_situacion->fetch(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db->beginTransaction();
        
        try {
            $ppl_id = $ppl['id']; // Get the PPL ID from the earlier query
            
            // Update situacionlegal table
            $stmt = $db->prepare("UPDATE situacionlegal SET 
                fecha_detencion = :fecha_detencion,
                dependencia = :dependencia,
                motivo_t = :motivo_t,
                situacionlegal = :situacionlegal,
                id_juzgado = :id_juzgado,
                en_prejucio = :en_prejucio,
                condena = :condena,
                categoria = :categoria,
                reingreso_falta = :reingreso_falta,
                causas_pend = :causas_pend,
                causa_nino = :causa_nino,
                cumplio_medida = :cumplio_medida,
                asistio_rehabi = :asistio_rehabi,
                tiene_defensor = :tiene_defensor,
                nombre_defensor = :nombre_defensor,
                tiene_com_defensor = :tiene_com_defensor
                WHERE id_ppl = :id_ppl");

            $params = [
                ':fecha_detencion' => $_POST['fecha_detencion'],
                ':dependencia' => $_POST['dependencia'],
                ':motivo_t' => $_POST['motivo_t'],
                ':situacionlegal' => $_POST['situacionlegal'],
                ':id_juzgado' => $_POST['id_juzgado'],
                ':en_prejucio' => $_POST['en_prejucio'],
                ':condena' => $_POST['condena'],
                ':categoria' => $_POST['categoria'],
                ':reingreso_falta' =>  $_POST['reingreso_falta']== '1' ? 1 : 0,
                ':causas_pend' => $_POST['causas_pend'],
                ':causa_nino' =>  $_POST['causa_nino']== '1' ? 1 : 0,
                ':cumplio_medida' => $_POST['cumplio_medida']== '1' ? 1 : 0,
                ':asistio_rehabi' =>  $_POST['asistio_rehabi']== '1' ? 1 : 0,
                ':tiene_defensor' => $_POST['tiene_defensor'] == '1' ? 1 : 0,
                ':nombre_defensor' => $_POST['nombre_defensor'],
                ':tiene_com_defensor' => $_POST['tiene_com_defensor'] == '1' ? 1 : 0,
                ':id_ppl' => $ppl_id
            ];

            $stmt->execute($params);

            // Handle causas (delitos)
            if (isset($_POST['causas']) && is_array($_POST['causas'])) {
                // Delete existing causes
                $stmt_delete = $db->prepare("DELETE FROM ppl_causas WHERE id_ppl = :id_ppl");
                $stmt_delete->bindParam(':id_ppl', $ppl_id);
                $stmt_delete->execute();

                // Insert new causes
                $stmt_insert = $db->prepare("INSERT INTO ppl_causas (id_ppl, id_causa) VALUES (:id_ppl, :id_causa)");
                foreach ($_POST['causas'] as $causa_id) {
                    $stmt_insert->bindParam(':id_ppl', $ppl_id);
                    $stmt_insert->bindParam(':id_causa', $causa_id);
                    $stmt_insert->execute();
                }
            }

            $db->commit();

            // Register audit
            $accion = 'Editar PPL - Situacion legal';
            $tabla_afectada = 'situacionlegal';
            $detalles = "Se editó el PPL con ID: $ppl_id";
            registrarAuditoria($db, $accion, $tabla_afectada, $ppl_id, $detalles);

            header("Location: ppl_informe.php?&id=" . $id);
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error en la actualización: " . $e->getMessage());
            $error_message = "Error al actualizar los datos. Por favor, intente nuevamente.";
        }
    }
    
    // Get all delitos for the form
    $stmt_delitos = $db->query("SELECT d.id_delito, d.nombre, t.id_tipo_delito 
                               FROM delitos d 
                               LEFT JOIN tiposdelito t ON d.id_tipo_delito = t.id_tipo_delito");
    $delitos = $stmt_delitos->fetchAll(PDO::FETCH_ASSOC);

    // Get all juzgados for the form
    $stmt_juzgados = $db->query("SELECT id, nombre, nombre_juez FROM juzgado");
    $juzgados = $stmt_juzgados->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    $error_message = "Error al cargar los datos. Por favor, intente nuevamente.";
}
?>

<div class="container mt-4">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Editar Situación Legal</h5>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_detencion" class="form-label">Fecha de detención:</label>
                        <input type="date" id="fecha_detencion" name="fecha_detencion" class="form-control" 
                               value="<?php echo htmlspecialchars($situacion_legal['fecha_detencion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="dependencia" class="form-label">Dependencia:</label>
                        <input type="text" id="dependencia" name="dependencia" class="form-control" 
                               value="<?php echo htmlspecialchars($situacion_legal['dependencia'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="motivo_t" class="form-label">Motivo de traslado:</label>
                        <input type="text" class="form-control" id="motivo_t" name="motivo_t" 
                               value="<?php echo htmlspecialchars($situacion_legal['motivo_t'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="situacionlegal" class="form-label">Situación Legal:</label>
                        <select id="situacionlegal" name="situacionlegal" class="form-select" required>
                            <option value="Penado" <?php echo (($situacion_legal['situacionlegal'] ?? '') == 'Penado' ? 'selected' : ''); ?>>Penado</option>
                            <option value="Procesado" <?php echo (($situacion_legal['situacionlegal'] ?? '') == 'Procesado' ? 'selected' : ''); ?>>Procesado</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-6">
                    <div class="col-md-16">
                        <label for="buscar-causas">Buscar Causas:</label>
                        <input type="text" id="buscar-causas" class="form-control" placeholder="Escribe para buscar..." onkeyup="filtrarCausas()">
                        
                        <label for="causas">Causas:</label>
                        <div id="causas-container" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Causa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $selected_causas = explode(',', $situacion_legal['ids_causas'] ?? '');
                                    foreach ($delitos as $delito) {
                                        $checked = in_array($delito['id_delito'], $selected_causas) ? 'checked' : '';
                                        echo "<tr class='causa-checkbox'>
                                            <td><input type='checkbox' id='causa_{$delito['id_delito']}' name='causas[]' 
                                                     value='{$delito['id_delito']}' {$checked}></td>
                                            <td><label for='causa_{$delito['id_delito']}'>{$delito['nombre']}</label></td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <p id="selected-causas-text" style="margin-top: 10px;">Selecciona hasta 4 causas.</p>
                    </div>
                </div>
                <!-- Scrip para buscar las causas  -->
                    <script>
                        function filtrarCausas() {
                            var input = document.getElementById('buscar-causas');
                            var filtro = input.value.toLowerCase();
                            var filas = document.querySelectorAll('.causa-checkbox');                            
                            filas.forEach(function(fila) {
                                var celdaCausa = fila.getElementsByTagName('td')[1];
                                if (celdaCausa) {
                                    var textoCausa = celdaCausa.textContent || celdaCausa.innerText;
                                    if (textoCausa.toLowerCase().indexOf(filtro) > -1) {
                                        fila.style.display = "";
                                    } else {
                                        fila.style.display = "none";
                                    }
                                }
                            });
                        }                    
                    </script>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="en_prejucio">En perjuicio de quien (si la causa es intrafamiliar):</label>
                        <input type="text" class="form-control" id="en_prejucio" name="en_prejucio" 
                               value="<?php echo htmlspecialchars($situacion_legal['en_prejucio'] ?? 'No hay dato', ENT_QUOTES, 'UTF-8'); ?>" >
                    </div>

                    <div class="col-md-6">
                        <label for="id_juzgado">Juzgado:</label>
                        <select class="form-select" id="id_juzgado" name="id_juzgado" required>
                            <option value="">-- Seleccione un Juez --</option>
                            <?php foreach ($juzgados as $juzgado): ?>
                                <option value="<?php echo $juzgado['id']; ?>" 
                                    <?php echo (($situacion_legal['id_juzgado'] ?? '') == $juzgado['id'] ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($juzgado['nombre'] . ' - ' . $juzgado['nombre_juez']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="condena">Condena:</label>
                        <input type="text" class="form-control" id="condena" name="condena" value="<?php echo htmlspecialchars($situacion_legal['condena'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="categoria">Categoría:</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="Primario" <?php echo ($situacion_legal['categoria'] == 'Primario' ? 'selected' : ''); ?>>Primario</option>
                            <option value="Reiterante" <?php echo ($situacion_legal['categoria'] == 'Reiterante' ? 'selected' : ''); ?>>Reiterante</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="causas_pend">Causas pendientes de resolución:</label>
                        <input type="text" class="form-control" id="causas_pend" name="causas_pend" value="<?php echo htmlspecialchars($situacion_legal['causas_pend'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="reingreso_falta">Reingreso en caso de quebrantamiento de beneficio y/o libertad:</label>
                        <select class="form-select" id="reingreso_falta" name="reingreso_falta" required>
                            <option value="1" <?php echo ($situacion_legal['reingreso_falta'] == '1' ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['reingreso_falta'] == '0' ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="causa_nino">¿Causas judicializadas durante la niñez o adolescencia?:</label>
                        <select class="form-select" id="causa_nino" name="causa_nino" required>
                            <option value="1" <?php echo ($situacion_legal['causa_nino'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['causa_nino'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="cumplio_medida">¿Cumplió medidas socioeducativas?:</label>
                        <select class="form-select" id="cumplio_medida" name="cumplio_medida" required>
                            <option value="1" <?php echo ($situacion_legal['cumplio_medida'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['cumplio_medida'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="asistio_rehabi">Institucionalizaciones en centros de rehabilitación por conflictos con la ley:</label>
                        <select class="form-select" id="asistio_rehabi" name="asistio_rehabi" required>
                            <option value="1" <?php echo ($situacion_legal['asistio_rehabi'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['asistio_rehabi'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="tiene_defensor">¿Cuenta con un defensor oficial?:</label>
                        <select class="form-select" id="tiene_defensor" name="tiene_defensor" required onchange="toggleDefensorFields()">
                            <option value="1" <?php echo ($situacion_legal['tiene_defensor'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['tiene_defensor'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div id="defensor-details" style="display: <?php echo ($situacion_legal['tiene_defensor'] == 1 ? 'block' : 'none'); ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre_defensor">¿Quién?:</label>
                            <input type="text" class="form-control" id="nombre_defensor" name="nombre_defensor" 
                                value="<?php echo htmlspecialchars($situacion_legal['nombre_defensor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="tiene_com_defensor">¿Tiene comunicación con él?:</label>
                            <select class="form-select" id="tiene_com_defensor" name="tiene_com_defensor" required>
                                <option value="1" <?php echo ($situacion_legal['tiene_com_defensor'] == 1 ? 'selected' : ''); ?>>Sí</option>
                                <option value="0" <?php echo ($situacion_legal['tiene_com_defensor'] == 0 ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- -------------- -->
                 <a href="ppl_informe.php?&id=<?php echo $id; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>                
                
                <!-- -------------- -->
            </form>
        </div>
    </div>
</div>
<!-- ------------------- -->
<script>
function toggleDefensorFields() {
    const tieneDefensor = document.getElementById('tiene_defensor');
    const defensorDetails = document.getElementById('defensor-details');
    
    if (tieneDefensor.value === '1') {
        defensorDetails.style.display = 'block';
    } else {
        defensorDetails.style.display = 'none';
        // Limpiar los campos cuando se ocultan
        document.getElementById('nombre_defensor').value = '';
        document.getElementById('tiene_com_defensor').selectedIndex = 0;
    }
}
document.addEventListener('DOMContentLoaded', toggleDefensorFields);

//<!-- ------------------- -->

    const checkboxes = document.querySelectorAll('.causa-checkbox input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedCheckboxes = document.querySelectorAll('.causa-checkbox input[type="checkbox"]:checked');
            const maxCausas = 4;
            if (selectedCheckboxes.length > maxCausas) {
                alert("Solo puedes seleccionar un máximo de 4 causas.");
                this.checked = false;
            }
        });
    });
</script>
