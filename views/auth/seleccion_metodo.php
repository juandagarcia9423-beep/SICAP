<?php require_once '../views/layouts/auth_header.php'; ?>
<div class="auth-card" style="width: 400px;">
    <h2>Seleccione Método de Marcación</h2>
    <p>Elija cómo desea registrar su asistencia:</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem;">
        <?php if($data['authConfig']['pin']): ?>
            <a href="<?php echo URLROOT; ?>/auth/pin" class="method-btn">
                <i class="fas fa-key"></i>
                <span>PIN</span>
            </a>
        <?php endif; ?>

        <?php if($data['authConfig']['facial']): ?>
            <a href="<?php echo URLROOT; ?>/auth/facial" class="method-btn">
                <i class="fas fa-user-check"></i>
                <span>Facial</span>
            </a>
        <?php endif; ?>

        <?php if($data['authConfig']['qr']): ?>
            <a href="<?php echo URLROOT; ?>/auth/qr" class="method-btn">
                <i class="fas fa-qrcode"></i>
                <span>QR</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
    .method-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none;
        color: #1e293b;
        transition: all 0.3s;
        gap: 0.5rem;
    }
    .method-btn i {
        font-size: 2.5rem;
        color: #3b82f6;
    }
    .method-btn span {
        font-weight: 700;
        font-size: 1rem;
    }
    .method-btn:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
</style>
<?php require_once '../views/layouts/auth_footer.php'; ?>
