<?php
// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_4'])) {
    try {
        $db->beginTransaction();

        // Insertar datos laborales
        $stmt = $db->prepare("INSERT INTO laboral (id_ppl, tiene_exp, experiencia, se_capacito, 
            en_que_se_capacito, posee_certific, formac_interes, tiene_incl_lab, lugar_inclusion,estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

        $stmt->execute([
            $_POST['id_ppl'],
            isset($_POST['tiene_exp']) ? 1 : 0,
            $_POST['experiencia'],
            isset($_POST['se_capacito']) ? 1 : 0,
            $_POST['en_que_se_capacito'],
            isset($_POST['posee_certific']) ? 1 : 0,
            $_POST['formac_interes'],
            isset($_POST['tiene_incl_lab']) ? 1 : 0,
            $_POST['lugar_inclusion'],
            'Activo'
        ]);

        // Insertar datos espirituales
        $stmt = $db->prepare("INSERT INTO asistencia_espiritual (id_ppl, practica_culto, culto, 
            desea_participar, eleccion_actividad,estado) 
            VALUES (?, ?, ?, ?, ?,?)");

        $stmt->execute([
            $_POST['id_ppl'],
            isset($_POST['practica_culto']) ? 1 : 0,
            $_POST['culto'],
            isset($_POST['desea_participar']) ? 1 : 0,
            $_POST['eleccion_actividad'],
            'Activo'
        ]);
        $db->commit();
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al guardar los datos: " . $e->getMessage() . "</div>";
    }
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
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* .btn {
            padding: 10px 15px;
            background-color: #212529;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        } */

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

    <form method="POST">
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($idppl); ?>">

        <!-- Sección Laboral -->
        <div class="form-section">
            <h3 id="titulo">Informe Laboral</h3>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="tiene_exp"> ¿Tiene experiencia previa a la detención?
                    </label>
                </div>
                <input type="text" name="experiencia" placeholder="Describa su experiencia...">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="se_capacito"> ¿Se capacitó en oficios?
                    </label>
                </div>
                <input type="text" name="en_que_se_capacito" placeholder="¿En qué se capacitó?">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="posee_certific"> Posee certificación del oficio
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Capacitación y/o formación que resulte de interés:</label>
                <input type="text" name="formac_interes">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="tiene_incl_lab"> Presenta posibilidad de inclusión laboral
                    </label>
                </div>
                <input type="text" name="lugar_inclusion" placeholder="¿Cuál/es?">
            </div>
        </div>

        <!-- Sección Asistencia Espiritual -->
        <div class="form-section">
            <h3 id="titulo">Asistencia Espiritual</h3>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="practica_culto"> ¿Practica algún culto?
                    </label>
                </div>
                <input type="text" name="culto" placeholder="Especifique el culto...">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="desea_participar"> ¿Desea participar en alguna actividad religiosa?
                    </label>
                </div>
                <input type="text" name="eleccion_actividad" placeholder="Especifique la actividad...">
            </div>
        </div>

        <button name="guardar_4" type="button" class="btn btn-primary">Guardar Información</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar sección de experiencia laboral
            const tieneExpCheckbox = document.querySelector('input[name="tiene_exp"]');
            const experienciaInput = document.querySelector('input[name="experiencia"]');

            // Ocultar el campo de experiencia por defecto
            experienciaInput.classList.add('hidden');

            tieneExpCheckbox.addEventListener('change', function() {
                experienciaInput.classList.toggle('hidden', !this.checked);
            });

            // Manejar sección de capacitación
            const seCapacitoCheckbox = document.querySelector('input[name="se_capacito"]');
            const enQueSeCapacitoInput = document.querySelector('input[name="en_que_se_capacito"]');

            // Ocultar el campo de capacitación por defecto
            enQueSeCapacitoInput.classList.add('hidden');

            seCapacitoCheckbox.addEventListener('change', function() {
                enQueSeCapacitoInput.classList.toggle('hidden', !this.checked);
            });

            // Manejar sección de inclusión laboral
            const tieneInclLabCheckbox = document.querySelector('input[name="tiene_incl_lab"]');
            const lugarInclusionInput = document.querySelector('input[name="lugar_inclusion"]');

            // Ocultar el campo de inclusión laboral por defecto
            lugarInclusionInput.classList.add('hidden');

            tieneInclLabCheckbox.addEventListener('change', function() {
                lugarInclusionInput.classList.toggle('hidden', !this.checked);
            });

            // Manejar sección de práctica de culto
            const practicaCultoCheckbox = document.querySelector('input[name="practica_culto"]');
            const cultoInput = document.querySelector('input[name="culto"]');

            // Ocultar el campo de práctica de culto por defecto
            cultoInput.classList.add('hidden');

            practicaCultoCheckbox.addEventListener('change', function() {
                cultoInput.classList.toggle('hidden', !this.checked);
            });

            // Manejar sección de deseo de participar en actividad religiosa
            const deseaParticiparCheckbox = document.querySelector('input[name="desea_participar"]');
            const eleccionActividadInput = document.querySelector('input[name="eleccion_actividad"]');

            // Ocultar el campo de elección de actividad por defecto
            eleccionActividadInput.classList.add('hidden');

            deseaParticiparCheckbox.addEventListener('change', function() {
                eleccionActividadInput.classList.toggle('hidden', !this.checked);
            });
        });
    </script>