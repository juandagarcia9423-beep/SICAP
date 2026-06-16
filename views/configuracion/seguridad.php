<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 
?>

<style>
    .security-tabs { 
        display: flex; 
        gap: 0.2rem; 
        margin-bottom: 1rem; 
        border-bottom: 2px solid #e2e8f0;
    }
    .sec-tab-btn { 
        padding: 0.5rem 1.2rem; 
        border: none; 
        background: #f1f5f9; 
        cursor: pointer; 
        font-weight: 700; 
        color: #64748b; 
        border-radius: 6px 6px 0 0; 
        transition: all 0.3s;
        font-size: 0.8rem;
        border: 1px solid #e2e8f0;
        border-bottom: none;
        margin-bottom: -2px;
    }
    .sec-tab-btn.active { 
        background: white; 
        color: var(--primary-color); 
        border-bottom: 2px solid white;
        border-top: 3px solid var(--primary-color);
    }
    .sec-tab-content { display: none; }
    .sec-tab-content.active { display: block; }
    
    .scroll-list {
        height: 140px; 
        overflow-y: auto; 
        border: 1px solid #e2e8f0; 
        border-radius: 8px; 
        padding: 0.6rem; 
        background: #f8fafc;
    }

    .sec-table { width: 100%; border-collapse: collapse; }
    .sec-table thead th {
        background: #f8fafc;
        padding: 8px;
        font-size: 0.6rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
    }
    .sec-table tbody td {
        padding: 6px;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
        font-size: 0.8rem;
    }
    .sec-table tbody td:first-child {
        text-align: left;
        font-weight: 700;
        color: var(--text-main);
        padding-left: 12px;
    }
</style>

<div class="card" style="background-color: var(--form-bg) !important; padding: 1rem 1.25rem; border: none; box-shadow: var(--shadow); max-width: 1050px; margin: 0 auto;">
    <div style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem; display: flex; align-items: center; gap: 0.8rem;">
        <div style="width: 35px; height: 35px; background: var(--primary-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem;">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <h2 style="margin:0; font-size: 1rem; color: var(--text-main); font-weight: 800;">Seguridad y Autorizaciones</h2>
        </div>
    </div>

    <div style="background: white; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--border-color); margin-bottom: 1rem; display: flex; align-items: flex-end; gap: 0.8rem;">
        <div style="flex: 1; max-width: 300px;">
            <label for="usuario-select" style="display: block; font-size: 0.65rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">COLABORADOR</label>
            <select id="usuario-select" style="width: 100%; padding: 0.45rem; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem;">
                <option value="">-- Buscar usuario --</option>
                <?php foreach($data['usuarios'] as $usuario): ?>
                    <option value="<?php echo $usuario->id; ?>">
                        <?php echo $usuario->nombre; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button id="btn-cargar" class="btn btn-primary" style="padding: 0.45rem 1.2rem; border-radius: 6px; height: 36px; font-weight: 700; font-size: 0.8rem;">
            <i class="fas fa-sync-alt"></i> Cargar
        </button>
    </div>

    <div class="security-tabs">
        <button class="sec-tab-btn active" data-target="tab-permisos">Módulos</button>
        <button class="sec-tab-btn" data-target="tab-autorizadores">Reglas de Autorización</button>
        <button class="sec-tab-btn" data-target="tab-metodos">Métodos de Acceso</button>
    </div>

    <!-- Tab: Permisos por Módulo -->
    <div id="tab-permisos" class="sec-tab-content active">
        <div style="overflow-x: auto; background: white; border-radius: 8px; border: 1px solid #e2e8f0; max-height: 280px; overflow-y: auto;">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Módulo</th>
                        <th>Ver</th>
                        <th>Crear</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                        <th>Config.</th>
                        <th>Stats</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $acciones_estandar = ['ver', 'crear', 'editar', 'eliminar', 'configurar'];
                    
                    // Función para verificar si YO tengo el permiso que quiero delegar
                    $yoTengoAccesso = function($m, $a) use ($data) {
                        if ($_SESSION['usuario_rol'] == 'superadmin') return true;
                        return isset($data['misPermisos'][$m]) && in_array($a, $data['misPermisos'][$m]);
                    };

                    foreach($data['modulos'] as $modulo): 
                        // Si no tengo permiso de 'ver' en este módulo, ni siquiera lo muestro en la tabla (jerarquía)
                        if (!$yoTengoAccesso($modulo, 'ver')) continue;
                    ?>
                        <tr>
                            <td><?php echo ucfirst($modulo); ?></td>
                            <?php foreach($acciones_estandar as $accion): ?>
                                <td>
                                    <?php if ($modulo == 'configuracion' && $accion == 'configurar'): ?>
                                        <span style="color: #cbd5e1;">-</span>
                                    <?php elseif ($yoTengoAccesso($modulo, $accion)): ?>
                                        <input type="checkbox" class="permiso-checkbox" 
                                               data-modulo="<?php echo $modulo; ?>" 
                                               data-accion="<?php echo $accion; ?>" 
                                               style="width: 15px; height: 15px; cursor: pointer; accent-color: var(--primary-color);">
                                    <?php else: ?>
                                        <i class="fas fa-lock" style="color: #e2e8f0; font-size: 10px;" title="No puedes delegar lo que no tienes"></i>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            
                            <td>
                                <?php if ($modulo == 'dashboard'): ?>
                                    <?php if ($yoTengoAccesso('dashboard', 'ver_estadisticas')): ?>
                                        <input type="checkbox" class="permiso-checkbox" 
                                               data-modulo="dashboard" 
                                               data-accion="ver_estadisticas" 
                                               style="width: 15px; height: 15px; cursor: pointer; accent-color: var(--success-color);">
                                    <?php else: ?>
                                        <i class="fas fa-lock" style="color: #e2e8f0; font-size: 10px;"></i>
                                    <?php endif; ?>
                                <?php elseif ($modulo == 'configuracion'): ?>
                                    <div style="display: flex; flex-direction: column; gap: 4px; align-items: center; padding: 4px;">
                                        <?php if ($yoTengoAccesso('configuracion', 'seguridad')): ?>
                                            <label title="Reglas de Autorización" style="font-size: 10px; display: flex; align-items: center; gap: 3px; cursor: pointer;">
                                                <input type="checkbox" class="permiso-checkbox" data-modulo="configuracion" data-accion="seguridad"> Seg.
                                            </label>
                                        <?php endif; ?>

                                        <?php if ($yoTengoAccesso('configuracion', 'metodos_acceso')): ?>
                                            <label title="Métodos de Acceso" style="font-size: 10px; display: flex; align-items: center; gap: 3px; cursor: pointer;">
                                                <input type="checkbox" class="permiso-checkbox" data-modulo="configuracion" data-accion="metodos_acceso"> Mét.
                                            </label>
                                        <?php endif; ?>

                                        <?php if ($yoTengoAccesso('configuracion', 'motivos_permiso')): ?>
                                            <label title="Motivos de Permiso" style="font-size: 10px; display: flex; align-items: center; gap: 3px; cursor: pointer;">
                                                <input type="checkbox" class="permiso-checkbox" data-modulo="configuracion" data-accion="motivos_permiso"> Mot.
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #cbd5e1;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 0.8rem; display: flex; justify-content: flex-end;">
            <button id="btn-guardar-permisos" class="btn btn-success" style="padding: 0.5rem 1.8rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem;">
                <i class="fas fa-save"></i> Guardar Permisos
            </button>
        </div>
    </div>

    <!-- Tab: Configurar Autorizadores -->
    <div id="tab-autorizadores" class="sec-tab-content">
        <div style="background: white; border-radius: 8px; border: 1px solid #e2e8f0; padding: 1rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Áreas -->
                <div>
                    <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 0.5rem; color: var(--text-main);">
                        <i class="fas fa-building"></i> Áreas:
                    </label>
                    <div class="scroll-list">
                        <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; font-size: 0.8rem; cursor: pointer; color: var(--primary-color); font-weight: 800; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.3rem;">
                            <input type="checkbox" class="auth-area-chk" value="*" style="width:14px; height:14px; accent-color: var(--primary-color);"> 
                            --- TODAS LAS ÁREAS ---
                        </label>
                        <?php foreach($data['areas_todas'] as $area): ?>
                            <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; font-size: 0.8rem; cursor: pointer; color: #334155;">
                                <input type="checkbox" class="auth-area-chk" value="<?php echo htmlspecialchars($area->area); ?>" style="width:14px; height:14px; accent-color: var(--primary-color);"> 
                                <?php echo htmlspecialchars($area->area); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Usuarios -->
                <div>
                    <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 0.5rem; color: var(--text-main);">
                        <i class="fas fa-users"></i> Usuarios:
                    </label>
                    <div class="scroll-list">
                        <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; font-size: 0.8rem; cursor: pointer; color: var(--primary-color); font-weight: 800; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.3rem;">
                            <input type="checkbox" class="auth-user-chk" value="*" style="width:14px; height:14px; accent-color: var(--primary-color);"> 
                            --- TODOS LOS USUARIOS ---
                        </label>
                        <?php foreach($data['usuarios'] as $u): ?>
                            <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem; font-size: 0.8rem; cursor: pointer; color: #334155;">
                                <input type="checkbox" class="auth-user-chk" value="<?php echo $u->id; ?>" style="width:14px; height:14px; accent-color: var(--primary-color);"> 
                                <?php echo htmlspecialchars($u->nombre); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                <button id="btn-guardar-autorizacion" class="btn btn-success" style="padding: 0.5rem 1.8rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem;">
                    <i class="fas fa-check-circle"></i> Guardar Reglas
                </button>
            </div>
        </div>
    </div>

    <!-- Tab: Métodos de Acceso -->
    <div id="tab-metodos" class="sec-tab-content">
        <form action="<?php echo URLROOT; ?>/configuracion/guardarMetodosAuth" method="POST" class="card" style="padding: 1.5rem; border: 1px solid #e2e8f0;">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="pin" value="1" <?php echo $data['authConfig']['pin'] ? 'checked' : ''; ?>> Habilitar PIN
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="facial" value="1" <?php echo $data['authConfig']['facial'] ? 'checked' : ''; ?>> Habilitar Reconocimiento Facial
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="qr" value="1" <?php echo $data['authConfig']['qr'] ? 'checked' : ''; ?>> Habilitar QR
                </label>
            </div>
            <button type="submit" class="btn btn-success" style="margin-top: 1rem;">Guardar Configuración</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const URLROOT = '<?php echo URLROOT; ?>';

    // Si viene un usuario_id por URL, seleccionarlo y cargar automáticamente
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedId = urlParams.get('usuario_id');
    if (preselectedId) {
        $('#usuario-select').val(preselectedId);
        setTimeout(() => { $('#btn-cargar').click(); }, 300);
    }

    // Manejo de Tabs
    $('.sec-tab-btn').click(function() {
        $('.sec-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.sec-tab-content').removeClass('active');
        $('#' + $(this).data('target')).addClass('active');
    });

    $('#btn-cargar').click(function() {
        const usuarioId = $('#usuario-select').val();
        if (!usuarioId) { alert('Seleccione un usuario'); return; }

        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
        
        $('.permiso-checkbox').prop('checked', false);
        $('.auth-area-chk').prop('checked', false);
        $('.auth-user-chk').prop('checked', false);

        const p1 = $.ajax({ url: `${URLROOT}/configuracion/obtenerPermisosUsuario/${usuarioId}`, method: 'GET', dataType: 'json' });
        const p2 = $.ajax({ url: `${URLROOT}/configuracion/obtenerConfigAutorizacion/${usuarioId}`, method: 'GET', dataType: 'json' });

        $.when(p1, p2).done(function(res1, res2) {
            const permisos = res1[0];
            const configAuth = res2[0];

            // Limpiar todo antes de cargar
            $('.permiso-checkbox, .auth-area-chk, .auth-user-chk').prop('checked', false);

            if(permisos) {
                permisos.forEach(p => {
                    $(`.permiso-checkbox[data-modulo="${p.modulo}"][data-accion="${p.accion}"]`).prop('checked', true);
                });
            }

            if(configAuth.areas_permitidas) {
                try {
                    const areas = JSON.parse(configAuth.areas_permitidas);
                    $('.auth-area-chk').each(function() {
                        if (areas.includes($(this).val())) {
                            $(this).prop('checked', true);
                        }
                    });
                } catch(e) { console.error("Error cargando áreas:", e); }
            }

            if(configAuth.usuarios_permitidos) {
                try {
                    const users = JSON.parse(configAuth.usuarios_permitidos);
                    $('.auth-user-chk').each(function() {
                        if (users.includes($(this).val().toString())) {
                            $(this).prop('checked', true);
                        }
                    });
                } catch(e) { console.error("Error cargando usuarios:", e); }
            }

            mostrarPopUpExito("Configuración cargada.");
        }).always(function() {
            btn.html('<i class="fas fa-sync-alt"></i> Cargar Configuración').prop('disabled', false);
        });
    });

    $('#btn-guardar-permisos').click(function() {
        const usuarioId = $('#usuario-select').val();
        if (!usuarioId) { alert('Seleccione un usuario'); return; }
        const btn = $(this);
        btn.prop('disabled', true);

        const permisos = [];
        $('.permiso-checkbox:checked').each(function() {
            permisos.push({ modulo: $(this).data('modulo'), accion: $(this).data('accion') });
        });

        $.ajax({
            url: `${URLROOT}/configuracion/guardarPermisosUsuario`,
            method: 'POST',
            data: { usuario_id: usuarioId, permisos: JSON.stringify(permisos) },
            success: function() { mostrarPopUpExito("Permisos actualizados."); },
            complete: function() { btn.prop('disabled', false); }
        });
    });

    $('#btn-guardar-autorizacion').click(function() {
        const usuarioId = $('#usuario-select').val();
        if (!usuarioId) { alert('Seleccione un usuario'); return; }
        const btn = $(this);
        btn.prop('disabled', true);

        const areas = [];
        $('.auth-area-chk:checked').each(function() {
            areas.push($(this).val());
        });

        const users = [];
        $('.auth-user-chk:checked').each(function() {
            users.push($(this).val());
        });

        console.log("Guardando áreas:", areas);
        console.log("Guardando usuarios:", users);

        $.ajax({
            url: `${URLROOT}/configuracion/guardarConfigAutorizacion`,
            method: 'POST',
            data: { 
                usuario_id: usuarioId, 
                areas: areas.length ? JSON.stringify(areas) : JSON.stringify([]),
                usuarios: users.length ? JSON.stringify(users) : JSON.stringify([])
            },
            success: function(res) { 
                mostrarPopUpExito("Configuración de autorizador guardada."); 
            },
            error: function() {
                mostrarPopUpError("Error al guardar la configuración.");
            },
            complete: function() { btn.prop('disabled', false); }
        });
    });
});
</script>

<?php require_once '../views/layouts/footer.php'; ?>
