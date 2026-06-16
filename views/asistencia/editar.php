<?php 
require_once '../views/layouts/header.php'; 
?>
<div class="content-body">
    <div class="card" style="padding: 2rem;">
        <h2><?php echo $data['titulo']; ?></h2>
        
        <form action="<?php echo URLROOT; ?>/asistencia/actualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $data['marcacion']->id; ?>">
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Tipo de Marcación</label>
                <select name="tipo" class="form-control">
                    <option value="entrada" <?php echo ($data['marcacion']->tipo == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                    <option value="salida" <?php echo ($data['marcacion']->tipo == 'salida') ? 'selected' : ''; ?>>Salida</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Fecha y Hora (Formato: YYYY-MM-DD HH:MM:SS)</label>
                <input type="text" name="registrado_en" class="form-control" value="<?php echo $data['marcacion']->registrado_en; ?>" required>
            </div>
            
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <a href="<?php echo URLROOT; ?>/asistencia" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php require_once '../views/layouts/footer.php'; ?>
