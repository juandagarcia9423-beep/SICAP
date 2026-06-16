<?php 
require_once '../views/layouts/header.php'; 
$turnos = ['Turno 1', 'Turno 2', 'Turno 3'];
?>
<style>
    .select-professional {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background-color: #fff;
        font-size: 0.9rem;
        color: #334155;
        transition: border-color 0.2s, box-shadow 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.2em;
    }
    .select-professional:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1); outline: none; }

    .turno-group { margin-top: 1rem; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
    .turno-header { background: #f1f5f9; padding: 1rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
    .turno-content { padding: 1rem; display: none; }
    .turno-content.open { display: block; }

    /* Colores */
    .color-turno-1 { color: #1e3a8a; font-weight: bold; }
    .color-turno-2 { color: #15803d; font-weight: bold; }
    .color-turno-3 { color: #991b1b; font-weight: bold; }
</style>

<div class="content-body">
    <div class="card" style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="margin: 0;"><?php echo $data['titulo']; ?></h2>
            <a href="<?php echo URLROOT; ?>/horarios" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <!-- [FORMULARIO SE MANTIENE IGUAL] -->
        <?php if (app\Helpers\SesionHelper::tienePermiso('horarios', 'editar')): ?>
            <form action="<?php echo URLROOT; ?>/horarios/guardarAsignacion" method="POST" class="card" style="background: #f8fafc; border: 1px solid var(--border-color);">
                <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">

                    <div class="form-group" style="flex: 2; min-width: 250px;">
                        <label style="font-weight: bold; margin-bottom: 0.5rem; display: block;">Seleccionar Empleados:</label>
                        <div style="height: 100px; overflow-y: auto; border: 1px solid #cbd5e1; padding: 0.5rem; border-radius: 8px; background: #fff;">
                            <?php 
                            $turnoColors = ['Turno 1' => 'color-turno-1', 'Turno 2' => 'color-turno-2', 'Turno 3' => 'color-turno-3'];
                            foreach($data['empleados'] as $e): 
                                $colorClass = isset($turnoColors[$e->turno_asignado]) ? $turnoColors[$e->turno_asignado] : '';
                            ?>
                                <div style="padding: 2px 0;">
                                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;" class="<?php echo $colorClass; ?>">
                                        <input type="checkbox" name="empleado_ids[]" value="<?php echo $e->id; ?>"> 
                                        <?php echo $e->nombre; ?> (<?php echo $e->area; ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <label style="font-weight: bold; margin-bottom: 0.5rem; display: block;">Turno:</label>
                        <select name="empleados_turno" class="select-professional">
                            <option value="">-- Sin Turno --</option>
                            <?php foreach($turnos as $tn): ?>
                                <option value="<?php echo $tn; ?>"><?php echo $tn; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" style="height: 2.5rem;">Asignar</button>
                </div>
            </form>
        <?php endif; ?>

        <h3>Asignaciones Actuales</h3>

        <?php 
        $turnoColors = ['Turno 1' => 'color-turno-1', 'Turno 2' => 'color-turno-2', 'Turno 3' => 'color-turno-3'];
        foreach($turnos as $tn): 
            $empleadosTurno = array_filter($data['empleados'], function($e) use ($tn) { return $e->turno_asignado == $tn; });
        ?>
        <div class="turno-group">
            <div class="turno-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #f1f5f9; cursor: pointer;">
                <div onclick="this.parentElement.nextElementSibling.classList.toggle('open')" style="flex-grow: 1;">
                    <h4 style="margin: 0;" class="<?php echo $turnoColors[$tn]; ?>"><?php echo $tn; ?> (<?php echo count($empleadosTurno); ?> empleados)</h4>
                </div>
                <?php if(!empty($empleadosTurno) && app\Helpers\SesionHelper::tienePermiso('horarios', 'editar')): ?>
                    <form action="<?php echo URLROOT; ?>/horarios/guardarAsignacion" method="POST" style="display:inline;">
                        <input type="hidden" name="desasignar_turno" value="1">
                        <input type="hidden" name="turno" value="<?php echo $tn; ?>">
                        <button type="submit" class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">Desasignar Todos</button>
                    </form>
                <?php endif; ?>
                <i class="fas fa-chevron-down" style="margin-left: 10px;"></i>
            </div>
            <div class="turno-content">
                <?php if(empty($empleadosTurno)): ?>
                    <p>No hay empleados asignados.</p>
                <?php else: ?>
                    <table class="table">
                        <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach($empleadosTurno as $e): ?>
                            <tr>
                                <td class="<?php echo $turnoColors[$tn]; ?>"><?php echo $e->nombre; ?></td>
                                <td>
                                    <?php if (app\Helpers\SesionHelper::tienePermiso('horarios', 'editar')): ?>
                                        <form action="<?php echo URLROOT; ?>/horarios/guardarAsignacion" method="POST" style="display:inline;">
                                            <input type="hidden" name="empleado_ids[]" value="<?php echo $e->id; ?>">
                                            <input type="hidden" name="empleados_turno" value="">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">Desasignar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Sin permisos</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php require_once '../views/layouts/footer.php'; ?>

