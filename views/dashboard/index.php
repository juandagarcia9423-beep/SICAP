<?php require_once '../views/layouts/header.php'; ?>

<?php if ($data['puede_ver_stats']): ?>
<div class="stats-grid">
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
</div>
<?php endif; ?>

<h3 style="margin-top: 2rem; color: #1e293b;">Acceso Rápido a Módulos</h3>
<div class="module-grid">
    <a href="<?php echo URLROOT; ?>/usuarios" class="module-card bg-usuarios">
        <i class="fas fa-users"></i>
        <span>Usuarios</span>
    </a>
    <a href="<?php echo URLROOT; ?>/asistencia" class="module-card bg-asistencia">
        <i class="fas fa-clock"></i>
        <span>Asistencia</span>
    </a>
    <a href="<?php echo URLROOT; ?>/permisos" class="module-card bg-permisos">
        <i class="fas fa-file-contract"></i>
        <span>Permisos</span>
    </a>
    <a href="<?php echo URLROOT; ?>/horarios" class="module-card bg-horarios">
        <i class="fas fa-calendar-alt"></i>
        <span>Horarios</span>
    </a>
    <a href="<?php echo URLROOT; ?>/alertas" class="module-card bg-alertas">
        <i class="fas fa-bell"></i>
        <span>Alertas</span>
    </a>
    <a href="<?php echo URLROOT; ?>/informes" class="module-card bg-informes">
        <i class="fas fa-chart-bar"></i>
        <span>Informes</span>
    </a>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3>Bienvenido al Panel de Control SICAP</h3>
    <p>Utilice las tarjetas superiores para ir directamente a cada módulo o revise el resumen estadístico inicial.</p>
</div>

<?php require_once '../views/layouts/footer.php'; ?>
