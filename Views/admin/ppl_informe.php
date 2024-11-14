<?php require 'navbar.php'; ?>

<section class="container mt-3">
    <div class="card rounded-2 border-0">
        <div class="card-header bg-dark text-white pb-0">
            <h5 class="d-inline-block ">Informe de Evaluación Integral Interdiciplinario (IEII)</h5>
            <!-- <a class="btn btn-primary float-right mb-2" href="">(boton)</a>                     -->
        </div>
        <div class="card-body table-responsive">
            <div class="container ">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="form1-tab" data-bs-toggle="tab" data-bs-target="#form1" type="button" role="tab" aria-controls="form1" aria-selected="true">Datos Personales</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="form2-tab" data-bs-toggle="tab" data-bs-target="#form2" type="button" role="tab" aria-controls="form2" aria-selected="false">Situacion Social y Familiar</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="form3-tab" data-bs-toggle="tab" data-bs-target="#form3" type="button" role="tab" aria-controls="form3" aria-selected="false">Situacion Laboral y Espiritual</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="form4-tab" data-bs-toggle="tab" data-bs-target="#form4" type="button" role="tab" aria-controls="form4" aria-selected="false">Informe Sanitario</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="form5-tab" data-bs-toggle="tab" data-bs-target="#form5" type="button" role="tab" aria-controls="form5" aria-selected="false">Clasificación</button>
                    </li>
                    <li class="nav-item" role="presentation"> <button class="nav-link" id="form7-tab" data-bs-toggle="tab" data-bs-target="#form7" type="button" role="tab" aria-controls="form7" aria-selected="false">Educación</button>
                </ul>
                <!-- --------------------------------------------------------------- -->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="form1" role="tabpanel" aria-labelledby="form1-tab">
                        <?php
                        //en esta ventana solo se puede listar el primer formulario
                        //dentro del archivo persona_lista.php se encuntra el boton persona_edita.php y se activa segun el rol
                        if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                            include 'persona_lista.php';
                        } else {
                        }
                        //en esta ventana solo se puede listar el primer formulario
                        //dentro del archivo persona_lista.php se encuntra el boton persona_edita.php y se activa segun el rol
                        if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                            include 'persona_lista.php';
                        } else {
                        }
                        ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form2" role="tabpanel" aria-labelledby="form2-tab">
                        <?php
                        if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                            include 'socio-familiar_lista.php';
                        } else {
                            include 'app.php';
                        }
                        ?>
                    </div>

                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form3" role="tabpanel" aria-labelledby="form3-tab">
                        <?php
                        if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                            include 'laboral_espiritual_lista.php';
                        } else {
                            include 'app.php';
                        }
                        ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form4" role="tabpanel" aria-labelledby="form4-tab">
                        <?php include 'sanitario_crea.php'; ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form5" role="tabpanel" aria-labelledby="form5-tab">
                        <div id="clasificacion-content" class="p-3">

                            <?php
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                require_once 'clasificacion_lista.php';
                            } else {
                                require_once 'app.php';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form6" role="tabpanel" aria-labelledby="form6-tab">

                    </div>
                    <!-- ---------------------------------------------- -->
                </div>
            </div>
            <div class="tab-pane fade" id="form7" role="tabpanel" aria-labelledby="form7-tab">
                <?php if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                    include 'educacion_lista.php';
                } else {
                    include 'app.php';
                } ?>
            </div>

        </div>
    </div>
    </div>
</section>



<?php require 'footer.php'; ?>