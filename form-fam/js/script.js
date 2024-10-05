function handleVinculoChange(vinculo) {
    const formContent = document.getElementById('form-content');
    formContent.innerHTML = ''; // Limpiar el contenido anterior

    if (vinculo) {
        const camposComunes = renderCamposComunes();
        let camposAdicionales = '';

        if (vinculo === 'PADRE' || vinculo === 'MADRE') {
            camposAdicionales = `
                <div class="field-group">
                    <label for="nacionalidad">Nacionalidad</label>
                    <input id="nacionalidad" placeholder="Ingrese nacionalidad" required>
                </div>
                <div class="field-group">
                    <label for="estadoCivil">Estado Civil</label>
                    <select id="estadoCivil" required>
                        <option value="">Seleccione estado civil</option>
                        <option value="SOLTERO">Soltero/a</option>
                        <option value="CASADO">Casado/a</option>
                        <option value="VIUDO">Viudo/a</option>
                        <option value="DIVORCIADO">Divorciado/a</option>
                        <option value="SEPARADO">Separado/a de Hecho</option>
                        <option value="CONCUBINO">Concubino/a</option>
                    </select>
                </div>
                ${renderEsFFAAyDetenido()}
                <div class="field-group">
                    <label for="gradoInstruccion">Grado de Instrucción</label>
                    <input id="gradoInstruccion" placeholder="Ingrese grado de instrucción" required>
                </div>
                <div class="field-group">
                    <label for="ocupacion">Oficio</label>
                    <input id="ocupacion" placeholder="Ingrese oficio" required>
                </div>
            `;
        } else if (vinculo === 'CONCUBINO' || vinculo === 'ESPOSO') {
            camposAdicionales = `
                <div class="field-group">
                    <label for="nacionalidad">Nacionalidad</label>
                    <input id="nacionalidad" placeholder="Ingrese nacionalidad" required>
                </div>
                <div class="field-group">
                    <label for="gradoInstruccion">Grado de Instrucción</label>
                    <input id="gradoInstruccion" placeholder="Ingrese grado de instrucción" required>
                </div>
                <div class="field-group">
                    <label for="ocupacion">Ocupación</label>
                    <input id="ocupacion" placeholder="Ingrese ocupación" required>
                </div>
                ${renderEsFFAAyDetenido()}
            `;
        } else if (vinculo === 'HERMANO' || vinculo === 'HIJO') {
            camposAdicionales = `
                ${renderEsFFAAyDetenido()}
            `;
        }

        formContent.innerHTML = camposComunes + camposAdicionales;
    }
}

function renderCamposComunes() {
    return `
        <div class="field-group">
            <label for="vive">Vive</label>
            <select id="vive" onchange="handleViveChange(this.value)" required>
                <option value="SI">Sí</option>
                <option value="NO">No</option>
            </select>
        </div>
        <div id="muerte-info"></div>
        <div class="field-group">
            <label for="apellido">Apellido</label>
            <input id="apellido" placeholder="Ingrese apellido" required>
        </div>
        <div class="field-group">
            <label for="nombre">Nombre</label>
            <input id="nombre" placeholder="Ingrese nombre" required>
        </div>
        <div class="field-group">
            <label for="edad">Edad</label>
            <input id="edad" type="number" placeholder="Ingrese edad" required>
        </div>
    `;
}

function handleViveChange(value) {
    const muerteInfo = document.getElementById('muerte-info');
    if (value === 'NO') {
        muerteInfo.innerHTML = `
            <div class="field-group">
                <label>Fecha de fallecimiento</label>
                <input id="fecha-muerte" type="date" required>
            </div>
            <div class="field-group">
                <label>Causa</label>
                <input id="causa-muerte" placeholder="Ingrese la causa de fallecimiento" required>
            </div>
        `;
    } else {
        muerteInfo.innerHTML = '';
    }
}

function renderEsFFAAyDetenido() {
    return `
        <div class="field-group">
            <label>Es FFAA</label>
            <select id="esFFAA" required>
                <option value="NO">No</option>
                <option value="SI">Sí</option>
            </select>
        </div>
        <div class="field-group">
            <label>Estuvo o está detenido</label>
            <select id="estaDetenido" required>
                <option value="NO">No</option>
                <option value="SI">Sí</option>
            </select>
        </div>
    `;
}