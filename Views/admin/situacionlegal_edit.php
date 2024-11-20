<?php require 'navbar.php'; ?>
<?php

$id = isset($_GET['id']) ? $_GET['id'] : null;

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

try {

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
    WHERE persona.id = :id
    ");
    $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_persona->execute();
    $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

    $stmt_ppl = $db->prepare("SELECT id, apodo, profesion, trabaja, foto, huella
    FROM ppl
    WHERE idpersona = :id
    ");
    $stmt_ppl->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_ppl->execute();
    $ppl = $stmt_ppl->fetch(PDO::FETCH_ASSOC);

    $stmt_situacion = $db->prepare("SELECT situacionlegal.id_ppl, situacionlegal.fecha_detencion, situacionlegal.dependencia, situacionlegal.motivo_t, 
                                    situacionlegal.situacionlegal, situacionlegal.causas, situacionlegal.id_juzgado, situacionlegal.en_prejucio, 
                                    situacionlegal.condena, situacionlegal.categoria, situacionlegal.reingreso_falta, situacionlegal.causas_pend, 
                                    situacionlegal.cumplio_medida, situacionlegal.asistio_rehabi, situacionlegal.causa_nino, 
                                    situacionlegal.tiene_defensor, situacionlegal.nombre_defensor, situacionlegal.tiene_com_defensor,
                                    juzgado.nombre AS nombre_juzgado, 
                                    juzgado.nombre_juez AS nombre_juez,
                                    GROUP_CONCAT(delitos.nombre SEPARATOR '\n') AS nombres_causas
                                    FROM situacionlegal
                                    LEFT JOIN juzgado ON situacionlegal.id_juzgado = juzgado.id
                                    LEFT JOIN ppl_causas ON situacionlegal.id_ppl = ppl_causas.id_ppl
                                    LEFT JOIN delitos ON ppl_causas.id_causa = delitos.id_delito
                                    WHERE situacionlegal.id_ppl = (SELECT id FROM ppl WHERE idpersona = :id)
                                    GROUP BY situacionlegal.id_ppl
                                ");
    $stmt_situacion->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_situacion->execute();
    $situacion_legal = $stmt_situacion->fetch(PDO::FETCH_ASSOC);

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=spp;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }

    $id_ppl = $ppl['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [];
        $data['id_ppl'] = $id_ppl;
        $data['fecha_detencion'] = $_POST['fecha_detencion'] ?? '';
        $data['dependencia'] = $_POST['dependencia'] ?? '';
        $data['motivo_t'] = $_POST['motivo_t'] ?? '';
        $data['situacionlegal'] = $_POST['situacionlegal'] ?? '';
        $data['id_juzgado'] = $_POST['id_juzgado'] ?? '';
        $data['en_prejucio'] = $_POST['en_prejucio'] ?? '';
        $data['condena'] = $_POST['condena'] ?? '';
        $data['categoria'] = $_POST['categoria'] ?? 'primario';
        $data['reingreso_falta'] = ($_POST['reingreso_falta'] ?? 'no') === 'si' ? 1 : 0;
        $data['causas_pend'] = $_POST['causas_pend'] ?? '';
        $data['cumplio_medida'] = ($_POST['cumplio_medida'] ?? 'no') === 'si' ? 1 : 0;
        $data['asistio_rehabi'] = ($_POST['asistio_rehabi'] ?? 'no') === 'si' ? 1 : 0;
        $data['tiene_defensor'] = ($_POST['tiene_defensor'] ?? 'no') === 'si' ? 1 : 0;
        $data['causa_nino'] = ($_POST['causa_nino'] ?? 'no') === 'si' ? 1 : 0;
        $data['nombre_defensor'] = $_POST['nombre_defensor'] ?? '';
        $data['tiene_com_defensor'] = ($_POST['tiene_com_defensor'] ?? 'no') === 'si' ? 1 : 0;

        $causaIds = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {

                if (isset($_POST['causas']) && is_array($_POST['causas'])) {

                    $stmtDelete = $pdo->prepare("DELETE FROM ppl_causas WHERE id_ppl = :id_ppl");
                    $stmtDelete->bindParam(':id_ppl', $id_ppl);
                    $stmtDelete->execute();

                    foreach ($_POST['causas'] as $causaId) {
                        $stmtCausa = $pdo->prepare("INSERT INTO ppl_causas (id_ppl, id_causa) VALUES (:id_ppl, :id_causa)");
                        $stmtCausa->bindParam(':id_ppl', $id_ppl);
                        $stmtCausa->bindParam(':id_causa', $causaId);
                        $stmtCausa->execute();

                        $causaIds[] = $pdo->lastInsertId();
                    }
                }

                $causasString = implode(',', $causaIds);

                $stmtUpdate = $pdo->prepare("UPDATE situacionlegal SET causas = :causas WHERE id_ppl = :id_ppl");
                $stmtUpdate->bindParam(':causas', $causasString);
                $stmtUpdate->bindParam(':id_ppl', $id_ppl);
                $stmtUpdate->execute();


                $stmt = $db->prepare("UPDATE situacionlegal SET 
                id_ppl = :id_ppl,
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


                foreach ($data as $key => $value) {
                    $stmt->bindValue(':' . $key, $value);
                }

                $stmt->execute();

                $accion = 'Editar PPL - Situacion legal';
                $tabla_afectada = 'situacionlegal';
                $detalles = "Se editó el PPL con ID: $id";
                registrarAuditoria($db, $accion, $tabla_afectada, $id, $detalles);

                header("Location: ppl_informe.php?id=" . urlencode($id) . "&mensaje=" . urlencode("PPL - Situacion Legal Editado con éxito."));
                exit();
            } catch (PDOException $e) {
                echo "Error en la actualización: " . $e->getMessage();
            }
        }
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Editar Situación Legal</h4>
        </div>
        <div class="card-body">
            <form action="situacionlegal_edit.php?id=<?php echo $situacion_legal['id_ppl']; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($situacion_legal['id_ppl'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_detencion" class="form-label">Fecha de detención:</label>
                        <input type="date" id="fecha_detencion" name="fecha_detencion" class="form-control" value="<?php echo htmlspecialchars($situacion_legal['fecha_detencion'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="dependencia" class="form-label">Dependencia:</label>
                        <input type="text" id="dependencia" name="dependencia" class="form-control" value="<?php echo htmlspecialchars($situacion_legal['dependencia'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="motivo_t" class="form-label">Motivo de traslado:</label>
                        <input type="text" class="form-control" id="motivo_t" name="motivo_t" value="<?php echo htmlspecialchars($situacion_legal['motivo_t'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="situacionlegal" class="form-label">Situación Legal:</label>
                        <select id="situacionlegal" name="situacionlegal" class="form-select" required>
                            <option value="penado" <?php echo ($situacion_legal['situacionlegal'] == 'penado' ? 'selected' : ''); ?>>Penado</option>
                            <option value="procesado" <?php echo ($situacion_legal['situacionlegal'] == 'procesado' ? 'selected' : ''); ?>>Procesado</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-6">
                    <div class="col-md-16">
                        <label for="causas">Causas:</label>
                        <div id="causas-container" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered" id="causas" name="causas" required>
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Causa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $resultado = $conexion->query("SELECT d.id_delito, d.nombre, t.id_tipo_delito FROM delitos d LEFT JOIN tiposdelito t ON d.id_tipo_delito = t.id_tipo_delito;");

                                    while ($fila = $resultado->fetch_assoc()) {
                                        echo "<tr class='causa-checkbox'>
                                            <td><input type='checkbox' id='causa_{$fila['id_delito']}' name='causas[]' value='{$fila['id_delito']}'></td>
                                            <td><label for='causa_{$fila['id_delito']}'>{$fila['nombre']}</label></td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <p id="selected-causas-text" style="margin-top: 10px;">Selecciona hasta 4 causas.</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="en_prejucio">En perjuicio de quien (si la causa es intrafamiliar):</label>
                        <input type="text" class="form-control" id="en_prejucio" name="en_prejucio" value="<?php echo !empty($situacion_legal['en_prejucio']) ? htmlspecialchars($situacion_legal['en_prejucio'], ENT_QUOTES, 'UTF-8') : 'No hay dato' ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="id_juzgado">Juzgado:</label>
                        <select class="form-select" id="id_juzgado" name="id_juzgado" required>
                            <option value="">-- Seleccione un Juez --</option>
                            <?php
                            $resultado = $conexion->query("SELECT id, nombre, nombre_juez FROM juzgado");
                            while ($fila = $resultado->fetch_assoc()) {
                                $selected = ($situacion_legal['id_juzgado'] == $fila['id']) ? 'selected' : '';
                                echo "<option value='{$fila['id']}' $selected>{$fila['nombre']} - {$fila['nombre_juez']}</option>";
                            }
                            ?>
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
                            <option value="primario" <?php echo ($situacion_legal['categoria'] == 'primario' ? 'selected' : ''); ?>>Primario</option>
                            <option value="reiterante" <?php echo ($situacion_legal['categoria'] == 'reiterante' ? 'selected' : ''); ?>>Reiterante</option>
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
                        <select class="form-select" id="tiene_defensor" name="tiene_defensor" required>
                            <option value="1" <?php echo ($situacion_legal['tiene_defensor'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['tiene_defensor'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre_defensor">¿Quién?:</label>
                        <input type="text" class="form-control" id="nombre_defensor" name="nombre_defensor" value="<?php echo htmlspecialchars($situacion_legal['nombre_defensor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="tiene_com_defensor">¿Tiene comunicación con él?:</label>
                        <select class="form-select" id="tiene_com_defensor" name="tiene_com_defensor" required>
                            <option value="1" <?php echo ($situacion_legal['tiene_com_defensor'] == 1 ? 'selected' : ''); ?>>Sí</option>
                            <option value="0" <?php echo ($situacion_legal['tiene_com_defensor'] == 0 ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-success btn-lg">Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
