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

    if (!$persona) {
        die("Persona no encontrada.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $stmt_update_direccion = $db->prepare("
            UPDATE domicilio 
            SET direccion = :direccion, 
                id_pais = :pais, 
                id_provincia = :provincia, 
                id_ciudad = :ciudad, 
                localidad = :localidad 
            WHERE id = :id_direccion
        ");
        $stmt_update_direccion->bindParam(':direccion', $direccion);
        $stmt_update_direccion->bindParam(':pais', $pais);
        $stmt_update_direccion->bindParam(':provincia', $provincia);
        $stmt_update_direccion->bindParam(':ciudad', $ciudad);
        $stmt_update_direccion->bindParam(':localidad', $localidad);
        $stmt_update_direccion->bindParam(':id_direccion', $persona['id_direccion']);

        $stmt_update = $db->prepare("
            UPDATE persona 
            SET dni = :dni, 
                nombres = :nombres, 
                apellidos = :apellidos, 
                fechanac = :fechaNacimiento, 
                edad = :edad, 
                genero = :genero, 
                estadocivil = :estadocivil
            WHERE id = :id
        ");
        $stmt_update->bindParam(':dni', $dni);
        $stmt_update->bindParam(':nombres', $nombres);
        $stmt_update->bindParam(':apellidos', $apellidos);
        $stmt_update->bindParam(':fechaNacimiento', $fechaNacimiento);
        $stmt_update->bindParam(':edad', $edad);
        $stmt_update->bindParam(':genero', $genero);
        $stmt_update->bindParam(':estadocivil', $estadocivil);
        $stmt_update->bindParam(':id', $id);

        $stmt_update->execute();
        $stmt_update_direccion->execute();

        $accion = 'Editar PPL - Persona';
        $tabla_afectada = 'persona;ppl';
        $detalles = "Se edito el PPL con ID: $id";
        registrarAuditoria($db, $accion, $tabla_afectada, $id, $detalles);

        header("Location: ppl_informe.php?id=" . urlencode($id) . "&mensaje=" . urlencode("PPL - Persona Editado con éxito."));
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Editar Persona</h4>
        </div>
        <div class="card-body">
            <form action="persona_edit.php?id=<?php echo $persona['id']; ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo ($persona['id']); ?>">

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
                        <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" value="<?php echo htmlspecialchars($persona['fechaNacimiento'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="edad" class="form-label">Edad:</label>
                        <input type="number" id="edad" name="edad" class="form-control" value="<?php echo htmlspecialchars($persona['edad'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="genero" class="form-label">Género:</label>
                        <select class="form-select" id="genero" name="genero" value="<?php echo htmlspecialchars($persona['genero'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <option value="Masculino" selected>Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estadocivil" class="form-label">Estado Civil:</label>
                        <select class="form-select" id="estadocivil" name="estadocivil" value="<?php echo htmlspecialchars($persona['estadocivil'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <option value="Soltero" selected>Soltero</option>
                            <option value="Casado">Casado</option>
                            <option value="Divorciado">Divorciado</option>
                            <option value="Viudo">Viudo</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-4">
                        <label for="pais" class="form-label">País:</label>
                        <select class="form-select" id="pais" name="pais" value="<?php echo htmlspecialchars($persona['pais'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <option value="" style="color: red;">-- Seleccione un País --</option>
                            <?php
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
                        <label for="provincia" class="form-label">Provincia:</label>
                        <select class="form-select" id="provincia" name="provincia" value="<?php echo htmlspecialchars($persona['provincia'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <option value="" style="color: red;">-- Seleccione una Provincia --</option>
                            <?php
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
                        <label for="ciudad" class="form-label">Ciudad:</label>
                        <select class="form-select" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($persona['ciudad'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            <option value="">-- Seleccione una Ciudad --</option>
                            <?php
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

                    <div class="col-md-4">
                        <label for="localidad" class="form-label">Localidad:</label>
                        <input type="text" id="localidad" name="localidad" class="form-control" value="<?php echo htmlspecialchars($persona['localidad'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($persona['direccion'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-success btn-lg">Actualizar Datos</button>
                </div>

            </form>
        </div>
    </div>
</div>