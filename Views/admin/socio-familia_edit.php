<?php require 'navbar.php'; ?>
<?php
// Definir la ruta base del proyecto
define('BASE_PATH', dirname(__DIR__, 2));

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar si se ha enviado un formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_datos_familiares'])) {
    try {
        $db->beginTransaction();

        // Actualizar información familiar general
        $stmt = $db->prepare("UPDATE ppl_familiar_info 
            SET familiares_ffaa = ?, ffaa_detalles = ?, 
                familiares_detenidos = ?, detenidos_detalles = ?, 
                telefono_familiar = ?, posee_dni = ?, motivo_no_dni = ? 
            WHERE idppl = ?");
        $stmt->execute([
            isset($_POST['familiares_ffaa']) && $_POST['familiares_ffaa'] == '1' ? 1 : 0,
            $_POST['ffaa_details'] ?? null,
            isset($_POST['familiares_detenidos']) && $_POST['familiares_detenidos'] == '1' ? 1 : 0,
            $_POST['detenidos_details'] ?? null,
            $_POST['telefono_familiar'] ?? null,
            $_POST['posee_dni'] === 'SI' ? 1 : 0,
            $_POST['motivo_no_dni'] ?? null,
            $idppl
        ]);

        // Actualizar situación sociofamiliar
        $stmt = $db->prepare("UPDATE ppl_situacion_sociofamiliar 
            SET edad_inicio_laboral = ?, 
                situacion_economica_precaria = ?, 
                mendicidad_calle = ? 
            WHERE idppl = ?");
        $stmt->execute([
            $_POST['edad_laboral'] ?? null,
            $_POST['situacion_economica'] === 'SI' ? 1 : 0,
            $_POST['mendicidad'] === 'SI' ? 1 : 0,
            $idppl
        ]);

        // Actualizar o insertar datos del padre
        if (!empty($_POST['padre_nombre'])) {
            $stmt = $db->prepare("DELETE FROM ppl_padres WHERE idppl = ? AND tipo = 'PADRE'");
            $stmt->execute([$idppl]);

            $stmt = $db->prepare("INSERT INTO ppl_padres 
                (idppl, tipo, vivo, apellido, nombre, edad, nacionalidad, estado_civil, instruccion, visita, estado) 
                VALUES (?, 'PADRE', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $idppl,
                $_POST['padre_vivo'] === 'Vivo' ? 1 : 0,
                $_POST['padre_apellido'],
                $_POST['padre_nombre'],
                $_POST['padre_edad'] ?? null,
                $_POST['padre_nacionalidad'] ?? null,
                $_POST['padre_estado_civil'] ?? null,
                $_POST['padre_instruccion'] ?? null,
                $_POST['visita_padre'] === 'SI' ? 1 : 0,
                'Activo'
            ]);
        }

        // Actualizar o insertar datos de la madre
        if (!empty($_POST['madre_nombre'])) {
            $stmt = $db->prepare("DELETE FROM ppl_padres WHERE idppl = ? AND tipo = 'MADRE'");
            $stmt->execute([$idppl]);

            $stmt = $db->prepare("INSERT INTO ppl_padres 
                (idppl, tipo, vivo, apellido, nombre, edad, nacionalidad, estado_civil, instruccion, visita, estado) 
                VALUES (?, 'MADRE', ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $idppl,
                $_POST['madre_viva'] === 'Viva' ? 1 : 0,
                $_POST['madre_apellido'],
                $_POST['madre_nombre'],
                $_POST['madre_edad'] ?? null,
                $_POST['madre_nacionalidad'] ?? null,
                $_POST['madre_estado_civil'] ?? null,
                $_POST['madre_instruccion'] ?? null,
                $_POST['visita_madre'] === 'SI' ? 1 : 0,
                'Activo'
            ]);
        }

        // Actualizar hermanos - primero eliminar los existentes
        $stmt = $db->prepare("DELETE FROM ppl_hermanos WHERE idppl = ?");
        $stmt->execute([$idppl]);

        // Insertar nuevos hermanos
        if (isset($_POST['num_hermanos']) && intval($_POST['num_hermanos']) > 0) {
            $stmt = $db->prepare("INSERT INTO ppl_hermanos 
                (idppl, apellido, nombre, edad, visita, estado) 
                VALUES (?, ?, ?, ?, ?, ?)");

            for ($i = 0; $i < intval($_POST['num_hermanos']); $i++) {
                if (!empty($_POST["hermano_nombre_$i"])) {
                    $stmt->execute([
                        $idppl,
                        $_POST["hermano_apellido_$i"] ?? null,
                        $_POST["hermano_nombre_$i"],
                        $_POST["hermano_edad_$i"] ?? null,
                        isset($_POST["hermano_visita_$i"]) && $_POST["hermano_visita_$i"] === 'SI' ? 1 : 0,
                        'Activo'
                    ]);
                }
            }
        }

        // Actualizar pareja - primero eliminar la existente
        $stmt = $db->prepare("DELETE FROM ppl_pareja WHERE idppl = ?");
        $stmt->execute([$idppl]);

        // Insertar nueva pareja si existe
        if (!empty($_POST['pareja_nombre'])) {
            $stmt = $db->prepare("INSERT INTO ppl_pareja 
                (idppl, apellido, nombre, edad, nacionalidad, instruccion, tipo_union, visita, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $visita_pareja = ($_POST['visita_esposo'] === 'SI' || $_POST['visita_concubino'] === 'SI') ? 1 : 0;
            $stmt->execute([
                $idppl,
                $_POST['pareja_apellido'] ?? null,
                $_POST['pareja_nombre'],
                $_POST['pareja_edad'] ?? null,
                $_POST['pareja_nacionalidad'] ?? null,
                $_POST['pareja_instruccion'] ?? null,
                $_POST['pareja_tipo_union'] ?? null,
                $visita_pareja,
                'Activo'
            ]);
        }

        // Actualizar hijos - primero eliminar los existentes
        $stmt = $db->prepare("DELETE FROM ppl_hijos WHERE idppl = ?");
        $stmt->execute([$idppl]);

        // Insertar nuevos hijos
        if (
            isset($_POST['tiene_hijos']) && $_POST['tiene_hijos'] == '1' &&
            isset($_POST['num_hijos']) && intval($_POST['num_hijos']) > 0
        ) {

            $stmt = $db->prepare("INSERT INTO ppl_hijos 
                (idppl, apellido, nombre, edad, fallecido, visita, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            for ($i = 0; $i < intval($_POST['num_hijos']); $i++) {
                if (!empty($_POST["hijo_nombre_$i"])) {
                    $stmt->execute([
                        $idppl,
                        $_POST["hijo_apellido_$i"] ?? null,
                        $_POST["hijo_nombre_$i"],
                        $_POST["hijo_edad_$i"] ?? null,
                        isset($_POST["hijo_fallecido_$i"]) ? 1 : 0,
                        isset($_POST["hijo_visita_$i"]) && $_POST["hijo_visita_$i"] === 'SI' ? 1 : 0,
                        'Activo'
                    ]);
                }
            }
        }

        // Actualizar otros visitantes - primero eliminar los existentes
        $stmt = $db->prepare("DELETE FROM ppl_otros_visitantes WHERE idppl = ?");
        $stmt->execute([$idppl]);

        // Insertar nuevos otros visitantes
        if (
            isset($_POST['visita_otros']) && $_POST['visita_otros'] === 'SI' &&
            isset($_POST['otro_nombre']) && is_array($_POST['otro_nombre'])
        ) {

            $stmt = $db->prepare("INSERT INTO ppl_otros_visitantes 
                (idppl, apellido, nombre, telefono, domicilio, vinculo_filial, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            foreach ($_POST['otro_nombre'] as $key => $nombre) {
                if (!empty($nombre)) {
                    $stmt->execute([
                        $idppl,
                        $_POST['otro_apellido'][$key] ?? null,
                        $nombre,
                        $_POST['otro_telefono'][$key] ?? null,
                        $_POST['otro_domicilio'][$key] ?? null,
                        $_POST['otro_vinculo'][$key] ?? null,
                        'Activo'
                    ]);
                }
            }
        }

        $db->commit();

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

        echo "<div class='alert alert-success'>Datos actualizados correctamente</div>";
        header("Location: ppl_informe.php?seccion=situacion-social&id=" . $idppl);
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        echo "<div class='alert alert-danger'>Error al actualizar los datos: " . $e->getMessage() . "</div>";
    }
}

// Consultar datos existentes
$datos = [
    'familiar_info' => null,
    'situacion_sociofamiliar' => null,
    'padre' => null,
    'madre' => null,
    'hermanos' => [],
    'pareja' => null,
    'hijos' => [],
    'otros_visitantes' => []
];

try {
    // Consultar información familiar
    $stmt = $db->prepare("SELECT * FROM ppl_familiar_info WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $datos['familiar_info'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consultar situación sociofamiliar
    $stmt = $db->prepare("SELECT * FROM ppl_situacion_sociofamiliar WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $datos['situacion_sociofamiliar'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consultar padre y madre
    $stmt = $db->prepare("SELECT * FROM ppl_padres WHERE idppl = ? AND tipo IN ('PADRE', 'MADRE')");
    $stmt->execute([$idppl]);
    $padres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($padres as $padre) {
        if ($padre['tipo'] === 'PADRE') {
            $datos['padre'] = $padre;
        } else {
            $datos['madre'] = $padre;
        }
    }

    // Consultar hermanos
    $stmt = $db->prepare("SELECT * FROM ppl_hermanos WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $datos['hermanos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consultar pareja
    $stmt = $db->prepare("SELECT * FROM ppl_pareja WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $datos['pareja'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consultar hijos
    $stmt = $db->prepare("SELECT * FROM ppl_hijos WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $datos['hijos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consultar otros visitantes
    $stmt = $db->prepare("SELECT * FROM ppl_otros_visitantes WHERE idppl = ?");
    $stmt->execute([$idppl]);
    $datos['otros_visitantes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al recuperar los datos: " . $e->getMessage();
    exit();
}
?>
<style>
    .form-section {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .hidden {
        display: none;
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
    input[type="number"],
    select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .familiar-container {
        border-left: 3px solid #212529;
        padding-left: 15px;
        margin-bottom: 20px;
    }

    .status-fallecido {
        border-left-color: #212529;
        background-color: #f9f9f9;
    }

    .children-container {
        margin-left: 20px;
        padding: 10px;
        border-left: 2px dashed #212529;
    }

    #titulo {
        padding-bottom: 1rem;
    }
</style>
<!-- ------------------------- -->
<section class="container mt-3">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="d-inline-block">Editar Información Familiar</h5>
        </div>
        <div class="card-body table-responsive">

        <a href="ppl_informe.php?seccion=situacion-social&id=<?php echo $idppl; ?>">
            <div class="btn btn-secondary">Cancelar</div>
        </a>

        
        <form onsubmit="enviarFormulario(event)" id="familyDataForm" method="POST" novalidate>
            <input type="hidden" name="idppl" value="<?php echo htmlspecialchars($idppl); ?>">
            <!-- Familiares FF.AA y Detenidos -->
            <div class="form-section">
                <div class="form-group">
                    <label>Familiares de FF.AA:</label>
                    <select name="familiares_ffaa" id="familiares_ffaa" required>
                        <option value="0" <?php echo ($datos['familiar_info']['familiares_ffaa'] == 0) ? 'selected' : ''; ?>>No</option>
                        <option value="1" <?php echo ($datos['familiar_info']['familiares_ffaa'] == 1) ? 'selected' : ''; ?>>Sí</option>
                    </select>
                </div>
                <div id="ffaa_details" class="<?php echo ($datos['familiar_info']['familiares_ffaa'] == 0) ? 'hidden' : ''; ?>">
                    <input type="text" name="ffaa_details"
                        value="<?php echo htmlspecialchars($datos['familiar_info']['ffaa_detalles'] ?? ''); ?>"
                        placeholder="Especifique familiares de FF.AA.">
                </div>

                <div class="form-group">
                    <label>Familiares Detenidos:</label>
                    <select name="familiares_detenidos" id="familiares_detenidos" required>
                        <option value="0" <?php echo ($datos['familiar_info']['familiares_detenidos'] == 0) ? 'selected' : ''; ?>>No</option>
                        <option value="1" <?php echo ($datos['familiar_info']['familiares_detenidos'] == 1) ? 'selected' : ''; ?>>Sí</option>
                    </select>
                </div>
                <div id="detenidos_details" class="<?php echo ($datos['familiar_info']['familiares_detenidos'] == 0) ? 'hidden' : ''; ?>">
                    <input type="text" name="detenidos_details"
                        value="<?php echo htmlspecialchars($datos['familiar_info']['detenidos_detalles'] ?? ''); ?>"
                        placeholder="Especifique familiares detenidos">
                </div>
            </div>

            <!-- Contacto y Documentación -->
            <div class="form-section">
                <div class="form-group">
                    <label>Teléfono Familiar:</label>
                    <input type="tel" class="form-control" name="telefono_familiar"
                        value="<?php echo htmlspecialchars($datos['familiar_info']['telefono_familiar'] ?? ''); ?>"
                        placeholder="Número de teléfono">
                </div>
                <div class="row align-items-cente">
                    <div class="col-md-4 ">
                        <label>Posee DNI:</label>
                        <select name="posee_dni" required>
                            <option value="NO" <?php echo ($datos['familiar_info']['posee_dni'] == 0) ? 'selected' : ''; ?>>No</option>
                            <option value="SI" <?php echo ($datos['familiar_info']['posee_dni'] == 1) ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                    <div class="col-md-8 mt-4 pt-1">
                        <div id="motivo_no_dni_section" class="<?php echo ($datos['familiar_info']['posee_dni'] == 1) ? 'hidden' : ''; ?>">
                            <input type="text" name="motivo_no_dni"
                            value="<?php echo htmlspecialchars($datos['familiar_info']['motivo_no_dni'] ?? ''); ?>"
                            placeholder="Motivo por el que no posee DNI">
                        </div>
                    </div>
                </div>   
            </div>

            <!-- Situación Socioeconómica -->
             
             
            <div class="form-section">

            <div class="row ">
                <div class="col">
                    <div class="form-group">
                        <label>Edad de Inicio Laboral:</label>
                        <input type="number" name="edad_laboral"
                            value="<?php echo htmlspecialchars($datos['situacion_sociofamiliar']['edad_inicio_laboral'] ?? ''); ?>"
                            placeholder="Edad de inicio laboral">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Situación Económica Precaria:</label>
                        <select name="situacion_economica" required>
                            <option value="NO" <?php echo ($datos['situacion_sociofamiliar']['situacion_economica_precaria'] == 0) ? 'selected' : ''; ?>>No</option>
                            <option value="SI" <?php echo ($datos['situacion_sociofamiliar']['situacion_economica_precaria'] == 1) ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Mendicidad en Calle:</label>
                        <select name="mendicidad" required>
                            <option value="NO" <?php echo ($datos['situacion_sociofamiliar']['mendicidad_calle'] == 0) ? 'selected' : ''; ?>>No</option>
                            <option value="SI" <?php echo ($datos['situacion_sociofamiliar']['mendicidad_calle'] == 1) ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                </div>
             </div>
            </div>
            <!-- Padre -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 py-0">Información del Padre</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" name="padre_nombre"
                                    value="<?php echo htmlspecialchars($datos['padre']['nombre'] ?? ''); ?>"
                                    placeholder="Nombre del padre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Apellido:</label>
                                <input type="text" name="padre_apellido"
                                    value="<?php echo htmlspecialchars($datos['padre']['apellido'] ?? ''); ?>"
                                    placeholder="Apellido del padre">
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Estado:</label>
                                <select name="padre_vivo">
                                    <option value="Vivo" <?php echo (isset($datos['padre']) && $datos['padre']['vivo'] == 1) ? 'selected' : ''; ?>>Vivo</option>
                                    <option value="Fallecido" <?php echo (isset($datos['padre']) && $datos['padre']['vivo'] == 0) ? 'selected' : ''; ?>>Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Edad:</label>
                                <input type="number" name="padre_edad"
                                    value="<?php echo htmlspecialchars($datos['padre']['edad'] ?? ''); ?>"
                                    placeholder="Edad del padre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Nacionalidad:</label>
                                <input type="text" name="padre_nacionalidad"
                                    value="<?php echo htmlspecialchars($datos['padre']['nacionalidad'] ?? ''); ?>"
                                    placeholder="Nacionalidad del padre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="padre_estado_civil">Estado Civil:</label>
                                <select name="padre_estado_civil" id="padre_estado_civil" class="form-control">
                                    <option value="Casado" <?php echo (isset($datos['padre']['estado_civil']) && $datos['padre']['estado_civil'] == 'Casado') ? 'selected' : ''; ?>>Casado</option>
                                    <option value="Soltero" <?php echo (isset($datos['padre']['estado_civil']) && $datos['padre']['estado_civil'] == 'Soltero') ? 'selected' : ''; ?>>Soltero</option>
                                    <option value="Divorciado" <?php echo (isset($datos['padre']['estado_civil']) && $datos['padre']['estado_civil'] == 'Divorciado') ? 'selected' : ''; ?>>Divorciado</option>
                                    <option value="Viudo" <?php echo (isset($datos['padre']['estado_civil']) && $datos['padre']['estado_civil'] == 'Viudo') ? 'selected' : ''; ?>>Viudo</option>
                                </select>
                            </div>                        
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Instrucción:</label>
                                <input type="text" name="padre_instruccion"
                                    value="<?php echo htmlspecialchars($datos['padre']['instruccion'] ?? ''); ?>"
                                    placeholder="Nivel de instrucción del padre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Visita:</label>
                                <select name="visita_padre">
                                    <option value="NO" <?php echo (isset($datos['padre']) && $datos['padre']['visita'] == 0) ? 'selected' : ''; ?>>No</option>
                                    <option value="SI" <?php echo (isset($datos['padre']) && $datos['padre']['visita'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>   
            <!-- Madre -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 py-0">Información de la Madre</h4>
                </div>
                <div class="card-body">                
                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" name="madre_nombre"
                                    value="<?php echo htmlspecialchars($datos['madre']['nombre'] ?? ''); ?>"
                                    placeholder="Nombre de la madre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Apellido:</label>
                                <input type="text" name="madre_apellido"
                                    value="<?php echo htmlspecialchars($datos['madre']['apellido'] ?? ''); ?>"
                                    placeholder="Apellido de la madre">
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Estado:</label>
                                <select name="madre_viva">
                                    <option value="Viva" <?php echo (isset($datos['madre']) && $datos['madre']['vivo'] == 1) ? 'selected' : ''; ?>>Viva</option>
                                    <option value="Fallecida" <?php echo (isset($datos['madre']) && $datos['madre']['vivo'] == 0) ? 'selected' : ''; ?>>Fallecida</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Edad:</label>
                                <input type="number" name="madre_edad"
                                    value="<?php echo htmlspecialchars($datos['madre']['edad'] ?? ''); ?>"
                                    placeholder="Edad de la madre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Nacionalidad:</label>
                                <input type="text" name="madre_nacionalidad"
                                    value="<?php echo htmlspecialchars($datos['madre']['nacionalidad'] ?? ''); ?>"
                                    placeholder="Nacionalidad de la madre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="madre_estado_civil">Estado Civil:</label>
                                <select name="madre_estado_civil" id="madre_estado_civil" class="form-control">
                                    <option value="Casado" <?php echo (isset($datos['madre']['estado_civil']) && $datos['madre']['estado_civil'] == 'Casado') ? 'selected' : ''; ?>>Casada</option>
                                    <option value="Soltero" <?php echo (isset($datos['madre']['estado_civil']) && $datos['madre']['estado_civil'] == 'Soltero') ? 'selected' : ''; ?>>Soltera</option>
                                    <option value="Divorciado" <?php echo (isset($datos['madre']['estado_civil']) && $datos['madre']['estado_civil'] == 'Divorciado') ? 'selected' : ''; ?>>Divorciada</option>
                                    <option value="Viudo" <?php echo (isset($datos['madre']['estado_civil']) && $datos['madre']['estado_civil'] == 'Viudo') ? 'selected' : ''; ?>>Viuda</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Instrucción:</label>
                                <input type="text" name="madre_instruccion"
                                    value="<?php echo htmlspecialchars($datos['madre']['instruccion'] ?? ''); ?>"
                                    placeholder="Nivel de instrucción de la madre">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Visita:</label>
                                <select name="visita_madre">
                                    <option value="NO" <?php echo (isset($datos['madre']) && $datos['madre']['visita'] == 0) ? 'selected' : ''; ?>>No</option>
                                    <option value="SI" <?php echo (isset($datos['madre']) && $datos['madre']['visita'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <!-- ------------ -->
                </div>
            </div>
            
            <!-- Hermanos -->
             <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 py-0">Información de Hermanos</h4>
                </div>
                <div class="card-body">                         
                    <div class="form-group">
                        <label>Número de Hermanos:</label>
                        <input type="number" name="num_hermanos" id="num_hermanos"
                            value="<?php echo count($datos['hermanos']); ?>"
                            placeholder="Número de hermanos" min="0">
                    </div>
                    <div id="hermanos-container">
                        <?php foreach ($datos['hermanos'] as $index => $hermano): ?>
                            <div class="hermano-grupo">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Nombre del Hermano <?php echo $index + 1; ?>:</label>
                                            <input type="text" name="hermano_nombre_<?php echo $index; ?>"
                                                value="<?php echo htmlspecialchars($hermano['nombre']); ?>"
                                                placeholder="Nombre">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Apellido:</label>
                                            <input type="text" name="hermano_apellido_<?php echo $index; ?>"
                                                value="<?php echo htmlspecialchars($hermano['apellido']); ?>"
                                                placeholder="Apellido">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Edad:</label>
                                            <input type="number" name="hermano_edad_<?php echo $index; ?>"
                                                value="<?php echo htmlspecialchars($hermano['edad']); ?>"
                                                placeholder="Edad">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Visita:</label>
                                            <select name="hermano_visita_<?php echo $index; ?>">
                                                <option value="NO" <?php echo ($hermano['visita'] == 0) ? 'selected' : ''; ?>>No</option>
                                                <option value="SI" <?php echo ($hermano['visita'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Pareja -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 py-0">Información de Pareja</h4>
                </div>
                <div class="card-body">
                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" name="pareja_nombre"
                                    value="<?php echo htmlspecialchars($datos['pareja']['nombre'] ?? ''); ?>"
                                    placeholder="Nombre de la pareja">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Apellido:</label>
                                <input type="text" name="pareja_apellido"
                                    value="<?php echo htmlspecialchars($datos['pareja']['apellido'] ?? ''); ?>"
                                    placeholder="Apellido de la pareja">
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                        <div class="form-group">
                            <label>Edad:</label>
                            <input type="number" name="pareja_edad"
                                value="<?php echo htmlspecialchars($datos['pareja']['edad'] ?? ''); ?>"
                                placeholder="Edad de la pareja">
                        </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                            <label>Nacionalidad:</label>
                            <input type="text" name="pareja_nacionalidad"
                                value="<?php echo htmlspecialchars($datos['pareja']['nacionalidad'] ?? ''); ?>"
                                placeholder="Nacionalidad de la pareja">
                        </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                            <div class="form-group">
                                <label>Instrucción:</label>
                                <input type="text" name="pareja_instruccion"
                                    value="<?php echo htmlspecialchars($datos['pareja']['instruccion'] ?? ''); ?>"
                                    placeholder="Nivel de instrucción de la pareja">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Tipo de Unión:</label>
                                <input type="text" name="pareja_tipo_union"
                                    value="<?php echo htmlspecialchars($datos['pareja']['tipo_union'] ?? ''); ?>"
                                    placeholder="Tipo de unión (matrimonio, concubinato, etc.)">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Visita:</label>
                                <select name="visita_esposo">
                                    <?php $isVisita = isset($datos['pareja']) && $datos['pareja']['visita'] == 1;?>
                                    <option value="NO" <?php echo !$isVisita ? 'selected' : ''; ?>>No</option>
                                    <option value="SI" <?php echo $isVisita ? 'selected' : ''; ?>>Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hijos -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 py-0">Información de Hijos</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tiene Hijos:</label>
                        <select name="tiene_hijos" id="tiene_hijos">
                            <option value="0" <?php echo (count($datos['hijos']) == 0) ? 'selected' : ''; ?>>No</option>
                            <option value="1" <?php echo (count($datos['hijos']) > 0) ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Número de Hijos:</label>
                        <input type="number" name="num_hijos" id="num_hijos"
                            value="<?php echo count($datos['hijos']); ?>"
                            placeholder="Número de hijos" min="0">
                    </div>
                    <div id="hijos-container">
                        <?php foreach ($datos['hijos'] as $index => $hijo): ?>
                            <div class="hijo-grupo">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                        <label>Nombre del Hijo <?php echo $index + 1; ?>:</label>
                                        <input type="text" name="hijo_nombre_<?php echo $index; ?>"
                                            value="<?php echo htmlspecialchars($hijo['nombre']); ?>"
                                            placeholder="Nombre">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                        <label>Apellido:</label>
                                        <input type="text" name="hijo_apellido_<?php echo $index; ?>"
                                            value="<?php echo htmlspecialchars($hijo['apellido']); ?>"
                                            placeholder="Apellido">
                                        </div>
                                    </div>
                                
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Edad:</label>
                                            <input type="number" name="hijo_edad_<?php echo $index; ?>"
                                                value="<?php echo htmlspecialchars($hijo['edad']); ?>"
                                                placeholder="Edad">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Fallecido:</label>
                                            <input type="checkbox" name="hijo_fallecido_<?php echo $index; ?>"
                                                <?php echo ($hijo['fallecido'] == 1) ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                    
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Visita:</label>
                                            <select name="hijo_visita_<?php echo $index; ?>">
                                                <option value="NO" <?php echo ($hijo['visita'] == 0) ? 'selected' : ''; ?>>No</option>
                                                <option value="SI" <?php echo ($hijo['visita'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Otros Visitantes -->
            <div class="card mt-4 border border-black">
                <div class="card-header bg-primary text-white">
                    <h4 class="my-0 py-0">Otros Visitantes</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Visita de Otros:</label>
                        <select name="visita_otros" id="visita_otros">
                            <option value="NO" <?php echo (count($datos['otros_visitantes']) == 0) ? 'selected' : ''; ?>>No</option>
                            <option value="SI" <?php echo (count($datos['otros_visitantes']) > 0) ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                    <div id="otros-visitantes-container">
                        <?php foreach ($datos['otros_visitantes'] as $index => $visitante): ?>
                            <div class="visitante-grupo">
                                <div class="row">
                                    <div class="form-group col">
                                        <label>Nombre:</label>
                                        <input type="text" name="otro_nombre[]"
                                            value="<?php echo htmlspecialchars($visitante['nombre']); ?>"
                                            placeholder="Nombre del visitante">
                                    </div>
                                    <div class="form-group col">
                                        <label>Apellido:</label>
                                        <input type="text" name="otro_apellido[]"
                                            value="<?php echo htmlspecialchars($visitante['apellido']); ?>"
                                            placeholder="Apellido">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label>Teléfono:</label>
                                        <input type="tel" class="form-control" name="otro_telefono[]"
                                            value="<?php echo htmlspecialchars($visitante['telefono']); ?>"
                                            placeholder="Teléfono">
                                    </div>
                                    <div class="form-group col">
                                        <label>Domicilio:</label>
                                        <input type="text" name="otro_domicilio[]"
                                            value="<?php echo htmlspecialchars($visitante['domicilio']); ?>"
                                            placeholder="Domicilio">
                                    </div>
                                    <div class="form-group col">
                                        <label>Vínculo Filial:</label>
                                        <input type="text" name="otro_vinculo[]"
                                            value="<?php echo htmlspecialchars($visitante['vinculo_filial']); ?>"
                                            placeholder="Vínculo familiar">
                                    </div>

                                </div>      
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="actualizar_datos_familiares" class="btn btn-primary ml-2">
                        Actualizar Información Familiar
                    </button>
                </div>
                


            </div>

            <!-- Botón de Actualización -->
                
        </form>
        </div>
    </div>
</section>    
<!-- ----------------------------- -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para manejar la adición de hijos
        function configurarAdicionHijos() {
    const numHijosInput = document.getElementById('num_hijos');
    const hijosContainer = document.getElementById('hijos-container');
    
    // Almacenar una copia de los hijos existentes
    let hijosSalvados = [];

    // Función para actualizar los campos de hijos
    function actualizarCamposHijos() {
        const numHijos = parseInt(numHijosInput.value) || 0;
        const hijosActuales = hijosContainer.children.length;
        
        // Si se reduce a cero, guardar los hijos actuales
        if (numHijos === 0 && hijosActuales > 0) {
            hijosSalvados = Array.from(hijosContainer.children).map(hijoGrupo => {
                // Extraer valores de los inputs
                const inputs = hijoGrupo.querySelectorAll('input, select');
                return Array.from(inputs).map(input => ({
                    name: input.name,
                    value: input.type === 'checkbox' ? input.checked : input.value,
                    type: input.type
                }));
            });
            
            // Limpiar el contenedor
            hijosContainer.innerHTML = '';
        }

        // Agregar nuevos campos de hijos
        if (numHijos > hijosActuales) {
            for (let i = hijosActuales; i < numHijos; i++) {
                const nuevoHijo = document.createElement('div');
                nuevoHijo.className = 'hijo-grupo';
                nuevoHijo.innerHTML = `
                    <div class="form-row">
                        <div class="form-group col">
                            <label>Nombre del Hijo ${i + 1}:</label>
                            <input type="text" class="form-control" name="hijo_nombre_${i}" placeholder="Nombre">
                        </div>
                        <div class="form-group col">
                            <label>Apellido:</label>
                            <input type="text" class="form-control" name="hijo_apellido_${i}" placeholder="Apellido">
                        </div>                    

                        <div class="form-group col">
                            <label>Edad:</label>
                            <input type="number" name="hijo_edad_${i}"
                                placeholder="Edad">
                        </div>
                        <div class="form-group col">
                            <label>Fallecido:</label>
                            <input type="checkbox" name="hijo_fallecido_${i}">
                        </div>
                        <div class="form-group col">
                            <label>Visita:</label>
                            <select name="hijo_visita_${i}">
                                <option value="NO">No</option>
                                <option value="SI">Sí</option>
                            </select>
                        </div>
                    </div>
                `;
                hijosContainer.appendChild(nuevoHijo);
            }

            // Restaurar información guardada si existe
            if (hijosSalvados.length > 0) {
                hijosSalvados.forEach((hijoSalvado, index) => {
                    if (index < numHijos) {
                        const inputs = hijosContainer.children[index].querySelectorAll('input, select');
                        hijoSalvado.forEach((inputData, inputIndex) => {
                            if (inputs[inputIndex]) {
                                inputs[inputIndex].name = inputData.name;
                                if (inputData.type === 'checkbox') {
                                    inputs[inputIndex].checked = inputData.value;
                                } else {
                                    inputs[inputIndex].value = inputData.value;
                                }
                            }
                        });
                    }
                });
                // Limpiar hijos salvados después de restaurar
                hijosSalvados = [];
            }
        }
        // Eliminar campos de hijos en exceso
        else if (numHijos < hijosActuales) {
            while (hijosContainer.children.length > numHijos) {
                hijosContainer.removeChild(hijosContainer.lastChild);
            }
        }
    }

    // Agregar múltiples event listeners para detectar cambios
    numHijosInput.addEventListener('input', actualizarCamposHijos);
    numHijosInput.addEventListener('keyup', actualizarCamposHijos);
    numHijosInput.addEventListener('change', actualizarCamposHijos);

    // Actualizar campos inmediatamente si hay un valor inicial
    actualizarCamposHijos();
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', configurarAdicionHijos);
        // Función para manejar la adición de hermanos
        function configurarAdicionHermanos() {
    const numHermanosInput = document.getElementById('num_hermanos');
    const hermanosContainer = document.getElementById('hermanos-container');
    
    // Almacenar una copia de los hermanos existentes
    let hermanosSalvados = [];

    // Función que maneja la actualización de campos
    function actualizarCamposHermanos() {
        const numHermanos = parseInt(numHermanosInput.value) || 0;
        const hermanosActuales = hermanosContainer.children.length;

        // Si se reduce a cero, guardar los hermanos actuales
        if (numHermanos === 0 && hermanosActuales > 0) {
            hermanosSalvados = Array.from(hermanosContainer.children).map(hermanoGrupo => {
                // Extraer valores de los inputs
                const inputs = hermanoGrupo.querySelectorAll('input, select');
                return Array.from(inputs).map(input => ({
                    name: input.name,
                    value: input.value,
                    type: input.type
                }));
            });
            
            // Limpiar el contenedor
            hermanosContainer.innerHTML = '';
        }

        // Agregar nuevos campos de hermanos
        if (numHermanos > hermanosActuales) {
            for (let i = hermanosActuales; i < numHermanos; i++) {
                const nuevoHermano = document.createElement('div');
                nuevoHermano.className = 'hermano-grupo';
                nuevoHermano.innerHTML = `
                <div class="form-row">
                    <div class="form-group col">
                        <label>Nombre del Hermano ${i + 1}:</label>
                        <input type="text" name="hermano_nombre_${i}" 
                            placeholder="Nombre">
                    </div>
                    <div class="form-group col">
                        <label>Apellido:</label>
                        <input type="text" name="hermano_apellido_${i}" 
                            placeholder="Apellido">
                    </div>
                    <div class="form-group col">
                        <label>Edad:</label>
                        <input type="number" name="hermano_edad_${i}" 
                            placeholder="Edad">
                    </div>
                    <div class="form-group col">
                        <label>Visita:</label>
                        <select name="hermano_visita_${i}">
                            <option value="NO">No</option>
                            <option value="SI">Sí</option>
                        </select>
                    </div>
                </div>
                `;
                hermanosContainer.appendChild(nuevoHermano);
            }

            // Restaurar información guardada si existe
            if (hermanosSalvados.length > 0) {
                hermanosSalvados.forEach((hermanoSalvado, index) => {
                    if (index < numHermanos) {
                        const inputs = hermanosContainer.children[index].querySelectorAll('input, select');
                        hermanoSalvado.forEach((inputData, inputIndex) => {
                            if (inputs[inputIndex]) {
                                inputs[inputIndex].name = inputData.name;
                                inputs[inputIndex].value = inputData.value;
                            }
                        });
                    }
                });
                // Limpiar hermanos salvados después de restaurar
                hermanosSalvados = [];
            }
        }
        // Eliminar campos de hermanos en exceso
        else if (numHermanos < hermanosActuales) {
            while (hermanosContainer.children.length > numHermanos) {
                hermanosContainer.removeChild(hermanosContainer.lastChild);
            }
        }
    }

    // Agregar múltiples eventos para capturar cualquier cambio
    numHermanosInput.addEventListener('input', actualizarCamposHermanos);
    numHermanosInput.addEventListener('keyup', actualizarCamposHermanos);
    numHermanosInput.addEventListener('change', actualizarCamposHermanos);
}
        // Función para manejar la adición de otros visitantes
function configurarAdicionVisitantes() {
            const visitaOtrosSelect = document.getElementById('visita_otros');
            const visitantesContainer = document.getElementById('otros-visitantes-container');
            visitaOtrosSelect.addEventListener('change', function() {
                if (this.value === 'SI') {
                    // Si no hay visitantes, agregar un primer grupo de visitante
                    if (visitantesContainer.children.length === 0) {
                        agregarNuevoVisitante();
                    }
                } else {
                    // Limpiar todos los visitantes
                    visitantesContainer.innerHTML = '';
                }
            });
            // Botón para agregar más visitantes
            const agregarVisitanteBtn = document.createElement('button');
            agregarVisitanteBtn.type = 'button';
            agregarVisitanteBtn.textContent = 'Agregar Otro Visitante';
            agregarVisitanteBtn.className = 'btn btn-warning'; // Agregar clases de Bootstrap
            agregarVisitanteBtn.addEventListener('click', agregarNuevoVisitante);

            function agregarNuevoVisitante() {
                const index = visitantesContainer.children.length;
                const nuevoVisitante = document.createElement('div');
                nuevoVisitante.className = 'visitante-grupo';
                nuevoVisitante.innerHTML = `
                <hr class="border-dark my-4">
                    <div class="form-row">
                        <div class="form-group col">
                            <label>Nombre:</label>
                            <input type="text" name="otro_nombre[]" 
                                placeholder="Nombre del visitante">
                        </div>
                        <div class="form-group col">
                            <label>Apellido:</label>
                            <input type="text" name="otro_apellido[]" 
                                placeholder="Apellido">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label>Teléfono:</label>
                            <input type="tel" class="form-control" name="otro_telefono[]" 
                                placeholder="Teléfono">
                        </div>
                        <div class="form-group col">
                            <label>Domicilio:</label>
                            <input type="text" name="otro_domicilio[]" 
                                placeholder="Domicilio">
                        </div>
                        <div class="form-group col">
                            <label>Vínculo Filial:</label>
                            <input type="text" name="otro_vinculo[]" 
                                placeholder="Vínculo familiar">
                        </div>
                    </div>    
            <button type="button" class="btn btn-danger btn-eliminar-visitante mb-3">Eliminar Visitante</button>
        `;
                // Agregar botón de eliminación para cada visitante
                nuevoVisitante.querySelector('.btn-eliminar-visitante').addEventListener('click', function() {
                    visitantesContainer.removeChild(nuevoVisitante);
                });
                visitantesContainer.appendChild(nuevoVisitante);
                // Cambiar el select a 'SI' si no lo está
                document.getElementById('visita_otros').value = 'SI';
            }
            // Agregar botón de agregar visitante después del contenedor
            visitantesContainer.parentNode.insertBefore(agregarVisitanteBtn, visitantesContainer.nextSibling);
        }

        // Inicializar funcionalidades
        configurarAdicionHijos();
        configurarAdicionHermanos();
        configurarAdicionVisitantes();
    });
    // Script para mostrar/ocultar detalles adicionales
    document.getElementById('familiares_ffaa').addEventListener('change', function() {
        document.getElementById('ffaa_details').style.display =
            this.value === '1' ? 'block' : 'none';
    });
    document.getElementById('familiares_detenidos').addEventListener('change', function() {
        document.getElementById('detenidos_details').style.display =
            this.value === '1' ? 'block' : 'none';
    });
    document.getElementById('posee_dni').addEventListener('change', function() {
        document.getElementById('motivo_no_dni_section').style.display =
            this.value === 'NO' ? 'block' : 'none';
    });
</script>

<?php require 'footer.php'; ?>