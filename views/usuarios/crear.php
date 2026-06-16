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
            <i class="fas fa-user-plus" style="color: var(--primary-color); margin-right: 8px;"></i> 
            Registrar Nuevo Usuario
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

        <form action="<?php echo URLROOT; ?>/usuarios/crear" method="POST">
            
            <div class="grid-form">
                <div class="section-title"><i class="fas fa-id-card"></i> Datos Personales y de Cuenta</div>
                
                <div class="input-group">
                    <label>Nombre Completo</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nombre" required placeholder="Ej. Juan Pérez">
                    </div>
                </div>

                <div class="input-group">
                    <label>Número de Cédula</label>
                    <div class="input-wrapper">
                        <i class="fas fa-fingerprint"></i>
                        <input type="text" name="cedula" required placeholder="Identificación">
                    </div>
                </div>

                <div class="input-group">
                    <label>Nombre de Usuario</label>
                    <div class="input-wrapper">
                        <i class="fas fa-at"></i>
                        <input type="text" name="usuario" required placeholder="User123">
                    </div>
                </div>

                <div class="input-group">
                    <label>Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required placeholder="correo@empresa.com">
                    </div>
                </div>

                <div class="input-group">
                    <label>Contraseña Provisional</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" required placeholder="********">
                    </div>
                </div>

                <div class="section-title" style="margin-top: 0.5rem;"><i class="fas fa-briefcase"></i> Perfil Laboral</div>

                <div class="input-group">
                    <label>Rol en el Sistema</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-tag"></i>
                        <select name="rol" required>
                            <option value="empleado">Empleado</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="administrativos">Administrativo</option>
                            <option value="gerencia">Gerencia</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Departamento / Área</label>
                    <div class="input-wrapper">
                        <i class="fas fa-sitemap"></i>
                        <input type="text" name="area" placeholder="Ej. Sistemas">
                    </div>
                </div>

                <div class="section-title" style="margin-top: 0.5rem;"><i class="fas fa-lock"></i> Métodos de Acceso</div>

                <div class="input-group" style="grid-column: span 3; display: flex; gap: 1rem; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permite_pin" value="1"> Permitir PIN
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permite_facial" value="1"> Permitir Facial
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="permite_qr" value="1"> Permitir QR
                    </label>
                </div>

                <div class="input-group">
                    <label>PIN Secreto</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="text" name="pin_secreto" placeholder="PIN de 4 dígitos">
                    </div>
                </div>

                <div class="section-title" style="margin-top: 0.5rem;"><i class="fas fa-camera"></i> Enrolamiento Facial</div>
                
                <div class="input-group" style="grid-column: span 3; text-align: center;">
                    <div id="facial-setup-container" style="display: flex; flex-direction: column; align-items: center; gap: 1rem; border: 1px dashed #cbd5e1; padding: 1.5rem; border-radius: 12px; background: #f8fafc;">
                        <div id="webcam-preview-container" style="position: relative; width: 320px; height: 240px; background: #000; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                            <video id="webcam" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                            <canvas id="captured-photo" style="display: none; width: 100%; height: 100%; object-fit: cover;"></canvas>
                            <div id="photo-placeholder" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #64748b; background: #f1f5f9;">
                                <i class="fas fa-user-circle" style="font-size: 4rem; margin-bottom: 0.5rem;"></i>
                                <span style="font-size: 0.8rem; font-weight: 600;">Cámara desactivada</span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.8rem;">
                            <button type="button" id="btn-start-webcam" class="btn" style="background: #3b82f6; color: white; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 6px;">
                                <i class="fas fa-video"></i> Activar Cámara
                            </button>
                            <button type="button" id="btn-capture-photo" class="btn" style="background: #10b981; color: white; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 6px; display: none;">
                                <i class="fas fa-camera"></i> Capturar Rostro
                            </button>
                            <button type="button" id="btn-retake-photo" class="btn" style="background: #f59e0b; color: white; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 6px; display: none;">
                                <i class="fas fa-redo"></i> Volver a Tomar
                            </button>
                        </div>
                        
                        <input type="hidden" name="foto_facial" id="foto_facial_input">
                        
                        <div id="current-photo-status" style="font-size: 0.85rem; color: #64748b;">
                            <i class="fas fa-info-circle"></i> Capture una foto del rostro del empleado para habilitar el acceso facial.
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button type="reset" class="btn btn-danger">Limpiar</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('captured-photo');
    const photoInput = document.getElementById('foto_facial_input');
    const btnStart = document.getElementById('btn-start-webcam');
    const btnCapture = document.getElementById('btn-capture-photo');
    const btnRetake = document.getElementById('btn-retake-photo');
    const placeholder = document.getElementById('photo-placeholder');
    const statusText = document.getElementById('current-photo-status');

    let stream = null;

    btnStart.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            placeholder.style.display = 'none';
            video.style.display = 'block';
            canvas.style.display = 'none';
            btnStart.style.display = 'none';
            btnCapture.style.display = 'inline-block';
            btnRetake.style.display = 'none';
        } catch (err) {
            console.error("Error accediendo a la cámara: ", err);
            alert("No se pudo acceder a la cámara. Asegúrese de dar los permisos necesarios.");
        }
    });

    btnCapture.addEventListener('click', () => {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg');
        photoInput.value = imageData;
        
        // Detener stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        video.style.display = 'none';
        canvas.style.display = 'block';
        btnCapture.style.display = 'none';
        btnRetake.style.display = 'inline-block';
        btnStart.style.display = 'inline-block';
        btnStart.innerHTML = '<i class="fas fa-video"></i> Reactivar Cámara';
        
        statusText.innerHTML = '<i class="fas fa-check-circle"></i> Rostro capturado exitosamente. Guarde el usuario para finalizar.';
        statusText.style.color = '#059669';
    });

    btnRetake.addEventListener('click', () => {
        btnStart.click();
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>
