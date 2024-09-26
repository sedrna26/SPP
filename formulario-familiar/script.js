function handleVinculoChange(vinculo) {
    const formContent = document.getElementById('form-content');
    formContent.innerHTML = ''; // Limpiar el contenido anterior
  
    if (vinculo === 'PADRE' || vinculo === 'MADRE') {
      formContent.innerHTML = `
        ${renderCamposComunes()}
        <div class="field-group">
          <label for="nacionalidad">Nacionalidad</label>
          <input id="nacionalidad" placeholder="Ingrese nacionalidad">
        </div>
        <div class="field-group">
          <label for="estadoCivil">Estado Civil</label>
          <select id="estadoCivil">
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
          <input id="gradoInstruccion" placeholder="Ingrese grado de instrucción">
        </div>
        <div class="field-group">
          <label for="ocupacion">Oficio</label>
          <input id="ocupacion" placeholder="Ingrese oficio">
        </div>
      `;
    } else if (vinculo === 'CONCUBINO' || vinculo === 'ESPOSO') {
      formContent.innerHTML = `
        ${renderCamposComunes()}
        <div class="field-group">
          <label for="nacionalidad">Nacionalidad</label>
          <input id="nacionalidad" placeholder="Ingrese nacionalidad">
        </div>
        <div class="field-group">
          <label for="gradoInstruccion">Grado de Instrucción</label>
          <input id="gradoInstruccion" placeholder="Ingrese grado de instrucción">
        </div>
        <div class="field-group">
          <label for="ocupacion">Ocupación</label>
          <input id="ocupacion" placeholder="Ingrese ocupación">
        </div>
        ${renderEsFFAAyDetenido()}
      `;
    
      }else if (vinculo === 'HERMANO' || vinculo === 'HIJO') {
      formContent.innerHTML = `
        ${renderCamposComunes()}
        ${renderEsFFAAyDetenido()}
        
      `;
    } 
    
  }
  
  function renderCamposComunes() {
    return `
      <div class="field-group">
        <label for="apellido">Apellido</label>
        <input id="apellido" placeholder="Ingrese apellido">
      </div>
      <div class="field-group">
        <label for="nombre">Nombre</label>
        <input id="nombre" placeholder="Ingrese nombre">
      </div>
      <div class="field-group">
        <label for="edad">Edad</label>
        <input id="edad" type="number" placeholder="Ingrese edad">
      </div>
    `;
  }
  
  function renderEsFFAAyDetenido() {
    return `
      <div class="field-group">
        <label>Es FFAA</label>
        <select id="esFFAA">
          <option value="NO">No</option>
          <option value="SI">Sí</option>
        </select>
      </div>
      <div class="field-group">
        <label>Estuvo o Está detenido</label>
        <select id="estaDetenido">
          <option value="NO">No</option>
          <option value="SI">Sí</option>
        </select>
      </div>
    `;
  }
  function vive() {
    return `
      <div class="field-group">
        <label>Fecha</label>
       <input id="fecha-muerte" type="date">
      </div>
      <div class="field-group">
        <label>Causa</label>
        <input id="causa-muerte" placeholder = "Ingrese la causa de fallecimiento">
      </div>
    `;
  }
