function abrirModal(id) {
    const modal = new bootstrap.Modal(document.getElementById(id));
    modal.show();
}

function cerrarModal(id) {
    const modal = bootstrap.Modal.getInstance(document.getElementById(id));
    modal.hide();
}

function cambiarMensajeModal(idModal, title, text, icon, btnClose = true) {
    const modal = $(idModal);
    const modalTitleText = $(idModal + "-header-label");
    const modalTitleIcon = $(idModal + "-header-icon");
    const modalBody = $(idModal + "-body");

    modalTitleText.empty().append(`${title}`);
    modalTitleIcon.removeClass().addClass(`${icon}`);

    if (btnClose) {
        $(idModal + "-close-btn").removeClass("visually-hidden");
    }

    modalBody.empty().append(`
        <div class="text-center py-4">
            <i class="${icon} fs-1"></i>

            <p class="text-muted">
                ${text}
            </p>
        </div>
    `);
    
}

function abrirModalConfirmacion({
    titulo = '¿Estás seguro?',
    texto = 'Esta acción no se puede revertir.',
    icono = 'bi bi-exclamation-triangle-fill',
    callback = null
}) {

    $('#modalConfirmacionTitulo').text(titulo);

    $('#modalConfirmacionTexto').text(texto);

    $('#modalConfirmacionIcon')
        .attr('class', `bi ${icono} fs-1`);

    callbackConfirmacion = callback;

    const modal = new bootstrap.Modal(
        document.getElementById('modalConfirmacion')
    );

    modal.show();
}

function ejecutarConfirmacionModal() {

    if(typeof callbackConfirmacion === 'function') {
        callbackConfirmacion();
    }

    const modalEl = document.getElementById(
        'modalConfirmacion'
    );

    const modal = bootstrap.Modal.getInstance(modalEl);

    modal.hide();
}