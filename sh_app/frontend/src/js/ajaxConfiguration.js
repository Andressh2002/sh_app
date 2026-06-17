function cambiarModoManteniento() {

    const estadoActual = $('#estado-mantenimiento').text() === "Activado";
    const nuevoValor = estadoActual ? "0" : "1";

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", 'Cambiando...', 'Espere un momento...', 'bi bi-wifi', false);

    $.ajax({
        url: backend + urlConfiguration,
        type: 'POST',
        data: {
            accion: "cambiarModoManteniento",
            clave: "maintenance_mode",
            valor: nuevoValor
        },
        success: function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;

            cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);

            buscarConfiguracionModoMantenimiento();
        }
    });
}

function buscarConfiguracionModoMantenimiento() {
    $.ajax({
        url: backend + urlConfiguration,
        type: 'POST',
        data: {
            accion: 'buscarConfiguracionModoMantenimiento',
            clave: 'maintenance_mode'
        },
        success: function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;

            const estado = data?.valor === "1";

            $('#estado-mantenimiento')
                .text(estado ? "Activado" : "Desactivado")
                .removeClass('text-danger text-success')
                .addClass(estado ? 'text-success' : 'text-danger');
        }
    });
}
