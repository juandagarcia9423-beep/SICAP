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

    /* Estilos Responsivos para el Modal */
    .modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .options-box {
        display: flex;
        flex-direction: row;
        gap: 1.2rem;
        background: #eff6ff;
        padding: 1rem;
        border-radius: 10px;
        border: 1px dashed var(--primary-color);
        margin-top: 0.3rem;
    }

    .scroll-container {
        height: 125px; /* Altura ajustada para 3 items compactos */
        overflow-y: auto;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.6rem;
        background: #f8fafc;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    @media (max-width: 800px) {
        .modal-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .modal-content {
            width: 95% !important;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1rem !important;
        }

        .options-box {
            flex-direction: column;
            gap: 0.6rem;
        }
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
    <div class="modal-content" style="max-width: 680px; text-align: left; border-top: 5px solid var(--primary-color); padding: 1.2rem;">
        <div class="modal-title" id="modal-title-motivo" style="color: var(--primary-color); font-size: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.8rem; margin-bottom: 1rem;">Nuevo Motivo</div>
        
        <form action="<?php echo URLROOT; ?>/configuracion/guardarMotivo" method="POST">
            <input type="hidden" name="id" id="motivo-id">
            
            <div class="modal-grid">
                <!-- Columna Izquierda: Datos Básicos -->
                <div>
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 700; margin-bottom: 0.3rem; font-size: 0.8rem; color: var(--text-main);">Nombre del Motivo *</label>
                        <input type="text" name="nombre" id="motivo-nombre" required placeholder="Ej. Cita Médica" style="width:100%; padding:0.6rem; border:1px solid var(--border-color); border-radius:8px; background: #f8fafc; font-size: 0.85rem;">
                    </div>

                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 700; margin-bottom: 0.3rem; font-size: 0.8rem; color: var(--text-main);">Descripción</label>
                        <textarea name="descripcion" id="motivo-descripcion" rows="3" style="width:100%; padding:0.6rem; border:1px solid var(--border-color); border-radius:8px; background: #f8fafc; resize: none; font-size: 0.85rem;"></textarea>
                    </div>

                    <div class="options-box">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.8rem; font-weight: 600; color: var(--primary-color);">
                            <input type="checkbox" name="repone_tiempo" id="motivo-repone" value="1" style="width:16px; height:16px; accent-color: var(--primary-color);">
                            ¿Repone tiempo?
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.8rem; font-weight: 600; color: var(--primary-color);">
                            <input type="checkbox" name="visible_para_usuarios" id="motivo-visible" value="1" checked style="width:16px; height:16px; accent-color: var(--primary-color);">
                            Activo para solicitar
                        </label>
                    </div>
                </div>

                <!-- Columna Derecha: Restricciones -->
                <div>
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                            <label style="display:block; font-weight: 700; font-size: 0.8rem; color: var(--text-main);">Áreas Permitidas</label>
                            <label style="font-size: 0.7rem; color: var(--primary-color); cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                <input type="checkbox" id="chk-all-areas" style="width:12px; height:12px; accent-color: var(--primary-color);"> Seleccionar todo
                            </label>
                        </div>
                        <div class="scroll-container">
                            <?php foreach($data['areas'] as $area): ?>
                                <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; font-size: 0.8rem; cursor: pointer; color: var(--text-main);">
                                    <input type="checkbox" name="areas_permitidas[]" value="<?php echo htmlspecialchars($area->area); ?>" class="chk-area" style="width:14px; height:14px; accent-color: var(--primary-color);">
                                    <?php echo htmlspecialchars($area->area); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="input-group" style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                            <label style="display:block; font-weight: 700; font-size: 0.8rem; color: var(--text-main);">Usuarios Permitidos</label>
                            <label style="font-size: 0.7rem; color: var(--primary-color); cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                <input type="checkbox" id="chk-all-usuarios" style="width:12px; height:12px; accent-color: var(--primary-color);"> Seleccionar todo
                            </label>
                        </div>
                        <div class="scroll-container">
                            <?php foreach($data['usuarios'] as $usuario): ?>
                                <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.6rem; font-size: 0.8rem; cursor: pointer; color: var(--text-main); min-height: 30px;">
                                    <input type="checkbox" name="usuarios_permitidos[]" value="<?php echo $usuario->id; ?>" class="chk-usuario" style="width:14px; height:14px; accent-color: var(--primary-color); flex-shrink: 0;">
                                    <span style="line-height: 1.1;"><?php echo htmlspecialchars($usuario->nombre); ?> <br><small style="color: #64748b; font-size: 0.7rem;"><?php echo htmlspecialchars($usuario->cedula); ?></small></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <small style="color: var(--text-muted); font-size: 0.7rem; display: block; margin-top: 0.3rem;">Si no selecciona áreas ni usuarios, el motivo será global.</small>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="justify-content: flex-end; gap: 0.6rem; margin-top: 1.2rem; padding-top: 0.8rem; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="cerrarModalMotivo()" class="btn btn-secondary" style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">Cancelar</button>
                <button type="submit" class="btn btn-success" style="padding: 0.5rem 1.5rem; font-size: 0.85rem;">
                    <i class="fas fa-save"></i> Guardar Motivo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Formulario Oculto para Eliminar Motivo -->
<form id="form-eliminar-motivo" action="" method="POST" style="display:none;"></form>

<!-- Modal de Confirmación de Eliminación -->
<div id="modal-eliminar" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="modal-title">¿Eliminar Motivo?</div>
        <div class="modal-text">Está a punto de eliminar <strong><span id="nombre-usuario-modal"></span></strong>. Esta acción no se puede deshacer.</div>
        <div class="modal-footer">
            <button id="btn-cancelar-modal" class="btn btn-secondary" onclick="cerrarModalEliminar()">Cancelar</button>
            <button id="btn-confirmar-modal" class="btn btn-danger">Sí, Eliminar</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Lógica de eliminación con Modal Personalizado
    $(document).on('click', '.btn-eliminar-motivo', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        $('#nombre-usuario-modal').text('el motivo "' + nombre + '"');
        $('#modal-eliminar').css('display', 'flex');
        
        $('#btn-confirmar-modal').off('click').on('click', function() {
            $('#form-eliminar-motivo').attr('action', '<?php echo URLROOT; ?>/configuracion/eliminarMotivo/' + id).submit();
        });
    });

    // Cerrar modal al hacer clic fuera o en cancelar
    $('#btn-cancelar-modal').on('click', function() {
        cerrarModalEliminar();
    });

    // Lógica de "Seleccionar todo" para Áreas
    $('#chk-all-areas').on('change', function() {
        $('.chk-area').prop('checked', $(this).prop('checked'));
    });

    // Lógica de "Seleccionar todo" para Usuarios
    $('#chk-all-usuarios').on('change', function() {
        $('.chk-usuario').prop('checked', $(this).prop('checked'));
    });

    // Actualizar el estado de "Seleccionar todo" si se marcan/desmarcan manualmente
    $(document).on('change', '.chk-area', function() {
        $('#chk-all-areas').prop('checked', $('.chk-area:checked').length === $('.chk-area').length);
    });

    $(document).on('change', '.chk-usuario', function() {
        $('#chk-all-usuarios').prop('checked', $('.chk-usuario:checked').length === $('.chk-usuario').length);
    });
});

function nuevoMotivo() {
    document.getElementById('modal-title-motivo').textContent = "Nuevo Motivo";
    document.getElementById('motivo-id').value = "";
    document.getElementById('motivo-nombre').value = "";
    document.getElementById('motivo-descripcion').value = "";
    document.getElementById('motivo-repone').checked = false;
    document.getElementById('motivo-visible').checked = true;
    
    // Desmarcar todos los checkboxes
    $('.chk-area').prop('checked', false);
    $('.chk-usuario').prop('checked', false);
    $('#chk-all-areas').prop('checked', false);
    $('#chk-all-usuarios').prop('checked', false);
    
    document.getElementById('modal-motivo').style.display = 'flex';
}

function editarMotivo(motivo) {
    document.getElementById('modal-title-motivo').textContent = "Editar Motivo";
    document.getElementById('motivo-id').value = motivo.id;
    document.getElementById('motivo-nombre').value = motivo.nombre;
    document.getElementById('motivo-descripcion').value = motivo.descripcion;
    document.getElementById('motivo-repone').checked = motivo.repone_tiempo == 1;
    document.getElementById('motivo-visible').checked = motivo.visible_para_usuarios == 1;
    
    // Limpiar checkboxes
    $('.chk-area').prop('checked', false);
    $('.chk-usuario').prop('checked', false);
    
    // Marcar areas guardadas
    let areas = [];
    if(motivo.areas_permitidas) {
        try { areas = JSON.parse(motivo.areas_permitidas); } catch(e){}
    }
    areas.forEach(a => {
        $('.chk-area[value="'+a+'"]').prop('checked', true);
    });

    // Marcar usuarios guardados
    let usuarios = [];
    if(motivo.usuarios_permitidos) {
        try { usuarios = JSON.parse(motivo.usuarios_permitidos); } catch(e){}
    }
    usuarios.forEach(u => {
        $('.chk-usuario[value="'+u+'"]').prop('checked', true);
    });

    // Actualizar estados de "Seleccionar todo"
    $('#chk-all-areas').prop('checked', areas.length > 0 && areas.length === $('.chk-area').length);
    $('#chk-all-usuarios').prop('checked', usuarios.length > 0 && usuarios.length === $('.chk-usuario').length);
    
    document.getElementById('modal-motivo').style.display = 'flex';
}

function cerrarModalMotivo() {
    document.getElementById('modal-motivo').style.display = 'none';
}

function cerrarModalEliminar() {
    document.getElementById('modal-eliminar').style.display = 'none';
}
</script>

<?php require_once '../views/layouts/footer.php'; ?>
