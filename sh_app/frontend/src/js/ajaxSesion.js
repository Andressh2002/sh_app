function iniciarSesion() {
    const nombreUsuario = $('#nombreUsuario').val();
    const contrasennia = $('#Contrasennia').val();

    alertLoadingBlocked(
        'Tratando de iniciar sesión',
        'Por favor espere un momento...',
        'warning',
    );

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
                alert('Error', 'Nombre de usuario o contraseña incorrectos.', 'error', 'Aceptar');
            }
        },
        error: function() {
            alert('Error', 'Ocurrió un problema al intentar iniciar sesión.', 'error', 'Aceptar');
        }
    });
}