<?php require 'navbar.php'; ?>
<!-- ------------------------ -->
    <style>
        .form-container {
            margin: 20px;
        }
        .foto {
            width: 250px;
            height: 250px;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ddd;
        }
        .line {
            display: inline-block;
            width: 150px;
            border-bottom: 1px solid #000;
            margin-left: 5px;
        }
        .section-title {
            font-weight: bold;
        }
    </style>
<!-- ------------------------------------ -->
<section class="content mt-3">
    <div class="row px-5 mx-5 ">
        <div class="col-sm">
            <div class="card rounded-2 border-0">
                <div class="card-header bg-dark text-white pb-0">
                    <h5 class="d-inline-block ">Juan Pérez Gómez </h5>    
                </div>
                <div class="card-body ">    
                    <div class="container mb-4">    
                        <div class="row mt-2">
                            <div class="col-md-7">            
                            <h5 class="section-title mb-3 ">A) DATOS PERSONALES</h5>           
                                <p>
                                    <label class="h6 ">Apellidos y Nombres:</label> Juan Pérez Gómez
                                    <label class="h6 ml-5">D.N.I.:</label> 12345678
                                </p>
                                <p>
                                    <label class="h6 ">Fecha de nacimiento:</label> 15/04/1990
                                    <label class="h6 ml-5">Edad:</label> 34
                                    <label class="h6 ml-5">Apodo:</label> Juanito
                                </p>
                                <p>
                                    <label class="h6 ">Lugar:</label> Buenos Aires
                                    <label class="h6 ml-5">Nacionalidad:</label> Argentina
                                </p>
                                <p>
                                    <label class="h6 ">Domicilio:</label> Calle Falsa 123
                                </p>
                                <p>
                                    <label class="h6 ">Departamento / Localidad:</label> Almagro
                                    <label class="h6 ml-5">Provincia:</label> Buenos Aires
                                </p>             
                            </div>
                            <div class="col-md-4 text-center ">
                                <div class="foto">
                                    <p>FOTOGRAFÍA</p>
                                </div>
                            </div>
                        </div>
                    </div>             
    <!-- ------------------------- -->
                        <table id="example" class="table table-striped table-sm" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>                
                                    <th>Fecha de Ingreso</th>
                                    <th>Fecha de Egreso</th>
                                    <th>Cargo/Delito</th>               
                                    <th>informe</th>                
                                </tr>
                            </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $query = "SELECT * FROM persona ";
                                        $stmt = $db->prepare($query);
                                        $stmt->execute();
                                        $pples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($pples as $ppl) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ppl['id'], ENT_QUOTES, 'UTF-8'); ?></td>  
                                        <td>15/04/2020</td>
                                        <td>15/04/2050</td>  
                                        <td>Cargo/Delito</td>                     
                                        <td><a class="btn btn-info " href="ppl_informe.php">Informe(IEII)</a></td>                    
                                    </tr>
                                    <?php
                                        }
                                    } catch (PDOException $e) {
                                        error_log("Error al obtener el ppl: " . $e->getMessage());
                                    }
                                    ?>
                                </tbody>
                        </table>
                    </div>                  
                </div>
            </div> 
        </div>   
    </div>
</section>

