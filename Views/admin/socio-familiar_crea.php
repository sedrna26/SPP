<?php
// Definir la ruta base del proyecto
define('BASE_PATH', dirname(__DIR__, 2));

// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_3'])) {
    try {
        $db->beginTransaction();

        // Insertar información familiar general en ppl_familiar_info
        $stmt = $db->prepare("INSERT INTO ppl_familiar_info (idppl, familiares_ffaa, ffaa_detalles, familiares_detenidos, detenidos_detalles, telefono_familiar, posee_dni, motivo_no_dni,estado) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
        $stmt->execute([
            $idppl,
            isset($_POST['familiares_ffaa']) && $_POST['familiares_ffaa'] == '1' ? 1 : 0,
            $_POST['ffaa_details'] ?? null,
            isset($_POST['familiares_detenidos']) && $_POST['familiares_detenidos'] == '1' ? 1 : 0,
            $_POST['detenidos_details'] ?? null,
            $_POST['telefono_familiar'] ?? null,
            $_POST['posee_dni'] === 'SI' ? 1 : 0,
            $_POST['motivo_no_dni'] ?? null,
            'Activo'  // Añadido estado por defecto
        ]);

        // Insertar situación sociofamiliar
        $stmt = $db->prepare("INSERT INTO ppl_situacion_sociofamiliar (idppl, edad_inicio_laboral, situacion_economica_precaria, mendicidad_calle,estado) 
                             VALUES (?, ?, ?, ?,?)");
        $stmt->execute([
            $idppl,
            $_POST['edad_laboral'] ?? null,
            $_POST['situacion_economica'] === 'SI' ? 1 : 0,
            $_POST['mendicidad'] === 'SI' ? 1 : 0,
            'Activo'
        ]);

        // Insertar datos del padre si está vivo
        if (!empty($_POST['padre_nombre']) && isset($_POST['padre_vivo'])) {
            $stmt = $db->prepare("INSERT INTO ppl_padres (idppl, tipo, vivo, apellido, nombre, edad, nacionalidad, estado_civil, instruccion, visita,estado) 
                                VALUES (?, 'PADRE', ?, ?, ?, ?, ?, ?, ?, ?,?)");
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

        // Insertar datos de la madre si está viva
        if (!empty($_POST['madre_nombre']) && isset($_POST['madre_viva'])) {
            $stmt = $db->prepare("INSERT INTO ppl_padres (idppl, tipo, vivo, apellido, nombre, edad, nacionalidad, estado_civil, instruccion, visita,estado) 
                                VALUES (?, 'MADRE', ?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->execute([
                $idppl,
                $_POST['padre_vivo'] === 'Vivo' ? 1 : 0,
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

        // Insertar datos de hermanos
        if (isset($_POST['num_hermanos']) && intval($_POST['num_hermanos']) > 0) {
            $stmt = $db->prepare("INSERT INTO ppl_hermanos (idppl, apellido, nombre, edad, visita,estado) 
                                VALUES (?, ?, ?, ?, ?,?)");

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

        // Insertar datos de la pareja si existe
        if (!empty($_POST['pareja_nombre'])) {
            $stmt = $db->prepare("INSERT INTO ppl_pareja (idppl, apellido, nombre, edad, nacionalidad, instruccion, tipo_union, visita,estado) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
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

        // Insertar datos de hijos
        if (
            isset($_POST['tiene_hijos']) && $_POST['tiene_hijos'] == '1' &&
            isset($_POST['num_hijos']) && intval($_POST['num_hijos']) > 0
        ) {

            $stmt = $db->prepare("INSERT INTO ppl_hijos (idppl, apellido, nombre, edad, fallecido, visita,estado) 
                                VALUES (?, ?, ?, ?, ?, ?.?)");

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

        // Insertar otros visitantes
        if (
            isset($_POST['visita_otros']) && $_POST['visita_otros'] === 'SI' &&
            isset($_POST['otro_nombre']) && is_array($_POST['otro_nombre'])
        ) {

            $stmt = $db->prepare("INSERT INTO ppl_otros_visitantes (idppl, apellido, nombre, telefono, domicilio, vinculo_filial,estado) 
                                VALUES (?, ?, ?, ?, ?, ?,?)");

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
        echo "<div class='alert alert-success'>Datos guardados correctamente</div>";
    } catch (Exception $e) {
        $db->rollBack();
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
<form onsubmit="enviarFormulario(event)" id="familyDataForm" method="POST" novalidate>
    <input type="hidden" name="idppl" value="<?php echo htmlspecialchars($idppl); ?>">
    <!-- Familiares FF.AA y Detenidos -->
    <div class="form-section">
        <div class="form-group">
            <label id="titulo">Familiares de FF.AA:</label>
            <select name="familiares_ffaa" id="familiares_ffaa" required>
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>

        </div>
        <div id="ffaa_details" class="hidden">
            <input type="text" name="ffaa_details" placeholder="Especifique familiares FF.AA" required>
        </div>
        <div class="form-group">
            <label id="titulo">Familiares Detenidos:</label>
            <select name="familiares_detenidos" id="familiares_detenidos" required>
                <option value="0">No</option>
                <option value="1">Sí</option>
            </select>

        </div>
        <div id="detenidos_details" class="hidden">
            <input type="text" name="detenidos_details" placeholder="Especifique familiares detenidos" required>
        </div>
    </div>

    <!-- Datos del Padre -->
    <div class="form-section">
        <h3 id="titulo">Datos del Padre</h3>
        <div class="form-group">
            <label>Estado del padre:</label>
            <select name="padre_vivo" id="padre_vivo" required>
                <option value="1">Vivo</option>
                <option value="0">Fallecido</option>
            </select>
        </div>
        <div id="padre_details" class="familiar-container">
            <input type="text" name="padre_apellido" placeholder="Apellido" required>
            <input type="text" name="padre_nombre" placeholder="Nombre" required>
            <input type="number" name="padre_edad" placeholder="Edad" required>
            <input type="text" name="padre_nacionalidad" placeholder="Nacionalidad" required>
            <select name="padre_estado_civil" required>
                <option value="">Estado Civil</option>
                <option value="Soltero">Soltero</option>
                <option value="Casado">Casado</option>
                <option value="Divorciado">Divorciado</option>
                <option value="Viudo">Viudo</option>
                <option value="Union Convivencial">Unión Convivencial</option>
            </select>
            <input type="text" name="padre_instruccion" placeholder="Grado de instrucción/profesión/oficio"
                required>
            <div id="padre_details" class="familiar-container">
                <input type="text" name="padre_apellido" placeholder="Apellido" required>
                <input type="text" name="padre_nombre" placeholder="Nombre" required>
                <input type="number" name="padre_edad" placeholder="Edad" required>
                <input type="text" name="padre_nacionalidad" placeholder="Nacionalidad" required>
                <select name="padre_estado_civil" required>
                    <option value="">Estado Civil</option>
                    <option value="Soltero">Soltero</option>
                    <option value="Casado">Casado</option>
                    <option value="Divorciado">Divorciado</option>
                    <option value="Viudo">Viudo</option>
                    <option value="Union Convivencial">Unión Convivencial</option>
                </select>
                <input type="text" name="padre_instruccion" placeholder="Grado de instrucción/profesión/oficio" required>
            </div>
        </div>

        <!-- Datos de la Madre -->
        <div class="form-section">
            <h3 id="titulo">Datos de la Madre</h3>
            <div class="form-group">
                <label>Estado de la madre:</label>
                <select name="madre_viva" id="madre_viva" required>
                    <option value="1">Viva</option>
                    <option value="0">Fallecida</option>
                </select>
            </div>
            <div id="madre_details" class="familiar-container">
                <input type="text" name="madre_apellido" placeholder="Apellido" required>
                <input type="text" name="madre_nombre" placeholder="Nombre" required>
                <input type="number" name="madre_edad" placeholder="Edad" required>
                <input type="text" name="madre_nacionalidad" placeholder="Nacionalidad" required>
                <select name="madre_estado_civil" required>
                    <option value="">Estado Civil</option>
                    <option value="Soltera">Soltera</option>
                    <option value="Casada">Casada</option>
                    <option value="Divorciada">Divorciada</option>
                    <option value="Viuda">Viuda</option>
                    <option value="Union Convivencial">Unión Convivencial</option>
                </select>
                <input type="text" name="madre_instruccion" placeholder="Grado de instrucción/profesión/oficio"
                    required>
            </div>
        </div>

        <!-- Hermanos -->
        <div class="form-section">
            <h3 id="titulo">Hermanos</h3>
            <div class="form-group">
                <label>Número de hermanos:</label>
                <select name="num_hermanos" id="num_hermanos" required>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
            </div>
            <div id="hermanos_container"></div>
        </div>

        <!-- Grupo Familiar Secundario -->
        <div class="form-section">
            <h3 id="titulo">Grupo Familiar Secundario</h3>
            <div class="familiar-container">
                <input type="text" name="pareja_apellido" placeholder="Apellido">
                <input type="text" name="pareja_nombre" placeholder="Nombre">
                <input type="number" name="pareja_edad" placeholder="Edad">
                <input type="text" name="pareja_nacionalidad" placeholder="Nacionalidad">
                <input type="text" name="pareja_instruccion" placeholder="Nivel de Instrucción y Ocupación">
                <select name="pareja_tipo_union">
                    <option value="">Tipo de Unión</option>
                    <option value="Casado/a">Casado/a</option>
                    <option value="Union Convivencial">Unión Convivencial</option>
                </select>

                <div class="form-group">
                    <label>¿Tiene hijos?</label>
                    <select name="tiene_hijos" id="tiene_hijos">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <div id="hijos_section" class="hidden">
                    <div class="form-group">
                        <label>Número de hijos:</label>
                        <select name="num_hijos" id="num_hijos">
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div id="hijos_container"></div>

                    <div class="form-group">
                        <label>¿Tiene hijos fallecidos?</label>
                        <select name="hijos_fallecidos">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Situación Sociofamiliar -->
        <div class="form-section">
            <h3 id="titulo">Situación Sociofamiliar Durante la Niñez o Adolescencia</h3>

            <div class="form-group">
                <label>Edad de inicio de actividades laborales:</label>
                <input type="number" name="edad_laboral" min="0" max="100" class="form-control">
            </div>

            <div class="form-group">
                <label>Situación económica precaria o inestable en el grupo familiar primario:</label>
                <select name="situacion_economica">
                    <option value="0">NO</option>
                    <option value="SI">SI</option>
                </select>
            </div>

            <div class="form-group">
                <label>Presentó mendicidad y/o habitabilidad en situación de calle:</label>
                <select name="mendicidad">
                    <option value="NO">NO</option>
                    <option value="SI">SI</option>
                </select>
            </div>

            <div class="form-group">
                <label>Número de teléfono de algún familiar:</label>
                <input type="tel" name="telefono_familiar" class="form-control">
            </div>
        </div>

        <!-- Personas que ingresarán como visita -->
        <div class="form-section">
            <h3 id="titulo">Personas que ingresarán como visita</h3>

            <div class="form-group">
                <label>Seleccione los visitantes:</label>
                <div class="familiar-container">
                    <div class="form-group">
                        <select name="visita_padre" id="visita_padre">
                            <option value="NO">Padre - NO</option>
                            <option value="SI">Padre - SI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="visita_madre" id="visita_madre">
                            <option value="NO">Madre - NO</option>
                            <option value="SI">Madre - SI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="visita_hermanos" id="visita_hermanos">
                            <option value="NO">Hermanos - NO</option>
                            <option value="SI">Hermanos - SI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="visita_esposo" id="visita_esposo">
                            <option value="NO">Esposo/a - NO</option>
                            <option value="SI">Esposo/a - SI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="visita_concubino" id="visita_concubino">
                            <option value="NO">Concubino/a - NO</option>
                            <option value="SI">Concubino/a - SI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="visita_hijos" id="visita_hijos">
                            <option value="NO">Hijos - NO</option>
                            <option value="SI">Hijos - SI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="visita_otros" id="visita_otros">
                            <option value="NO">Otros - NO</option>
                            <option value="SI">Otros - SI</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="otrosVisitantes" class="hidden">
                <div class="familiar-container">
                    <div class="form-group">
                        <label>Apellido:</label>
                        <input type="text" name="otro_apellido[]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="otro_nombre[]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Número de teléfono:</label>
                        <input type="tel" name="otro_telefono[]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Domicilio:</label>
                        <input type="text" name="otro_domicilio[]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Vínculo Filial:</label>
                        <input type="text" name="otro_vinculo[]" class="form-control">
                    </div>
                </div>

                <div id="otrosVisitantes" class="hidden">
                    <div class="familiar-container">
                        <div class="form-group">
                            <label>Apellido:</label>
                            <input type="text" name="otro_apellido[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="otro_nombre[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Número de teléfono:</label>
                            <input type="tel" name="otro_telefono[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Domicilio:</label>
                            <input type="text" name="otro_domicilio[]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Vínculo Filial:</label>
                            <input type="text" name="otro_vinculo[]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos Anexos -->
            <div class="form-section">
                <h3 id="titulo">Datos Anexos</h3>

                <div class="form-group">
                    <label>¿Posee su D.N.I?</label>
                    <select name="posee_dni" id="posee_dni">
                        <option value="">Seleccionar</option>
                        <option value="NO">NO</option>
                        <option value="SI">SI</option>
                    </select>
                </div>

                <div id="motivo_dni" class="form-group hidden">
                    <label>¿Por qué no?</label>
                    <input type="text" name="motivo_no_dni" class="form-control">
                </div>
            </div>
            <button name="guardar_3" type="button" class=" btn btn-primary">Guardar Datos</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar familiares FF.AA
        document.getElementById('familiares_ffaa').addEventListener('change', function() {
            document.getElementById('ffaa_details').classList.toggle('hidden', this.value === 'NO');
        });

        // Manejar familiares detenidos
        document.getElementById('familiares_detenidos').addEventListener('change', function() {
            document.getElementById('detenidos_details').classList.toggle('hidden', this.value === 'NO');
        });

        // Manejar estado del padre
        document.getElementById('padre_vivo').addEventListener('change', function() {
            const detallesPadre = document.getElementById('padre_details');
            detallesPadre.classList.toggle('status-fallecido', this.value === 'NO');
        });

        // Manejar estado de la madre
        document.getElementById('madre_viva').addEventListener('change', function() {
            const detallesMadre = document.getElementById('madre_details');
            detallesMadre.classList.toggle('status-fallecido', this.value === 'NO');
        });

        // Manejar hermanos
        document.getElementById('num_hermanos').addEventListener('change', function() {
            const container = document.getElementById('hermanos_container');
            container.innerHTML = '';

            const numHermanos = parseInt(this.value);
            for (let i = 0; i < numHermanos; i++) {
                const hermanoDiv = document.createElement('div');
                hermanoDiv.className = 'familiar-container';
                hermanoDiv.innerHTML = `
                        <h4>Hermano ${i + 1}</h4>
                        <input type="text" name="hermano_apellido_${i}" placeholder="Apellido" required>
                        <input type="text" name="hermano_nombre_${i}" placeholder="Nombre" required>
                        <input type="number" name="hermano_edad_${i}" placeholder="Edad" required>
                    `;
                container.appendChild(hermanoDiv);
            }
        });

        // Manejar hijos
        document.getElementById('tiene_hijos').addEventListener('change', function() {
            document.getElementById('hijos_section').classList.toggle('hidden', this.value === 'NO');
        });

        document.getElementById('num_hijos').addEventListener('change', function() {
            const container = document.getElementById('hijos_container');
            container.innerHTML = '';

            const numHijos = parseInt(this.value);
            for (let i = 0; i < numHijos; i++) {
                const hijoDiv = document.createElement('div');
                hijoDiv.className = 'familiar-container';
                hijoDiv.innerHTML = `
                        <h4>Hijo ${i + 1}</h4>
                        <input type="text" name="hijo_apellido_${i}" placeholder="Apellido" required>
                        <input type="text" name="hijo_nombre_${i}" placeholder="Nombre" required>
                        <input type="number" name="hijo_edad_${i}" placeholder="Edad" required>
                    `;
                container.appendChild(hijoDiv);
            }
        });
    });
    // Actualizar el script para manejar los nuevos selects
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar otros visitantes
        document.getElementById('visita_otros').addEventListener('change', function() {
            document.getElementById('otrosVisitantes').classList.toggle('hidden', this.value === 'NO');
        });

        // Manejar motivo DNI
        document.getElementById('posee_dni').addEventListener('change', function() {
            document.getElementById('motivo_dni').classList.toggle('hidden', this.value === 'SI');
        });
    });
    // Modificar el HTML para las secciones de visitas de hermanos e hijos
    const visitaHermanosSelect = document.getElementById('visita_hermanos');
    const visitaHijosSelect = document.getElementById('visita_hijos');

    // Contenedores para las selecciones específicas
    const visitaHermanosContainer = document.createElement('div');
    visitaHermanosContainer.id = 'visita_hermanos_container';
    visitaHermanosContainer.className = 'hidden familiar-container';
    visitaHermanosSelect.parentNode.insertBefore(visitaHermanosContainer, visitaHermanosSelect.nextSibling);

    const visitaHijosContainer = document.createElement('div');
    visitaHijosContainer.id = 'visita_hijos_container';
    visitaHijosContainer.className = 'hidden familiar-container';
    visitaHijosSelect.parentNode.insertBefore(visitaHijosContainer, visitaHijosSelect.nextSibling);

    // Event listeners para los selects de visitas
    visitaHermanosSelect.addEventListener('change', function() {
        visitaHermanosContainer.classList.toggle('hidden', this.value === 'NO');
        if (this.value === 'SI') {
            updateHermanosVisitaOptions();
        }
    });

    visitaHijosSelect.addEventListener('change', function() {
        visitaHijosContainer.classList.toggle('hidden', this.value === 'NO');
        if (this.value === 'SI') {
            updateHijosVisitaOptions();
        }
    });

    // Función para actualizar las opciones de hermanos visitantes
    function updateHermanosVisitaOptions() {
        const container = document.getElementById('visita_hermanos_container');
        container.innerHTML = '<h4>Seleccione los hermanos que ingresarán como visita:</h4>';

        // Obtener todos los hermanos ingresados
        const numHermanos = parseInt(document.getElementById('num_hermanos').value);

        for (let i = 0; i < numHermanos; i++) {
            const hermanoApellido = document.querySelector(`input[name="hermano_apellido_${i}"]`)?.value || '';
            const hermanoNombre = document.querySelector(`input[name="hermano_nombre_${i}"]`)?.value || '';

            if (hermanoApellido || hermanoNombre) {
                const div = document.createElement('div');
                div.className = 'form-group';
                div.innerHTML = `
                <select name="hermano_visita_${i}" class="form-control">
                    <option value="NO">${hermanoApellido} ${hermanoNombre} - NO</option>
                    <option value="SI">${hermanoApellido} ${hermanoNombre} - SI</option>
                </select>
            `;
                container.appendChild(div);
            }
        }
    }

    // Función para actualizar las opciones de hijos visitantes
    function updateHijosVisitaOptions() {
        const container = document.getElementById('visita_hijos_container');
        container.innerHTML = '<h4>Seleccione los hijos que ingresarán como visita:</h4>';

        // Obtener todos los hijos ingresados
        const numHijos = parseInt(document.getElementById('num_hijos').value);

        for (let i = 0; i < numHijos; i++) {
            const hijoApellido = document.querySelector(`input[name="hijo_apellido_${i}"]`)?.value || '';
            const hijoNombre = document.querySelector(`input[name="hijo_nombre_${i}"]`)?.value || '';

            if (hijoApellido || hijoNombre) {
                const div = document.createElement('div');
                div.className = 'form-group';
                div.innerHTML = `
                <select name="hijo_visita_${i}" class="form-control">
                    <option value="NO">${hijoApellido} ${hijoNombre} - NO</option>
                    <option value="SI">${hijoApellido} ${hijoNombre} - SI</option>
                </select>
            `;
                container.appendChild(div);
            }
        }
    }

    // Agregar listeners para actualizar las opciones cuando se modifiquen los datos de hermanos o hijos
    document.getElementById('num_hermanos').addEventListener('change', function() {
        if (visitaHermanosSelect.value === 'SI') {
            setTimeout(updateHermanosVisitaOptions, 100); // Pequeño delay para permitir que se creen los inputs
        }
    });

    document.getElementById('num_hijos').addEventListener('change', function() {
        if (visitaHijosSelect.value === 'SI') {
            setTimeout(updateHijosVisitaOptions, 100); // Pequeño delay para permitir que se creen los inputs
        }
    });

    // Agregar listeners para los campos de nombre y apellido
    document.getElementById('hermanos_container').addEventListener('input', function(e) {
        if (visitaHermanosSelect.value === 'SI' && (e.target.name.startsWith('hermano_apellido_') || e.target.name.startsWith('hermano_nombre_'))) {
            updateHermanosVisitaOptions();
        }
    });

    document.getElementById('hijos_container').addEventListener('input', function(e) {
        if (visitaHijosSelect.value === 'SI' && (e.target.name.startsWith('hijo_apellido_') || e.target.name.startsWith('hijo_nombre_'))) {
            updateHijosVisitaOptions();
        }
    });
</script>