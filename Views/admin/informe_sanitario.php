
<div class="tab-pane fade show active" id="tab4">
    <div class="content ">
        <div class="row m-auto ">
            <div class="col-sm">
                <h1 class="">Informe Sanitario</h1>
                <form action="procesar_informe.php" method="POST">
                    <!-- Estado General De Salud -->
                    <div class="mb-3">
                        <label for="estado_general" class="form-label">Estado General De Salud:</label>
                        <textarea maxlength="255" type="text" class="form-control" id="estado_general" name="estado_general" required></textarea>
                    </div>
                    <!-- Preguntas de enfermedades en columnas -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">¿Tiene alguna de las siguientes condiciones?</label>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="hipertension" name="hipertension" data-bs-toggle="modal" data-bs-target="#modalHipertension">
                                <label class="form-check-label" for="hipertension">Hipertensión</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="diabetes" name="diabetes" data-bs-toggle="modal" data-bs-target="#modalDiabetes">
                                <label class="form-check-label" for="diabetes">Diabetes</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enfermedad_corazon" name="enfermedad_corazon" data-bs-toggle="modal" data-bs-target="#modalCorazon">
                                <label class="form-check-label" for="enfermedad_corazon">¿Sufre alguna enfermedad del corazón?</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="asma" name="asma" data-bs-toggle="modal" data-bs-target="#modalAsma">
                                <label class="form-check-label" for="asma">Asma</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="alergias" name="alergias" data-bs-toggle="modal" data-bs-target="#modalAlergias">
                                <label class="form-check-label" for="alergias">Alergias</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="celiaco" name="celiaco">
                                <label class="form-check-label" for="celiaco">¿Es celiaco?</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="bulimia" name="bulimia">
                                <label class="form-check-label" for="bulimia">¿Padece bulimia o anorexia?</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="medicacion" name="medicacion" data-bs-toggle="modal" data-bs-target="#modalMedicacion">
                                <label class="form-check-label" for="medicacion">¿Toma alguna medicación?</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sonambulismo" name="sonambulismo">
                                <label class="form-check-label" for="sonambulismo">¿Sufre de sonambulismo?</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="embarazo" name="embarazo">
                                <label class="form-check-label" for="embarazo">¿Está embarazada?</label>
                            </div>
                        </div>
                    </div>
                    <!-- Enfermedades recientes-->
                    <div class="mb-3">
                        <label class="form-label">¿Ha padecido alguna de las siguientes condiciones recientemente?</label>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hepatitis" name="hepatitis">
                            <label class="form-check-label" for="hepatitis">Hepatitis</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mononucleosis" name="mononucleosis">
                            <label class="form-check-label" for="mononucleosis">Mononucleosis infecciosa</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="esguince" name="esguince">
                            <label class="form-check-label" for="esguince">Esguinces o luxaciones</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="convulsiones" name="convulsiones">
                            <label class="form-check-label" for="convulsiones">Convulsiones u operaciones recientes</label>
                        </div>
                    </div>
                    <!-- Datos Antropométricos-->
                    <div class="mb-3">
                        <h4>Datos Antropométricos</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="peso" class="form-label">Peso Actual (kg):</label>
                                <input type="number" step="0.1" class="form-control" id="peso" name="peso" required>
                            </div>
                            <div class="col-md-6">
                                <label for="talla" class="form-label">Talla (cm):</label>
                                <input type="number" class="form-control" id="talla" name="talla" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="imc" class="form-label">IMC:</label>
                                <input type="text" class="form-control" id="imc" name="imc" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="diagnostico" class="form-label">Diagnóstico:</label>
                                <input type="text" class="form-control" id="diagnostico" name="diagnostico" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="tipificacion_dieta" class="form-label">Tipificación de Dieta:</label>
                                <input type="text" class="form-control" id="tipificacion_dieta" name="tipificacion_dieta" required>
                            </div>
                        </div>
                    </div>
                    <!-- Botón de envío -->
                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Modales para que tiene que especificar-->
<div class="modal fade" id="modalCorazon" tabindex="-1" aria-labelledby="modalCorazonLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCorazonLabel">Especificar Enfermedad del Corazón</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="detalle_corazon" placeholder="Especificar condición">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAlergias" tabindex="-1" aria-labelledby="modalAlergiasLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAlergiasLabel">Especificar Alergias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="detalle_alergias" placeholder="Especificar condición">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalMedicacion" tabindex="-1" aria-labelledby="modalMedicacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMedicacionLabel">Especificar Medicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="detalle_medicacion" placeholder="Especificar medicación">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalHipertension" tabindex="-1" aria-labelledby="modalHipertensionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHipertensionLabel">Especificar Hipertensión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="detalle_hipertension" placeholder="Especificar condición">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDiabetes" tabindex="-1" aria-labelledby="modalDiabetesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiabetesLabel">Especificar Diabetes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="detalle_diabetes" placeholder="Especificar condición">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAsma" tabindex="-1" aria-labelledby="modalAsmaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsmaLabel">Especificar Asma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="detalle_asma" placeholder="Especificar condición">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('peso').addEventListener('input', calcularIMC);
    document.getElementById('talla').addEventListener('input', calcularIMC);

    function calcularIMC() {
        const peso = parseFloat(document.getElementById('peso').value);
        const talla = parseFloat(document.getElementById('talla').value) / 100;
        const imc = peso / (talla * talla);
        document.getElementById('imc').value = imc.toFixed(2);
    }
</script>