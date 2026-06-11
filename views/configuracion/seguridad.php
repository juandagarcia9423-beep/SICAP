<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 
?>

<div class="card" style="background-color: var(--form-bg) !important; padding: 1.25rem; border: none; box-shadow: var(--shadow); max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin:0; font-size: 1.1rem; color: var(--primary-color); font-weight: 800;">
            <i class="fas fa-user-shield"></i> Seguridad por Usuario
        </h2>
        <p style="margin: 0; color: var(--text-muted); font-size: 0.8rem;">Control de accesos individuales.</p>
    </div>
    
    <div style="margin-bottom: 1rem; display: flex; align-items: flex-end; gap: 0.75rem; background: rgba(255,255,255,0.6); padding: 1rem; border-radius: 8px; border: 1px dashed var(--primary-color);">
        <div style="flex: 1; max-width: 300px;">
            <label for="usuario-select" style="display: block; font-size: 0.7rem; font-weight: 800; color: var(--primary-color); text-transform: uppercase; margin-bottom: 0.25rem;">Colaborador</label>
            <select id="usuario-select" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.85rem; background: white;">
                <option value="">-- Seleccione --</option>
                <?php foreach($data['usuarios'] as $usuario): ?>
                    <option value="<?php echo $usuario->id; ?>">
                        <?php echo $usuario->nombre; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button id="btn-cargar" class="btn btn-primary" style="padding: 0.5rem 1rem; border-radius: 6px; height: 35px; font-size: 0.8rem;">
            <i class="fas fa-sync-alt"></i> Cargar
        </button>
    </div>

    <div style="overflow-x: auto; background: white; border-radius: 10px; border: 1px solid var(--border-color);">
        <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
            <thead>
                <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 10px 15px; width: 18%; font-size: 0.65rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Módulo</th>
                    <th style="padding: 10px 5px; text-align: center; font-size: 0.6rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Ver</th>
                    <th style="padding: 10px 5px; text-align: center; font-size: 0.6rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Crear</th>
                    <th style="padding: 10px 5px; text-align: center; font-size: 0.6rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Editar</th>
                    <th style="padding: 10px 5px; text-align: center; font-size: 0.6rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Eliminar</th>
                    <th style="padding: 10px 5px; text-align: center; font-size: 0.6rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Configurar</th>
                    <th style="padding: 10px 5px; text-align: center; font-size: 0.6rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Estadísticas</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $acciones_estandar = ['ver', 'crear', 'editar', 'eliminar', 'configurar'];
                foreach($data['modulos'] as $modulo): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 8px 15px; font-weight: 700; color: var(--text-main); font-size: 0.85rem; background: #fcfcfc;">
                            <?php echo ucfirst($modulo); ?>
                        </td>
                        <?php foreach($acciones_estandar as $accion): ?>
                            <td style="padding: 8px 5px; text-align: center;">
                                <input type="checkbox" class="permiso-checkbox" 
                                       data-modulo="<?php echo $modulo; ?>" 
                                       data-accion="<?php echo $accion; ?>" 
                                       style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary-color);">
                            </td>
                        <?php endforeach; ?>
                        
                        <!-- Columna Especial de Estadísticas -->
                        <td style="padding: 8px 5px; text-align: center;">
                            <?php if ($modulo == 'dashboard'): ?>
                                <input type="checkbox" class="permiso-checkbox" 
                                       data-modulo="dashboard" 
                                       data-accion="ver_estadisticas" 
                                       style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--success-color);">
                            <?php else: ?>
                                <span style="color: #e2e8f0;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
        <button id="btn-guardar-permisos" class="btn btn-success" style="padding: 0.6rem 2rem; border-radius: 8px; font-size: 0.9rem;">
            <i class="fas fa-save"></i> Guardar Seguridad
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    const URLROOT = '<?php echo URLROOT; ?>';

    $('#btn-cargar').click(function() {
        const usuarioId = $('#usuario-select').val();
        if (!usuarioId) { alert('Seleccione un usuario'); return; }

        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
        $('.permiso-checkbox').prop('checked', false);

        $.ajax({
            url: `${URLROOT}/configuracion/obtenerPermisosUsuario/${usuarioId}`,
            method: 'GET',
            dataType: 'json',
            success: function(permisos) {
                permisos.forEach(p => {
                    $(`.permiso-checkbox[data-modulo="${p.modulo}"][data-accion="${p.accion}"]`).prop('checked', true);
                });
                mostrarPopUpExito("Permisos cargados.");
            },
            complete: function() { btn.html('<i class="fas fa-sync-alt"></i> Cargar').prop('disabled', false); }
        });
    });

    $('#btn-guardar-permisos').click(function() {
        const usuarioId = $('#usuario-select').val();
        if (!usuarioId) { alert('Seleccione un usuario'); return; }

        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

        const permisos = [];
        $('.permiso-checkbox:checked').each(function() {
            permisos.push({ modulo: $(this).data('modulo'), accion: $(this).data('accion') });
        });

        $.ajax({
            url: `${URLROOT}/configuracion/guardarPermisosUsuario`,
            method: 'POST',
            data: { usuario_id: usuarioId, permisos: JSON.stringify(permisos) },
            success: function() { mostrarPopUpExito("Seguridad actualizada."); },
            complete: function() { btn.html('<i class="fas fa-save"></i> Guardar Seguridad').prop('disabled', false); }
        });
    });
});
</script>

<?php require_once '../views/layouts/footer.php'; ?>
