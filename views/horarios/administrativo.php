<?php 
require_once '../views/layouts/header.php'; 
$dias = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado', 7=>'Domingo'];
?>
<style>
    .admin-section { margin-top: 2rem; padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); background-color: #f8fafc; border-left: 5px solid #64748b; }
    .admin-section .day-card { border-top: 3px solid #64748b; }
    .days-container { display: flex; flex-wrap: nowrap; gap: 0.25rem; margin-top: 1rem; width: 100%; max-width: 100%; overflow-x: auto; }
    .day-card { border: 1px solid var(--border-color); padding: 0.4rem; border-radius: 6px; flex: 1 1 auto; min-width: 100px; background: #fff; font-size: 0.75rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .day-card .form-control { padding: 0.2rem; font-size: 0.75rem; height: auto; width: 100%; box-sizing: border-box; }
</style>
<div class="card" style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0;"><?php echo $data['titulo']; ?></h2>
        <a href="<?php echo URLROOT; ?>/horarios" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
    <form action="<?php echo URLROOT; ?>/horarios/guardar" method="POST">
        <input type="hidden" name="tipo_personal" value="administrativo">
        
        <div class="admin-section">
            <h3 style="margin-top: 0;">Configuración Administrativa</h3>
            <div class="days-container">
                <?php foreach($dias as $k => $v): 
                    $conf = null;
                    foreach($data['configuraciones'] as $c) if($c->dia_semana == $k) $conf = $c;
                ?>
                <div class="day-card">
                    <input type="hidden" name="config[<?php echo $k; ?>][Administrativo][turno_id]" value="">
                    <strong><?php echo $v; ?></strong><br>
                    <label><input type="checkbox" name="config[<?php echo $k; ?>][Administrativo][activo]" value="1" <?php echo ($conf && $conf->activo) ? 'checked' : ''; ?>> Activo</label><br>
                    <small>Entrada:</small>
                    <input type="time" name="config[<?php echo $k; ?>][Administrativo][hora_entrada]" class="form-control" value="<?php echo $conf ? $conf->hora_entrada : ''; ?>"><br>
                    <small>Salida:</small>
                    <input type="time" name="config[<?php echo $k; ?>][Administrativo][hora_salida]" class="form-control" value="<?php echo $conf ? $conf->hora_salida : ''; ?>"><br>
                    <small>Horas:</small>
                    <input type="number" step="0.5" name="config[<?php echo $k; ?>][Administrativo][horas]" class="form-control" value="<?php echo $conf ? $conf->horas_ordinarias : 8; ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (app\Helpers\SesionHelper::tienePermiso('horarios', 'editar')): ?>
            <button type="submit" class="btn btn-success" style="margin-top: 2rem;">Guardar Cambios</button>
        <?php endif; ?>
    </form>
</div>
<?php require_once '../views/layouts/footer.php'; ?>
