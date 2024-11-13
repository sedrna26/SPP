<?php 
require 'navbar.php'; 
require '../../conn/connection.php'; 

// Consulta para obtener todos los registros de la tabla auditoria
$query = "SELECT * FROM auditoria ORDER BY fecha DESC";
$statement = $db->prepare($query);
$statement->execute();
$auditoria = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="content mt-3">
    <link rel="shortcut icon" href="/img/LOGO.ico" type="image/x-icon" />
    <div class="row">
        <div class="col mx-3">
            <div class="row">
                <div class="col">
                    <div class="card rounded-2 border-0">
                        <h5 class="card-header bg-dark text-white">Listado de Auditoría</h5>
                        <div class="card-body bg-light">
                            <?php if (count($auditoria) > 0): ?>
                                <table id="example" class="table table-striped table-sm" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID Auditoría</th>
                                            <th>ID Usuario</th>
                                            <th>Acción</th>
                                            <th>Detalles</th>
                                            <th>Tabla Afectada</th>
                                            <th>ID Registro</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($auditoria as $registro): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($registro['id_auditoria']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['id_usuario']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['accion']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['detalles']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['tabla_afectada']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['registro_id']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['fecha']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No hay registros en la auditoría.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<? require 'footer.php'; ?>