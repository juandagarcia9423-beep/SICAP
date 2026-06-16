<?php 
require_once '../views/layouts/header.php'; 
// Definimos tipos, colores, iconos y filtros
$tipos = [
    'Todas' => ['color' => '#0f172a', 'filtro' => '', 'icon' => 'fa-bell'],
    'Llegada Tarde' => ['color' => '#d97706', 'filtro' => 'Llegada Tarde', 'icon' => 'fa-user-clock'],
    'Salida Anticipada' => ['color' => '#8b5cf6', 'filtro' => 'Salida Anticipada', 'icon' => 'fa-sign-out-alt'],
    'Tardanza en Salir' => ['color' => '#c2410c', 'filtro' => 'Tardanza en Salir', 'icon' => 'fa-stopwatch'],
    'Inasistencia' => ['color' => '#991b1b', 'filtro' => 'Inasistencia', 'icon' => 'fa-user-times'],
    'Permiso Laboral' => ['color' => '#1e40af', 'filtro' => 'Permiso Laboral', 'icon' => 'fa-file-alt']
];
$contadores = [];
foreach($data['contadores'] as $c) $contadores[$c->tipo_alerta] = $c->total;
$totalAlertas = array_sum($contadores);
?>
<div class="content-body">
    <div class="card" style="padding: 2rem;">
        <h2><?php echo $data['titulo']; ?></h2>

        <!-- Tarjetas de Resumen -->
        <div class="module-grid" style="grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
            <?php foreach($tipos as $nombre => $config): 
                $count = ($nombre == 'Todas') ? $totalAlertas : ($contadores[$nombre] ?? 0);
            ?>
            <a href="<?php echo URLROOT; ?>/alertas/detalle?tipo=<?php echo urlencode($config['filtro']); ?>" 
               class="module-card" style="background: <?php echo $config['color']; ?>; padding: 1rem; justify-content: center; height: 110px; text-decoration:none; color: white;">
                <i class="fas <?php echo $config['icon']; ?>" style="font-size: 1.5rem; margin-bottom: 0.3rem; opacity: 0.8;"></i>
                <span style="font-size: 0.85rem;"><?php echo $nombre; ?></span>
                <div style="font-size: 1.2rem; font-weight: bold;"><?php echo $count; ?></div>
            </a>
            <?php endforeach; ?>
        </div>
</div>
<?php require_once '../views/layouts/footer.php'; ?>
