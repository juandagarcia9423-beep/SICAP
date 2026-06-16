<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 
?>

<style>
    /* Estilos de la Tarjeta y Contenedores */
    .employee-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background-color: white !important;
        overflow: hidden;
    }

    .card-header-custom {
        background: linear-gradient(to right, #f8fafc, #eff6ff);
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Contenedor del Buscador debajo del Título */
    .search-section {
        padding: 1rem 1.5rem 0 1.5rem;
        background: white;
    }

    .local-search-wrapper {
        position: relative;
        max-width: 500px;
    }

    .local-search-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .local-search-wrapper input {
        width: 100%;
        padding: 0.7rem 1rem 0.7rem 2.8rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background-color: #f8fafc;
        font-size: 0.9rem;
        transition: all 0.3s;
        box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.03);
    }

    .local-search-wrapper input:focus {
        background-color: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.05);
        outline: none;
    }

    .table-container {
        padding: 0.5rem 1.5rem 1.5rem 1.5rem;
    }
    
    /* Tabla y Alineación */
    table#tabla-usuarios {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 !important;
    }

    .col-nombre { width: 35%; }
    .col-usuario { width: 15%; }
    .col-rol { width: 15%; }
    .col-area { width: 20%; }
    .col-acciones { width: 15%; }

    table#tabla-usuarios thead th {
        background-color: #f8fafc !important;
        color: var(--text-muted) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.1em !important;
        padding: 15px !important;
        text-align: left !important;
        border-bottom: 2px solid var(--border-color) !important;
    }

    table#tabla-usuarios tbody td {
        padding: 10px 15px !important;
        text-align: left !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    /* Ocultar el input original de DataTables */
    .dataTables_filter { display: none !important; }

    /* Estilos de Avatar y Badges */
    .user-info-cell { display: flex; align-items: center; gap: 0.75rem; }
    .user-avatar {
        width: 34px; height: 34px; border-radius: 8px;
        background: var(--primary-color); color: white;
        display: flex; justify-content: center; align-items: center;
        font-weight: 700; font-size: 0.8rem; flex-shrink: 0;
    }
    .user-name { font-weight: 700; color: var(--text-main); font-size: 0.9rem; margin: 0; }
    .user-email { font-size: 0.7rem; color: var(--text-muted); margin: 0; }

    .role-badge { padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .badge-superadmin { background: #fae8ff; color: #701a75; }
    .badge-admin { background: #e0e7ff; color: #3730a3; }
    .badge-supervisor { background: #fff7ed; color: #9a3412; }
    .badge-empleado { background: #f0fdf4; color: #166534; }

    /* Quitar flechas de DataTables */
    table.dataTable thead .sorting:before, table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before, table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before, table.dataTable thead .sorting_desc:after {
        display: none !important;
    }
</style>

<div class="card employee-card">
    <!-- Cabecera -->
    <div class="card-header-custom">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <i class="fas fa-users-cog" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 style="margin:0; font-size: 1.2rem; color: var(--text-main); font-weight: 800;">Gestión de Personal</h2>
        </div>
        <?php if (app\Helpers\SesionHelper::tienePermiso('usuarios', 'crear')): ?>
            <a href="<?php echo URLROOT; ?>/usuarios/crear" class="btn btn-success" style="padding: 0.5rem 1.2rem; border-radius: 8px; font-size: 0.9rem;">
                <i class="fas fa-plus-circle"></i> Nuevo Usuario
            </a>
        <?php endif; ?>
    </div>

    <!-- Sección de Búsqueda debajo de Gestión de Personal -->
    <div class="search-section">
        <div class="local-search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="filtro-usuarios-local" placeholder="Buscar empleado por nombre o usuario...">
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-container">
        <table id="tabla-usuarios" class="display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th class="col-nombre">Nombre y Contacto</th>
                    <th class="col-usuario">Usuario</th>
                    <th class="col-rol">Perfil</th>
                    <th class="col-area">Área</th>
                    <th class="col-acciones" style="text-align: center !important;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['usuarios'] as $usuario): 
                    $nombres = explode(' ', $usuario->nombre);
                    $iniciales = strtoupper(substr($nombres[0], 0, 1) . (isset($nombres[1]) ? substr($nombres[1], 0, 1) : ''));
                    $badgeClass = ($usuario->rol == 'superadmin') ? 'badge-superadmin' : (($usuario->rol == 'administrativos') ? 'badge-admin' : (($usuario->rol == 'supervisor') ? 'badge-supervisor' : 'badge-empleado'));
                    
                    // Función para generar color único
                    $hash = md5($usuario->nombre);
                    $color = '#' . substr($hash, 0, 6);
                ?>
                <tr>
                    <td>
                        <div class="user-info-cell">
                            <div class="user-avatar" style="background-color: <?php echo $color; ?>;"><?php echo $iniciales; ?></div>
                            <div class="user-details">
                                <span class="user-name"><?php echo $usuario->nombre; ?></span>
                                <span class="user-email"><?php echo $usuario->email; ?></span>
                            </div>
                        </div>
                    </td>
                    <td><code style="color: var(--primary-color); font-weight: 600;"><?php echo $usuario->usuario; ?></code></td>
                    <td><span class="role-badge <?php echo $badgeClass; ?>"><?php echo $usuario->rol; ?></span></td>
                    <td><span style="color: var(--text-main); font-size: 0.85rem;"><?php echo $usuario->area; ?></span></td>
                    <td style="text-align: center !important;">
                        <div style="display: flex; justify-content: center; gap: 6px;">
                            <?php if (app\Helpers\SesionHelper::tienePermiso('usuarios', 'editar')): ?>
                                <a href="<?php echo URLROOT; ?>/usuarios/editar/<?php echo $usuario->id; ?>" class="btn btn-primary" style="padding: 6px 10px; font-size: 10px;" title="Editar Datos"><i class="fas fa-edit"></i></a>
                            <?php endif; ?>

                            <?php if (app\Helpers\SesionHelper::tienePermiso('configuracion', 'seguridad')): ?>
                                <a href="<?php echo URLROOT; ?>/configuracion/seguridad?usuario_id=<?php echo $usuario->id; ?>" class="btn btn-warning" style="padding: 6px 10px; font-size: 10px; background-color: #f59e0b; color: white !important;" title="Gestionar Permisos"><i class="fas fa-user-lock"></i></a>
                            <?php endif; ?>
                            
                            <?php if (app\Helpers\SesionHelper::tienePermiso('usuarios', 'eliminar')): ?>
                                <button class="btn btn-danger btn-eliminar-trigger" data-id="<?php echo $usuario->id; ?>" data-nombre="<?php echo $usuario->nombre; ?>" style="padding: 6px 10px; font-size: 10px;"><i class="fas fa-trash-alt"></i></button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmación -->
<div id="modal-eliminar" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="modal-title">¿Eliminar Usuario?</div>
        <div class="modal-text">Está a punto de eliminar a <strong><span id="nombre-usuario-modal"></span></strong>.</div>
        <div class="modal-footer">
            <button id="btn-cancelar-modal" class="btn btn-secondary">Cancelar</button>
            <button id="btn-confirmar-modal" class="btn btn-danger">Sí, Eliminar</button>
        </div>
    </div>
</div>

<form id="form-eliminar" action="" method="POST" style="display:none;"></form>

<script>
    $(document).ready(function() {
        // Inicializar tabla
        var table = $('#tabla-usuarios').DataTable({
            "pageLength": 10,
            "autoWidth": false,
            "dom": 'rt<"bottom"ip><"clear">',
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [{ "orderable": false, "targets": 4 }]
        });

        // Lógica de búsqueda conectada al nuevo input
        $('#filtro-usuarios-local').on('keyup input', function() {
            table.search(this.value).draw();
        });

        // Lógica de eliminación
        $(document).on('click', '.btn-eliminar-trigger', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            $('#nombre-usuario-modal').text(nombre);
            $('#modal-eliminar').css('display', 'flex');
            $('#modal-eliminar').data('id', id);
        });

        $('#btn-cancelar-modal').click(function() { $('#modal-eliminar').hide(); });

        $('#btn-confirmar-modal').click(function() {
            const id = $('#modal-eliminar').data('id');
            if(id) { $('#form-eliminar').attr('action', '<?php echo URLROOT; ?>/usuarios/eliminar/' + id).submit(); }
        });
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>
