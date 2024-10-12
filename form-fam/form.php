<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario Familiar</title>
  <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
  <form action="" method="post" autocomplete="off" class="card">
    <div class="card-header">
      <h2>Formulario Familiar</h2>
    </div>
    <div class="card-content">
      <div class="field-group">
        <div class="buscador-ppl">
          <label for="ppl">Buscar PPL</label>
          <input type="text" name="campo" id="campo" required>
          <ul id="lista"></ul>
        </div>
        <label for="vinculo">Vínculo Familiar</label>
        <select id="vinculo" onchange="handleVinculoChange(this.value)" required>
          <option value="">Seleccione vínculo familiar</option>
          <option value="PADRE">Padre</option>
          <option value="MADRE">Madre</option>
          <option value="HERMANO">Hermano/a</option>
          <option value="CONCUBINO">Concubino/a</option>
          <option value="ESPOSO">Esposo/a</option>
          <option value="HIJO">Hijo/a</option>
        </select>
      </div>
      <div id="form-content">
        <!-- Los campos específicos según el vínculo se agregarán aquí -->
      </div>
      <button class="submit-button" type="submit">Agregar familiar</button>
    </div>
  </form>

  <script src="./js/script.js"></script>
  <script src="js/peticiones.js"></script>
</body>

</html>
