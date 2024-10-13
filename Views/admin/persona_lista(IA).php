<style>
    .form-group {
      margin-bottom:   
 1rem;
      display: flex;
      align-items: center;
    }
    .form-group label {
      margin-right: 1rem;
      flex-shrink: 0;
    }
    .form-control-static {
      padding-top: calc(0.375rem + 1px);
      padding-bottom: calc(0.375rem + 1px);
      margin-bottom: 0;
      line-height: 1.5;
      flex-grow: 1; 
    }
  </style>
<!-- ----------------------------------- -->
    <div class="container mt-4">
    <?php
    if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {        
        ?>
            <a class="btn btn-warning btn-sm" href="persona_edit.php?id=<?php echo $ppl['id']; ?>" type="button" title="Editar">Editar </a>
        <?php 
    }    
    ?>
<!-- ----------------------------------------- -->
        <div class="card">
            <div class="card-body">
                <h4>Datos Personales</h4>

                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <p class="form-control-static" id="dni">12345678</p>
                </div>

                <div class="form-group">
                    <label for="nombres">Nombres:</label>
                    <p class="form-control-static" id="nombres">Juan Carlos</p>
                </div>

                <div class="form-group">
                    <label for="apellidos">Apellidos:</label>
                    <p class="form-control-static" id="apellidos">Pérez García</p>
                </div>

                <div class="form-group">
                    <label for="fechaNacimiento">Fecha de Nacimiento:</label>
                    <p class="form-control-static" id="fechaNacimiento">15/05/1985</p>
                </div>

                <div class="form-group">
                    <label for="edad">Edad:</label>
                    <p class="form-control-static" id="edad">38</p>
                </div>

                <div class="form-group">
                    <label for="genero">Género:</label>
                    <p class="form-control-static" id="genero">Masculino</p>
                </div>

                <div class="form-group">
                    <label for="estadoCivil">Estado Civil:</label>
                    <p class="form-control-static" id="estadoCivil">Casado</p>
                </div>

                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <img src="placeholder.jpg" alt="Foto de la persona" class="img-thumbnail" style="max-width: 200px;">
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <p class="form-control-static" id="direccion">Calle Principal 123, Ciudad, Provincia</p>
                </div>

                <h4 class="mt-4">Información de PPL</h4>

                <div class="form-group">
                    <label for="apodo">Apodo:</label>
                    <p class="form-control-static" id="apodo">El Rápido</p>
                </div>

                <div class="form-group">
                    <label for="profesion">Profesión:</label>
                    <p class="form-control-static" id="profesion">Mecánico</p>
                </div>

                <div class="form-group">
                    <label for="trabaja">¿Trabaja?:</label>
                    <p class="form-control-static" id="trabaja">Sí</p>
                </div>

                <div class="form-group">
                    <label for="fechaEntrevista">Fecha de Entrevista:</label>
                    <p class="form-control-static" id="fechaEntrevista">10/10/2023 14:30</p>
                </div>

                <h4 class="mt-4">Situación Legal</h4>

                <div class="form-group">
                    <label for="motivoDetencion">Motivo de detención:</label>
                    <p class="form-control-static" id="motivoDetencion">Robo agravado</p>
                </div>

                <div class="form-group">
                    <label for="situacionLegal">Situación Legal:</label>
                    <p class="form-control-static" id="situacionLegal">Procesado</p>
                </div>

                <div class="form-group">
                    <label for="prontuario">Prontuario:</label>
                    <p class="form-control-static" id="prontuario">987654</p>
                </div>

                <div class="form-group">
                    <label for="reincidencia">Reincidencia:</label>
                    <p class="form-control-static" id="reincidencia">No</p>
                </div>

                <div class="form-group">
                    <label for="salidaTransitoria">Salida Transitoria:</label>
                    <p class="form-control-static" id="salidaTransitoria">No</p>
                </div>

                <div class="form-group">
                    <label for="libertadAsistida">Libertad Asistida:</label>
                    <p class="form-control-static" id="libertadAsistida">No</p>
                </div>

                <div class="form-group">
                    <label for="libertadCondicional">Libertad Condicional:</label>
                    <p class="form-control-static" id="libertadCondicional">No</p>
                </div>

                <div class="form-group">
                    <label for="delito">Delito:</label>
                    <p class="form-control-static" id="delito">Robo - Con arma</p>
                </div>

                <div class="form-group">
                    <label for="fechaDetencion">Fecha de Detención:</label>
                    <p class="form-control-static" id="fechaDetencion">01/09/2023</p>
                </div>

                <div class="form-group">
                    <label for="juzgado">Juzgado:</label>
                    <p class="form-control-static" id="juzgado">Juzgado Penal N°3 - Dr. Martínez</p>
                </div>

                <div class="form-group">
                    <label for="caracteristicas">Características:</label>
                    <p class="form-control-static" id="caracteristicas">Tatuaje - Brazo derecho - Águila - Grande</p>
                </div>
            </div>
        </div>
    </div>
