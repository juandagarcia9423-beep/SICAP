<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['titulo'] . ' - ' . SITENAME; ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        :root {
            --primary-color: #1e3a8a; /* Azul Oscuro Sólido */
            --primary-hover: #1e40af;
            --success-color: #15803d; /* Verde Oscuro Sólido */
            --success-hover: #166534;
            --danger-color: #991b1b;  /* Rojo Oscuro Sólido */
            --danger-hover: #7f1d1d;
            --secondary-color: #64748b;
            --bg-color: #f1f5f9;
            --form-bg: #e0f2fe; /* Azul Claro Sólido */
            --sidebar-color: #0f172a;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --radius: 12px;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        body { font-family: 'Inter', sans-serif; margin: 0; background-color: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }
        
        /* Componentes Globales */
        .card { background: var(--card-bg); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border-color); margin-bottom: 1.5rem; }
        .btn { padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-size: 0.9rem; color: white !important; }
        
        .btn-primary { background: var(--primary-color) !important; }
        .btn-primary:hover { background: var(--primary-hover) !important; transform: translateY(-1px); }
        
        .btn-success { background: var(--success-color) !important; }
        .btn-success:hover { background: var(--success-hover) !important; transform: translateY(-1px); }
        
        .btn-danger { background: var(--danger-color) !important; }
        .btn-danger:hover { background: var(--danger-hover) !important; transform: translateY(-1px); }
        
        .btn-secondary { background: #f1f5f9 !important; color: var(--text-main) !important; }
        .btn-secondary:hover { background: #e2e8f0 !important; }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-color); color: white; display: flex; flex-direction: column; transition: all 0.3s; overflow-y: auto; z-index: 100; position: sticky; top: 0; height: 100vh; }
        .sidebar-brand { 
            padding: 2rem 1.5rem; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        .logo-container {
            background: white;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 80px;
            height: 80px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .sidebar-header { font-size: 1.5rem; font-weight: 600; margin: 0; }
        .sidebar-menu { flex: 1; padding: 1rem 0; display: flex; flex-direction: column; }
        .menu-item { padding: 0.8rem 1.5rem; display: flex; align-items: center; color: #cbd5e1; text-decoration: none; transition: 0.2s; cursor: pointer; border: none; background: none; width: 100%; text-align: left; font-size: 1rem; font-family: inherit; box-sizing: border-box; }
        .menu-item:hover, .menu-item.active { background-color: rgba(255,255,255,0.1); color: white; }
        .menu-item i { margin-right: 10px; width: 20px; text-align: center; }
        .menu-item .chevron { margin-left: auto; transition: transform 0.3s; }
        
        /* Logout Button Sidebar */
        .sidebar-footer { padding: 0.75rem 1.25rem; border-top: 1px solid rgba(255,255,255,0.05); margin-top: auto; }
        .btn-logout-sidebar { 
            background: var(--danger-color); 
            color: white; 
            width: 100%; 
            justify-content: center;
            border-radius: 6px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .btn-logout-sidebar:hover { background: var(--danger-hover); transform: translateY(-1px); }
        
        /* Modal Custom Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid var(--border-color);
            animation: modalAppear 0.3s ease-out;
        }
        @keyframes modalAppear {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .modal-icon {
            font-size: 4rem;
            color: var(--danger-color);
            margin-bottom: 1rem;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }
        .modal-text {
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        /* Success Modal Styles */
        .modal-success-content {
            border-top: 5px solid var(--success-color);
        }
        .modal-icon-success {
            font-size: 4rem;
            color: var(--success-color);
            margin-bottom: 1rem;
            animation: pulseSuccess 2s infinite;
        }
        @keyframes pulseSuccess {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Dropdown Logic */
        .dropdown-container { display: none; background-color: rgba(0,0,0,0.2); }
        .dropdown-btn.active + .dropdown-container { display: block; }
        .dropdown-btn.active .chevron { transform: rotate(90deg); }
        .submenu-item { padding: 0.6rem 1.5rem 0.6rem 3rem; font-size: 0.9rem; }
        
        /* Main Content */
        .main-content { flex: 1; display: flex; flex-direction: column; width: 100%; }
        .top-nav { height: 65px; background: white; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        
        /* Ocultar buscador nativo de DataTables */
        .dataTables_filter { display: none !important; }

        .user-info { display: flex; align-items: center; gap: 10px; font-weight: 600; min-width: max-content; }
        .content-body { padding: 2rem; }
        
        /* Dashboard Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .card-title { color: var(--secondary-color); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.025em; margin-bottom: 0.5rem; }
        .card-value { font-size: 1.875rem; font-weight: 600; color: #1e293b; }
        .card-icon { float: right; font-size: 2rem; color: var(--primary-color); opacity: 0.2; }
        
        /* Module Quick Access Cards */
        .module-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1rem; }
        .module-card { 
            padding: 1.5rem; 
            border-radius: 12px; 
            color: white; 
            text-decoration: none; 
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.2); }
        .module-card i { font-size: 2.5rem; margin-bottom: 1rem; }
        .module-card span { font-weight: 600; font-size: 1.1rem; }

        /* Module Colors */
        .bg-usuarios { background: linear-gradient(135deg, #1e3a8a, #1e40af); }
        .bg-asistencia { background: linear-gradient(135deg, #10b981, #047857); }
        .bg-permisos { background: linear-gradient(135deg, #f59e0b, #b45309); }
        .bg-horarios { background: linear-gradient(135deg, #8b5cf6, #5b21b6); }
        .bg-alertas { background: linear-gradient(135deg, #991b1b, #7f1d1d); }
        .bg-informes { background: linear-gradient(135deg, #6366f1, #3730a3); }

        /* Responsividad Global */
        @media (max-width: 992px) {
            .sidebar { width: 70px; }
            .sidebar-header, .menu-item span, .menu-item .chevron, .sidebar-footer span { display: none; }
            .sidebar-header { font-size: 0; padding: 1rem 0; }
            .sidebar-header::after { content: 'S'; font-size: 1.5rem; display: block; }
            .menu-item { justify-content: center; padding: 1rem; }
            .menu-item i { margin-right: 0; font-size: 1.2rem; }
            .submenu-item { padding: 1rem; }
            .sidebar-footer { padding: 1rem 0; }
            .btn-logout-sidebar { border-radius: 0; padding: 1rem 0; }
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .sidebar-header { display: block; font-size: 1.5rem; }
            .sidebar-header::after { content: ''; }
            .sidebar-menu { display: flex; overflow-x: auto; padding: 0.5rem; flex-direction: row; }
            .menu-item { white-space: nowrap; padding: 0.8rem 1rem; }
            .menu-item span { display: inline; }
            .sidebar-footer { border-top: none; padding: 0.5rem; }
            .btn-logout-sidebar { border-radius: 8px; padding: 0.8rem; }
            .dropdown-container { position: absolute; background: var(--sidebar-color); top: 100%; left: 0; width: 100%; z-index: 1000; }
            .main-content { min-height: calc(100vh - 120px); }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-container">
                <img src="<?php echo URLROOT; ?>/img/gyp.png" alt="Logo GyP">
            </div>
            <h1 class="sidebar-header">SICAP</h1>
            <div style="color: white; font-size: 0.9rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-circle"></i> <?php echo $_SESSION['usuario_nombre']; ?>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="<?php echo URLROOT; ?>/dashboard" class="menu-item"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            
            <button class="menu-item dropdown-btn">
                <i class="fas fa-cubes"></i> <span>Módulos</span>
                <i class="fas fa-chevron-right chevron"></i>
            </button>
            <div class="dropdown-container">
                <?php if (app\Helpers\SesionHelper::tienePermiso('usuarios', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/usuarios" class="menu-item submenu-item"><i class="fas fa-users"></i> <span>Usuarios</span></a>
                <?php endif; ?>

                <?php if (app\Helpers\SesionHelper::tienePermiso('asistencia', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/asistencia" class="menu-item submenu-item"><i class="fas fa-clock"></i> <span>Asistencia</span></a>
                <?php endif; ?>

                <?php if (app\Helpers\SesionHelper::tienePermiso('permisos', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/permisos" class="menu-item submenu-item"><i class="fas fa-file-contract"></i> <span>Permisos</span></a>
                <?php endif; ?>

                <?php if (app\Helpers\SesionHelper::tienePermiso('horarios', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/horarios" class="menu-item submenu-item"><i class="fas fa-calendar-alt"></i> <span>Horarios</span></a>
                <?php endif; ?>

                <?php if (app\Helpers\SesionHelper::tienePermiso('alertas', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/alertas" class="menu-item submenu-item"><i class="fas fa-bell"></i> <span>Alertas</span></a>
                <?php endif; ?>

                <?php if (app\Helpers\SesionHelper::tienePermiso('informes', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/informes" class="menu-item submenu-item"><i class="fas fa-chart-bar"></i> <span>Informes</span></a>
                <?php endif; ?>

                <?php if (app\Helpers\SesionHelper::tienePermiso('usuarios', 'ver')): ?>
                    <a href="<?php echo URLROOT; ?>/bancohoras" class="menu-item submenu-item"><i class="fas fa-university"></i> <span>Banco de Horas</span></a>
                <?php endif; ?>
            </div>

            <?php if (app\Helpers\SesionHelper::tienePermiso('configuracion', 'ver')): ?>
                <button class="menu-item dropdown-btn">
                    <i class="fas fa-cog"></i> <span>Configuración</span>
                    <i class="fas fa-chevron-right chevron"></i>
                </button>
                <div class="dropdown-container">
                    <?php if (app\Helpers\SesionHelper::tienePermiso('configuracion', 'seguridad')): ?>
                        <a href="<?php echo URLROOT; ?>/configuracion/seguridad" class="menu-item submenu-item"><i class="fas fa-shield-alt"></i> <span>Seguridad</span></a>
                    <?php endif; ?>
                    
                    <?php if (app\Helpers\SesionHelper::tienePermiso('configuracion', 'motivos_permiso')): ?>
                        <a href="<?php echo URLROOT; ?>/configuracion/motivos" class="menu-item submenu-item"><i class="fas fa-list-ul"></i> <span>Motivos de Permisos</span></a>
                    <?php endif; ?>

                    <?php if (app\Helpers\SesionHelper::tienePermiso('configuracion', 'metodos_acceso')): ?>
                        <a href="<?php echo URLROOT; ?>/configuracion/metodosAuth" class="menu-item submenu-item"><i class="fas fa-key"></i> <span>Métodos de Acceso</span></a>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['usuario_rol'] == 'superadmin'): ?>
                        <a href="<?php echo URLROOT; ?>/configuracion/sistema" class="menu-item submenu-item"><i class="fas fa-desktop"></i> <span>Sistema</span></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="sidebar-footer">
            <a href="<?php echo URLROOT; ?>/auth/logout" class="btn btn-logout-sidebar">
                <i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>
    
    <script>
        /* Lógica para el menú desplegable */
        document.addEventListener('DOMContentLoaded', function() {
            var dropdown = document.getElementsByClassName("dropdown-btn");
            for (var i = 0; i < dropdown.length; i++) {
                dropdown[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                });
            }
        });
    </script>

    <div class="main-content">
        <div class="content-body">
            <!-- Modal de Éxito Personalizado (Pop-up) -->
            <div id="modal-exito" class="modal-overlay">
                <div class="modal-content modal-success-content">
                    <div class="modal-icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="modal-title" style="color: var(--success-color);">¡Acción Exitosa!</div>
                    <div id="mensaje-exito-modal" class="modal-text"></div>
                    <div class="modal-footer">
                        <button onclick="cerrarModalExito()" class="btn btn-success" style="padding: 0.8rem 2rem;">Aceptar</button>
                    </div>
                </div>
            </div>

            <!-- Modal de Error Personalizado (Pop-up) -->
            <div id="modal-error" class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="modal-title" style="color: var(--danger-color);">Error</div>
                    <div id="mensaje-error-modal" class="modal-text"></div>
                    <div class="modal-footer">
                        <button onclick="cerrarModalError()" class="btn btn-danger" style="padding: 0.8rem 2rem;">Aceptar</button>
                    </div>
                </div>
            </div>
            
            <?php 
            // Verificar si hay mensajes en la sesión
            if (isset($_SESSION['mensaje_exito'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        mostrarPopUpExito("<?php echo $_SESSION['mensaje_exito']; ?>");
                    });
                </script>
            <?php unset($_SESSION['mensaje_exito']); endif; 
            
            if (isset($_SESSION['mensaje_error'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        mostrarPopUpError("<?php echo $_SESSION['mensaje_error']; ?>");
                    });
                </script>
            <?php unset($_SESSION['mensaje_error']); endif; ?>

            <script>
                function mostrarPopUpExito(mensaje) {
                    const modal = document.getElementById('modal-exito');
                    const textContainer = document.getElementById('mensaje-exito-modal');
                    textContainer.innerHTML = mensaje;
                    modal.style.display = 'flex';
                    setTimeout(() => { cerrarModalExito(); }, 5000);
                }

                function cerrarModalExito() {
                    const modal = document.getElementById('modal-exito');
                    if(modal) modal.style.display = 'none';
                }

                function mostrarPopUpError(mensaje) {
                    const modal = document.getElementById('modal-error');
                    const textContainer = document.getElementById('mensaje-error-modal');
                    textContainer.innerHTML = mensaje;
                    modal.style.display = 'flex';
                }

                function cerrarModalError() {
                    const modal = document.getElementById('modal-error');
                    if(modal) modal.style.display = 'none';
                }
            </script>
