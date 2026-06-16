<?php 
/** @var array $data */
require_once '../views/layouts/header.php'; 
$s = $data['solicitud'];
?>

<style>
    .solicitud-card { max-width: 1000px; margin: 0 auto; background-color: var(--form-bg) !important; }
    .form-section { background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; border: 1px solid var(--border-color); }
    .section-title-sm { font-size: 0.8rem; font-weight: 800; color: var(--primary-color); text-transform: uppercase; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    
    .grid-solicitud { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    .input-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem; }
    .input-group input, .input-group select, .input-group textarea { width: 100%; padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; }
    
    /* Firma Digital Canvas */
    .signature-pad { border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; cursor: crosshair; touch-action: none; width: 100%; height: 150px; }
    .signature-controls { display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; }

    /* Lógica condicional */
    #seccion-reposicion { display: <?php echo $s->requiere_reposicion == 1 ? 'block' : 'none'; ?>; }
</style>

<div class="card solicitud-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="margin:0; font-size: 1.3rem; color: var(--primary-color);"><i class="fas fa-edit"></i> Editar Solicitud de Permiso</h2>
        <a href="<?php echo URLROOT; ?>/permisos" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <form action="<?php echo URLROOT; ?>/permisos/editar/<?php echo $s->id; ?>" method="POST" id="form-permiso" enctype="multipart/form-data">
        
        <!-- Bloque 1: Datos del Permiso -->
        <div class="form-section">
            <div class="section-title-sm"><i class="fas fa-info-circle"></i> Detalles del Permiso</div>
            <div class="grid-solicitud">
                <div class="input-group">
                    <label>Fecha del Permiso *</label>
                    <input type="date" name="fecha_permiso" required value="<?php echo $s->fecha_permiso; ?>">
                </div>
                <div class="input-group">
                    <label>Hora Inicio *</label>
                    <input type="time" name="hora_permiso" required value="<?php echo $s->hora_permiso; ?>">
                </div>
                <div class="input-group">
                    <label>Tiempo Solicitado *</label>
                    <div style="display: flex; gap: 10px;">
                        <?php 
                            $hsEnt = floor($s->horas_solicitadas);
                            $msEnt = round(($s->horas_solicitadas - $hsEnt) * 60);
                        ?>
                        <input type="number" name="horas" step="1" min="0" value="<?php echo $hsEnt; ?>" placeholder="Horas" style="flex: 1;">
                        <input type="number" name="minutos" step="1" min="0" max="59" value="<?php echo $msEnt; ?>" placeholder="Minutos" style="flex: 1;">
                    </div>
                </div>
                <div class="input-group" id="container-forma-pago" style="display: <?php echo $data['motive_config']->permite_forma_pago ? 'block' : 'none'; ?>;">
                    <label>Forma de Pago del Tiempo *</label>
                    <select name="forma_pago" id="select-forma-pago">
                        <option value="banco_horas" <?php echo $s->forma_pago == 'banco_horas' ? 'selected' : ''; ?>>Usar Banco de Horas (Saldo a favor)</option>
                        <option value="reposicion" <?php echo $s->forma_pago == 'reposicion' ? 'selected' : ''; ?>>Reposición Posterior (Deuda)</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Motivo del Permiso *</label>
                    <select name="motivo_id" id="select-motivo" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach($data['motivos'] as $m): ?>
                            <option value="<?php echo $m->id; ?>" 
                                    data-repone="<?php echo $m->repone_tiempo; ?>" 
                                    data-pago="<?php echo isset($m->permite_forma_pago) ? $m->permite_forma_pago : 0; ?>"
                                    <?php echo $m->id == $s->motivo_id ? 'selected' : ''; ?>>
                                <?php echo $m->nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Adjuntar Soporte (PDF)</label>
                    <input type="file" name="soporte" accept=".pdf">
                    <?php if($s->soporte_nombre): ?>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 5px;">Archivo actual: <?php echo basename($s->soporte_nombre); ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-group" style="display: flex; align-items: center; gap: 0.5rem; padding-top: 1.5rem;">
                    <input type="checkbox" name="regresa_laborar" value="1" <?php echo $s->regresa_laborar == 1 ? 'checked' : ''; ?> style="width: auto;">
                    <label style="margin:0;">¿Regresa a laborar?</label>
                </div>
            </div>
        </div>

        <!-- Bloque 2: Reposición de Tiempo (Condicional) -->
        <input type="hidden" name="requiere_reposicion" id="input-requiere-reposicion" value="<?php echo $s->requiere_reposicion; ?>">
        <div class="form-section" id="seccion-reposicion">
            <div class="section-title-sm" style="color: #b45309;"><i class="fas fa-hourglass-half"></i> Plan de Reposición de Tiempo</div>
            <div class="grid-solicitud">
                <div class="input-group">
                    <label>Fecha de Reposición</label>
                    <input type="date" name="reposicion_fecha" id="rep-fecha" value="<?php echo $s->reposicion_fecha; ?>">
                </div>
                <div class="input-group">
                    <label>Hora de Reposición</label>
                    <input type="time" name="reposicion_hora" id="rep-hora" value="<?php echo $s->reposicion_hora; ?>">
                </div>
                <div class="input-group">
                    <label>Observaciones</label>
                    <textarea name="reposicion_observacion" rows="1" placeholder="Detalles de la reposición..."><?php echo $s->reposicion_observacion; ?></textarea>
                </div>
            </div>
        </div>

        <!-- Bloque 3: Firma Digital -->
        <div class="form-section">
            <div class="section-title-sm"><i class="fas fa-pen-nib"></i> Firma Digital del Empleado</div>
            <canvas id="canvas-firma" class="signature-pad"></canvas>
            <div class="signature-controls">
                <span style="font-size: 0.75rem; color: var(--text-muted);">Estampe una nueva firma si desea cambiarla, de lo contrario se mantendrá la actual.</span>
                <button type="button" id="btn-limpiar-firma" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.7rem;">Limpiar Firma</button>
            </div>
            <input type="hidden" name="firma_digital" id="input-firma" value="<?php echo $s->firma_digital; ?>">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-success" style="padding: 0.8rem 2.5rem;">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectMotivo = document.getElementById('select-motivo');
        const seccionReposicion = document.getElementById('seccion-reposicion');
        const inputRequiereRep = document.getElementById('input-requiere-reposicion');
        const repFecha = document.getElementById('rep-fecha');
        const repHora = document.getElementById('rep-hora');

        // Lógica condicional de Reposición
        selectMotivo.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const repone = option.getAttribute('data-repone') == "1";
            const permitePago = option.getAttribute('data-pago') == "1";
            
            if (repone) {
                seccionReposicion.style.display = 'block';
                inputRequiereRep.value = "1";
                repFecha.required = true;
                repHora.required = true;
            } else {
                seccionReposicion.style.display = 'none';
                inputRequiereRep.value = "0";
                repFecha.required = false;
                repHora.required = false;
            }

            // Lógica para Forma de Pago
            const containerPago = document.getElementById('container-forma-pago');
            const selectPago = document.getElementById('select-forma-pago');
            if (permitePago) {
                containerPago.style.display = 'block';
                selectPago.required = true;
            } else {
                containerPago.style.display = 'none';
                selectPago.required = false;
            }
        });

        // Lógica de Firma Digital (Canvas)
        const canvas = document.getElementById('canvas-firma');
        const ctx = canvas.getContext('2d');
        const inputFirma = document.getElementById('input-firma');
        const btnLimpiar = document.getElementById('btn-limpiar-firma');
        let dibujando = false;
        let firmaModificada = false;

        // Ajustar resolución del canvas
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;

        // Cargar firma actual si existe
        const img = new Image();
        img.onload = function() {
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };
        img.src = inputFirma.value;

        ctx.strokeStyle = "#1e3a8a";
        ctx.lineWidth = 2;
        ctx.lineJoin = "round";
        ctx.lineCap = "round";

        function empezarDibujo(e) {
            if (!firmaModificada) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                firmaModificada = true;
            }
            dibujando = true;
            ctx.beginPath();
            ctx.moveTo(obtenerPosicion(e).x, obtenerPosicion(e).y);
        }

        function dibujar(e) {
            if (!dibujando) return;
            e.preventDefault();
            const pos = obtenerPosicion(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function detenerDibujo() {
            if (dibujando) {
                dibujando = false;
                inputFirma.value = canvas.toDataURL(); // Guardar en el input oculto
            }
        }

        function obtenerPosicion(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        canvas.addEventListener('mousedown', empezarDibujo);
        canvas.addEventListener('mousemove', dibujar);
        canvas.addEventListener('mouseup', detenerDibujo);
        canvas.addEventListener('touchstart', empezarDibujo);
        canvas.addEventListener('touchmove', dibujar);
        canvas.addEventListener('touchend', detenerDibujo);

        btnLimpiar.addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            inputFirma.value = "";
            firmaModificada = true;
        });

        // Validar firma antes de enviar
        document.getElementById('form-permiso').addEventListener('submit', function(e) {
            if (!inputFirma.value) {
                alert("Por favor, estampe su firma digital antes de enviar.");
                e.preventDefault();
            }
        });
    });
</script>

<?php require_once '../views/layouts/footer.php'; ?>
