<?php 
require 'navbar.php'; 
require '../../conn/connection.php';
?>

    <div class="container mt-2">
        <h2>Informe de PPL</h2>
        <!-- Navegación de Pestañas -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link /3text-dark active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Datos Personales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link /3text-dark" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Situacion Legal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link /3text-dark" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Datos Familiares</a>
            </li>
            <li class="nav-item">
                <a class="nav-link /3text-dark" id="tab4-tab" data-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false">Informe Sanitario</a>
            </li>
        </ul>

        <!-- Contenido de las Pestañas -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                <?php include 'datos_personales.php'; ?>
            </div>
            <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                <?php include 'situacion_legal.php'; ?>
            </div>
            <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                <?php include 'datos_familiares.php.php'; ?>
            </div>
            <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
            <?php include 'informe_sanitario.php'; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<?php require 'footer.php'; ?>