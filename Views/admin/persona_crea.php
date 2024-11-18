<?php require 'navbar.php'; ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos de la tabla persona
    $dni = $_POST['dni'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $fechanac = $_POST['fechanac'];
    $edad = $_POST['edad'];
    $genero = $_POST['genero'];
    $estadocivil = $_POST['estadocivil'];
    $direccionp = $_POST['direccionp'];

    // Manejar ubicación
    $idubicacionValue = $direccionp;
    if ($direccionp === 'new') {
        $pais = $_POST['pais'];
        $provincia = $_POST['provincia'];
        $ciudad = $_POST['ciudad'];
        $localidad = $_POST['localidad'];
        $direccion = $_POST['direccion'];

        $sql_ubicacion = "INSERT INTO domicilio (id_pais, id_provincia, id_ciudad, localidad, direccion) 
                          VALUES (:pais, :provincia, :ciudad, :localidad, :direccion)";
        $stmt = $db->prepare($sql_ubicacion);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':provincia', $provincia);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':localidad', $localidad);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();

        $idubicacionValue = $db->lastInsertId(); // Obtiene el ID de la nueva ubicación
        echo "Inserción en ubicación exitosa.<br>";
    } else {
        // Si se seleccionó una ubicación existente
        // Procesa el id_direccion
        $id_direccion = $direccionp;

        // Aquí puedes realizar otras acciones necesarias, como usar el ID para algo específico
    }

    // Inserción de datos en la tabla persona
    $sql_persona = "INSERT INTO persona (dni, apellidos, nombres, fechanac, edad, genero, estadocivil, direccion) 
            VALUES (:dni, :apellidos, :nombres, :fechanac, :edad, :genero, :estadocivil, :idubicacion)";
    $stmt = $db->prepare($sql_persona);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':fechanac', $fechanac);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':estadocivil', $estadocivil);
    $stmt->bindParam(':idubicacion', $idubicacionValue);
    $stmt->execute();

    // Obtiene el id de la persona insertada
    $idpersona = $db->lastInsertId();

    echo "Inserción en persona exitosa.<br>";

    // Datos de la tabla PPL
    $apodo = $_POST['apodo'] ?? '';
    $trabaja = ($_POST['trabaja'] === 'Si') ? 1 : 0;
    $profesion = $_POST['profesion'] ?? '';
    $foto = $_FILES['foto']['name'];
    $fotoTmp = $_FILES['foto']['tmp_name'];

    // Procesar subida de imagen (validación simple)
    $target_dir_foto = "imagenes_p";
    $target_file = $target_dir_foto . basename($foto);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si es una imagen
    $check = getimagesize($fotoTmp);
    if ($check !== false) {
        if (move_uploaded_file($fotoTmp, $target_file)) {
            echo "Imagen de persona subida correctamente.<br>";
        } else {
            die("Error al subir la imagen.");
        }
    } else {
        die("El archivo no es una imagen válida.");
    }

    // Procesar huella (binario)
    if (isset($_FILES['huella']) && $_FILES['huella']['error'] == 0) {
        $huella = file_get_contents($_FILES['huella']['tmp_name']);
    } else {
        die("Error al cargar la huella.");
    }

    // En este punto reutilizamos la misma foto de persona para PPL
    $pplFoto = $foto;  // Mismo nombre de archivo

    // Inserción de datos en la tabla PPL
    $sql_ppl = "INSERT INTO ppl (idpersona, apodo, trabaja, profesion, huella, foto) 
                    VALUES (:idpersona, :apodo, :trabaja, :profesion, :huella, :foto)";
    $stmt = $db->prepare($sql_ppl);
    $stmt->bindParam(':idpersona', $idpersona); // Usamos el idpersona generado
    $stmt->bindParam(':apodo', $apodo);
    $stmt->bindParam(':trabaja', $trabaja);
    $stmt->bindParam(':profesion', $profesion);
    $stmt->bindParam(':huella', $huella, PDO::PARAM_LOB);
    $stmt->bindParam(':foto', $foto);
    $stmt->execute();

    $id_ppl = $db->lastInsertId();
    echo "Inserción en PPL exitosa.<br>";

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=sp;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }

    // Variables para la inserción en situacionlegal
    $data = [];
    $data['id_ppl'] = $id_ppl;
    $data['fecha_detencion'] = $_POST['fecha_detencion'] ?? '';
    $data['dependencia'] = $_POST['dependencia'] ?? '';
    $data['motivo_t'] = $_POST['motivo_t'] ?? '';
    $data['situacionlegal'] = $_POST['situacionlegal'] ?? ''; // Verificar que se envía desde el formulario
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

    // Recopilación de datos enviados por POST
    $causaIds = [];

    // Inserción del nuevo delito (si es necesario)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Verificamos si hay causas seleccionadas
            if (isset($_POST['causas']) && is_array($_POST['causas'])) {
                // Recopilamos los IDs de las causas seleccionadas
                foreach ($_POST['causas'] as $causaId) {
                    // Insertamos cada causa seleccionada en la tabla ppl_causas
                    $stmtCausa = $pdo->prepare("INSERT INTO ppl_causas (id_ppl, id_causa) VALUES (:id_ppl, :id_causa)");
                    $stmtCausa->bindParam(':id_ppl', $id_ppl);
                    $stmtCausa->bindParam(':id_causa', $causaId);
                    $stmtCausa->execute();

                    // Recogemos los ids de las causas insertadas
                    $causaIds[] = $pdo->lastInsertId();
                }
            }

            // Verificamos si se ha añadido un nuevo delito
            if (isset($_POST['delito']) && !empty($_POST['delito'])) {
                $delito = $_POST['delito'];
                $tipodelito = $_POST['tipodelito'];

                // Insertar el nuevo delito en la tabla 'delitos'
                $stmtDelito = $pdo->prepare("INSERT INTO delitos (nombre, id_tipo_delito) VALUES (:delito, :tipodelito)");
                $stmtDelito->bindParam(':delito', $delito);
                $stmtDelito->bindParam(':tipodelito', $tipodelito);
                $stmtDelito->execute();

                // Obtener el ID del nuevo delito insertado
                $nuevoDelitoId = $pdo->lastInsertId();

                // Agregar el nuevo delito a la lista de causas
                $causaIds[] = $nuevoDelitoId;
            }

            // Convertimos el array de causaIds en una cadena separada por comas
            $causasString = implode(',', $causaIds);

            // Insertar en la tabla situacionlegal, incluyendo las causas como una cadena de IDs
            $stmt = $pdo->prepare("INSERT INTO situacionlegal 
            (id_ppl, fecha_detencion, dependencia, motivo_t, situacionlegal, id_juzgado, en_prejucio, condena, categoria, reingreso_falta, causas_pend, cumplio_medida, causa_nino, asistio_rehabi, tiene_defensor, nombre_defensor, tiene_com_defensor, causas) 
            VALUES (:id_ppl, :fecha_detencion, :dependencia, :motivo_t, :situacionlegal, :id_juzgado, :en_prejucio, :condena, :categoria, :reingreso_falta, :causas_pend, :cumplio_medida, :causa_nino, :asistio_rehabi, :tiene_defensor, :nombre_defensor, :tiene_com_defensor, :causas)");

            // Bind de cada valor individualmente
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            // Bind para la columna causas (como una cadena separada por comas)
            $stmt->bindValue(':causas', $causasString);

            // Ejecutamos la inserción
            $stmt->execute();

            echo "Datos insertados correctamente.";
        } catch (PDOException $e) {
            echo "Error en la inserción: " . $e->getMessage();
        }
    }
}
?>

<style>
    .is-invalid {
        border: 10px solid red;
        /* Estilo para resaltar campos inválidos */
    }
</style>



<script>
    function toggleNewLocation() {
        const direccionp = document.getElementById('direccionp').value;
        const newLocationSection = document.getElementById('newLocationSection');
        if (direccionp === 'new') {
            style = "color: green;"
            newLocationSection.style.display = 'block';
        } else {
            newLocationSection.style.display = 'none';
        }
    }

    function toggleNewjuez() {
        const newjuezSection = document.getElementById('newjuezSection');
        const selectedjuez = document.getElementById('id_juzgado').value;

        if (selectedjuez === 'new') {
            newjuezSection.style.display = 'block';
        } else {
            newjuezSection.style.display = 'none';
        }

        // Verificar si la sección se ha mostrado correctamente
        console.log("Visibilidad de la sección después de mostrar: ", newjuezSection.style.display);
    }

    function previewImage(event) {
        const input = event.target;
        const imagePreview = document.getElementById('Foto');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]); // Leer archivo como Data URL
        }
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('Foto');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    let selectedCausas = [];

    function updateSelectedCausas() {
        selectedCausas = [];
        let checkboxes = document.querySelectorAll('input[name="causas[]"]:checked');
        checkboxes.forEach(checkbox => {
            selectedCausas.push(checkbox.nextElementSibling.innerText);
        });

        // Actualizar el texto con las causas seleccionadas
        let selectedText = selectedCausas.join(', ') || 'Selecciona hasta 4 causas.';
        document.getElementById('selected-causas-text').innerText = 'Causas seleccionadas: ' + selectedText;

        // Si se seleccionan más de 4 causas, desmarcar la última
        if (selectedCausas.length > 4) {
            alert("Solo puedes seleccionar hasta 4 causas.");
            checkboxes[checkboxes.length - 1].checked = false;
            selectedCausas.pop();
        }
    }

    // Esta función es para asegurarse de que no se seleccionen más de 4 causas.
    function updateSelectedCausas() {
        var checkboxes = document.querySelectorAll('input[name="causas[]"]:checked');
        if (checkboxes.length > 4) {
            alert('Solo puedes seleccionar hasta 4 causas.');
            checkboxes[checkboxes.length - 1].checked = false;
        }
    }

    // Función para mostrar u ocultar la sección de agregar nuevo delito
    function toggleNewDelitoSection() {
        const newDelitoSection = document.getElementById('newDelitoSection');
        if (newDelitoSection.style.display === 'none') {
            newDelitoSection.style.display = 'block';
        } else {
            newDelitoSection.style.display = 'none';
        }
    }
</script>

<section class="container mt-3">
    
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="d-inline-block ">Nuevo Informe de Evaluación Integral Interdisciplinario (IEII)</h5>
            <!-- <a class="btn btn-primary float-right mb-2" href="">(boton)</a>                     -->
        </div>
        <div class="card-body table-responsive">
            <form id="personaForm" action="" method="POST" enctype="multipart/form-data">

                <h4>Datos de la Persona</h4>
                <!-- Formulario de Persona -->
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="number" class="form-control" id="dni" name="dni" required>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="nombres">Nombres:</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                        </div>
                    </div>
                </div>    
                <!-- Selección de Ubicación -->
                <div class="form-group">
                    <label style="color: red;" for="direccionp" class="form-label">Seleccionar Ubicación</label>
                    <select class="form-control" id="direccionp" name="direccionp" onchange="toggleNewLocation()" required>
                        <option value="" style="color: red;">-- Seleccione una ubicación --</option>
                        <option style="color: green;" value="new">Agregar nueva ubicación</option>
                        <?php
                        // Código PHP para obtener las ubicaciones de la base de datos
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
                        ?>
                    </select>
                </div>

                <!-- Sección para agregar nueva Ubicación -->
                <div id="newLocationSection" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pais" class="form-label">País</label>
                            <select class="form-control" id="pais" name="pais">
                                <option value="" style="color: red;">-- Seleccione un País --</option> <!-- Opción predeterminada -->
                                <?php
                                // Código PHP para obtener solo los países seleccionados de la base de datos
                                $resultado = $db->query("SELECT id, nombre FROM paises
                                 WHERE nombre IN ('Argentina', 'Chile', 'Uruguay', 'Paraguay', 'Bolivia')
                                 ORDER BY nombre ASC;");
                                while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$fila['id']}'>{$fila['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="provincia" class="form-label">Provincia</label>
                            <select class="form-control" id="provincia" name="provincia">
                                <option value="" style="color: red;">-- Seleccione una Provincia --</option> <!-- Opción predeterminada -->
                                <?php
                                // Código PHP para obtener las provincias de los países seleccionados
                                $resultado = $db->query("SELECT provincias.id, provincias.nombre, paises.nombre AS pais_nombre
                                 FROM provincias
                                 LEFT JOIN paises ON provincias.id_pais = paises.id
                                 WHERE paises.nombre IN ('Argentina', 'Chile', 'Uruguay', 'Paraguay', 'Bolivia')
                                 ORDER BY pais_nombre ASC, provincias.nombre ASC;");
                                while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$fila['id']}'>{$fila['nombre']} - {$fila['pais_nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <select class="form-control" id="ciudad" name="ciudad">
                                <option value="">-- Seleccione una Ciudad --</option> <!-- Opción predeterminada -->
                                <?php
                                // Código PHP para obtener las ubicaciones de la base de datos
                                $resultado = $db->query("SELECT ciudades.id, ciudades.nombre, provincias.nombre AS provincia_nombre, paises.nombre AS pais_nombre
                                 FROM ciudades
                                 LEFT JOIN provincias ON ciudades.id_prov = provincias.id
                                 LEFT JOIN paises ON provincias.id_pais = paises.id
                                 WHERE paises.nombre IN ('Argentina', 'Chile', 'Uruguay', 'Bolivia', 'Paraguay')
                                 ORDER BY paises.nombre ASC, provincias.nombre ASC;");
                                while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$fila['id']}'>{$fila['nombre']} - {$fila['provincia_nombre']} ({$fila['pais_nombre']})</option>";
                                }
                                ?>
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

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="fechanac">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control" id="fechanac" name="fechanac" required onchange="calcularEdad()">
                        </div>
                    </div>
                    <div class="col">                        
                        <div class="form-group">
                            <label for="edad">Edad:</label>
                            <input type="number" class="form-control" id="edad" name="edad" min="18" max="70" required readonly>
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

                    <div class="mb-3">
                        <label for="apodo" class="form-label">Apodo</label>
                        <input type="text" class="form-control" id="apodo" name="apodo" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="profesion" class="form-label">Profesión</label>
                                <input type="text" class="form-control" id="profesion" name="profesion" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras y espacios">
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="trabaja" class="form-label">¿Trabajaba en el momento de la detención?</label>
                                <select class="form-control" id="trabaja" name="trabaja" required>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    

                    
                                
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" onchange="previewImage(event)" required>
                        <br>
                        <img id="Foto" class="form-control" style="max-width: 150px; height: 100px;" alt="Foto de PERSONA">
                    </div>

                    <div class="mb-3">
                        <label for="huella" class="form-label">Huella</label>
                        <input type="file" class="form-control" id="huella" name="huella" required>
                    </div>
                </div>

                <!-- Sección Situación Legal -->
                <div>
                    <h4 class="mt-4">Datos de Situación Legal</h4>

                    <input type="hidden" id="id_ppl" name="id_ppl" value="<?php echo $id_ppl; ?>">

                    <div class="form-group">
                        <label for="fecha_detencion">Fecha de detención:</label>
                        <input type="date" class="form-control" id="fecha_detencion" name="fecha_detencion" required>
                    </div>

                    <div class="form-group">
                        <label for="dependencia">Dependencia:</label>
                        <input type="text" class="form-control" id="dependencia" name="dependencia" required>
                    </div>

                    <div class="form-group">
                        <label for="motivo_t">Motivo de traslado:</label>
                        <input type="text" class="form-control" id="motivo_t" name="motivo_t" required>
                    </div>

                    <div class="form-group">
                        <label for="situacionlegal">Situación Legal:</label>
                        <select class="form-control" id="situacionlegal" name="situacionlegal" required>
                            <option value="penado">Penado</option>
                            <option value="procesado">Procesado</option>
                        </select>
                    </div>

                    <div class="form-group">
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
                                    // Código PHP para obtener los delitos de la base de datos
                                    $resultado = $conexion->query("
                                        SELECT d.id_delito, d.nombre, t.id_tipo_delito 
                                        FROM delitos d 
                                        LEFT JOIN tiposdelito t ON d.id_tipo_delito = t.id_tipo_delito;
                                    ");
                                    $i = 0; // Contador para saber cuántas causas hemos mostrado
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

                    <!-- Botón para agregar nueva causa -->
                    <button type="button" class="btn btn-primary" onclick="toggleNewDelitoSection()">Añadir nueva causa</button>
                    <br>

                    <!-- Sección para agregar nuevo delito -->
                    <div id="newDelitoSection" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipodelito" class="form-label">Tipo de delito</label>
                                <select class="form-control" id="tipodelito" name="tipodelito">
                                    <option value="" style="color: red;">-- Seleccione el tipo de delito --</option> <!-- Opción predeterminada -->
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

                    <div>
                        <div class="form-group" id="abusoSection">
                            <label for="en_prejucio">En perjuicio de quien (si la causa es intrafamiliar):</label>
                            <input type="text" class="form-control" id="en_prejucio" name="en_prejucio" onchange="toggleViolenciaSection()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_juzgado">ID del Juzgado:</label>
                        <select class="form-control" id="id_juzgado" name="id_juzgado" onchange="toggleNewjuez()" required>
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

                    <div class="form-group">
                        <label for="condena">Condena:</label>
                        <input type="text" class="form-control" id="condena" name="condena" required>
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoria:</label>
                        <select class="form-control" id="categoria" name="categoria" required>
                            <option value="primario">Primario</option>
                            <option value="reiterante">Reiterante</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="causas_pend">Causas pendientes de resolución:</label>
                        <input type="text" class="form-control" id="causas_pend" name="causas_pend" required>
                    </div>

                    <div class="form-group">
                        <label for="reingreso_falta">Reingreso en caso de quebrantamiento de beneficio y/o libertad:</label>
                        <select class="form-control" id="reingreso_falta" name="reingreso_falta" required>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="causa_nino">¿Causas judicializadas durante la niñez o adolescencia?:</label>
                        <select class="form-control" id="causa_nino" name="causa_nino" required>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cumplio_medida">¿Cumplió medidas socioeducativas?:</label>
                        <select class="form-control" id="cumplio_medida" name="cumplio_medida" required>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asistio_rehabi">Institucionalizaciones en centros de rehabilitación por conflictos con la ley:</label>
                        <select class="form-control" id="asistio_rehabi" name="asistio_rehabi" required>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tiene_defensor">¿Cuenta con un defensor oficial?:</label>
                        <select class="form-control" id="tiene_defensor" name="tiene_defensor" required>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group hidden" id="nombreDefensorDiv">
                        <label for="nombre_defensor">¿Quién?:</label>
                        <input type="text" class="form-control" id="nombre_defensor" name="nombre_defensor">
                    </div>

                    <div class="form-group hidden" id="tieneComDefensorDiv">
                        <label for="tiene_com_defensor">¿Tiene comunicación con él?:</label>
                        <select class="form-control" id="tiene_com_defensor" name="tiene_com_defensor" required>
                            <option value="si">Sí</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>


        </div>
        <!-- <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-primary">Guardar Todos los datos</button>
                    </div> -->
        <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-primary ms-2">Guardar </button>
        </div>
    </div>
    </form>
    </div>
    </div>
    </div>
</section>