<?php require_once '../views/layouts/auth_header.php'; ?>
<div class="auth-card">
    <h2>Acceso por QR</h2>
    <p>Funcionalidad en desarrollo. Por favor use otro método de marcación.</p>
    
    <div style="margin-top: 1.5rem; border-top: 1px solid #ddd; padding-top: 1rem; text-align: center;">
        <a href="<?php echo URLROOT; ?>/auth/metodos" class="btn" style="background-color: #f59e0b; color: white; padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 4px; text-decoration: none; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Cambiar Método
        </a>
    </div>
</div>
<?php require_once '../views/layouts/auth_footer.php'; ?>
