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
    }

    table#tabla-asistencia {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
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
    .badge-entrada { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .badge-salida { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    table.dataTable thead .sorting:before, table.dataTable thead .sorting:after {
        display: none !important;
    }
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
                    <th class="col-cedula">Cédula</th>
                    <th class="col-nombre">Nombre Empleado</th>
                    <th class="col-evento">Evento</th>
                    <th class="col-fecha">Fecha</th>
                    <th class="col-hora">Hora (12h)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['marcaciones'] as $marcacion): 
                    $dt = new DateTime($marcacion->registrado_en);
                    $fecha = $dt->format('d/m/Y');
                    $hora = $dt->format('h:i:s A');
                    $eventoClass = ($marcacion->tipo == 'entrada') ? 'badge-entrada' : 'badge-salida';
                    $eventoIcon = ($marcacion->tipo == 'entrada') ? 'fa-arrow-right' : 'fa-arrow-left';
                ?>
                <tr>
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
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabla-asistencia').DataTable({
            "pageLength": 15,
            "autoWidth": false,
            "dom": 'rt<"bottom"ip><"clear">',
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[3, "desc"], [4, "desc"]]
        });
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>
