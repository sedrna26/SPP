<?php require 'navbar.php'; ?>
<?php
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

function insertarPersona($db, $datosPersona)
{
    $sql = "INSERT INTO persona (dni, apellidos, nombres, fechanac, edad, genero, estadocivil) 
            VALUES (:dni, :apellidos, :nombres, :fechanac, :edad, :genero, :estadocivil)";
    $stmt = $db->prepare($sql);
    $stmt->execute($datosPersona);
    return $db->lastInsertId();
}

function insertarPPL($db, $datosPPL)
{
    $sql = "INSERT INTO ppl (idpersona, apodo, trabaja, profesion, huella, foto) 
            VALUES (:idpersona, :apodo, :trabaja, :profesion, :huella, :foto)";
    $stmt = $db->prepare($sql);
    $stmt->execute($datosPPL);
    return $db->lastInsertId();
}


function insertarPPLCausa($db, $id_ppl, $id_causa, $id_situacionlegal)
{
    $sql = "INSERT INTO ppl_causas (id_ppl, id_causa, id_situacionlegal) 
            VALUES (:id_ppl, :id_causa, :id_situacionlegal)";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id_ppl' => $id_ppl, ':id_causa' => $id_causa, ':id_situacionlegal' => $id_situacionlegal]);
}

function insertarSituacionLegal($db, $datosSituacion)
{
    $estado = isset($datosSituacion['estado']) ? $datosSituacion['estado'] : 'Activo';

    $sql = "INSERT INTO situacionlegal (id_ppl,
                fecha_detencion, 
                dependencia, 
                motivo_t, 
                id_juzgado, 
                situacionlegal, 
                en_prejucio, 
                condena, 
                categoria, 
                reingreso_falta, 
                causas_pend, 
                cumplio_medida, 
                causa_nino, 
                asistio_rehabi, 
                tiene_defensor, 
                nombre_defensor, 
                tiene_com_defensor,
                estado
            ) 
            VALUES (
                :id_ppl,
                :fecha_detencion, 
                :dependencia, 
                :motivo_t, 
                :id_juzgado, 
                :situacionlegal, 
                :en_prejucio, 
                :condena, 
                :categoria, 
                :reingreso_falta, 
                :causas_pend, 
                :cumplio_medida, 
                :causa_nino, 
                :asistio_rehabi, 
                :tiene_defensor, 
                :nombre_defensor, 
                :tiene_com_defensor,
                :estado
            )";
    $stmt = $db->prepare($sql);
    $datosSituacion['estado'] = $estado;  
    $stmt->execute($datosSituacion);
    return $db->lastInsertId();
}

function insertarPPLCausas($db, $id_persona, $causas)
{
   
    foreach ($causas as $id_causa) {
        $sql = "INSERT INTO ppl_causas (id_ppl, id_causa) VALUES (:id_ppl, :id_causa)";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id_ppl' => $id_persona, ':id_causa' => $id_causa]);
    }
}


function insertarFechaPPL($db, $id_persona, $inicio_condena, $fin_condena)
{
    $sql = "INSERT INTO fechappl (idppl, inicio_condena, fin_condena) 
            VALUES (:idppl, :inicio_condena, :fin_condena)";
    $stmt = $db->prepare($sql);
    $stmt->execute([':idppl' => $id_persona, ':inicio_condena' => $inicio_condena, ':fin_condena' => $fin_condena]);
}

function agregarDireccion($db, $id_persona, $direccionp)
{
    $idubicacionValue = $direccionp;

    if ($direccionp === 'new') {
        $pais = $_POST['pais'];
        $provincia = $_POST['provincia'];
        $ciudad = $_POST['ciudad'];
        $localidad = $_POST['localidad'];
        $direccion = $_POST['direccion'];

        $sql_ubicacion = "INSERT INTO domicilio (id_pais, id_persona, id_provincia, id_ciudad, localidad, direccion) 
                          VALUES (:pais, :id_persona, :provincia, :ciudad, :localidad, :direccion)";
        $stmt = $db->prepare($sql_ubicacion);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':provincia', $provincia);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':localidad', $localidad);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':id_persona', $id_persona);
        $stmt->execute();

        $idubicacionValue = $db->lastInsertId();
    }

    $sql_actualiza_direccion = "UPDATE persona SET direccion = :id_domicilio WHERE id = :id_persona";
    $stmt = $db->prepare($sql_actualiza_direccion);
    $stmt->bindParam(':id_domicilio', $idubicacionValue);
    $stmt->bindParam(':id_persona', $id_persona);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db->beginTransaction();
        $datosPersona = [
            ':dni' => $_POST['dni'],
            ':apellidos' => $_POST['apellidos'],
            ':nombres' => $_POST['nombres'],
            ':fechanac' => $_POST['fechanac'],
            ':edad' => $_POST['edad'],
            ':genero' => $_POST['genero'],
            ':estadocivil' => $_POST['estadocivil'],
        ];
        $id_persona = insertarPersona($db, $datosPersona);

        $direccionp = $_POST['direccionp'];
        agregarDireccion($db, $id_persona, $direccionp);
        //Seccion para crear la carpeta img_ppl
        $upload_dir = '../../img_ppl';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Traigo la foto del POST
        $foto = $_FILES['foto']['name'];
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_temp = $_FILES['foto']['tmp_name'];
            $original_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));        
            // genera un nombre mas corto y unico
            $foto_nombre = substr(hash('md5', uniqid('', true)), 0, 10) . '.' . $original_ext;
            $foto_path = $upload_dir . '/' . $foto_nombre;
            if (move_uploaded_file($foto_temp, $foto_path)) {
                $foto = $foto_nombre; // Save only the filename in the database
            } else {
                echo "Error al mover el archivo.";
                exit;
            }
        }

        $datosPPL = [
            ':idpersona' => $id_persona,  
            ':apodo' => $_POST['apodo'] ?? '',
            ':trabaja' => ($_POST['trabaja'] === 'Si') ? 1 : 0,
            ':profesion' => $_POST['profesion'] ?? '',
            ':huella' => null, 
            ':foto' => $foto,
        ];
        $id_ppl = insertarPPL($db, $datosPPL);
   
        $datosSituacion = [
            ':id_ppl' => $id_persona,
            ':fecha_detencion' => $_POST['fecha_detencion'],
            ':dependencia' => $_POST['dependencia'],
            ':motivo_t' => $_POST['motivo_t'],
            ':id_juzgado' => $_POST['id_juzgado'],
            ':situacionlegal' => $_POST['situacionlegal'],
            ':en_prejucio' => $_POST['en_prejucio'],
            ':condena' => $_POST['condena'],
            ':categoria' => $_POST['categoria'],
            ':reingreso_falta' => ($_POST['reingreso_falta'] ?? 'no') === 'si' ? 1 : 0,  
            ':causas_pend' => ($_POST['causas_pend'] ?? 'no') === 'si' ? 1 : 0,  
            ':cumplio_medida' => ($_POST['cumplio_medida'] ?? 'no') === 'si' ? 1 : 0,  
            ':causa_nino' => ($_POST['causa_nino'] ?? 'no') === 'si' ? 1 : 0,  
            ':asistio_rehabi' => ($_POST['asistio_rehabi'] ?? 'no') === 'si' ? 1 : 0,  
            ':tiene_defensor' => ($_POST['tiene_defensor'] ?? 'no') === 'si' ? 1 : 0,  
            ':nombre_defensor' => $_POST['nombre_defensor'],
            ':tiene_com_defensor' => ($_POST['tiene_com_defensor'] ?? 'no') === 'si' ? 1 : 0,  
          
        ];
        $id_situacionlegal = insertarSituacionLegal($db, $datosSituacion);

        
        if (isset($_POST['causas']) && is_array($_POST['causas'])) {
          
            foreach ($_POST['causas'] as $id_causa) {
                
                insertarPPLCausa($db, $id_persona, $id_causa, $id_situacionlegal);
            }
        } else {
            echo "No se seleccionaron causas.";
        }
    
        insertarFechaPPL($db, $id_persona, $_POST['inicio_condena'], $_POST['fin_condena']);

        registrarAuditoria($db, 'Agregar PPL', 'situacionlegal', $id_persona, 'Situación legal y Datos  de PPL creado' . $id_persona);

        $db->commit();

        header("Location: ppl_index.php?mensaje=" . urlencode("PPL creado con éxito."));
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<style>
    input[type="radio"] {
        width: 20px;
        height: 20px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: white;
        border: 2px solid #007bff;
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        display: inline-block;
        vertical-align: middle;
    }

    .hidden {
        display: none;
    }

    input[type="radio"]:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    input[type="radio"]:checked::after {
        content: '✔';
        font-size: 14px;
        color: white;
        font-family: Arial, sans-serif;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-weight: bold;
    }

    .is-invalid {
        border: 10px solid red;
    }
</style>
<!-- --------------------------- -->
<section class="container mt-3">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="d-inline-block ">Nuevo Informe de Evaluación Integral Interdisciplinario (IEII)</h5>
            <!-- <a class="btn btn-primary float-right mb-2" href="">(boton)</a>                     -->
        </div>
        <div class="card-body table-responsive">
            <form id="personaForm" action="" method="POST" enctype="multipart/form-data">
                <h4 class="d-inline">Datos de la Persona</h4>
                <div class=" mt-4 d-inline">
                    <h4 class="text-danger fst-italic d-inline">*</h4>
                    <h6 class="text-danger small fst-italic d-inline">Campo requerido</h6>
                </div>

                <input type="hidden" name="id_persona" value="<?php echo $id_persona; ?>">
                <div class="row mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="dni">DNI:</label>
                            <input type="number" class="form-control" id="dni" name="dni" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="nombres">Nombre/s:</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="apellidos">Apellido/s:</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                        </div>
                    </div>
                </div>
                <!-- --------------------------- -->
                <div class="form-group">
                    <label for="direccionp" class="form-label">Seleccionar Ubicación</label>
                    <select class="form-select" id="direccionp" name="direccionp" onchange="toggleNewLocation()" required>
                        <option value="">-- Seleccione una ubicación --</option>
                        <option style="color: green;" value="new">Agregar nueva ubicación</option>
                        <!-- Lo he eliminado corque causa error a la hora de ingresar los datos.
                        Tampoco es escalable a la hora de tener muchas consultas.
                        <?php
                        $resultado = $db->query("SELECT 
                                                    d.id AS id_direccion, 
                                                    p.nombre AS pais, 
                                                    pr.nombre AS provincia, 
                                                    c.nombre AS ciudad, 
                                                    d.localidad, 
                                                    d.direccion 
                                                FROM 
                                                    domicilio d
                                                LEFT JOIN 
                                                    paises p ON d.id_pais = p.id
                                                LEFT JOIN 
                                                    provincias pr ON d.id_provincia = pr.id
                                                LEFT JOIN 
                                                    ciudades c ON d.id_ciudad = c.id");
                        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$fila['id_direccion']}'>{$fila['pais']} - {$fila['provincia']} - {$fila['ciudad']} - {$fila['localidad']} - {$fila['direccion']}</option>";
                        }
                        ?> -->
                    </select>
                </div>
                <!-- --------------------------- -->
                <div id="newLocationSection" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pais" class="form-label">País</label>
                            <select class="form-select" id="pais" name="pais" onchange="loadProvincias()">
                                <option value="">-- Seleccione un País --</option>
                                <?php
                                $resultado = $db->query("SELECT id, nombre FROM paises
                                ORDER BY nombre ASC;");
                                while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$fila['id']}'>{$fila['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="provincia" class="form-label">Provincia</label>
                            <select class="form-select" id="provincia" name="provincia" onchange="loadCiudades()">
                                <option value="">-- Seleccione una Provincia --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <select class="form-select" id="ciudad" name="ciudad">
                                <option value="">-- Seleccione una Ciudad --</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="localidad" class="form-label">Localidad</label>
                            <input type="text" class="form-control" id="localidad" name="localidad">
                        </div>
                        <div class="col-md-5">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                    </div>
                </div>
                <!-- --------------------------- -->
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="fechanac">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control" id="fechanac" name="fechanac" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="edad">Edad:</label>
                            <input type="number" class="form-control" id="edad" name="edad" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="genero">Género:</label>
                            <select class="form-control" id="genero" name="genero" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="estadocivil">Estado Civil:</label>
                            <select class="form-control" id="estadocivil" name="estadocivil" required>
                                <option value="Soltero">Soltero</option>
                                <option value="Casado">Casado</option>
                                <option value="Divorciado">Divorciado</option>
                                <option value="Viudo">Viudo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Sección PPL -->
                <div>
                    <h4 class="mt-4">Datos de PPL</h4>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="profesion" class="form-label">Profesión</label>
                                <input type="text" class="form-control" id="profesion" name="profesion" pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="trabaja" class="form-label">¿Trabajaba en el momento de la detención?:</label>
                                <select class="form-select" id="trabaja" name="trabaja" required>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- --------------------- -->
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto"
                            accept=".jpg,.jpeg,.png,.svg,.ico,.tga,.dds,.ai,image/jpeg,image/png,image/svg+xml,image/x-icon,image/tga,image/x-dds,application/postscript"
                            onchange="previewImage(event)">
                        <!-- <br>
                        <img id="Foto" class="form-control" style="max-width: 150px; height: 100px;" alt="Foto de PERSONA"> -->
                    </div>
                    <div class="mb-3">
                        <label for="huella" class="form-label">Huella</label>
                        <input type="file" class="form-control" id="huella" name="huella">
                    </div>
                </div>
                <!-- Sección Situación Legal -->
                <div>
                    <h4 class="mt-4">Datos de Situación Legal</h4>
                    <input type="hidden" id="id_ppl" name="id_ppl" value="<?php echo $id_ppl; ?>">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="fecha_detencion">Fecha de detención:</label>
                                <input type="date" class="form-control" id="fecha_detencion" name="fecha_detencion" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="inicio_condena">Inicio de Condena:</label>
                                <input type="date" class="form-control" id="inicio_condena" name="inicio_condena" required>
                            </div>
                        </div>
                        <script>
                            const fecha = new Date();
                            const offset = fecha.getTimezoneOffset();
                            fecha.setMinutes(fecha.getMinutes() - offset);
                            const hoy = fecha.toISOString().split('T')[0];
                            document.getElementById('fecha_detencion').value = hoy;
                            document.getElementById('inicio_condena').value = hoy;
                        </script>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="dependencia">Dependencia:</label>
                                <input type="text" class="form-control" id="dependencia" name="dependencia" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="motivo_t">Motivo de traslado:</label>
                                <input type="text" class="form-control" id="motivo_t" name="motivo_t" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="situacionlegal">Situación Legal:</label>
                                <select class="form-select" id="situacionlegal" name="situacionlegal" required>
                                    <option value="Penado">Penado</option>
                                    <option value="Procesado">Procesado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- -------------------------------- -->
                    <div class="form-group">
                        <label for="buscar-causas">Buscar Causas:</label>
                        <input type="text" id="buscar-causas" class="form-control" placeholder="Escribe para buscar..." onkeyup="filtrarCausas()">
                        <label for="causas">Causas:</label>
                        <div id="causas-container" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Causa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $resultado = $conexion->query("
                                        SELECT d.id_delito, d.nombre, t.id_tipo_delito 
                                        FROM delitos d 
                                        LEFT JOIN tiposdelito t ON d.id_tipo_delito = t.id_tipo_delito;
                                    ");
                                    $i = 0;
                                    while ($fila = $resultado->fetch_assoc()) {
                                        echo "<tr class='causa-checkbox'>
                                            <td>
                                                <input type='checkbox' id='causa_{$fila['id_delito']}' name='causas[]' value='{$fila['id_delito']}' onclick='updateSelectedCausas()'>
                                            </td>
                                            <td><label for='causa_{$fila['id_delito']}'>{$fila['nombre']}</label></td>
                                        </tr>";
                                        $i++;
                                    }
                                    
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <p id="selected-causas-text" style="margin-top: 10px;">Selecciona hasta 4 causas.</p>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="toggleNewDelitoSection()">Añadir nueva causa</button>
                    <br>
                    <!-- Sección para agregar nuevo delito -->
                    <div id="newDelitoSection" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipodelito" class="form-label">Tipo de delito</label>
                                <select class="form-select" id="tipodelito" name="tipodelito">
                                    <option value="">-- Seleccione el tipo de delito --</option> <!-- Opción predeterminada -->
                                    <?php
                                    // Código PHP para obtener los tipos de delito
                                    $resultado = $db->query("SELECT id_tipo_delito, nombre FROM tiposdelito
                                                            ORDER BY nombre ASC;");
                                    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                        // Cambia 'pais' a 'nombre' para que coincida con la columna seleccionada en la consulta SQL
                                        echo "<option value='{$fila['id_tipo_delito']}'>{$fila['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="delito" class="form-label">Delito</label>
                                <input class="form-control" type="text" name="delito" id="delito">
                            </div>
                        </div>
                    </div>
                    <br>
                    <!-- -------------------- -->
                    <div>
                        <div class="form-group">
                            <label for="en_prejucio">En perjuicio de quien (si la causa es intrafamiliar):</label>
                            <input type="text" class="form-control" id="en_prejucio" name="en_prejucio">
                        </div>
                    </div>
                    <!-- -------------------- -->
                    <div class="form-group">
                        <label for="id_juzgado">Juzgado:</label>
                        <select class="form-select" id="id_juzgado" name="id_juzgado" onchange="toggleNewjuez()" required>
                            <option value="">-- Seleccione un Juez --</option>
                            <option style="color: green;" value="new">Agregar nuevo juez</option>
                            <?php
                            // Código PHP para obtener juzgados de la base de datos
                            $resultado = $conexion->query("SELECT id, nombre, nombre_juez FROM juzgado");
                            while ($fila = $resultado->fetch_assoc()) {
                                echo "<option value='{$fila['id']}'>{$fila['nombre']} - {$fila['nombre_juez']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Sección para agregar un nuevo juez -->
                    <div id="newjuezSection" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre del juzgado</label>
                                <input type="text" class="form-control" id="nombre" name="nombre">
                            </div>

                            <div class="col-md-6">
                                <label for="nombrejuez" class="form-label">Nombre del juez</label>
                                <input type="text" class="form-control" id="nombrejuez" name="nombrejuez">
                            </div>
                        </div>
                    </div>
                    <!-- ------------------------------ -->
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="condena">Condena:</label>
                                <input type="text" class="form-control" id="condena" name="condena" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="categoria">Categoria:</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="Primario">Primario</option>
                                    <option value="Reiterante">Reiterante</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="causas_pend">Causas pendientes de resolución:</label>
                                <input type="text" class="form-control" id="causas_pend" name="causas_pend" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Reingreso en caso de quebrantamiento de beneficio y/o libertad:
                            <input type="radio" name="reingreso_falta" value="si" required checked> Sí
                            <input type="radio" name="reingreso_falta" value="no" required> No
                        </label>
                    </div>
                    <div class="form-group">
                        <label>¿Causas judicializadas durante la niñez o adolescencia?:
                            <input type="radio" name="causa_nino" value="si" required checked> Sí
                            <input type="radio" name="causa_nino" value="no" required> No
                        </label>
                    </div>
                    <div class="form-group">
                        <label>¿Cumplió medidas socioeducativas?:
                            <input type="radio" name="cumplio_medida" value="si" required checked> Sí
                            <input type="radio" name="cumplio_medida" value="no" required> No
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Institucionalizaciones en centros de rehabilitación por conflictos con la ley:
                            <input type="radio" name="asistio_rehabi" value="si" required checked> Sí
                            <input type="radio" name="asistio_rehabi" value="no" required> No
                        </label>
                    </div>
                    <!-- -----------DEFENSOR OFICIAL--------- -->
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>¿Cuenta con un defensor oficial?:
                                    <input type="radio" name="tiene_defensor" value="si" required> Sí
                                    <input type="radio" name="tiene_defensor" value="no" required checked> No
                                </label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group hidden" id="nombreDefensorDiv">
                                <label for="nombre_defensor">¿Quién?:</label>
                                <input type="text" class="form-control" id="nombre_defensor" name="nombre_defensor">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group hidden" id="tieneComDefensorDiv">
                                <label for="tiene_com_defensor">¿Tiene comunicación con él?:</label>
                                <select class="form-select" id="tiene_com_defensor" name="tiene_com_defensor">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary mb-3">Guardar Nuevo PPL</button>
        </div>
    </div>
    </form>
    </div>
    </div>
    </div>
</section>
<script>
    function updateSelectedCausas() {
        let selectedCausas = [];
        let checkboxes = document.querySelectorAll("input[name='causas[]']:checked");
        checkboxes.forEach(function(checkbox) {
            selectedCausas.push(checkbox.value);
        });
        document.getElementById('causas').value = selectedCausas.join(',');
    }
</script>
<script>
    function loadCiudades() {
        var provinciaId = document.getElementById('provincia').value;
        if (provinciaId) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "getCiudades.php?provincia_id=" + provinciaId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('ciudad').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        } else {
            document.getElementById('ciudad').innerHTML = "<option value=''>-- Seleccione una Ciudad --</option>";
        }
    }

    function loadProvincias() {
        var paisId = document.getElementById('pais').value;
        if (paisId) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "getProvincias.php?pais_id=" + paisId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('provincia').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    }
</script>
<script>
    function toggleDefensorFields() {
        const tieneDefensorRadios = document.getElementsByName('tiene_defensor');
        const nombreDefensorDiv = document.getElementById('nombreDefensorDiv');
        const tieneComDefensorDiv = document.getElementById('tieneComDefensorDiv');
        tieneDefensorRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'si') {
                    nombreDefensorDiv.classList.remove('hidden');
                    tieneComDefensorDiv.classList.remove('hidden');
                    document.getElementById('nombre_defensor').required = true;
                    document.getElementById('tiene_com_defensor').required = true;
                } else {
                    nombreDefensorDiv.classList.add('hidden');
                    tieneComDefensorDiv.classList.add('hidden');
                    document.getElementById('nombre_defensor').required = false;
                    document.getElementById('tiene_com_defensor').required = false;
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        const tieneDefensorSi = document.querySelector('input[name="tiene_defensor"][value="si"]');
        if (tieneDefensorSi.checked) {
            document.getElementById('nombreDefensorDiv').classList.remove('hidden');
            document.getElementById('tieneComDefensorDiv').classList.remove('hidden');
        }
        toggleDefensorFields();
    });
</script>
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
<script>
    const checkboxes = document.querySelectorAll('.causa-checkbox input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const selectedCheckboxes = document.querySelectorAll('.causa-checkbox input[type="checkbox"]:checked');
            const maxCausas = 4;
            const minCausas = 1;
            if (selectedCheckboxes.length > maxCausas) {
                alert("Solo puedes seleccionar un máximo de 4 causas.");
                this.checked = false;
            }
            if(selectedCheckboxes.length < minCausas){
                alert("Debe seleccionar al menos una causa.");
                this.checked = true;
            }
        });
    });
</script>
<?php require 'footer.php'; ?>