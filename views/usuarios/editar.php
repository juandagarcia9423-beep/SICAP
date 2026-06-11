<?php require_once '../views/layouts/header.php'; ?>

<style>
    .form-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        max-width: 1100px;
        margin: 0 auto;
        overflow: hidden;
    }
    .form-header {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .form-body {
        padding: 1.5rem 2rem;
    }
    .section-title {
        grid-column: 1 / -1;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0.5rem 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.3rem;
    }
    .grid-form {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem 1.5rem;
    }
    .input-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.4rem;
    }
    .input-wrapper {
        position: relative;
    }
    .input-wrapper i {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .input-wrapper input, .input-wrapper select {
        width: 100%;
        padding: 0.6rem 0.8rem 0.6rem 2.2rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s;
        background-color: #fcfcfc;
        box-sizing: border-box;
    }
    .input-wrapper input:focus, .input-wrapper select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background-color: white;
    }
    .form-footer {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    /* Responsividad */
    @media (max-width: 992px) {
        .grid-form { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .grid-form { grid-template-columns: 1fr; }
        .form-body { padding: 1rem; }
    }
</style>

<div class="form-card" style="background-color: var(--form-bg) !important;">
    <div class="form-header">
        <h3 style="margin:0; font-size: 1.1rem; color: var(--text-main);">
            <i class="fas fa-user-edit" style="color: var(--primary-color); margin-right: 8px;"></i> 
            Editar Usuario: <?php echo $data['usuario']->nombre; ?>
        </h3>
        <a href="<?php echo URLROOT; ?>/usuarios" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="form-body">
        <?php if(!empty($data['error'])): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 0.8rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #ef4444; font-size: 0.9rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $data['error']; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/usuarios/editar/<?php echo $data['usuario']->id; ?>" method="POST">
            
            <div class="grid-form">
                <div class="section-title"><i class="fas fa-id-card"></i> Datos Personales y de Cuenta</div>
                
                <div class="input-group">
                    <label>Nombre Completo</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nombre" required value="<?php echo $data['usuario']->nombre; ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label>Número de Cédula</label>
                    <div class="input-wrapper">
                        <i class="fas fa-fingerprint"></i>
                        <input type="text" name="cedula" required value="<?php echo $data['usuario']->cedula; ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label>Nombre de Usuario</label>
                    <div class="input-wrapper">
                        <i class="fas fa-at"></i>
                        <input type="text" name="usuario" required value="<?php echo $data['usuario']->usuario; ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label>Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required value="<?php echo $data['usuario']->email; ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label>Nueva Contraseña (Opcional)</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" placeholder="Dejar vacío para no cambiar">
                    </div>
                </div>

                <div class="section-title" style="margin-top: 0.5rem;"><i class="fas fa-briefcase"></i> Perfil Laboral</div>

                <div class="input-group">
                    <label>Rol en el Sistema</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-tag"></i>
                        <select name="rol" required>
                            <option value="empleado" <?php echo $data['usuario']->rol == 'empleado' ? 'selected' : ''; ?>>Empleado</option>
                            <option value="supervisor" <?php echo $data['usuario']->rol == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                            <option value="administrativos" <?php echo $data['usuario']->rol == 'administrativos' ? 'selected' : ''; ?>>Administrativo</option>
                            <option value="gerencia" <?php echo $data['usuario']->rol == 'gerencia' ? 'selected' : ''; ?>>Gerencia</option>
                            <option value="superadmin" <?php echo $data['usuario']->rol == 'superadmin' ? 'selected' : ''; ?>>Superadmin</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Departamento / Área</label>
                    <div class="input-wrapper">
                        <i class="fas fa-sitemap"></i>
                        <input type="text" name="area" value="<?php echo $data['usuario']->area; ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label>Tipo de Jornada</label>
                    <div class="input-wrapper">
                        <i class="fas fa-business-time"></i>
                        <select name="tipo_jornada">
                            <option value="rotativo" <?php echo $data['usuario']->tipo_jornada == 'rotativo' ? 'selected' : ''; ?>>Rotativo</option>
                            <option value="fijo" <?php echo $data['usuario']->tipo_jornada == 'fijo' ? 'selected' : ''; ?>>Fijo</option>
                            <option value="flexible" <?php echo $data['usuario']->tipo_jornada == 'flexible' ? 'selected' : ''; ?>>Flexible</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button type="reset" class="btn btn-danger">Restablecer</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>
