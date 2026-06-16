<?php require_once '../views/layouts/header.php'; ?>

<style>
    .perfil-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .perfil-grid {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
    }
    .perfil-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        padding: 2rem;
        text-align: center;
    }
    .avatar-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: var(--bg-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem auto;
        border: 4px solid var(--primary-color);
        color: var(--primary-color);
        font-size: 5rem;
    }
    .user-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }
    .user-role {
        display: inline-block;
        padding: 0.3rem 1rem;
        background: var(--form-bg);
        color: var(--primary-color);
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .info-label {
        color: var(--text-muted);
        font-weight: 500;
    }
    .info-value {
        color: var(--text-main);
        font-weight: 600;
    }
    .password-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        padding: 2rem;
    }
    .password-header {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
    }
    .password-form {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .form-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
    }
    .form-group input {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.95rem;
        background: #fcfcfc;
    }
    .form-group input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrapper input {
        width: 100%;
    }
    .toggle-password {
        position: absolute;
        right: 1rem;
        cursor: pointer;
        color: #94a3b8;
        transition: color 0.2s;
    }
    .toggle-password:hover {
        color: var(--primary-color);
    }
    @media (max-width: 768px) {
        .perfil-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="perfil-container">
    <div style="margin-bottom: 2rem;">
        <h2 style="margin:0; font-weight: 800; color: var(--text-main);"><i class="fas fa-user-circle"></i> Mi Perfil</h2>
        <p style="color: var(--text-muted); margin-top: 0.3rem;">Gestiona tu información personal y seguridad.</p>
    </div>

    <div class="perfil-grid">
        <div class="perfil-card">
            <div class="avatar-large">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-name"><?php echo $data['usuario']->nombre; ?></div>
            <div class="user-role"><?php echo $data['usuario']->rol; ?></div>
            
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">Usuario:</span>
                    <span class="info-value"><?php echo $data['usuario']->usuario; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo $data['usuario']->email; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cédula:</span>
                    <span class="info-value"><?php echo $data['usuario']->cedula; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Área:</span>
                    <span class="info-value"><?php echo $data['usuario']->area; ?></span>
                </div>
                <div class="info-item" style="border-bottom: none;">
                    <span class="info-label">Último cambio pass:</span>
                    <span class="info-value" style="font-size: 0.8rem;"><?php echo date('d/m/Y H:i', strtotime($data['usuario']->ultimo_cambio_password)); ?></span>
                </div>
            </div>
        </div>

        <div class="password-card">
            <div class="password-header">
                <i class="fas fa-shield-alt" style="font-size: 1.5rem;"></i>
                <h3 style="margin:0;">Cambiar Contraseña</h3>
            </div>

            <?php if(!empty($data['error'])): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $data['error']; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>/perfil/cambiarPassword" method="POST" class="password-form">
                <div class="form-group">
                    <label>Contraseña Actual</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_actual" id="password_actual" required placeholder="Ingrese su contraseña actual">
                        <i class="fas fa-eye toggle-password" data-target="password_actual"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nueva Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_nueva" id="password_nueva" required placeholder="Mínimo 6 caracteres">
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirmar Nueva Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmar" id="password_confirmar" required placeholder="Repita la nueva contraseña">
                    </div>
                </div>

                <div style="margin-top: -0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">
                        <input type="checkbox" id="ver_nuevas_pass"> Mostrar nuevas contraseñas
                    </label>
                </div>

                <div style="margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.8rem;">
                        <i class="fas fa-save"></i> Actualizar Seguridad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle para Contraseña Actual (Icono de Ojo)
        const toggleActual = document.querySelector('.toggle-password');
        toggleActual.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });

        // Toggle para Nuevas Contraseñas (Check)
        const checkVer = document.getElementById('ver_nuevas_pass');
        const passNueva = document.getElementById('password_nueva');
        const passConfirmar = document.getElementById('password_confirmar');

        checkVer.addEventListener('change', function() {
            const type = this.checked ? 'text' : 'password';
            passNueva.type = type;
            passConfirmar.type = type;
        });
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>
