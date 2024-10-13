<?php require 'navbar.php'; ?>
<?php
function validarEdad($edad)
{
    if ($edad <= 0 || $edad > 70) {
        return false; 
    }
    return true; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos de la tabla persona
    $dni = $_POST['dni'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $fechanac = $_POST['fechanac'];
    $edad = $_POST['edad'];
    $genero = $_POST['genero'];
    $estadocivil = $_POST['estadocivil'];
    $foto = $_FILES['foto']['name'];
    $fotoTmp = $_FILES['foto']['tmp_name'];
    $direccionp = $_POST['direccionp'];

    if (!validarEdad($edad)) {
        echo "Error: La edad debe ser mayor que 0 y menor o igual a 70.";
    } else {
        // Proceder a guardar los datos en la base de datos
        // Tu código de inserción aquí...
        echo "Inserción en persona exitosa"; // Cambia esto según tu lógica
    }

    // Manejar ubicación
    $idubicacionValue = $direccionp;
    if ($direccionp === 'new') {
        $pais = $_POST['pais'];
        $provincia = $_POST['provincia'];
        $departamento = $_POST['departamento'];
        $direccion = $_POST['direccion'];

        $sql_ubicacion = "INSERT INTO ubicacion (pais, provincia, departamento, direccion) 
                          VALUES (:pais, :provincia, :departamento, :direccion)";
        $stmt = $db->prepare($sql_ubicacion);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':provincia', $provincia);
        $stmt->bindParam(':departamento', $departamento);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();

        $idubicacionValue = $db->lastInsertId(); // Obtiene el ID de la nueva ubicación
        echo "Inserción en ubicación exitosa.<br>";
    }

    // Procesar subida de imagen (validación simple)
    $target_dir_foto = "../imagenes/";
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

    // Inserción de datos en la tabla persona
    $sql_persona = "INSERT INTO persona (dni, apellidos, nombres, fechanac, edad, genero, estadocivil, foto, direccion) 
            VALUES (:dni, :apellidos, :nombres, :fechanac, :edad, :genero, :estadocivil, :foto, :idubicacion)";
    $stmt = $db->prepare($sql_persona);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':fechanac', $fechanac);
    $stmt->bindParam(':edad', $edad);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':estadocivil', $estadocivil);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':idubicacion', $idubicacionValue);
    $stmt->execute();

    // Obtiene el id de la persona insertada
    $idpersona = $db->lastInsertId();

    echo "Inserción en persona exitosa.<br>";

    // Verificar si es PPL
    if (isset($_POST['esPPL']) && $_POST['esPPL'] === 'on') {
        // Datos de la tabla PPL
        $apodo = $_POST['apodo'] ?? '';
        $trabaja = ($_POST['trabaja'] === 'Si') ? 1 : 0;
        $profesion = $_POST['profesion'] ?? '';
        $f_entrevista = $_POST['datetimeInput'] ?? '';

        // Procesar huella (binario)
        if (isset($_FILES['huella']) && $_FILES['huella']['error'] == 0) {
            $huella = file_get_contents($_FILES['huella']['tmp_name']);
        } else {
            die("Error al cargar la huella.");
        }

        // En este punto reutilizamos la misma foto de persona para PPL
        $pplFoto = $foto;  // Mismo nombre de archivo

        // Inserción de datos en la tabla PPL
        $sql_ppl = "INSERT INTO ppl (idpersona, apodo, trabaja, profesion, huella, foto, fechaentrevista) 
                    VALUES (:idpersona, :apodo, :trabaja, :profesion, :huella, :foto, :datetimeInput)";
        $stmt = $db->prepare($sql_ppl);
        $stmt->bindParam(':idpersona', $idpersona); // Usamos el idpersona generado
        $stmt->bindParam(':apodo', $apodo);
        $stmt->bindParam(':trabaja', $trabaja);
        $stmt->bindParam(':profesion', $profesion);
        $stmt->bindParam(':huella', $huella, PDO::PARAM_LOB);
        $stmt->bindParam(':foto', $pplFoto);  // Reutilizamos la misma foto
        $stmt->bindParam(':datetimeInput', $f_entrevista);
        $stmt->execute();

        $idppl = $db->lastInsertId();
        echo "Inserción en PPL exitosa.<br>";
    }

    // Variables para la inserción en situacionlegal
    $ppl = $idppl; // Asegúrate de que este valor exista en el formulario
    $motivo_t = $_POST['motivo_t'];
    $situacionlegal = $_POST['situacionlegal'];
    $prontuario = $_POST['prontuario'];
    $delitos = $_POST['delito'];
    $fechappls = $_POST['fechappl'];
    $juzgados = $_POST['juzgado'];
    $senas_partics = $_POST['senas_partic'];

    // Los valores de las casillas de verificación se capturan correctamente
    $reincidencia = isset($_POST['reincidencia']) ? 1 : 0;
    $salida_transitoria = isset($_POST['salida_transitoria']) ? 1 : 0;
    $libertad_asistida = isset($_POST['libertad_asistida']) ? 1 : 0;
    $libertad_condicional = isset($_POST['libertad_condicional']) ? 1 : 0;

    // Manejar delito
    if ($_POST['delito'] === 'new') {
        $titulo = $_POST['titulo'];
        $subcategoria = $_POST['subcategoria'];

        // Consulta para insertar nuevo delito en la tabla tipodelito
        $sql_delito = "INSERT INTO tipodelito (titulo, subcategoria) 
                   VALUES (:titulo, :subcategoria)";

        $stmt = $db->prepare($sql_delito);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':subcategoria', $subcategoria);
        $stmt->execute();

        // Obtener el ID del nuevo delito
        $delitos = $db->lastInsertId();


        echo "Inserción en tipodelito exitosa.<br>";
    }

    // Manejar fechappl
    if ($fechappls === 'new') {
        $fechadet = $_POST['fechadet'];
        $fechacond = $_POST['fechacond'];
        $fechavenc = $_POST['fechavenc'];

        // Consulta para insertar nueva fecha en la tabla fechappl
        $sql_fechappl = "INSERT INTO fechappl (fechadet, fechacond, fechavenc) 
                     VALUES (:fechadet, :fechacond, :fechavenc)";

        $stmt = $db->prepare($sql_fechappl);
        $stmt->bindParam(':fechadet', $fechadet);
        $stmt->bindParam(':fechacond', $fechacond);
        $stmt->bindParam(':fechavenc', $fechavenc);
        $stmt->execute();

        // Obtener el ID de la nueva fecha en fechappl
        $fechappls = $db->lastInsertId();
        echo "Inserción en fechappl exitosa.<br>";
    }

    // Manejar juzgado
    if ($juzgados === 'new') {
        $nombre = $_POST['nombre'];
        $nombrejuez = $_POST['nombrejuez'];

        // Consulta para insertar un nuevo juzgado
        $sql_juzgado = "INSERT INTO juzgado (nombre, nombre_juez) 
                    VALUES (:nombre, :nombrejuez)";

        $stmt = $db->prepare($sql_juzgado);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':nombrejuez', $nombrejuez);
        $stmt->execute();

        // Obtener el ID del nuevo juzgado
        $juzgados = $db->lastInsertId();
        echo "Inserción en juzgado exitosa.<br>";
    }

    // Manejar características
    if ($senas_partics === 'new') {
        $zona = $_POST['zona'];
        $tipo = $_POST['tipo'];
        $descripcion = $_POST['descripcion'];
        $tamano = $_POST['tamano'];

        // Definir la consulta correctamente
        $sql_senaspart = "INSERT INTO caracteristicas (zona, tipo, descripcion, tamaño) 
                      VALUES (:zona, :tipo, :descripcion, :tamano)";

        $stmt = $db->prepare($sql_senaspart); // Cambié $sql_caracteristicas a $sql_senaspart
        $stmt->bindParam(':zona', $zona);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':tamano', $tamano);
        $stmt->execute();

        // Obtener el ID de la nueva característica insertada
        $senas_partics = $db->lastInsertId();
        echo "Inserción en características exitosa.<br>";
    }


    // Inserción en la tabla situacionlegal
    $sql_situacionlegal = "INSERT INTO situacionlegal 
    (ppl, motivo_t, situacionlegal, prontuario, reincidencia, 
     salida_transitoria, libertad_asistida, libertad_condicional, delito, fecha, juzgado, señas_partic) 
     VALUES 
    (:ppl, :motivo_t, :situacionlegal, :prontuario, :reincidencia, 
     :salida_transitoria, :libertad_asistida, :libertad_condicional, :delito, :fechappl, :juzgado, :senas_partic)";
    $stmt = $db->prepare($sql_situacionlegal);
    // Vincular parámetros
    $stmt->bindParam(':ppl', $idppl, PDO::PARAM_INT);
    $stmt->bindParam(':motivo_t', $motivo_t, PDO::PARAM_STR);
    $stmt->bindParam(':situacionlegal', $situacionlegal, PDO::PARAM_STR);
    $stmt->bindParam(':prontuario', $prontuario, PDO::PARAM_INT);
    $stmt->bindParam(':reincidencia', $reincidencia, PDO::PARAM_INT);
    $stmt->bindParam(':salida_transitoria', $salida_transitoria, PDO::PARAM_INT);
    $stmt->bindParam(':libertad_asistida', $libertad_asistida, PDO::PARAM_INT);
    $stmt->bindParam(':libertad_condicional', $libertad_condicional, PDO::PARAM_INT);
    $stmt->bindParam(':delito', $delitos, PDO::PARAM_INT);
    $stmt->bindParam(':fechappl', $fechappls, PDO::PARAM_INT);
    $stmt->bindParam(':juzgado', $juzgados, PDO::PARAM_INT);
    $stmt->bindParam(':senas_partic', $senas_partics, PDO::PARAM_INT);
    $stmt->execute();
    echo "Inserción en situacionlegal exitosa.<br>";
}
?>


    <script>
        function guardarPersona() {
            // Crear un objeto FormData para enviar los datos del formulario
            const formData = new FormData(document.getElementById('personaForm'));

            // Realizar una solicitud AJAX
            fetch('persona_crea.php', { // Cambia 'tu_archivo_php.php' por el nombre de tu archivo PHP
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la red');
                    }
                    return response.text(); // O response.json() si esperas un JSON
                })
                .then(data => {
                    // Maneja la respuesta del servidor
                    console.log(data); // Puedes mostrar un mensaje al usuario o realizar otras acciones

                    // Redirigir a persona_crea.php si la inserción es exitosa
                    if (data.includes('Inserción en persona exitosa')) { // Verifica que el mensaje indica éxito
                        alert('Datos de la persona guardados correctamente.');
                        window.location.href = 'persona_crea.php'; // Cambia la URL si es necesario
                    } else {
                        alert('Error al guardar los datos.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar los datos.');
                });
        }

        // Mostrar u ocultar la sección de nuevo PPL
        function togglePPL() {
            var pplSection = document.getElementById('pplSection');
            var situacionLegalSection = document.getElementById('situacionLegalSection');
            var esPPL = document.getElementById('esPPL').checked; // Usas 'esPPL' aquí

            pplSection.style.display = esPPL ? 'block' : 'none'; // Y también aquí
            situacionLegalSection.style.display = esPPL ? 'block' : 'none'; // Cambiar 'isPPL' por 'esPPL'
        }

        function toggleNewLocation() {
            const direccionp = document.getElementById('direccionp').value;
            const newLocationSection = document.getElementById('newLocationSection');
            if (direccionp === 'new') {
                newLocationSection.style.display = 'block';
            } else {
                newLocationSection.style.display = 'none';
            }
        }

        function toggleNewDelito() {
            var selectDelito = document.getElementById('delito');
            var newDelitoSection = document.getElementById('newDelitoSection');
            newDelitoSection.style.display = (selectDelito.value === 'new') ? 'block' : 'none';
        }

        function toggleNewfecha() {
            const newfechaSection = document.getElementById('newfechaSection');
            const selectedfecha = document.getElementById('fechappl').value;
            newfechaSection.style.display = (selectedfecha === 'new') ? 'block' : 'none';
        }

        function toggleNewjuez() {
            const newjuezSection = document.getElementById('newjuezSection');
            const selectedjuez = document.getElementById('juzgado').value;
            newjuezSection.style.display = (selectedjuez === 'new') ? 'block' : 'none';
        }

        function toggleNewcaracteristica() {
            const newcaracteristicaSection = document.getElementById('newcaracteristicaSection');
            const selectedcaracteristica = document.getElementById('senas_partic').value;
            newcaracteristicaSection.style.display = (selectedcaracteristica === 'new') ? 'block' : 'none';
        }

        function copyPhoto() {
            const photoInput = document.getElementById('foto');
            const personPhotoPreview = document.getElementById('Foto'); // Previsualización para Persona
            const pplPhotoPreview = document.getElementById('pplFoto'); // Previsualización para PPL

            if (photoInput.files.length > 0) {
                const file = photoInput.files[0]; // Obtiene el archivo seleccionado
                const reader = new FileReader();

                // Cuando se lee el archivo exitosamente
                reader.onload = function(e) {
                    // Muestra la imagen seleccionada en ambos previews
                    personPhotoPreview.setAttribute('src', e.target.result);
                    pplPhotoPreview.setAttribute('src', e.target.result);
                };

                // Lee el archivo como una URL en base64
                reader.readAsDataURL(file);
            } else {
                // Limpia la previsualización si no hay archivo seleccionado
                personPhotoPreview.removeAttribute('src');
                pplPhotoPreview.removeAttribute('src');
            }
        }

        // Función para establecer la fecha y hora actual en el campo
        const inputDateTime = document.getElementById('datetimeInput');
        const now = new Date();

        // Formato YYYY-MM-DDTHH:MM
        const formattedDateTime = now.toISOString().slice(0, 16);
        inputDateTime.value = formattedDateTime;
    </script>



<section class="content mt-3">
    <div class="row px-5 mx-5 ">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block ">Nuevo Informe de Evaluación Integral Interdiciplinario (IEII)</h5>
                    <!-- <a class="btn btn-primary float-right mb-2" href="">(boton)</a>                     -->
                </div>
                <div class="card-body table-responsive">
                <div class="container ">
                    
                                        
<form id="personaForm" action="" method="POST" enctype="multipart/form-data">
        <div class="container">
             
            <h4>Datos de la Persona</h4>
            <!-- Formulario de Persona -->
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="number" class="form-control" id="dni" name="dni" required>
            </div>
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto</label>
                <input type="file" class="form-control" id="foto" name="foto" onchange="copyPhoto()">
                <br>
                <img id="Foto" class="form-control" style="max-width: 150px; height: 100px;" alt="Foto de PERSONA">
            </div>

            <!-- Selección de Ubicación -->
            <div class="form-group">
                <label for="direccionp" class="form-label">Seleccionar Ubicación</label>
                <select class="form-control" id="direccionp" name="direccionp" onchange="toggleNewLocation()">
                    <option value="">-- Seleccione una ubicación --</option>
                    <option style="color: green;" value="new">Agregar nueva ubicación</option>
                    <?php
                    // Código PHP para obtener las ubicaciones de la base de datos
                    $resultado = $db->query("SELECT id, id_pais, id_provincia, localidad, direccion FROM domicilio");
                    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$fila['id']}'>{$fila['pais']} - {$fila['provincia']} - {$fila['departamento']} - {$fila['direccion']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Sección para agregar nueva Ubicación -->
            <div id="newLocationSection" style="display: none;">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="pais" class="form-label">País</label>
                        <input type="text" class="form-control" id="pais" name="pais">
                    </div>
                    <div class="col-md-3">
                        <label for="provincia" class="form-label">Provincia</label>
                        <input type="text" class="form-control" id="provincia" name="provincia">
                    </div>
                    <div class="col-md-3">
                        <label for="departamento" class="form-label">Departamento</label>
                        <input type="text" class="form-control" id="departamento" name="departamento">
                    </div>
                    <div class="col-md-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="fechanac">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="fechanac" name="fechanac" required>
            </div>

            <div class="form-group">
                <label for="edad">Edad:</label>
                <input type="number" class="form-control" id="edad" name="edad" min="1" max="70" required>
            </div>
            <div class="form-group">
                <label for="genero">Género:</label>
                <select class="form-control" id="genero" name="genero" required>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="estadocivil">Estado Civil:</label>
                <select class="form-control" id="estadocivil" name="estadocivil">
                    <option value="Soltero">Soltero</option>
                    <option value="Casado">Casado</option>
                    <option value="Divorciado">Divorciado</option>
                    <option value="Viudo">Viudo</option>
                </select>
            </div>
            <!-- ¿Es PPL? -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="esPPL" name="esPPL" onclick="togglePPL()" >
                <label class="form-check-label" for="esPPL">¿Es una Persona Privada de la Libertad (PPL)?</label>
            </div>

            <!-- Sección PPL -->
            <div id="pplSection" style="display: none;">
                <h4 class="mt-4">Datos de PPL</h4>

                <div class="mb-3">
                    <label for="datetimeInput"  class="form-label">Selecciona fecha y hora:</label>
                    <input type="datetime-local" class="form-control"  id="datetimeInput" name="datetimeInput">
                </div>

                <div class="mb-3">
                    <label for="apodo" class="form-label">Apodo</label>
                    <input type="text" class="form-control" id="apodo" name="apodo">
                </div>

                <div class="mb-3">
                    <label for="profesion" class="form-label">Profesión</label>
                    <input type="text" class="form-control" id="profesion" name="profesion">
                </div>

                <div class="mb-3">
                    <label for="trabaja" class="form-label">¿Trabaja?</label>
                    <select class="form-control" id="trabaja" name="trabaja">
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pplFoto" class="form-label">Foto de PPL</label>
                    <img id="pplFoto" class="form-control" style="max-width: 150px; height: 100px;" alt="Foto de PPL">
                </div>

                <div class="mb-3">
                    <label for="huella" class="form-label">Huella</label>
                    <input type="file" class="form-control" id="huella" name="huella">
                </div>

            </div>

            <!-- Sección Situación Legal -->
            <div id="situacionLegalSection" style="display: none;">
                <h4 class="mt-4">Datos de Situación Legal</h4>
                <input type="hidden" id="idppl" name="idppl" value="<?php echo $idppl; ?>">

                <div class="form-group">
                    <label for="motivo_t">Motivo de detención:</label>
                    <input type="text" class="form-control" id="motivo_t" name="motivo_t" required>
                </div>

                <div class="form-group">
                    <label for="situacionlegal">Situación Legal:</label>
                    <input type="text" class="form-control" id="situacionlegal" name="situacionlegal" required>
                </div>

                <div class="form-group">
                    <label for="prontuario">Prontuario:</label>
                    <input type="number" class="form-control" id="prontuario" name="prontuario" required>
                </div>

                <div class="form-group">
                    <label for="reincidencia">Reincidencia:</label>
                    <input type="checkbox" id="reincidencia" name="reincidencia" value="1">
                    <input type="hidden" name="reincidencia" value="0">
                </div>

                <div class="form-group">
                    <label for="salida_transitoria">Salida Transitoria:</label>
                    <input type="checkbox" id="salida_transitoria" name="salida_transitoria" value="1">
                    <input type="hidden" name="salida_transitoria" value="0">
                </div>

                <div class="form-group">
                    <label for="libertad_asistida">Libertad Asistida:</label>
                    <input type="checkbox" id="libertad_asistida" name="libertad_asistida" value="1">
                    <input type="hidden" name="libertad_asistida" value="0">
                </div>

                <div class="form-group">
                    <label for="libertad_condicional">Libertad Condicional:</label>
                    <input type="checkbox" id="libertad_condicional" name="libertad_condicional" value="1">
                    <input type="hidden" name="libertad_condicional" value="0">
                </div>

                <div class="form-group">
                    <label for="delito">ID Delito:</label>
                    <select class="form-control" id="delito" name="delito" onchange="toggleNewDelito()">
                        <option value="">-- Seleccione un delito --</option>
                        <option style="color: green;" value="new">Agregar nuevo delito</option>
                        <?php
                        // Código PHP para obtener los delitos de la base de datos
                        $resultado = $conexion->query("SELECT id, titulo, subcategoria FROM tipodelito");
                        while ($fila = $resultado->fetch_assoc()) {
                            echo "<option value='{$fila['id']}'>{$fila['titulo']} - {$fila['subcategoria']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Sección para agregar nuevo delito -->
                <div id="newDelitoSection" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo">
                        </div>
                        <div class="col-md-6">
                            <label for="subcategoria" class="form-label">Subcategoría</label>
                            <input type="text" class="form-control" id="subcategoria" name="subcategoria">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fechappl">Fecha de la situación (timestamp):</label>
                    <select class="form-control" id="fechappl" name="fechappl" onchange="toggleNewfecha()">
                        <option value="">-- Seleccione una fecha de la situación --</option>
                        <option style="color: green;" value="new">Agregar nueva fecha</option>
                        <?php
                        // Código PHP para obtener las fechas de la base de datos
                        $resultado = $conexion->query("SELECT id, fechadet, fechacond, fechavenc FROM fechappl");
                        while ($fila = $resultado->fetch_assoc()) {
                            // Mostrar las fechas de detención, condena y vencimiento correctamente
                            echo "<option value='{$fila['id']}'>Fecha de detención: {$fila['fechadet']} - Fecha de condena: {$fila['fechacond']} - Fecha de vencimiento: {$fila['fechavenc']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Sección para agregar nueva fecha -->
                <div id="newfechaSection" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="fechadet" class="form-label">Fecha de detención</label>
                            <input type="date" class="form-control" id="fechadet" name="fechadet">
                        </div>
                        <div class="col-md-3">
                            <label for="fechacond" class="form-label">Fecha de condena</label>
                            <input type="date" class="form-control" id="fechacond" name="fechacond">
                        </div>
                        <div class="col-md-3">
                            <label for="fechavenc" class="form-label">Fecha de vencimiento</label>
                            <input type="date" class="form-control" id="fechavenc" name="fechavenc">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="juzgado">ID del Juzgado:</label>
                    <select class="form-control" id="juzgado" name="juzgado" onchange="toggleNewjuez()">
                        <option value="">-- Seleccione un Juez --</option>
                        <option style="color: green;" value="new">Agregar nuevo juez</option>
                        <?php
                        // Código PHP para obtener juzgados de la base de datos
                        $resultado = $conexion->query("SELECT id, nombre, nombre_juez FROM juzgado");
                        while ($fila = $resultado->fetch_assoc()) {
                            // Corregir las comillas del value y del contenido
                            echo "<option value='{$fila['id']}'>{$fila['nombre']} - {$fila['nombre_juez']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Sección para agregar un nuevo juez -->
                <div id="newjuezSection" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="nombre" class="form-label">Nombre del juzgado</label>
                            <input type="text" class="form-control" id="nombre" name="nombre">
                        </div>
                        <div class="col-md-3">
                            <label for="nombrejuez" class="form-label">Nombre del juez</label>
                            <input type="text" class="form-control" id="nombrejuez" name="nombrejuez">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senas_partic">ID de las características:</label>
                    <select class="form-control" id="senas_partic" name="senas_partic" onchange="toggleNewcaracteristica()">
                        <option value="">-- Seleccione las características --</option>
                        <option style="color: green;" value="new">Agregar nueva característica</option>
                        <?php
                        // Código PHP para obtener características de la base de datos
                        $resultado = $conexion->query("SELECT id, zona, tipo, descripcion, tamaño FROM caracteristicas");
                        while ($fila = $resultado->fetch_assoc()) {
                            // Corregir las comillas del value y del contenido
                            echo "<option value='{$fila['id']}'>{$fila['zona']} - {$fila['tipo']} - {$fila['descripcion']} - {$fila['tamaño']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Sección para agregar nueva característica -->
                <div id="newcaracteristicaSection" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="zona" class="form-label">Zona</label>
                            <input type="text" class="form-control" id="zona" name="zona">
                        </div>
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <input type="text" class="form-control" id="tipo" name="tipo">
                        </div>
                        <div class="col-md-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion">
                        </div>
                        <div class="col-md-3">
                            <label for="tamano" class="form-label">Tamaño</label>
                            <input type="text" class="form-control" id="tamano" name="tamano">
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <!-- <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-primary">Guardar Todos los datos</button>
        </div> -->
        <div class="d-flex justify-content-center mt-3">
            <button type="button" class="btn btn-primary ms-2" onclick="guardarPersona()">Guardar </button>
        </div>
    </form>


                </div>
                <div></div>
            </div> 
        </div>   
        </div>   
    </div>
</section>
    
