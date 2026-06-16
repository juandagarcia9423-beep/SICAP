<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 

// Función auxiliar para traducir meses
function nombreMes($fecha) {
    $meses = [
        "01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril",
        "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto",
        "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"
    ];
    $partes = explode('-', $fecha);
    return $meses[$partes[1]] . " " . $partes[0];
}
?>

<style>
    .permisos-container { max-width: 1200px; margin: 0 auto; }
    
    /* Sistema de Tabs */
    .tabs-header { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; }
    .tab-btn { 
        padding: 0.8rem 1.5rem; border-radius: 10px 10px 0 0; border: none; background: transparent; 
        cursor: pointer; font-weight: 700; color: var(--text-muted); transition: all 0.3s;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .tab-btn.active { background: var(--primary-color); color: white; box-shadow: 0 -4px 10px rgba(30, 58, 138, 0.1); }
    .tab-btn:hover:not(.active) { background: #e2e8f0; color: var(--text-main); }

    .tab-content { display: none; animation: fadeIn 0.3s ease-in; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Estilo de Acordeón por Mes */
    .month-group { margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; background: white; }
    .month-header { 
        padding: 1rem 1.5rem; background: #f8fafc; cursor: pointer; display: flex; 
        justify-content: space-between; align-items: center; transition: background 0.2s;
        border-bottom: 1px solid transparent;
    }
    .month-header:hover { background: #f1f5f9; }
    .month-header.active { background: #e2e8f0; border-bottom-color: var(--border-color); }
    .month-title { font-weight: 800; color: var(--text-main); font-size: 1.1rem; display: flex; align-items: center; gap: 0.8rem; }
    .month-badge { background: var(--primary-color); color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; }
    
    .month-content { display: none; padding: 1.5rem; background: #fff; }
    .month-content.show { display: block; }

    /* Estilo de Tarjetas Horizontales */
    .card-permiso {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
        display: flex;
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: transform 0.2s;
    }
    .card-permiso:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
    
    /* Colores por Estado */
    .card-pendiente { background-color: #fffbeb; border-color: #fde68a; }
    .card-aprobada { background-color: #f0fdf4; border-color: #bbf7d0; }
    .card-rechazada { background-color: #fef2f2; border-color: #fecaca; }

    .card-part {
        padding: 1.2rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        border-right: 1px solid rgba(0,0,0,0.05);
        flex: 1;
    }
    .card-part:last-child { border-right: none; }
    
    .card-part-1 { flex: 1.5; background: rgba(0,0,0,0.02); }
    .permiso-no { font-size: 0.7rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.3rem; }
    .permiso-nombre { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 0.2rem; }
    .permiso-sub { font-size: 0.8rem; color: #64748b; margin-bottom: 0.1rem; }

    .label-small { font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 0.3rem; }
    .value-med { font-size: 0.9rem; font-weight: 600; color: #334155; }

    /* Badges de Estado */
    .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; display: inline-block; text-align: center; }
    .status-pendiente { background: #fef9c3; color: #854d0e; }
    .status-aprobada { background: #dcfce7; color: #166534; }
    .status-rechazada { background: #fee2e2; color: #991b1b; }

    .btn-action { 
        padding: 0 16px; 
        height: 36px;
        border-radius: 8px; 
        font-size: 11px; 
        font-weight: 800; 
        cursor: pointer; 
        border: none; 
        transition: 0.2s; 
        width: 100%; 
        color: white !important; 
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-sizing: border-box;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .btn-action:last-child { margin-bottom: 0; }

    .btn-mustard { background-color: #d97706; }
    .btn-mustard:hover { background-color: #b45309; }

    .btn-primary { background-color: #3b82f6; }
    .btn-success { background-color: #10b981; }
    .btn-danger { background-color: #ef4444; }
    .btn-warning { background-color: #f59e0b; }

    /* Paginación */
    .pagination-container { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; padding-bottom: 1rem; }
    .page-link { 
        padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); 
        background: white; color: var(--text-main); text-decoration: none; cursor: pointer;
        font-weight: 600; transition: all 0.2s;
    }
    .page-link.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    .page-link:hover:not(.active) { background: #f1f5f9; }

    /* Modal de Firma */
    .modal-overlay { 
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.6); display: none; justify-content: center; 
        align-items: center; z-index: 1000; backdrop-filter: blur(4px);
    }
    .modal-overlay.show { display: flex; }
    .modal-content { 
        background: white; padding: 2rem; border-radius: 16px; width: 90%; max-width: 500px; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.3); text-align: center;
    }
    .signature-pad { 
        border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; 
        width: 100%; height: 200px; margin: 1.5rem 0; cursor: crosshair; touch-action: none;
    }
    .modal-buttons { display: flex; gap: 1rem; justify-content: center; margin-top: 1rem; }
</style>

<div class="permisos-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin:0; font-size: 1.5rem; color: var(--primary-color); font-weight: 800;">Permisos Laborales</h2>
        <a href="<?php echo URLROOT; ?>/permisos/solicitar" class="btn btn-success" id="btn-nueva-solicitud">
            <i class="fas fa-plus-circle"></i> Nueva Solicitud
        </a>
    </div>

    <!-- Navegación por Tabs -->
    <div class="tabs-header">
        <button class="tab-btn active" data-tab="mis-solicitudes">
            <i class="fas fa-user-edit"></i> Mis Solicitudes
        </button>
        <button class="tab-btn" data-tab="autorizaciones">
            <i class="fas fa-check-double"></i> Autorizaciones
        </button>
    </div>

    <!-- Contenido: Mis Solicitudes -->
    <div id="mis-solicitudes" class="tab-content active">
        <div id="container-mis-solicitudes" class="months-pagination-wrapper"></div>
        <div id="pagination-months-mis" class="pagination-container"></div>
    </div>

    <!-- Contenido: Autorizaciones -->
    <div id="autorizaciones" class="tab-content">
        <div id="container-autorizaciones" class="months-pagination-wrapper"></div>
        <div id="pagination-months-aut" class="pagination-container"></div>
    </div>
</div>

<!-- Modal para Firma de Autorización -->
<div id="modal-firma" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin-bottom: 0.5rem; color: var(--primary-color); font-weight: 800;">Firma de Autorización</h3>
        <p style="font-size: 0.9rem; color: var(--text-muted);">Estampe su firma digital para aprobar la solicitud.</p>
        <canvas id="canvas-autorizacion" class="signature-pad"></canvas>
        <div class="modal-buttons">
            <button id="btn-cancelar-firma" type="button" style="background: #94a3b8; border:none; padding: 0.8rem 1.5rem; border-radius: 8px; color: white; cursor: pointer; font-weight: bold;">Cancelar</button>
            <button id="btn-limpiar-modal" type="button" style="background: #f59e0b; border:none; padding: 0.8rem 1.5rem; border-radius: 8px; color: white; cursor: pointer; font-weight: bold;">Limpiar</button>
            <button id="btn-confirmar-firma" type="button" style="background: #10b981; border:none; padding: 0.8rem 1.5rem; border-radius: 8px; color: white; cursor: pointer; font-weight: bold;">Confirmar</button>
        </div>
    </div>
</div>

<script>
    const URLROOT = "<?php echo URLROOT; ?>";
    const misSolicitudesRaw = <?php echo json_encode($data['mis_solicitudes']) ?: '{}'; ?>;
    const autorizacionesRaw = <?php echo json_encode($data['autorizaciones']) ?: '{}'; ?>;
    const usuarioNombre = "<?php echo $_SESSION['usuario_nombre']; ?>";

    const CONFIG = { monthsPerPage: 12, cardsPerPage: 12 };
    let appState = JSON.parse(localStorage.getItem('sicap_permisos_state')) || {
        activeTab: 'mis-solicitudes', misPage: 1, autPage: 1, expandedMonths: {}, innerPages: {}
    };

    let currentPermisoId = null;
    let hasSigned = false;
    let canvas, ctx, modal, dibujando = false;

    // Helper functions
    function formatMonthTitle(monthKey) {
        if (!monthKey) return '';
        const [y, m] = monthKey.split('-');
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        return `${meses[parseInt(m)-1]} ${y}`;
    }

    function formatDate(dateStr) {
        if(!dateStr) return 'N/A';
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    function formatTime(timeStr) {
        if(!timeStr) return 'N/A';
        const [h, m] = timeStr.split(':');
        let hours = parseInt(h, 10);
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // el 0 debe ser 12
        return `${String(hours).padStart(2, '0')}:${m} ${ampm}`;
    }

    function saveState() {
        localStorage.setItem('sicap_permisos_state', JSON.stringify(appState));
    }

    document.addEventListener('DOMContentLoaded', () => {
        canvas = document.getElementById('canvas-autorizacion');
        ctx = canvas.getContext('2d');
        modal = document.getElementById('modal-firma');

        // Tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.onclick = () => {
                appState.activeTab = btn.dataset.tab;
                renderTabs();
                saveState();
            };
        });

        // Eventos Canvas
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        document.getElementById('btn-limpiar-modal').onclick = limpiarCanvas;
        document.getElementById('btn-cancelar-firma').onclick = () => modal.classList.remove('show');
        document.getElementById('btn-confirmar-firma').onclick = confirmarAprobacion;

        renderTabs();
        renderMonths('mis-solicitudes', misSolicitudesRaw, appState.misPage);
        renderMonths('autorizaciones', autorizacionesRaw, appState.autPage);
    });

    function startDraw(e) {
        dibujando = true;
        ctx.beginPath();
        const pos = getPos(e);
        ctx.moveTo(pos.x, pos.y);
        if (e.type === 'touchstart') e.preventDefault();
    }

    function draw(e) {
        if (!dibujando) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        hasSigned = true;
        if (e.type === 'touchmove') e.preventDefault();
    }

    function stopDraw() { dibujando = false; }

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - rect.left, y: clientY - rect.top };
    }

    function resizeCanvas() {
        if (!modal.classList.contains('show')) return;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        ctx.strokeStyle = "#1e3a8a";
        ctx.lineWidth = 3;
        ctx.lineJoin = "round";
        ctx.lineCap = "round";
        limpiarCanvas();
    }

    function limpiarCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSigned = false;
    }

    // El objeto currentSignatureAction rastreará el tipo de firma
    let currentSignatureAction = null;

    function confirmarAprobacion() {
        if (!hasSigned) { alert("Por favor, estampe su firma."); return; }
        if (!currentPermisoId || !currentSignatureAction) { alert("Error: Contexto no válido."); return; }

        const btn = document.getElementById('btn-confirmar-firma');
        btn.disabled = true;
        btn.innerText = "Guardando...";

        const formData = new FormData();
        formData.append('id', currentPermisoId);
        
        let endpoint = '';
        if (currentSignatureAction === 'aprobar') {
            formData.append('firma_autorizacion', canvas.toDataURL('image/png'));
            endpoint = `${URLROOT}/permisos/aprobar`;
        } else if (currentSignatureAction === 'rechazar') {
            formData.append('firma_rechazo', canvas.toDataURL('image/png'));
            endpoint = `${URLROOT}/permisos/rechazar`;
        } else if (currentSignatureAction === 'regreso_empleado') {
            formData.append('firma_regreso_empleado', canvas.toDataURL('image/png'));
            endpoint = `${URLROOT}/permisos/firmar_regreso`;
        } else if (currentSignatureAction === 'regreso_autorizador') {
            formData.append('firma_regreso_autorizador', canvas.toDataURL('image/png'));
            endpoint = `${URLROOT}/permisos/confirmar_regreso`;
        }

        fetch(endpoint, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else throw new Error(data.message || 'Error en servidor');
        })
        .catch(err => {
            alert("Error: " + err.message);
            btn.disabled = false;
            btn.innerText = "Confirmar";
        });
    }

    window.openSignatureModal = function(id, action = 'aprobar') {
        currentPermisoId = id;
        currentSignatureAction = action;
        hasSigned = false;
        
        const titleEl = document.querySelector('#modal-firma h3');
        const descEl = document.querySelector('#modal-firma p');

        if (action === 'aprobar') {
            titleEl.innerText = "Autorización";
            descEl.innerText = "Estampe su firma digital para aprobar la solicitud.";
        } else if (action === 'rechazar') {
            titleEl.innerText = "Rechazo";
            descEl.innerText = "Estampe su firma digital para rechazar la solicitud.";
        } else if (action === 'regreso_empleado') {
            titleEl.innerText = "Regreso";
            descEl.innerText = "Estampe su firma para confirmar que ha regresado a laborar.";
        } else if (action === 'regreso_autorizador') {
            titleEl.innerText = "Confirmación";
            descEl.innerText = "Estampe su firma para confirmar que el empleado ha regresado.";
        }

        modal.classList.add('show');
        setTimeout(resizeCanvas, 200);
    };

    // rechazarPermiso removed to match aprobar behavior

    // Renderers
    function renderTabs() {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.toggle('active', btn.dataset.tab === appState.activeTab));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.toggle('active', content.id === appState.activeTab));
    }

    function renderMonths(tab, data, page) {
        const container = document.getElementById(`container-${tab}`);
        const pagContainer = document.getElementById(`pagination-months-${tab === 'mis-solicitudes' ? 'mis' : 'aut'}`);
        if (!container) return;
        container.innerHTML = '';
        pagContainer.innerHTML = '';

        const keys = Object.keys(data).sort().reverse();
        if (keys.length === 0) { container.innerHTML = `<div class='card' style='text-align:center; padding: 3rem;'>No hay registros.</div>`; return; }

        const totalPages = Math.ceil(keys.length / CONFIG.monthsPerPage);
        keys.slice((page-1)*CONFIG.monthsPerPage, page*CONFIG.monthsPerPage).forEach(monthKey => {
            const isExpanded = appState.expandedMonths[tab]?.includes(monthKey);
            const group = document.createElement('div');
            group.className = 'month-group';
            group.innerHTML = `
                <div class="month-header ${isExpanded ? 'active' : ''}" onclick="toggleMonth('${tab}', '${monthKey}')">
                    <div class="month-title"><i class="fas ${isExpanded ? 'fa-chevron-down' : 'fa-chevron-right'}"></i> ${formatMonthTitle(monthKey)} <span class="month-badge">${data[monthKey].length}</span></div>
                </div>
                <div class="month-content ${isExpanded ? 'show' : ''}">
                    <div id="cards-${tab}-${monthKey}"></div>
                    <div id="pag-cards-${tab}-${monthKey}" class="pagination-container"></div>
                </div>
            `;
            container.appendChild(group);
            if (isExpanded) renderCards(tab, monthKey, data[monthKey]);
        });

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `page-link ${i === page ? 'active' : ''}`;
            btn.innerText = i;
            btn.onclick = () => { if(tab==='mis-solicitudes') appState.misPage=i; else appState.autPage=i; renderMonths(tab, data, i); saveState(); };
            pagContainer.appendChild(btn);
        }
    }

    window.toggleMonth = (tab, key) => {
        if(!appState.expandedMonths[tab]) appState.expandedMonths[tab]=[];
        const idx = appState.expandedMonths[tab].indexOf(key);
        if(idx > -1) appState.expandedMonths[tab].splice(idx,1); else appState.expandedMonths[tab].push(key);
        saveState();
        renderMonths(tab, tab==='mis-solicitudes'?misSolicitudesRaw:autorizacionesRaw, tab==='mis-solicitudes'?appState.misPage:appState.autPage);
    };

    function renderCards(tab, monthKey, permisos) {
        const container = document.getElementById(`cards-${tab}-${monthKey}`);
        const pagContainer = document.getElementById(`pag-cards-${tab}-${monthKey}`);
        const page = appState.innerPages[`${tab}_${monthKey}`] || 1;
        if(!container) return;
        container.innerHTML = ''; pagContainer.innerHTML = '';

        const totalPages = Math.ceil(permisos.length / CONFIG.cardsPerPage);
        permisos.slice((page-1)*CONFIG.cardsPerPage, page*CONFIG.cardsPerPage).forEach(s => {
            const card = document.createElement('div');
            card.className = `card-permiso card-${s.estado}`;
            card.innerHTML = generateCardHTML(tab, s);
            container.appendChild(card);
        });

        if(totalPages > 1) {
            for(let i=1; i<=totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `page-link ${i===page?'active':''}`;
                btn.innerText = i;
                btn.onclick = () => { appState.innerPages[`${tab}_${monthKey}`]=i; renderCards(tab, monthKey, permisos); saveState(); };
                pagContainer.appendChild(btn);
            }
        }
    }

    function generateCardHTML(tab, s) {
        const nombre = tab === 'mis-solicitudes' ? usuarioNombre : s.empleado_nombre;
        
        let firmasRegresoHTML = '';
        if (s.regresa_laborar == 1 && s.estado === 'aprobada') {
            firmasRegresoHTML = `
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1;">
                    <div style="flex: 1;">
                        <span class="label-small" style="margin-bottom: 5px; display: block;">regreso</span>
                        <div style="border: 1px dashed #cbd5e1; height: 50px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 6px;">
                            ${s.firma_regreso_empleado ? `<img src="${s.firma_regreso_empleado}" style="max-height: 100%; max-width: 100%;">` : '<span style="font-size: 0.6rem; color: #94a3b8;">Pendiente</span>'}
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <span class="label-small" style="margin-bottom: 5px; display: block;">confirmacion</span>
                        <div style="border: 1px dashed #cbd5e1; height: 50px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 6px;">
                            ${s.firma_regreso_autorizador ? `<img src="${s.firma_regreso_autorizador}" style="max-height: 100%; max-width: 100%;">` : '<span style="font-size: 0.6rem; color: #94a3b8;">Pendiente</span>'}
                        </div>
                    </div>
                </div>
            `;
        }

        return `
            <div class="card-part card-part-1">
                <span class="permiso-no">#${String(s.id).padStart(5, '0')}</span>
                <span class="permiso-nombre">${nombre}</span>
                <span class="permiso-sub"><i class="far fa-id-card"></i> ${s.cedula}</span>
                <span class="permiso-sub"><i class="fas fa-briefcase"></i> ${s.area}</span>
                ${s.autorizador_nombre ? `<span class="permiso-sub" style="font-weight: 600; color: #475569; margin-top: 0.3rem;"><i class="fas fa-user-check"></i> ${s.estado === 'rechazada' ? 'Rechazó' : 'Autorizó'}: ${s.autorizador_nombre}</span>` : ''}
                <span class="permiso-sub" style="font-weight: 700; color: ${s.regresa_laborar == 1 ? '#10b981' : '#ef4444'}; margin-top: 0.3rem;">
                    <i class="fas ${s.regresa_laborar == 1 ? 'fa-walking' : 'fa-door-open'}"></i> 
                    ${s.regresa_laborar == 1 ? 'Regresa a laborar' : 'No regresa'}
                </span>
            </div>
            <div class="card-part" style="flex: 1.5;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <div><span class="label-small">Tiempo Solicitado</span><span class="value-med" style="display: block; color: var(--primary-color);">${s.horas_solicitadas} Horas</span></div>
                    <div style="text-align: right;"><span class="label-small">Tiempo Usado</span><span class="value-med" style="display: block; color: #10b981;"><i class="fas fa-stopwatch"></i> 00:00:00</span></div>
                </div>
                <div style="margin-bottom: 0.5rem; text-align: center;"><span class="label-small">Tiempo de Deuda</span><span class="value-med" style="display: block; color: #ef4444;">${s.requiere_reposicion == 1 ? s.horas_solicitadas + ' hrs' : '0 hrs'}</span></div>
                <div style="text-align: center;"><span class="label-small">Programación</span><span class="value-med" style="display: block; font-size: 0.85rem;"><i class="far fa-calendar-alt"></i> ${formatDate(s.fecha_permiso)} <i class="far fa-clock" style="margin-left: 5px;"></i> ${formatTime(s.hora_permiso)}</span></div>
                
                <div style="text-align: center; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e2e8f0;">
                    <span class="label-small">Soporte PDF</span>
                    <div style="margin-top: 0.2rem;">
                        ${s.soporte_nombre ? `
                            <a href="${URLROOT}/${s.soporte_nombre}" target="_blank" class="btn-action btn-primary" style="text-decoration: none; padding: 4px 10px; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 5px; width: auto; margin: 0 auto;">
                                <i class="fas fa-file-pdf"></i> Ver Soporte
                            </a>
                        ` : '<span style="font-size: 0.7rem; color: #94a3b8;">No adjunto</span>'}
                    </div>
                </div>
            </div>
            <div class="card-part" style="flex: 1.2;">
                <div style="margin-bottom: 0.8rem;"><span class="label-small">Motivo</span><span class="value-med" style="display: block;">${s.motivo_nombre}</span></div>
                <div><span class="label-small">Observaciones</span><span class="value-med" style="display: block; font-size: 0.8rem; font-style: italic;">${s.reposicion_observacion || 'Sin observaciones'}</span></div>
            </div>
            <div class="card-part" style="flex: 1.5; text-align: center;">
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <div style="flex: 1;"><span class="label-small">Solicitud</span><div style="border: 1px dashed #cbd5e1; height: 60px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 6px;">${s.firma_digital ? `<img src="${s.firma_digital}" style="max-height: 100%; max-width: 100%;">` : 'Sin Firma'}</div></div>
                    <div style="flex: 1;"><span class="label-small">Autorización</span><div style="border: 1px dashed #cbd5e1; height: 60px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 6px;">${s.firma_autorizacion ? `<img src="${s.firma_autorizacion}" style="max-height: 100%; max-width: 100%;">` : 'Pendiente'}</div></div>
                </div>
                ${firmasRegresoHTML}
                <div style="margin-top: 0.8rem;"><span class="status-badge status-${s.estado}">${s.estado}</span></div>
            </div>
            <div class="card-part" style="min-width: 160px; display: flex; flex-direction: column; gap: 0.4rem;">
                ${String(s.estado).toLowerCase() === 'pendiente' ? `
                    <a href="${URLROOT}/permisos/editar/${s.id}" class="btn-action btn-primary" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                ` : `
                    <button class="btn-action btn-secondary" disabled style="background-color: #e2e8f0; color: #94a3b8 !important;"><i class="fas fa-eye"></i> Detalles</button>
                `}
                
                ${tab === 'mis-solicitudes' && String(s.estado).toLowerCase() === 'pendiente' ? `
                    <button class="btn-action btn-danger" onclick="alert('Funcionalidad próximamente')">
                        <i class="fas fa-trash"></i> Cancelar
                    </button>
                ` : ''}
                
                ${tab === 'mis-solicitudes' && String(s.estado).toLowerCase() === 'aprobada' && Number(s.regresa_laborar) === 1 && !s.firma_regreso_empleado ? `
                    <button class="btn-action btn-mustard" onclick="openSignatureModal(${s.id}, 'regreso_empleado')">
                        <i class="fas fa-pen"></i> Firmar Regreso
                    </button>
                ` : ''}

                ${tab === 'autorizaciones' && String(s.estado).toLowerCase() === 'pendiente' && <?php echo app\Helpers\SesionHelper::tienePermiso('permisos', 'editar') ? 'true' : 'false'; ?> ? `
                    <button type="button" class="btn-action btn-success" onclick="openSignatureModal(${s.id}, 'aprobar')">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                    <button type="button" class="btn-action btn-danger" onclick="openSignatureModal(${s.id}, 'rechazar')">
                        <i class="fas fa-ban"></i> Rechazar
                    </button>
                ` : ''}

                ${tab === 'autorizaciones' && String(s.estado).toLowerCase() === 'aprobada' && Number(s.regresa_laborar) === 1 && s.firma_regreso_empleado && !s.firma_regreso_autorizador && <?php echo app\Helpers\SesionHelper::tienePermiso('permisos', 'editar') ? 'true' : 'false'; ?> ? `
                    <button class="btn-action btn-mustard" onclick="openSignatureModal(${s.id}, 'regreso_autorizador')">
                        <i class="fas fa-check-double"></i> Confirmar Regreso
                    </button>
                ` : ''}
            </div>
        `;
    }

</script>

<?php require_once '../views/layouts/footer.php'; ?>
