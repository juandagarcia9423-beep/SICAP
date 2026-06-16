<?php require_once '../views/layouts/auth_header.php'; ?>
<div class="auth-card">
    <h2>Marcación de Entrada y Salida por PIN</h2>
    <p>Ingrese su cédula y su PIN de 4 dígitos para marcar:</p>
    
    <form action="<?php echo URLROOT; ?>/auth/validar_pin" method="POST" id="pinForm">
        <div class="form-group" style="text-align: left; margin-bottom: 1rem;">
            <label>Cédula</label>
            <input type="text" name="cedula" required class="form-control" placeholder="Ingrese su cédula" autofocus>
        </div>
        <div class="form-group" style="text-align: left; margin-bottom: 1.5rem;">
            <label>PIN Secreto</label>
            <input type="password" name="pin" id="pinInput" maxlength="4" required class="form-control" placeholder="****" oninput="checkPinLength(this)">
        </div>
        <!-- Botón oculto para envío automático -->
        <button type="submit" id="btnSubmit" style="display:none;"></button>
    </form>
    <div style="margin-top: 1.5rem; border-top: 1px solid #ddd; padding-top: 1rem; text-align: center;">
        <a href="<?php echo URLROOT; ?>/auth/metodos" class="btn" style="background-color: #f59e0b; color: white; padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 4px; text-decoration: none; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Cambiar Método
        </a>
    </div>
</div>

<script>
    function checkPinLength(input) {
        if (input.value.length === 4) {
            document.getElementById('btnSubmit').click();
        }
    }
</script>
<?php require_once '../views/layouts/auth_footer.php'; ?>