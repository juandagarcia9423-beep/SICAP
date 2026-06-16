<?php require_once '../views/layouts/auth_header.php'; ?>
<div class="auth-card">
    <h2>Hola, <?php echo $data['usuario']->nombre; ?></h2>
    
    <?php 
    $tipo = (!$data['ultimaMarcacion'] || $data['ultimaMarcacion']->tipo == 'salida') ? 'entrada' : 'salida';
    $mensaje = ($tipo == 'entrada') ? 'Debes marcar tu ENTRADA' : 'Debes marcar tu SALIDA';
    ?>
    
    <p style="font-size: 1.2rem; font-weight: bold; margin: 1.5rem 0;"><?php echo $mensaje; ?></p>

    <form action="<?php echo URLROOT; ?>/auth/registrar_marcacion" method="POST">
        <input type="hidden" name="usuario_id" value="<?php echo $data['usuario']->id; ?>">
        <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
        
        <button type="submit" class="btn <?php echo ($tipo == 'entrada') ? 'btn-success' : 'btn-danger'; ?>">
            <?php echo ($tipo == 'entrada') ? 'Registrar Entrada' : 'Registrar Salida'; ?>
        </button>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="<?php echo URLROOT; ?>/auth/metodos" class="btn" style="background-color: #f59e0b; color: white; padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 4px;">Volver atrás</a>
        </div>
    </form>
</div>
<?php require_once '../views/layouts/auth_footer.php'; ?>