<?php


// Obtener el ID del PPsL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Obtener el ID del PPL de la URL
$idppl = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Función para obtener datos de las diferentes tablas
function obtenerDatosFamiliares($db, $idppl) {
    $datos = [
        'info_familiar' => null,
        'situacion_social' => null,
        'padres' => [],
        'hermanos' => [],
        'pareja' => null,
        'hijos' => [],
        'otros_visitantes' => []
    ];

    try {
        // Obtener información familiar general
        $stmt = $db->prepare("SELECT * FROM ppl_familiar_info WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['info_familiar'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener situación sociofamiliar
        $stmt = $db->prepare("SELECT * FROM ppl_situacion_sociofamiliar WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['situacion_social'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener datos de padres
        $stmt = $db->prepare("SELECT * FROM ppl_padres WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['padres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener datos de hermanos
        $stmt = $db->prepare("SELECT * FROM ppl_hermanos WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['hermanos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener datos de la pareja
        $stmt = $db->prepare("SELECT * FROM ppl_pareja WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['pareja'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener datos de hijos
        $stmt = $db->prepare("SELECT * FROM ppl_hijos WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['hijos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener otros visitantes
        $stmt = $db->prepare("SELECT * FROM ppl_otros_visitantes WHERE idppl = ? AND estado = 'Activo'");
        $stmt->execute([$idppl]);
        $datos['otros_visitantes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    } catch (PDOException $e) {
        // Manejar errores de base de datos
        echo "Error al recuperar datos: " . $e->getMessage();
        return null;
    }
}

// Obtener los datos familiares
$datosFamiliares = obtenerDatosFamiliares($db, $idppl);

// Si no se encuentran datos, redirigir o mostrar un mensaje
if (!$datosFamiliares) {
    die("No se encontraron datos para este PPL.");
}
?>


<div class="container ">
    
    <!-- Información Familiar General -->
    <div class="card mb-4">
        <div class="card-header d-flex bg-primary text-white justify-content-between align-items-center">
            <h4 class="mb-0 ">Datos Familiares Generales</h4>
            <a class="btn btn-warning btn-sm" href='socio-familia_edit.php?id=<?php echo $idppl; ?>'>
                <i class="fas fa-edit me-1"></i>Editar
            </a> 
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Familiares</h4>
                        <p><strong>Familiares FF.AA:</strong> 
                    <span class="badge py-2 px-3 fs-6">
                        <span class="badge py-2 px-3 fs-6 <?php echo $datosFamiliares['info_familiar']['familiares_ffaa'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $datosFamiliares['info_familiar']['familiares_ffaa'] ? 'Sí' : 'No'; ?>
                        </span>
                        <?php if ($datosFamiliares['info_familiar']['familiares_ffaa']): ?>
                            <span class="text-black">- <?php echo $datosFamiliares['info_familiar']['ffaa_detalles']; ?></span>
                        <?php endif; ?>
                        </span>
                    </p>
                    <p><strong>Familiares Detenidos:</strong>
                        <span class="badge py-2 px-3 fs-6">
                            <span class="badge py-2 px-3 fs-6 <?php echo $datosFamiliares['info_familiar']['familiares_detenidos'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $datosFamiliares['info_familiar']['familiares_detenidos'] ? 'Sí' : 'No'; ?>
                            </span>
                            <?php if ($datosFamiliares['info_familiar']['familiares_detenidos']): ?>
                            <span class="text-black">- <?php echo $datosFamiliares['info_familiar']['detenidos_detalles']; ?></span>
                            <?php endif; ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h4>Documentación</h4>
                    <p><strong>Posee DNI:</strong> 
                        <span class="badge py-2 px-3 fs-6 <?php echo $datosFamiliares['info_familiar']['posee_dni'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $datosFamiliares['info_familiar']['posee_dni'] ? 'Sí' : 'No - ' . $datosFamiliares['info_familiar']['motivo_no_dni']; ?>
                        </span>
                    </p>
                    <p><strong>Teléfono Familiar:</strong> <?php echo $datosFamiliares['info_familiar']['telefono_familiar'] ?? 'No registrado'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Situación Sociofamiliar -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Situación Sociofamiliar</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Edad Inicio Laboral:</strong> <?php echo $datosFamiliares['situacion_social']['edad_inicio_laboral'] ?? 'No registrada'; ?></p>
                    <p><strong>Situación Económica Precaria:</strong> 
                        <span class="badge py-2 px-3 fs-6 <?php echo $datosFamiliares['situacion_social']['situacion_economica_precaria'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $datosFamiliares['situacion_social']['situacion_economica_precaria'] ? 'Sí' : 'No'; ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Mendicidad/Situación de Calle:</strong> 
                        <span class="badge py-2 px-3 fs-6 <?php echo $datosFamiliares['situacion_social']['mendicidad_calle'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $datosFamiliares['situacion_social']['mendicidad_calle'] ? 'Sí' : 'No'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Padres -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Información de Padres</h4>
        </div>
        <div class="card-body">
            <?php foreach ($datosFamiliares['padres'] as $padre): ?>
                <div class="row mb-3">                    
                    <div class="col-md-6">
                        <h4><?php echo $padre['tipo']; ?></h4>
                        <p><strong>Nombre:</strong> <?php echo $padre['nombre'] . ' ' . $padre['apellido']; ?></p>
                        <p><strong>Edad:</strong> <?php echo $padre['edad'] ?? 'No registrada'; ?></p>
                        <p><strong>Nacionalidad:</strong> <?php echo $padre['nacionalidad'] ?? 'No registrada'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado Civil:</strong> <?php echo $padre['estado_civil'] ?? 'No registrado'; ?></p>
                        <p><strong>Instrucción:</strong> <?php echo $padre['instruccion'] ?? 'No registrada'; ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="badge py-2 px-3 fs-6 <?php echo $padre['vivo'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $padre['vivo'] ? 'Vivo' : 'Fallecido'; ?>
                            </span>
                        </p>
                        <p><strong>Visita:</strong> 
                            <span class="badge py-2 px-3 fs-6 <?php echo $padre['visita'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $padre['visita'] ? 'Sí' : 'No'; ?>
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row mb-3">
                    <!-- Segunda fila de contenido aquí -->
                </div>

            <?php endforeach; ?>
        </div>
    </div>

    <!-- Hermanos -->
    <?php if (!empty($datosFamiliares['hermanos'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Hermanos</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Visita</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datosFamiliares['hermanos'] as $hermano): ?>
                            <tr>
                                <td><?php echo $hermano['nombre'] . ' ' . $hermano['apellido']; ?></td>
                                <td><?php echo $hermano['edad'] ?? 'No registrada'; ?></td>
                                <td>
                                    <span class="badge py-2 px-3 fs-6 <?php echo $hermano['visita'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $hermano['visita'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pareja -->
    <?php if ($datosFamiliares['pareja']): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Información de Pareja</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre:</strong> <?php echo $datosFamiliares['pareja']['nombre'] . ' ' . $datosFamiliares['pareja']['apellido']; ?></p>
                    <p><strong>Edad:</strong> <?php echo $datosFamiliares['pareja']['edad'] ?? 'No registrada'; ?></p>
                    <p><strong>Nacionalidad:</strong> <?php echo $datosFamiliares['pareja']['nacionalidad'] ?? 'No registrada'; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Instrucción:</strong> <?php echo $datosFamiliares['pareja']['instruccion'] ?? 'No registrada'; ?></p>
                    <p><strong>Tipo de Unión:</strong> <?php echo $datosFamiliares['pareja']['tipo_union'] ?? 'No registrado'; ?></p>
                    <p><strong>Visita:</strong> 
                        <span class="badge py-2 px-3 fs-6 <?php echo $datosFamiliares['pareja']['visita'] ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $datosFamiliares['pareja']['visita'] ? 'Sí' : 'No'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hijos -->
    <?php if (!empty($datosFamiliares['hijos'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Hijos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Fallecido</th>
                            <th>Visita</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datosFamiliares['hijos'] as $hijo): ?>
                            <tr>
                                <td><?php echo $hijo['nombre'] . ' ' . $hijo['apellido']; ?></td>
                                <td><?php echo $hijo['edad'] ?? 'No registrada'; ?></td>
                                <td>
                                    <span class="badge py-2 px-3 fs-6 <?php echo $hijo['fallecido'] ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $hijo['fallecido'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge py-2 px-3 fs-6 <?php echo $hijo['visita'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $hijo['visita'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Otros Visitantes (continued) -->
    <?php if (!empty($datosFamiliares['otros_visitantes'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Otros Visitantes</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Domicilio</th>
                            <th>Vínculo Filial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datosFamiliares['otros_visitantes'] as $visitante): ?>
                            <tr>
                                <td><?php echo $visitante['nombre'] . ' ' . $visitante['apellido']; ?></td>
                                <td><?php echo $visitante['telefono'] ?? 'No registrado'; ?></td>
                                <td><?php echo $visitante['domicilio'] ?? 'No registrado'; ?></td>
                                <td><?php echo $visitante['vinculo_filial'] ?? 'No registrado'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</document_content>
</document>
</documents>