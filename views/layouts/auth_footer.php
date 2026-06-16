<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    console.log("Auth Footer cargado. Verificando mensajes...");
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($_SESSION['mensaje_exito'])): ?>
            console.log("Mensaje de éxito detectado: <?php echo strip_tags($_SESSION['mensaje_exito']); ?>");
            Swal.fire({

                title: '¡Marcación Correcta!',
                html: '<?php echo $_SESSION['mensaje_exito']; ?>',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                timer: 5000,
                timerProgressBar: true
            });
            <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['mensaje_error'])): ?>
            Swal.fire({
                title: 'Error',
                text: '<?php echo $_SESSION['mensaje_error']; ?>',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
            <?php unset($_SESSION['mensaje_error']); ?>
        <?php endif; ?>
    });
</script>
</body>
</html>