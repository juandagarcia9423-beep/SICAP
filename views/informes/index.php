<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="margin: 0;"><i class="fas fa-chart-bar"></i> <?php echo $data['titulo']; ?></h2>
    </div>
    
    <p class="text-muted">Seleccione el tipo de informe que desea generar:</p>

    <div class="module-grid">
        <a href="<?php echo URLROOT; ?>/informes/asistencia" class="module-card bg-asistencia">
            <i class="fas fa-clock"></i>
            <span>Asistencia General</span>
        </a>

        <a href="<?php echo URLROOT; ?>/informes/permisos" class="module-card bg-permisos">
            <i class="fas fa-file-contract"></i>
            <span>Reporte de Permisos</span>
        </a>

        <a href="<?php echo URLROOT; ?>/informes/usuarios" class="module-card bg-usuarios">
            <i class="fas fa-users"></i>
            <span>Resumen por Usuario</span>
        </a>

        <a href="<?php echo URLROOT; ?>/informes/bancohoras" class="module-card bg-informes">
            <i class="fas fa-university"></i>
            <span>Banco de Horas</span>
        </a>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
