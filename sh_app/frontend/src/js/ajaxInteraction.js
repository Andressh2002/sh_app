function guardarInteraccion(datos) {
    const url = window.location.href;
    const accion = 'guardar';
    const data = {
        accion, 
        idCliente: datos.usuario,
        accionInteraccion: datos.accion,
        url,
    };

    $.ajax({
        url: backend + urlInteraction,
        type: 'POST',
        data: data,
    });
}

function buscarInteraccion(id) {
    $.ajax({
        url: backend + urlInteraction,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const interaccion = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarInteraccion(interaccion);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function aplicarFiltrosInteraccion() {
    const nombre = $('#Nombre').val();
    seleccionarInteracciones(nombre);
}

let tokenCargaInteracciones = 0;

async function seleccionarInteracciones(accion = '') {
    const token = ++tokenCargaInteracciones;
    const container = $('#list-container');

    container.empty();

    const orden = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try {
        const ids = await $.ajax({
            url: backend + urlInteraction,
            type: 'POST',
            dataType: 'json',
            data: {
                accion: 'listarIds',
                accionInteraccion: accion,
                orden: orden,
                limite: $('#Limite').val(),
            }
        });

        if (token !== tokenCargaInteracciones) {
            return;
        }

        mostrarTotalRegistros(ids.length, [
            'interacción',
            'interacciones'
        ], true);

        if (ids.length === 0) {
            container.html(`
                <div class="orders-empty">
                    No se encontraron interacciones.
                </div>
            `);

            return;
        }

        await cargarInteracciones(ids, token);

    } catch (error) {
        console.error(error);
    }
}

async function cargarInteracciones(ids, token) {
    for (const id of ids) {
        renderInteraccionSkeleton(id);

        try {
            const data = await $.ajax({
                url: backend + urlInteraction,
                type: 'POST',
                dataType: 'json',
                data: {
                    accion: 'obtener',
                    id
                }
            });

            if (token !== tokenCargaInteracciones) {
                return;
            }

            $(`#interaction-${id}`)
                .replaceWith(
                    renderInteraccionCard(data, true)
                );

        } catch (error) {
            console.error(error);
        }
    }
}

function renderInteraccionCard(item, returnHtml = false) {
    const html = `
        <div class="product-admin-card">
            <div class="product-admin-header">
                <div>
                    <p class="product-number">
                        ${formatearFechaConHora(item.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${item.accion}
                    </h5>
                </div>
            </div>

            <div class="product-admin-body">
                <div class="product-admin-image">
                    <img
                        id="img-${item.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${item.id}"
                    >
                </div>
                <div class="product-info">
                    <div class="product-info-grid">
                        <div>
                            <span>URL:</span>
                            <strong>
                                ${item.url}
                            </strong>
                        </div>
                        <div>
                            <span>Usuario:</span>
                            <strong>
                                ${item.cliente ?? 'Invitado'}
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="order-actions">
                </div>
            </div>
        </div>
    `;

    return returnHtml
        ? html
        : $('#list-container').append(html);
}

function renderInteraccionSkeleton(id) {
    $('#list-container').append(`
        <div
            id="interaction-${id}"
            class="product-admin-card product-skeleton"
        >
            <div class="product-admin-body">
                <div class="product-info">
                    <div class="product-info-grid">
                        <div>
                            <div class="skeleton-line"></div>

                            <div class="skeleton-line skeleton-small"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);
}

function limpiarFiltrosInteraccion() {
    $('#Accion').val('');
}

