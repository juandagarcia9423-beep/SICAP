<?php 
require_once '../views/layouts/header.php'; 
?>
<div class="content-body">
    <div class="card" style="padding: 2rem; display: flex; flex-direction: column; height: calc(100vh - 150px);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <a href="<?php echo URLROOT; ?>/alertas" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
            
            <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'eliminar')): ?>
                <button type="button" id="btn-eliminar-masivo" class="btn btn-danger" style="display: none;" onclick="abrirModalMasivo()">
                    <i class="fas fa-trash-alt"></i> Eliminar seleccionadas (<span id="count-seleccionadas">0</span>)
                </button>
            <?php endif; ?>
        </div>

<style>
    .alert-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .alert-table th { background-color: #f1f5f9; padding: 1rem; text-align: left; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; z-index: 10; }
    .alert-table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    
    /* Checkbox styling */
    .checkbox-col { width: 40px; text-align: center; }
    input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }

    /* Colores de estado */
    .row-leida { background-color: #dcfce7; } /* Verde claro */
    .row-no-leida { background-color: #fef9c3; } /* Amarillo claro */
    .table-wrapper { flex-grow: 1; overflow-y: auto; }
</style>

        <div class="table-wrapper">
        <table class="alert-table">
            <thead>
                <tr>
                    <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'eliminar')): ?>
                        <th class="checkbox-col"><input type="checkbox" id="select-all"></th>
                    <?php endif; ?>
                    <th>Usuario</th>
                    <th>Área</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['alertas'] as $a): 
                    $rowClass = $a->leido ? 'row-leida' : 'row-no-leida';
                ?>
                <tr class="<?php echo $rowClass; ?>">
                    <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'eliminar')): ?>
                        <td class="checkbox-col"><input type="checkbox" class="alert-checkbox" value="<?php echo $a->id; ?>"></td>
                    <?php endif; ?>
                    <td><strong style="color: var(--text-main);"><?php echo $a->usuario_nombre; ?></strong></td>
                    <td><?php echo $a->usuario_area; ?></td>
                    <td><span class="role-badge <?php echo $a->leido ? 'badge-success' : 'badge-warning'; ?>"><?php echo $a->tipo_alerta; ?></span></td>
                    <td style="max-width: 300px;"><?php echo $a->descripcion; ?></td>
                    <td><?php echo (new DateTime($a->fecha_alerta))->format('d/m/Y h:i:s A'); ?></td>
                    <td style="display: flex; gap: 0.5rem; justify-content: center;">
                        <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'editar')): ?>
                            <a href="<?php echo URLROOT; ?>/alertas/toggle/<?php echo $a->id; ?>?tipo=<?php echo urlencode($data['filtros']['tipo']); ?>&page=<?php echo $data['currentPage']; ?>" 
                            class="btn" 
                            style="padding: 0.4rem 0.6rem; font-size: 0.9rem; background-color: <?php echo $a->leido ? '#ca8a04' : '#16a34a'; ?>; color: white;" 
                            title="<?php echo $a->leido ? 'Marcar como no leída' : 'Marcar como leída'; ?>">
                            <i class="fas fa-<?php echo $a->leido ? 'undo' : 'check'; ?>"></i>
                            </a>
                        <?php endif; ?>

                        <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'eliminar')): ?>
                            <button type="button" class="btn btn-danger" style="padding: 0.4rem 0.6rem; font-size: 0.9rem;" 
                                onclick="abrirModalEliminar('<?php echo URLROOT; ?>/alertas/eliminar/<?php echo $a->id; ?>?tipo=<?php echo urlencode($data['filtros']['tipo']); ?>&page=<?php echo $data['currentPage']; ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
        <!-- Paginación -->
        <div style="margin-top: 2rem; display: flex; gap: 0.5rem; justify-content: center;">
            <?php 
            $tipoParam = urlencode($data['filtros']['tipo']);
            for($i = 1; $i <= $data['totalPages']; $i++): 
                if($i > 5 && $i < $data['totalPages']) {
                    if($i == 6) echo '<span>...</span>';
                    continue;
                }
            ?>
                <a href="<?php echo URLROOT; ?>/alertas/detalle?page=<?php echo $i; ?>&tipo=<?php echo $tipoParam; ?>" 
                   class="btn <?php echo ($i == $data['currentPage']) ? 'btn-primary' : 'btn-secondary'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if($data['currentPage'] < $data['totalPages']): ?>
                <a href="<?php echo URLROOT; ?>/alertas/detalle?page=<?php echo $data['currentPage'] + 1; ?>&tipo=<?php echo $tipoParam; ?>" class="btn btn-secondary">Siguiente</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div id="modal-eliminar" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-title">¿Eliminar Alerta?</div>
        <div class="modal-text">Esta acción no se puede deshacer. ¿Está seguro?</div>
        <div class="modal-footer" style="padding-top: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalEliminar()">Cancelar</button>
            <a id="btn-confirmar-eliminar" href="#" class="btn btn-danger">Sí, Eliminar</a>
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

    // Lógica de Selección Múltiple
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.alert-checkbox');
    const btnMasivo = document.getElementById('btn-eliminar-masivo');
    const countSpan = document.getElementById('count-seleccionadas');

    function actualizarBotonMasivo() {
        const seleccionados = document.querySelectorAll('.alert-checkbox:checked');
        const total = seleccionados.length;
        
        if (total > 0) {
            btnMasivo.style.display = 'inline-block';
            countSpan.innerText = total;
        } else {
            btnMasivo.style.display = 'none';
        }
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        actualizarBotonMasivo();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', actualizarBotonMasivo);
    });

    function abrirModalMasivo() {
        document.getElementById('modal-eliminar-masivo').style.display = 'flex';
    }

    function cerrarModalMasivo() {
        document.getElementById('modal-eliminar-masivo').style.display = 'none';
    }

    function confirmarEliminacionMasiva() {
        const seleccionados = Array.from(document.querySelectorAll('.alert-checkbox:checked')).map(cb => cb.value);
        document.getElementById('ids-input').value = seleccionados.join(',');
        document.getElementById('form-eliminar-masivo').submit();
    }
</script>

<!-- Modal Eliminar Masivo -->
<div id="modal-eliminar-masivo" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-title">¿Eliminar Alertas Seleccionadas?</div>
        <div class="modal-text">Se eliminarán permanentemente las alertas marcadas.</div>
        <div class="modal-footer" style="padding-top: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalMasivo()">Cancelar</button>
            <button type="button" class="btn btn-danger" onclick="confirmarEliminacionMasiva()">Sí, Eliminar Todo</button>
        </div>
    </div>
</div>

<!-- Formulario Oculto para Borrado Masivo -->
<form id="form-eliminar-masivo" action="<?php echo URLROOT; ?>/alertas/eliminar_masivo" method="POST" style="display: none;">
    <input type="hidden" name="alertas_ids" id="ids-input">
    <input type="hidden" name="tipo_filtro" value="<?php echo $data['filtros']['tipo']; ?>">
    <input type="hidden" name="page_actual" value="<?php echo $data['currentPage']; ?>">
</form>

<?php require_once '../views/layouts/footer.php'; ?>
