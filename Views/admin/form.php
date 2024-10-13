<section>
<div class="container mt-4">
  <!-- <a class="btn btn-warning btn-sm" href="persona_edit.php?id=<?php echo $ppl['id']; ?>" type="button" title="Editar">Editar </a> -->
    <div class="card">
        <div class="card-body">
              <form id="familiarForm" action="insert_familiar.php" method="post" autocomplete="off" >
                <div class="card-content">
                  <div class="field-group">
                    <div class="buscador-ppl">
                      <label for="ppl">Buscar PPL</label>
                      <input type="text" name="campo" id="campo" required>
                      <ul id="lista"></ul>
                    </div>
                    <input type="hidden" name="id_ppl" id="id_ppl">
                    <label for="vinculo">Vínculo Familiar</label>
                    <select id="vinculo" name="vinculo" onchange="handleVinculoChange(this.value)" required>
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
                  <button class="submit-button btn btn-primary" type="submit">Agregar familiar</button>
                </div>
              </form>
            </div>
            <div></div>
        </div> 
    </div>   
    </div>
</section>


  

  <script>
    document.getElementById('familiarForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      console.log('Formulario enviado. Procesando datos...'); // Mensaje de confirmación en consola
      
      let formData = new FormData(this);
      
      fetch('insert_familiar.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          console.log('Datos cargados exitosamente:', data.message); // Mensaje de éxito en consola
          alert(data.message);
          this.reset();
        } else {
          console.error('Error al cargar los datos:', data.message); // Mensaje de error en consola
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error en la solicitud:', error); // Mensaje de error en consola
      });
    });
  </script>  

  <script src="./js/script.js"></script>
  <script src="js/peticiones.js"></script>