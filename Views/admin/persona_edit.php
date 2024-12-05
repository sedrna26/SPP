<?php require 'navbar.php'; ?>
<?php 

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
            echo "Error en el registro de auditoría: " . $e->getMessage();
        }
    }

    try {
        // Obtener los datos de la persona
        $stmt_persona = $db->prepare("SELECT 
            persona.id, persona.dni, persona.nombres, persona.apellidos, 
            DATE_FORMAT(persona.fechanac, '%d-%m-%Y') AS fechaNacimiento, persona.edad, 
            persona.genero, persona.estadocivil, d.id AS id_direccion, 
            p.id AS id_pais, pr.id AS id_provincia, c.id AS id_ciudad, 
            d.localidad, d.direccion
            FROM persona
            LEFT JOIN domicilio d ON persona.id = d.id_persona
            LEFT JOIN paises p ON d.id_pais = p.id
            LEFT JOIN provincias pr ON d.id_provincia = pr.id
            LEFT JOIN ciudades c ON d.id_ciudad = c.id
            WHERE persona.id = :id");
        $stmt_persona->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_persona->execute();
        $persona = $stmt_persona->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            die("Persona no encontrada.");
        }

        // Obtener los países
        $stmt_paises = $db->prepare("SELECT * FROM paises");
        $stmt_paises->execute();
        $paises = $stmt_paises->fetchAll(PDO::FETCH_ASSOC);

        // Obtener las provincias según el país seleccionado
        $stmt_provincias = $db->prepare("SELECT * FROM provincias WHERE id_pais = :pais_id");
        $stmt_provincias->bindParam(':pais_id', $persona['id_pais'], PDO::PARAM_INT);
        $stmt_provincias->execute();
        $provincias = $stmt_provincias->fetchAll(PDO::FETCH_ASSOC);

        // Obtener las ciudades según la provincia seleccionada
        $stmt_ciudades = $db->prepare("SELECT * FROM ciudades WHERE id_prov = :provincia_id");
        $stmt_ciudades->bindParam(':provincia_id', $persona['id_provincia'], PDO::PARAM_INT);
        $stmt_ciudades->execute();
        $ciudades = $stmt_ciudades->fetchAll(PDO::FETCH_ASSOC);

        // Actualización de datos si se hace una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger datos del formulario
            $dni = $_POST['dni'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $fechaNacimiento = $_POST['fechaNacimiento'];
            $edad = $_POST['edad'];
            $genero = $_POST['genero'];
            $estadocivil = $_POST['estadocivil'];
            $direccion = $_POST['direccion'];
            $pais = $_POST['pais'];
            $provincia = $_POST['provincia'];
            $ciudad = $_POST['ciudad'];
            $localidad = $_POST['localidad'];
            $id = $_POST['id'];

            // Actualizar persona
            $stmt_update = $db->prepare("
                UPDATE persona 
                SET dni = :dni, 
                    nombres = :nombres, 
                    apellidos = :apellidos, 
                    fechanac = :fechaNacimiento, 
                    edad = :edad, 
                    genero = :genero, 
                    estadocivil = :estadocivil
                WHERE id = :id");
            $stmt_update->bindParam(':dni', $dni);
            $stmt_update->bindParam(':nombres', $nombres);
            $stmt_update->bindParam(':apellidos', $apellidos);
            $stmt_update->bindParam(':fechaNacimiento', $fechaNacimiento);
            $stmt_update->bindParam(':edad', $edad);
            $stmt_update->bindParam(':genero', $genero);
            $stmt_update->bindParam(':estadocivil', $estadocivil);
            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update->execute();

            // Actualizar dirección
            $stmt_update_persona_direccion = $db->prepare("
                UPDATE domicilio 
                SET direccion = :direccion, id_pais = :pais, id_provincia = :provincia, 
                    id_ciudad = :ciudad, localidad = :localidad
                WHERE id_persona = :id");
            $stmt_update_persona_direccion->bindParam(':direccion', $direccion);
            $stmt_update_persona_direccion->bindParam(':pais', $pais);
            $stmt_update_persona_direccion->bindParam(':provincia', $provincia);
            $stmt_update_persona_direccion->bindParam(':ciudad', $ciudad);
            $stmt_update_persona_direccion->bindParam(':localidad', $localidad);
            $stmt_update_persona_direccion->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update_persona_direccion->execute();

            // Registrar auditoría
            $accion = 'Editar PPL - Persona';
            $tabla_afectada = 'persona;domicilio';
            $detalles = "Se editó la persona con ID: $id y la dirección.";
            registrarAuditoria($db, $accion, $tabla_afectada, $id, $detalles);

            header("Location: ppl_informe.php?id=" . urlencode($id) . "&mensaje=" . urlencode("PPL - Persona editada con éxito."));
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<script>
    // Usando fetch para simplificar las solicitudes AJAX
    async function loadProvincias() {
        const paisId = document.getElementById('pais').value;
        const provinciaSelect = document.getElementById('provincia');

        if (paisId) {
            const response = await fetch(`getProvincias.php?pais_id=${paisId}`);
            const provincias = await response.text();
            provinciaSelect.innerHTML = provincias;
        }
    }

    async function loadCiudades() {
        const provinciaId = document.getElementById('provincia').value;
        const ciudadSelect = document.getElementById('ciudad');

        if (provinciaId) {
            const response = await fetch(`getCiudades.php?provincia_id=${provinciaId}`);
            const ciudades = await response.text();
            ciudadSelect.innerHTML = ciudades;
        } else {
            ciudadSelect.innerHTML = "<option value=''>-- Seleccione una Ciudad --</option>";
        }
    }
</script>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Editar Persona</h4>
        </div>
        <div class="card-body">
            <form action="persona_edit.php?id=<?php echo $persona['id']; ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $persona['id']; ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="dni" class="form-label">DNI:</label>
                        <input type="number" id="dni" name="dni" class="form-control" value="<?php echo htmlspecialchars($persona['dni'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="nombres" class="form-label">Nombres:</label>
                        <input type="text" id="nombres" name="nombres" class="form-control" value="<?php echo htmlspecialchars($persona['nombres'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?php echo htmlspecialchars($persona['apellidos'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento:</label>
                        <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" value="<?php echo date('Y-m-d', strtotime($persona['fechaNacimiento'])); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="edad" class="form-label">Edad:</label>
                        <input type="number" id="edad" name="edad" class="form-control" value="<?php echo $persona['edad']; ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="genero" class="form-label">Género:</label>
                        <select name="genero" id="genero" class="form-control" required>
                            <option value="Masculino" <?php echo ($persona['genero'] === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Femenino" <?php echo ($persona['genero'] === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                            <option value="Otro" <?php echo ($persona['genero'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estadocivil" class="form-label">Estado Civil:</label>
                        <select name="estadocivil" id="estadocivil" class="form-control" required>
                            <option value="Soltero" <?php echo ($persona['estadocivil'] === 'Soltero') ? 'selected' : ''; ?>>Soltero</option>
                            <option value="Casado" <?php echo ($persona['estadocivil'] === 'Casado') ? 'selected' : ''; ?>>Casado</option>
                            <option value="Divorciado" <?php echo ($persona['estadocivil'] === 'Divorciado') ? 'selected' : ''; ?>>Divorciado</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="pais" class="form-label">País:</label>
                        <select name="pais" id="pais" class="form-control" onchange="loadProvincias()" required>
                            <option value="">-- Seleccione un País --</option>
                            <?php foreach ($paises as $pais): ?>
                                <option value="<?php echo $pais['id']; ?>" <?php echo ($pais['id'] == $persona['id_pais']) ? 'selected' : ''; ?>><?php echo $pais['nombre']; ?></option>
                            <?php endforeach; ?>
                           
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="provincia" class="form-label">Provincia:</label>
                        <select name="provincia" id="provincia" class="form-control" onchange="loadCiudades()" required>
                            <option value="">-- Seleccione una Provincia --</option>
                            <?php foreach ($provincias as $provincia): ?>
                                <option value="<?php echo $provincia['id']; ?>" <?php echo ($provincia['id'] == $persona['id_provincia']) ? 'selected' : ''; ?>><?php echo $provincia['nombre']; ?></option>
                            <?php endforeach; ?>
                           
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="ciudad" class="form-label">Ciudad:</label>
                        <select name="ciudad" id="ciudad" class="form-control" required>
                            <option value="">-- Seleccione una Ciudad --</option>
                            <?php foreach ($ciudades as $ciudad): ?>
                                <option value="<?php echo $ciudad['id']; ?>" <?php echo ($ciudad['id'] == $persona['id_ciudad']) ? 'selected' : ''; ?>><?php echo $ciudad['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">

                    <div class="col-md-4">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($persona['direccion'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="localidad" class="form-label">Localidad:</label>
                        <input type="text" id="localidad" name="localidad" class="form-control" value="<?php echo htmlspecialchars($persona['localidad'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>