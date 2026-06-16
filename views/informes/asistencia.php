<?php 
/** @var array $data */
require_once APPROOT . '/views/layouts/header.php'; 
?>

<style>
    .informe-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background-color: white !important;
        overflow: hidden;
    }

    .card-header-custom {
        background: linear-gradient(to right, #f8fafc, #eff6ff);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

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
        overflow-x: auto;
    }

    table#tabla-informe {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    table#tabla-informe thead th {
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

    table#tabla-informe tbody td {
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
    .badge-entrada { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-salida { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    .badge-a-tiempo { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-tardanza-salir { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
    .badge-antes-tiempo { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-tarde { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    .btn-volver { background: #475569 !important; color: white !important; }
    .btn-volver:hover { background: #334155 !important; }
    
    .btn-imprimir { background: #0284c7 !important; color: white !important; }
    .btn-imprimir:hover { background: #0369a1 !important; }

    @media print {
        .sidebar, .filter-section, .btn-export, .no-print {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>

<div class="card informe-card">
    <div class="card-header-custom">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="<?php echo URLROOT; ?>/informes" class="btn btn-volver no-print" style="padding: 0.4rem 0.8rem;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <i class="fas fa-file-alt" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 style="margin:0; font-size: 1.2rem; color: var(--text-main); font-weight: 800;"><?php echo $data['titulo']; ?></h2>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-imprimir btn-export">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?php echo URLROOT; ?>/informes/excel_asistencia?usuario_id=<?php echo $data['filtros']['usuario_id']; ?>&fecha_inicio=<?php echo $data['filtros']['fecha_inicio']; ?>&fecha_fin=<?php echo $data['filtros']['fecha_fin']; ?>" class="btn btn-success btn-export">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>

    <div class="filter-section no-print">
        <form action="<?php echo URLROOT; ?>/informes/asistencia" method="GET" class="filter-grid">
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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?php echo URLROOT; ?>/informes/asistencia" class="btn btn-danger">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table id="tabla-informe" class="display nowrap">
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombre Empleado</th>
                    <th>Evento</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
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
                    <td><strong><?php echo $marcacion->cedula; ?></strong></td>
                    <td><?php echo $marcacion->nombre; ?></td>
                    <td>
                        <span class="event-badge <?php echo $eventoClass; ?>">
                            <i class="fas <?php echo $eventoIcon; ?>"></i>
                            <?php echo $marcacion->tipo; ?>
                        </span>
                    </td>
                    <td><?php echo $fecha; ?></td>
                    <td><span style="font-weight: 600; color: var(--primary-color);"><?php echo $hora; ?></span></td>
                    <td><span class="event-badge <?php echo $estadoClass; ?>"><?php echo $marcacion->estado_marcacion; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabla-informe').DataTable({
            "pageLength": 50,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[3, "desc"], [4, "desc"]]
        });
    });
</script>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
