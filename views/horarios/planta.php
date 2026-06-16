<?php 
require_once '../views/layouts/header.php'; 
$dias = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado', 7=>'Domingo'];
$turnosPlanta = ['Turno 1', 'Turno 2', 'Turno 3'];
?>
<style>
    .turno-section { margin-top: 2rem; padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); }
    .turno-1 { background-color: #eff6ff; border-left: 5px solid #1e3a8a; }
    .turno-1 .day-card { border-top: 3px solid #1e3a8a; }
    .turno-2 { background-color: #f0fdf4; border-left: 5px solid #15803d; }
    .turno-2 .day-card { border-top: 3px solid #15803d; }
    .turno-3 { background-color: #fef2f2; border-left: 5px solid #991b1b; }
    .turno-3 .day-card { border-top: 3px solid #991b1b; }
    
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
        <input type="hidden" name="tipo_personal" value="planta">
        
        <?php 
        $colors = ['Turno 1' => 'turno-1', 'Turno 2' => 'turno-2', 'Turno 3' => 'turno-3'];
        foreach($turnosPlanta as $tn): ?>
            <div class="turno-section <?php echo $colors[$tn]; ?>">
                <h3 style="margin-top: 0;"><?php echo $tn; ?></h3>
                <div class="days-container">
                    <?php foreach($dias as $k => $v): 
                        $conf = null;
                        foreach($data['configuraciones'] as $c) if($c->dia_semana == $k && $c->turno_nombre == $tn) $conf = $c;
                    ?>
                    <div class="day-card">
                        <strong><?php echo $v; ?></strong><br>
                        <label><input type="checkbox" name="config[<?php echo $k; ?>][<?php echo $tn; ?>][activo]" value="1" <?php echo ($conf && $conf->activo) ? 'checked' : ''; ?>> Activo</label><br>
                        <small>Entrada:</small>
                        <input type="time" name="config[<?php echo $k; ?>][<?php echo $tn; ?>][hora_entrada]" class="form-control" value="<?php echo $conf ? $conf->hora_entrada : ''; ?>"><br>
                        <small>Salida:</small>
                        <input type="time" name="config[<?php echo $k; ?>][<?php echo $tn; ?>][hora_salida]" class="form-control" value="<?php echo $conf ? $conf->hora_salida : ''; ?>"><br>
                        <small>Horas:</small>
                        <input type="number" step="0.5" name="config[<?php echo $k; ?>][<?php echo $tn; ?>][horas]" class="form-control" value="<?php echo $conf ? $conf->horas_ordinarias : 8; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (app\Helpers\SesionHelper::tienePermiso('horarios', 'editar')): ?>
            <button type="submit" class="btn btn-success" style="margin-top: 2rem;">Guardar Cambios</button>
        <?php endif; ?>
    </form>
</div>
<?php require_once '../views/layouts/footer.php'; ?>
