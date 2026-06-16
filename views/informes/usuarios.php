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

    .table-container {
        padding: 1.5rem;
        overflow-x: auto;
    }

    table#tabla-usuarios {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    table#tabla-usuarios thead th {
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

    table#tabla-usuarios tbody td {
        padding: 10px 15px !important;
        font-size: 0.9rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
    }

    .btn-volver { background: #475569 !important; color: white !important; }
    .btn-volver:hover { background: #334155 !important; }
    
    .btn-imprimir { background: #0284c7 !important; color: white !important; }
    .btn-imprimir:hover { background: #0369a1 !important; }

    @media print {
        .sidebar, .btn-export, .no-print {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<div class="card informe-card">
    <div class="card-header-custom">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="<?php echo URLROOT; ?>/informes" class="btn btn-volver no-print" style="padding: 0.4rem 0.8rem;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <i class="fas fa-users" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 style="margin:0; font-size: 1.2rem; color: var(--text-main); font-weight: 800;"><?php echo $data['titulo']; ?></h2>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-imprimir btn-export">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?php echo URLROOT; ?>/informes/excel_usuarios" class="btn btn-success btn-export">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>

    <div class="table-container">
        <table id="tabla-usuarios" class="display nowrap">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Área</th>
                    <th>Marcaciones Totales</th>
                    <th>Permisos (Cant)</th>
                    <th>Horas Permiso</th>
                    <th>Saldo Banco Horas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['resumen'] as $r): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600;"><?php echo $r->nombre; ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $r->cedula; ?></div>
                    </td>
                    <td><?php echo $r->area; ?></td>
                    <td style="text-align: center;"><?php echo $r->total_asistencias; ?></td>
                    <td style="text-align: center;"><?php echo $r->total_permisos; ?></td>
                    <td style="text-align: center;"><?php echo $r->horas_permisos ?? 0; ?></td>
                    <td style="text-align: center; font-weight: 700; color: <?php echo ($r->saldo_horas < 0) ? 'var(--danger-color)' : 'var(--success-color)'; ?>">
                        <?php echo $r->saldo_horas; ?> h
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabla-usuarios').DataTable({
            "pageLength": 50,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[0, "asc"]]
        });
    });
</script>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
