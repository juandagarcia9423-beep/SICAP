<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 
?>

<style>
    .motivos-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
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
    }

    table.table-custom {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table.table-custom thead th {
        background-color: #f8fafc;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid var(--border-color);
    }

    table.table-custom tbody td {
        padding: 12px 15px;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    table.table-custom tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Anchos de columna */
    .col-nombre { width: 30%; }
    .col-desc { width: 40%; }
    .col-repone { width: 15%; }
    .col-acciones { width: 15%; }

    .btn-action-sm {
        padding: 6px 10px;
        font-size: 11px;
        border-radius: 8px;
    }
</style>

<div class="card motivos-card" style="background-color: var(--form-bg) !important;">
    <div class="card-header-custom">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <i class="fas fa-list-ul" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <h2 style="margin:0; font-size: 1.2rem; color: var(--text-main); font-weight: 800;">Motivos de Permisos</h2>
        </div>
        <button class="btn btn-success" style="padding: 0.5rem 1.2rem; font-size: 0.9rem;" onclick="nuevoMotivo()">
            <i class="fas fa-plus-circle"></i> Nuevo Motivo
        </button>
    </div>

    <div class="table-container">
        <div style="background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th class="col-nombre">Nombre del Motivo</th>
                        <th class="col-desc">Descripción</th>
                        <th class="col-repone" style="text-align: center;">Repone Tiempo</th>
                        <th class="col-acciones" style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['motivos'] as $m): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?php echo $m->nombre; ?></td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;"><?php echo isset($m->descripcion) ? $m->descripcion : ''; ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <?php if($m->repone_tiempo): ?>
                                <span class="role-badge badge-danger" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background-color: var(--danger-color) !important; color: white !important;">
                                    <i class="fas fa-history" style="font-size: 10px;"></i> REPOSICIÓN
                                </span>
                            <?php else: ?>
                                <span class="role-badge badge-success" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; background-color: var(--success-color) !important; color: white !important;">
                                    <i class="fas fa-check-circle" style="font-size: 10px;"></i> NO APLICA
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button class="btn btn-primary btn-action-sm" onclick='editarMotivo(<?php echo json_encode($m); ?>)' title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-action-sm btn-eliminar-motivo" data-id="<?php echo $m->id; ?>" data-nombre="<?php echo $m->nombre; ?>" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Motivo -->
<div id="modal-motivo" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px; text-align: left; border-top: 5px solid var(--primary-color);">
        <div class="modal-title" id="modal-title-motivo" style="color: var(--primary-color);">Nuevo Motivo</div>
        <form action="<?php echo URLROOT; ?>/configuracion/guardarMotivo" method="POST" style="margin-top: 1.5rem;">
            <input type="hidden" name="id" id="motivo-id">
            
            <div class="input-group" style="margin-bottom: 1.25rem;">
                <label style="display:block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.85rem; color: var(--text-main);">Nombre del Motivo *</label>
                <input type="text" name="nombre" id="motivo-nombre" required placeholder="Ej. Cita Médica" style="width:100%; padding:0.7rem; border:1px solid var(--border-color); border-radius:10px; background: #f8fafc;">
            </div>

            <div class="input-group" style="margin-bottom: 1.25rem;">
                <label style="display:block; font-weight: 700; margin-bottom: 0.4rem; font-size: 0.85rem; color: var(--text-main);">Descripción</label>
                <textarea name="descripcion" id="motivo-descripcion" rows="3" style="width:100%; padding:0.7rem; border:1px solid var(--border-color); border-radius:10px; background: #f8fafc; resize: none;"></textarea>
            </div>

            <div style="display: flex; gap: 2rem; margin-bottom: 1.5rem; background: #eff6ff; padding: 1rem; border-radius: 10px; border: 1px dashed var(--primary-color);">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: var(--primary-color);">
                    <input type="checkbox" name="repone_tiempo" id="motivo-repone" value="1" style="width:18px; height:18px; accent-color: var(--primary-color);">
                    ¿Repone tiempo?
                </label>
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: var(--primary-color);">
                    <input type="checkbox" name="visible_para_usuarios" id="motivo-visible" value="1" checked style="width:18px; height:18px; accent-color: var(--primary-color);">
                    Visible usuarios
                </label>
            </div>

            <div class="modal-footer" style="justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="cerrarModalMotivo()" class="btn btn-secondary" style="padding: 0.7rem 1.5rem;">Cancelar</button>
                <button type="submit" class="btn btn-success" style="padding: 0.7rem 2rem;">
                    <i class="fas fa-save"></i> Guardar Motivo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Formulario Oculto para Eliminar Motivo -->
<form id="form-eliminar-motivo" action="" method="POST" style="display:none;"></form>

<script>
$(document).ready(function() {
    // Lógica de eliminación con Modal Personalizado
    $(document).on('click', '.btn-eliminar-motivo', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        // Usamos el modal de eliminación global que ya definimos en el header
        const modal = $('#modal-eliminar');
        $('#nombre-usuario-modal').text('el motivo "' + nombre + '"');
        modal.css('display', 'flex');
        
        // Sobrescribimos el evento del botón confirmar del modal global
        $('#btn-confirmar-modal').off('click').on('click', function() {
            $('#form-eliminar-motivo').attr('action', '<?php echo URLROOT; ?>/configuracion/eliminarMotivo/' + id).submit();
        });
    });
});

function nuevoMotivo() {
    document.getElementById('modal-title-motivo').textContent = "Nuevo Motivo";
    document.getElementById('motivo-id').value = "";
    document.getElementById('motivo-nombre').value = "";
    document.getElementById('motivo-descripcion').value = "";
    document.getElementById('motivo-repone').checked = false;
    document.getElementById('motivo-visible').checked = true;
    document.getElementById('modal-motivo').style.display = 'flex';
}

function editarMotivo(motivo) {
    document.getElementById('modal-title-motivo').textContent = "Editar Motivo";
    document.getElementById('motivo-id').value = motivo.id;
    document.getElementById('motivo-nombre').value = motivo.nombre;
    document.getElementById('motivo-descripcion').value = motivo.descripcion;
    document.getElementById('motivo-repone').checked = motivo.repone_tiempo == 1;
    document.getElementById('motivo-visible').checked = motivo.visible_para_usuarios == 1;
    document.getElementById('modal-motivo').style.display = 'flex';
}

function cerrarModalMotivo() {
    document.getElementById('modal-motivo').style.display = 'none';
}
</script>

<?php require_once '../views/layouts/footer.php'; ?>
