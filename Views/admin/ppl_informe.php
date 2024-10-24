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
                        <button class="nav-link" id="form3-tab" data-bs-toggle="tab" data-bs-target="#form3" type="button" role="tab" aria-controls="form3" aria-selected="false">Informe Sanitario</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="form4-tab" data-bs-toggle="tab" data-bs-target="#form4" type="button" role="tab" aria-controls="form4" aria-selected="false">Informe Educacional</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="form5-tab" data-bs-toggle="tab" data-bs-target="#form5" type="button" role="tab" aria-controls="form5" aria-selected="false">Marcas del Cuerpo</button>
                    </li>            
                </ul>
                <!-- --------------------------------------------------------------- -->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="form1" role="tabpanel" aria-labelledby="form1-tab">
                        <?php
                            //en esta ventana solo se puede listar el primer formulario
                            //dentro del archivo persona_lista.php se encuntra el boton persona_edita.php y se activa segun el rol
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'persona_lista(IA).php';
                            } else {
                            }
                        ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form2" role="tabpanel" aria-labelledby="form2-tab">
                        <?php
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {
                                include 'socio-familiar_crea(IA).php';
                            } else {
                                include 'app.php';
                            }
                        ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form3" role="tabpanel" aria-labelledby="form3-tab">
                        <?php include 'sanitario_crea.php'; ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form4" role="tabpanel" aria-labelledby="form4-tab">
                        <?php
                            if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 3) {     
                                //busca si el informe de educacion está activo segun el id del ppl, si está activo, solo se lista, sino se crea.                                 
                                $sql_educa = "SELECT * FROM educacion WHERE estado = 'activo'";
                                $result_educa = $conexion->query($sql_educa);
                                $educa = $result_educa->fetch_assoc();
                                //condicion para que el error no aparezca en la pantalla.
                                //Warning: Trying to access array offset on value of type null in C:\xampp\htdocs\PLANTILLAS\SPP\SPP\Views\admin\ppl_informe.php on line 61
                                $select_educa = isset($educa['estado']) ? $educa['estado'] : null; 
                                
                                if ($select_educa ==='activo'){
                                    include 'educacion_lista.php';
                                }else{
                                    include 'educacion_crea.php';
                                }    
                            } else {
                                include 'educacion_lista.php';
                            }
                        ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                    <div class="tab-pane fade" id="form5" role="tabpanel" aria-labelledby="form5-tab">
                        <?php include 'app.php'; ?>
                    </div>
                    <!-- ---------------------------------------------- -->
                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'footer.php'; ?>