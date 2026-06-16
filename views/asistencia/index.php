<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 
?>

<style>
    .asistencia-card {
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

    /* Sección de Filtros Avanzados */
    .filter-section {
        background-color: var(--form-bg);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .filter-group label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
    }

    .filter-group input, .filter-group select {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.85rem;
        background: white;
    }

    .table-container {
        padding: 1.5rem;
        overflow-x: auto; /* Ensure horizontal scroll for smaller screens */
    }

    table#tabla-asistencia {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important; /* Changed from fixed to auto to prevent overflow */
    }

    .col-cedula { width: 15%; }
    .col-nombre { width: 35%; }
    .col-evento { width: 15%; }
    .col-fecha { width: 15%; }
    .col-hora { width: 20%; }

    table#tabla-asistencia thead th {
        background-color: #f8fafc !important;
        color: var(--text-muted) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.1em !important;
        padding: 12px 15px !important;
        text-align: left !important;
        border-bottom: 2px solid var(--border-color) !important;
    }

    table#tabla-asistencia tbody td {
        padding: 10px 15px !important;
        font-size: 0.9rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
    }

    .event-badge {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    /* Event Badges */
    .badge-entrada { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-salida { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* State Badges */
    .badge-a-tiempo { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-tardanza-salir { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
    .badge-antes-tiempo { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-tarde { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    table.dataTable thead .sorting:before, table.dataTable thead .sorting:after {
        display: none !important;
    }
    .modal-input-uniform {
        width: 100%;
        height: 28px;
        padding: 2px 6px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 0.75rem;
    }
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem;
    }
    .select-professional:hover { border-color: #cbd5e1; }
    .select-professional:focus { border-color: #3b82f6; background-color: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }
    
    .modal-content {
        background: #fff;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        width: 400px;
        max-width: 90%;
    }
    .modal-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; }
    .form-group label { display: block; font-weight: 600; color: #64748b; margin-bottom: 0.5rem; font-size: 0.9rem; }
</style>

<div class="card asistencia-card">
    <div class="card-header-custom">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <i class="fas fa-history" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 style="margin:0; font-size: 1.2rem; color: var(--text-main); font-weight: 800;">Historial de Marcaciones</h2>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="filter-section">
        <form action="<?php echo URLROOT; ?>/asistencia/index" method="GET" class="filter-grid">
            <div class="filter-group">
                <label>Empleado</label>
                <select name="usuario_id">
                    <option value="">-- Todos los Empleados --</option>
                    <?php foreach($data['usuarios'] as $usuario): ?>
                        <option value="<?php echo $usuario->id; ?>" <?php echo ($data['filtros']['usuario_id'] == $usuario->id) ? 'selected' : ''; ?>>
                            <?php echo $usuario->nombre; ?> (<?php echo $usuario->cedula; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="<?php echo $data['filtros']['fecha_inicio']; ?>">
            </div>

            <div class="filter-group">
                <label>Fecha Fin</label>
                <input type="date" name="fecha_fin" value="<?php echo $data['filtros']['fecha_fin']; ?>">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.2rem;">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?php echo URLROOT; ?>/asistencia/index" class="btn btn-danger" style="padding: 0.6rem 1.2rem;">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table id="tabla-asistencia" class="display nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th style="display:none;">Timestamp</th>
                    <th class="col-cedula">Cédula</th>
                    <th class="col-nombre">Nombre Empleado</th>
                    <th class="col-evento">Evento</th>
                    <th class="col-fecha">Fecha</th>
                    <th class="col-hora">Hora (12h)</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['marcaciones'] as $marcacion): 
                    $dt = new DateTime($marcacion->registrado_en);
                    $fecha = $dt->format('d/m/Y');
                    $hora = $dt->format('h:i:s A');
                    $eventoClass = ($marcacion->tipo == 'entrada') ? 'badge-entrada' : 'badge-salida';
                    $eventoIcon = ($marcacion->tipo == 'entrada') ? 'fa-arrow-right' : 'fa-arrow-left';
                    $estadoClass = 'badge-a-tiempo';
                    if($marcacion->estado_marcacion == 'Tarde') $estadoClass = 'badge-tarde';
                    elseif($marcacion->estado_marcacion == 'Antes de Tiempo') $estadoClass = 'badge-antes-tiempo';
                    elseif($marcacion->estado_marcacion == 'Tardanza en Salir') $estadoClass = 'badge-tardanza-salir';
                ?>
                <tr>
                    <td style="display:none;"><?php echo $marcacion->registrado_en; ?></td>
                    <td><strong style="color: var(--text-main);"><?php echo $marcacion->cedula; ?></strong></td>
                    <td><?php echo $marcacion->nombre; ?></td>
                    <td>
                        <span class="event-badge <?php echo $eventoClass; ?>">
                            <i class="fas <?php echo $eventoIcon; ?>"></i>
                            <?php echo $marcacion->tipo; ?>
                        </span>
                    </td>
                    <td><?php echo $fecha; ?></td>
                    <td><span style="font-weight: 600; color: var(--primary-color);"><?php echo $hora; ?></span></td>
                    <td><span class="role-badge <?php echo $estadoClass; ?>"><?php echo $marcacion->estado_marcacion; ?></span></td>
                    <td style="display: flex; gap: 0.3rem; justify-content: center;">
                        <?php if (app\Helpers\SesionHelper::tienePermiso('asistencia', 'editar')): ?>
                            <button type="button" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" 
                                onclick="abrirModalEditar(<?php echo $marcacion->id; ?>, '<?php echo $marcacion->tipo; ?>', '<?php echo $marcacion->registrado_en; ?>')">
                                Editar
                            </button>
                        <?php endif; ?>

                        <?php if (app\Helpers\SesionHelper::tienePermiso('asistencia', 'eliminar')): ?>
                            <button type="button" class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" 
                                onclick="abrirModalEliminar('<?php echo URLROOT; ?>/asistencia/eliminar/<?php echo $marcacion->id; ?>')">
                                Eliminar
                            </button>
                        <?php endif; ?>
                    </td>

                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        </table>
                        </div>
                        </div>

                        <!-- Modal Eliminar -->
                        <div id="modal-eliminar" class="modal-overlay">
                        <div class="modal-content">
                        <div class="modal-title">¿Eliminar Marcación?</div>
                        <div class="modal-text">Esta acción no se puede deshacer. ¿Está seguro?</div>
                        <div class="modal-footer" style="padding-top: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
                        <button type="button" class="btn" style="background-color: #b91c1c; color: #ffffff; padding: 0.4rem 1rem; border: none; border-radius: 4px;" onclick="cerrarModalEliminar()">Cancelar</button>
                        <a id="btn-confirmar-eliminar" href="#" class="btn btn-success">Sí, Eliminar</a>
                        </div>
                        </div>
                        </div>

                        <script>
                        function abrirModalEliminar(url) {
                        document.getElementById('btn-confirmar-eliminar').href = url;
                        document.getElementById('modal-eliminar').style.display = 'flex';
                        }
                        function cerrarModalEliminar() {
                        document.getElementById('modal-eliminar').style.display = 'none';
                        }
                        </script>

                    <style>
    /* Modal compacto y uniforme */
    #modal-editar .modal-content {
        width: 300px !important;
        padding: 1.5rem;
    }
    .modal-input-uniform {
        width: 100%;
        height: 28px;
        padding: 2px 6px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .modal-input-uniform:focus { border-color: #3b82f6; background-color: #fff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
    #modal-editar .form-group {
        margin-bottom: 0.75rem;
    }
</style>

<!-- Modal Editar -->
<div id="modal-editar" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-title">Editar Marcación</div>
        <form action="<?php echo URLROOT; ?>/asistencia/actualizar" method="POST">
            <input type="hidden" name="id" id="edit-id">
            
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo" id="edit-tipo" class="modal-input-uniform">
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" id="edit-fecha" class="modal-input-uniform" required>
            </div>
            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora" id="edit-hora" class="modal-input-uniform" required>
            </div>
            
            <div class="modal-footer" style="padding-top: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
                <button type="button" class="btn" style="background-color: #b91c1c; color: #ffffff; padding: 0.4rem 1rem; border: none; border-radius: 4px;" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
</div>

                    <script>
                    function abrirModalEditar(id, tipo, registrado_en) {
                        document.getElementById('edit-id').value = id;
                        document.getElementById('edit-tipo').value = tipo;
                        // Separar registrado_en (YYYY-MM-DD HH:MM:SS)
                        var parts = registrado_en.split(' ');
                        document.getElementById('edit-fecha').value = parts[0];
                        document.getElementById('edit-hora').value = parts[1].substring(0,5);
                        document.getElementById('modal-editar').style.display = 'flex';
                    }
                    function cerrarModalEditar() {
                    document.getElementById('modal-editar').style.display = 'none';
                    }
                    </script>

<script>
    $(document).ready(function() {
        $('#tabla-asistencia').DataTable({
            "pageLength": 15,
            "autoWidth": false,
            "dom": 'rt<"bottom"ip><"clear">',
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[0, "desc"]]
        });
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>
