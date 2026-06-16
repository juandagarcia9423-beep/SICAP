<?php require_once '../views/layouts/header.php'; ?>

<div class="stats-grid">
    <?php if (app\Helpers\SesionHelper::tienePermiso('bancohoras', 'ver_propio')): ?>
        <div class="card" style="border-left: 5px solid <?php echo $data['saldo'] >= 0 ? '#15803d' : '#991b1b'; ?>;">
            <i class="fas fa-university card-icon"></i>
            <div class="card-title">Mi Saldo de Tiempo</div>
            <div class="card-value" style="color: <?php echo $data['saldo'] >= 0 ? '#15803d' : '#991b1b'; ?>;">
                <?php 
                    $saldoAbs = abs($data['saldo']);
                    $horasEnteras = floor($saldoAbs);
                    $minutos = round(($saldoAbs - $horasEnteras) * 60);
                    echo ($data['saldo'] < 0 ? '-' : '') . $horasEnteras . 'h ' . str_pad($minutos, 2, '0', STR_PAD_LEFT) . 'm';
                ?>
            </div>
            <small style="color: #64748b;"><?php echo $data['saldo'] >= 0 ? 'Horas a favor para usar' : 'Horas pendientes por reponer'; ?></small>
        </div>
    <?php endif; ?>

    <?php if ($data['puede_ver_stats']): ?>
        <div class="card">
            <i class="fas fa-users card-icon"></i>
            <div class="card-title">Total Usuarios</div>
            <div class="card-value"><?php echo $data['stats']['total_usuarios']; ?></div>
        </div>
        <div class="card">
            <i class="fas fa-user-check card-icon"></i>
            <div class="card-title">Asistencia Hoy</div>
            <div class="card-value"><?php echo $data['stats']['asistencia_hoy']; ?></div>
        </div>
        <div class="card">
            <i class="fas fa-file-signature card-icon"></i>
            <div class="card-title">Permisos Pendientes</div>
            <div class="card-value"><?php echo $data['stats']['permisos_pendientes']; ?></div>
        </div>
        <div class="card">
            <i class="fas fa-exclamation-triangle card-icon"></i>
            <div class="card-title">Alertas Activas</div>
            <div class="card-value"><?php echo $data['stats']['alertas_activas']; ?></div>
        </div>
        
        <?php if (app\Helpers\SesionHelper::tienePermiso('bancohoras', 'ver')): ?>
            <a href="<?php echo URLROOT; ?>/bancohoras/index?filtro=deudores" class="card" style="background: #fff1f2; border: 1px solid #fecdd3; text-decoration: none; display: block; transition: transform 0.2s;">
                <i class="fas fa-hand-holding-usd card-icon" style="color: #e11d48;"></i>
                <div class="card-title" style="color: #9f1239;">Deuda Banco de Horas</div>
                <div class="card-value" style="color: #e11d48;">
                    <?php 
                        $val = $data['stats']['total_deuda_horas'];
                        $h = floor($val);
                        $m = round(($val - $h) * 60);
                        echo $h . 'h ' . str_pad($m, 2, '0', STR_PAD_LEFT) . 'm';
                    ?>
                </div>
                <small style="color: #be123c;">Click para ver quiénes deben tiempo</small>
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<h3 style="margin-top: 2rem; color: #1e293b;">Acceso Rápido a Módulos</h3>
<div class="module-grid">
    <?php if (app\Helpers\SesionHelper::tienePermiso('usuarios', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/usuarios" class="module-card bg-usuarios">
            <i class="fas fa-users"></i>
            <span>Usuarios</span>
        </a>
    <?php endif; ?>

    <?php if (app\Helpers\SesionHelper::tienePermiso('asistencia', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/asistencia" class="module-card bg-asistencia">
            <i class="fas fa-clock"></i>
            <span>Asistencia</span>
        </a>
    <?php endif; ?>

    <?php if (app\Helpers\SesionHelper::tienePermiso('permisos', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/permisos" class="module-card bg-permisos">
            <i class="fas fa-file-contract"></i>
            <span>Permisos</span>
        </a>
    <?php endif; ?>

    <?php if (app\Helpers\SesionHelper::tienePermiso('horarios', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/horarios" class="module-card bg-horarios">
            <i class="fas fa-calendar-alt"></i>
            <span>Horarios</span>
        </a>
    <?php endif; ?>

    <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/alertas" class="module-card bg-alertas">
            <i class="fas fa-bell"></i>
            <span>Alertas</span>
        </a>
    <?php endif; ?>

    <?php if (app\Helpers\SesionHelper::tienePermiso('informes', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/informes" class="module-card bg-informes">
            <i class="fas fa-chart-bar"></i>
            <span>Informes</span>
        </a>
    <?php endif; ?>

    <?php if (app\Helpers\SesionHelper::tienePermiso('bancohoras', 'ver')): ?>
        <a href="<?php echo URLROOT; ?>/bancohoras" class="module-card" style="background: linear-gradient(135deg, #1e3a8a, #0f172a);">
            <i class="fas fa-university"></i>
            <span>Banco de Horas</span>
        </a>
    <?php endif; ?>
</div>

<div class="card" style="margin-top: 2rem; background-color: var(--primary-color); color: white; border: none;">
    <h3 style="color: white; margin: 0;">Bienvenido al Panel de Control SICAP - Desarrollado por JD Soluciones</h3>
    <p style="margin-bottom: 0; opacity: 0.9;">Utilice las tarjetas superiores para ir directamente a cada módulo o revise el resumen estadístico inicial.</p>
</div>

<?php require_once '../views/layouts/footer.php'; ?>
