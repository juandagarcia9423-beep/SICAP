<?php require_once '../views/layouts/header.php'; ?>

<div class="content-body">
    <div class="card" style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin:0;"><i class="fas fa-university" style="color: var(--primary-color);"></i> <?php echo $data['titulo']; ?></h2>
            
            <form action="<?php echo URLROOT; ?>/bancohoras/index" method="GET" style="display: flex; gap: 1rem; align-items: center; background: #f1f5f9; padding: 0.75rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                <label style="font-size: 0.85rem; font-weight: 700; color: #475569;">Filtrar por Empleado:</label>
                <select name="usuario_id" style="padding: 0.5rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem; min-width: 250px;">
                    <option value="">-- Todos los empleados --</option>
                    <?php foreach($data['usuarios'] as $u): ?>
                        <option value="<?php echo $u->id; ?>" <?php echo ($data['usuario_id'] == $u->id) ? 'selected' : ''; ?>>
                            <?php echo $u->nombre; ?> (<?php echo $u->cedula; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <?php if ($data['usuario_id']): ?>
                    <a href="<?php echo URLROOT; ?>/bancohoras/index" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($data['usuario_id'] && !empty($data['movimientos'])): ?>
            <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem;">
                <div style="background: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary-color); border: 1px solid #e2e8f0;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;"><?php echo $data['movimientos'][0]->empleado_nombre; ?></h3>
                    <p style="margin: 0.2rem 0 0; color: #64748b; font-size: 0.85rem;">Saldo Actual de Tiempo</p>
                </div>
                <div style="margin-left: auto; text-align: right;">
                    <?php 
                        // El saldo lo obtenemos de la tabla usuarios, pero para esta vista rápida
                        // sumaremos los movimientos cargados (si es que cargamos todos).
                        // Sin embargo, es mejor mostrar el saldo real desde el modelo.
                        $usuarioModel = new \app\Models\Usuario();
                        $saldoReal = $usuarioModel->obtenerSaldo($data['usuario_id']);
                        $saldoAbs = abs($saldoReal);
                        $h = floor($saldoAbs);
                        $m = round(($saldoAbs - $h) * 60);
                        $colorSaldo = $saldoReal >= 0 ? '#15803d' : '#991b1b';
                    ?>
                    <div style="font-size: 1.8rem; font-weight: 800; color: <?php echo $colorSaldo; ?>;">
                        <?php echo ($saldoReal < 0 ? '-' : '') . $h . 'h ' . str_pad($m, 2, '0', STR_PAD_LEFT) . 'm'; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <style>
            .banco-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
            .banco-table th { background: #f8fafc; padding: 1rem; text-align: left; border-bottom: 2px solid #e2e8f0; }
            .banco-table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; }
            .badge-credito { background: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }
            .badge-debito { background: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }
            
            /* Paginación */
            .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; }
            .pagination a, .pagination span { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; text-decoration: none; color: #475569; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
            .pagination a:hover { background: #f1f5f9; border-color: #cbd5e1; }
            .pagination .active { background: var(--primary-color); color: white; border-color: var(--primary-color); }
            .pagination .disabled { opacity: 0.5; cursor: not-allowed; background: #f8fafc; }
        </style>

        <table class="banco-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Empleado</th>
                    <th>Tipo</th>
                    <th>Horas</th>
                    <th>Concepto</th>
                    <th>Autorizado Por</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['movimientos'])): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">No hay movimientos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data['movimientos'] as $m): ?>
                        <tr>
                            <td><?php echo date('d/m/Y h:i A', strtotime($m->fecha_movimiento)); ?></td>
                            <td><strong><?php echo $m->empleado_nombre; ?></strong></td>
                            <td>
                                <span class="<?php echo $m->tipo == 'credito' ? 'badge-credito' : 'badge-debito'; ?>">
                                    <?php echo strtoupper($m->tipo); ?>
                                </span>
                            </td>
                            <td style="font-weight: bold; color: <?php echo $m->tipo == 'credito' ? '#166534' : '#991b1b'; ?>">
                                <?php 
                                    $horasEnteras = floor($m->horas);
                                    $minutos = round(($m->horas - $horasEnteras) * 60);
                                    echo ($m->tipo == 'credito' ? '+' : '-') . $horasEnteras . 'h ' . str_pad($minutos, 2, '0', STR_PAD_LEFT) . 'm'; 
                                ?>
                            </td>
                            <td><?php echo $m->concepto; ?></td>
                            <td><?php echo $m->autorizador_nombre ?? 'Sistema'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Controles de Paginación -->
        <?php if ($data['totalPages'] > 1): ?>
            <div class="pagination">
                <?php 
                    $params = $_GET;
                    // Mostrar páginas del 1 al 5 (o hasta el total si es menor a 5)
                    $max_visible = min(5, $data['totalPages']);
                    for($i = 1; $i <= $max_visible; $i++): 
                        $params['page'] = $i;
                        $url = URLROOT . '/bancohoras/index?' . http_build_query($params);
                ?>
                    <a href="<?php echo $url; ?>" class="<?php echo ($data['currentPage'] == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($data['totalPages'] > 5): ?>
                    <?php if ($data['currentPage'] < $data['totalPages']): 
                        $params['page'] = $data['currentPage'] + 1;
                        // Si la página actual es menor a 5, el siguiente debe ser 6 si existe
                        if($data['currentPage'] < 5) $params['page'] = 6;
                        $url = URLROOT . '/bancohoras/index?' . http_build_query($params);
                    ?>
                        <a href="<?php echo $url; ?>">Siguiente <i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <span class="disabled">Siguiente <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>
