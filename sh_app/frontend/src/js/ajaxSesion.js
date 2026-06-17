function iniciarSesion() {
    const nombreUsuario = $('#nombreUsuario').val();
    const contrasennia = $('#Contrasennia').val();

    abrirModal('modalLogueando');
    cambiarMensajeModal("#modalLogueando", "Iniciando sesión", 'Espere, estamos tratando de iniciar sesión...', "bi bi-wifi", false);

    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'login',
            nombreUsuario: nombreUsuario,
            contrasennia: contrasennia
        },
        success: function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            if (data.success) {
                window.location.href = 'home.php';
            } else {
                cambiarMensajeModal("#modalLogueando", "¡Error!", 'Nombre de usuario o contraseña incorrectos.', "bi bi-x-circle", true);
            }
        },
        error: function() {
            cambiarMensajeModal("#modalLogueando", "¡Error!", 'Ocurrió un problema al intentar iniciar sesión.', "bi bi-x-circle", true);
        }
    });
}