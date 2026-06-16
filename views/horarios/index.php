<?php 
require_once '../views/layouts/header.php'; 
?>
<div class="card" style="padding: 2rem;">
    <h2>Configuración de Horarios</h2>
    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <a href="<?php echo URLROOT; ?>/horarios/planta" class="btn btn-primary">Gestionar Planta</a>
        <a href="<?php echo URLROOT; ?>/horarios/administrativo" class="btn btn-primary">Gestionar Administrativo</a>
        <a href="<?php echo URLROOT; ?>/horarios/asignar" class="btn btn-primary">Asignar Turnos</a>
    </div>
</div>
<?php require_once '../views/layouts/footer.php'; ?>
