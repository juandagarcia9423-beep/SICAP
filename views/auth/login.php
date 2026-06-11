<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SICAP</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-image: url('<?php echo URLROOT; ?>/img/fondologin.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-container { 
            background: rgba(255, 255, 255, 0.85); 
            padding: 2rem; 
            border-radius: 12px; 
            box-shadow: 0 8px 32px rgba(0,0,0,0.2); 
            width: 90%; 
            max-width: 750px; 
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .login-left {
            flex: 1;
            text-align: left;
        }
        .login-right {
            flex: 0 0 240px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-left: 1px solid rgba(0,0,0,0.1);
            padding-left: 2rem;
        }
        .logo-login {
            max-width: 160px;
            margin-bottom: 0.8rem;
        }
        h2 { margin: 0; color: #333; font-size: 1.6rem; }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .form-group { 
            text-align: left;
        }
        label { display: block; margin-bottom: 0.4rem; color: #444; font-weight: bold; font-size: 0.9rem; }
        input { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }
        button { 
            width: 100%;
            padding: 0.85rem; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 0.4rem;
        }
        button:hover { background-color: #0056b3; }
        .error { color: #d9534f; text-align: center; margin-bottom: 1.2rem; font-weight: bold; font-size: 0.9rem; }
        
        @media (max-width: 768px) {
            .login-container { flex-direction: column-reverse; padding: 1.5rem; gap: 1.5rem; max-width: 400px; }
            .login-right { border-left: none; padding-left: 0; flex: none; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 1.5rem; width: 100%; }
        }
    </style>
</head>
<body>
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; width: 100%;">
        <div class="login-container">
            <!-- Parte Izquierda: Formulario -->
            <div class="login-left">
                <?php if(!empty($data['error'])): ?>
                    <div class="error"><?php echo $data['error']; ?></div>
                <?php endif; ?>
                <form action="<?php echo URLROOT; ?>/auth/login" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="usuario">Usuario o Email</label>
                        <input type="text" name="usuario" id="usuario" required value="<?php echo $data['identificador']; ?>" placeholder="Ingrese su usuario">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" required placeholder="Ingrese su contraseña">
                    </div>
                    <button type="submit">Entrar al Sistema</button>
                </form>
            </div>

            <!-- Parte Derecha: Logo y Título -->
            <div class="login-right">
                <img src="<?php echo URLROOT; ?>/img/gyp.png" alt="Logo" class="logo-login">
                <h2>SICAP</h2>
                <p style="color: #444; margin-top: 0.5rem; font-weight: bold; text-align: center;">Sistema de Control y Asistencia de Personal</p>
            </div>
        </div>
        
        <!-- Copyright fuera del contenedor traslúcido -->
        <div style="margin-top: 1.5rem; color: white; font-size: 0.85rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
            © 2026 JDSoluciones. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
