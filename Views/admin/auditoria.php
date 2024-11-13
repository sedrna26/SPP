<?php 
require 'navbar.php'; 
require '../../conn/connection.php'; 

// Consulta para obtener todos los registros de la tabla auditoria
$query = "SELECT a.*, u.nombre_usuario AS nombre_usuario_afecto, r.nombre_rol AS rol_afecto, u2.nombre_usuario AS nombre_usuario_registro, r2.nombre_rol AS rol_registro 
          FROM auditoria a
          LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
          LEFT JOIN rol r ON u.id_rol = r.id_rol
          LEFT JOIN usuarios u2 ON a.registro_id = u2.id_usuario
          LEFT JOIN rol r2 ON u2.id_rol = r2.id_rol
          ORDER BY a.fecha DESC";
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
                                            <th>Registro Afectado</th>
                                            <th>Quién lo Afectó</th>
                                            <th>Acción</th>
                                            <th>ID Registro</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($auditoria as $registro): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($registro['id_auditoria']); ?></td>
                                                <!-- Registro Afectado: Muestra id_registro, nombre_usuario y rol -->
                                                <td>
                                                    ID: <?php echo htmlspecialchars($registro['registro_id']); ?><br>
                                                    Usuario: <?php echo htmlspecialchars($registro['nombre_usuario_registro']); ?><br>
                                                    Rol: <?php echo htmlspecialchars($registro['rol_registro']); ?>
                                                </td>
                                                <!-- Quién lo Afectó: Muestra nombre_usuario, rol y id_usuario -->
                                                <td>
                                                    Usuario: <?php echo htmlspecialchars($registro['nombre_usuario_afecto']); ?><br>
                                                    Rol: <?php echo htmlspecialchars($registro['rol_afecto']); ?><br>
                                                    ID Usuario: <?php echo htmlspecialchars($registro['id_usuario']); ?>
                                                </td>
                                               
                                                <td><?php echo htmlspecialchars($registro['accion']); ?></td>
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