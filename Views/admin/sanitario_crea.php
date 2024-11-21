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
$id_ppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO datos_medicos (id_ppl, hipertension, diabetes, enfermedad_corazon, asma, 
            epilepsia, alergia, es_celiaco, bulimia_anorexia, medicacion, metabolismo, embarazo, hepatitis, 
            mononucleosis, otras_enfermedades, peso_actual, talla, imc, diagnostico, tipificacion_dieta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_ppl'],
            isset($_POST['hipertension']) ? 1 : 0,
            isset($_POST['diabetes']) ? 1 : 0,
            isset($_POST['enfermedad_corazon']) ? 1 : 0,
            isset($_POST['asma']) ? 1 : 0,
            isset($_POST['epilepsia']) ? 1 : 0,
            isset($_POST['alergia']) ? 1 : 0,
            isset($_POST['es_celiaco']) ? 1 : 0,
            isset($_POST['bulimia_anorexia']) ? 1 : 0,
            $_POST['medicacion'],
            isset($_POST['metabolismo']) ? 1 : 0,
            isset($_POST['embarazo']) ? 1 : 0,
            isset($_POST['hepatitis']) ? 1 : 0,
            isset($_POST['mononucleosis']) ? 1 : 0,
            $_POST['otras_enfermedades'],
            $_POST['peso_actual'],
            $_POST['talla'],
            $_POST['imc'],
            $_POST['diagnostico'],
            $_POST['tipificacion_dieta']
        ]);
        $db->commit();
        $accion = 'Agregar Datos Médicos';
        $tabla_afectada = 'datos_medicos';
        $detalles = "Se insertaron los datos médicos para el paciente con ID: $id_ppl";
        registrarAuditoria($db, $accion, $tabla_afectada, $id_ppl, $detalles);
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
        <input type="hidden" name="id_ppl" value="<?php echo htmlspecialchars($id_ppl); ?>">

        <div class="form-section">
            <h3 id="titulo">Datos Médicos</h3>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="hipertension"> Hipertensión
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="diabetes"> Diabetes
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="enfermedad_corazon"> ¿Sufre alguna enfermedad al corazón?
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>¿Cuál?</label>
                <input type="text" name="enfermedad_corazon_cual">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="asma"> Asma
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="epilepsia"> Enfermedades del sistema nervioso - Epilepsia
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="alergia"> Alergia
                    </label>
                </div>
                <input type="text" name="alergia_especifique" placeholder="Especifique...">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="es_celiaco"> ¿Es celiaco?
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="bulimia_anorexia"> ¿Padece bulimia o anorexia?
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>¿Toma alguna medicación?</label>
                <input type="text" name="medicacion" placeholder="Especifique medicación...">
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="metabolismo"> ¿Sufre metabolismo?
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="embarazo"> Embarazo
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="hepatitis"> Hepatitis
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="mononucleosis"> Mononucleosis infecciosa
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Otras enfermedades, luxaciones, etc.:</label>
                <input type="text" name="otras_enfermedades" placeholder="Especifique...">
            </div>
            <h3 id="titulo">Datos Antropométricos</h3>
            <div class="form-group">
                <label>Peso actual (kg):</label>
                <input type="text" name="peso_actual" required>
            </div>
            <div class="form-group">
                <label>Talla (cm):</label>
                <input type="text" name="talla">
            </div>
            <div class="form-group">
                <label>IMC:</label>
                <input type="text" name="imc" readonly>
            </div>
            <div class="form-group">
                <label>Diagnóstico:</label>
                <input type="text" name="diagnostico">
            </div>
            <div class="form-group">
                <label>Tipificación de dieta:</label>
                <input type="text" name="tipificacion_dieta">
            </div>
        </div>
        
        <button name="guardar" type="submit" class="btn btn-primary">Guardar Información</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pesoInput = document.querySelector('input[name="peso_actual"]');
            const tallaInput = document.querySelector('input[name="talla"]');
            const imcInput = document.querySelector('input[name="imc"]');

            pesoInput.addEventListener('input', function() {
                const peso = parseFloat(this.value);
                const talla = parseFloat(tallaInput.value);
                const imc = calcularIMC(peso, talla);
                imcInput.value = imc;
            });

            tallaInput.addEventListener('input', function() {
                const peso = parseFloat(pesoInput.value);
                const talla = parseFloat(this.value);
                const imc = calcularIMC(peso, talla);
                imcInput.value = imc;
            });
        });

        function calcularIMC(peso, talla) {
            if (talla > 0) {
                return (peso / ((talla / 100) * (talla / 100))).toFixed(2);
            } else {
                return 0;
            }
        }
    </script>
</body>