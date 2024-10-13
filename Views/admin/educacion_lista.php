
<!-- ----------------------------------- -->
    <div class="container mt-4">
    <?php
    if ($_SESSION['id_rol'] === 1 || $_SESSION['id_rol'] === 2) {        
        ?>
            <a class="btn btn-warning btn-sm" href="educacion_edit.php?id=<?php echo $ppl['id']; ?>" type="button" title="Editar">Editar </a>
        <?php 
    }    
    ?>
<!-- ----------------------------------------- -->
        <div class="card">
            <div class="card-body">
                <h4>Educacion</h4>

                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <p class="form-control-static" id="dni">12345678</p>
                </div>







            </div>
        </div>
    </div>
