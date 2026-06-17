function guardarComentario() {
    const idCliente = $('#Sesion').val();
    const idProducto = $('#Id').val();
    const mensaje = $('#Comentario').val();
    const estrellas = $('#Calificacion').val();

    if (!validarCampos(
        [idCliente, idProducto, mensaje],
        ['tu identificador único', 'el identificador del producto', 'por lo menos un caractér o letra en el comentario']
    )) {
        return;
    }
    
    guardarDatos();

    function guardarDatos() {
        abrirModal('modalGuardando');
        cambiarMensajeModal("#modalGuardando", "Guardando...", 'Espere un momento...', "bi bi-wifi", false);

        const accion = 'insertar';
        const data = {
            accion: accion,
            idCliente: idCliente,
            idProducto: idProducto,
            mensaje: mensaje,
            estrellas: estrellas
        };

        $.ajax({
            url: backend + urlComentary,
            type: 'POST',
            data: data,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);
            },
            error: function() {
                cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);
            }
        });
    }
}

function mostrarComentarios(comentarios) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    comentarios = ordenar(comentarios, order);

    if (!Array.isArray(comentarios) || comentarios.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron comentarios.</td></tr>');
        return;
    }

    comentarios.forEach((comentario, index) => {
        const json = encodeURIComponent(JSON.stringify(comentario));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${comentario.producto}</td>
                <td class="align-middle">${comentario.mensaje}</td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="verDetallesComentario('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function aplicarFiltrosComentario() {
    const producto = $('#Producto').val();
    seleccionarComentarios(producto);
}

let tokenCargaComentarios = 0;

async function seleccionarComentarios(producto = '') {
    const token = ++tokenCargaComentarios;
    const container = $('#list-container');

    container.empty();

    const orden = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try {
        const ids = await $.ajax({
            url: backend + urlComentary,
            type: 'POST',
            dataType: 'json',
            data: {
                accion: 'listarIds',
                nombre: producto,
                orden
            }
        });

        if (token !== tokenCargaComentarios) {
            return;
        }

        mostrarTotalRegistros(ids.length, [
            'comentario',
            'comentarios'
        ]);

        if (!ids.length) {
            container.html(`
                <div class="orders-empty">
                    No se encontraron comentarios
                </div>
            `);

            return;
        }

        await cargarComentarios(ids, token);

    } catch (e) {
        console.error(e);
    }
}

async function cargarComentarios(ids, token) {
    for (const id of ids) {
        renderComentarioSkeleton(id);

        try {
            const comentario = await $.ajax({
                url: backend + urlComentary,
                type: 'POST',
                dataType: 'json',
                data: {
                    accion: 'buscarPorId',
                    id
                }
            });

            if (token !== tokenCargaComentarios) {
                return;
            }

            $(`#comentario-skeleton-${id}`).replaceWith(
                renderComentarioCard(comentario, true)
            );

            buscarImagenProducto(comentario.idProducto, comentario.id);

        } catch (error) {
            console.error(error);
        }
    }
}

function renderComentarioCard(comentario, returnHtml = false) {
    const json = encodeURIComponent(
        JSON.stringify(comentario)
    );

    function renderEstrellas(
        estrellas = 0,
        maximo = 5
    ){
        estrellas = Number(estrellas) || 0;
        let html = '';
        for(let i = 1; i <= maximo; i++){
            html += `
                <i class="
                    bi
                    ${
                        i <= estrellas
                        ? 'bi-star-fill'
                        : 'bi-star'
                    }
                "></i>
            `;
        }
        return html;
    }

    const html = `
        <div class="product-admin-card">
            <div class="product-admin-header">
                <div>
                    <p class="product-number">
                        ${formatearFechaConHora(comentario.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${comentario.producto}
                    </h5>
                </div>
            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <img
                        id="img-${comentario.idProducto}-${comentario.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${comentario.id}"
                    >

                </div>

                <div class="product-info">
                    <div class="product-info-grid">

                        <div>
                            <span>Cliente:</span>
                            <strong>${comentario.cliente + ' ' + (comentario.segundo_nombre || '') + comentario.primer_apellido + ' ' + (comentario.segundo_apellido || '')}</strong>
                        </div>

                        <div>
                            <span>Comentario:</span>
                            <strong>${comentario.mensaje}</strong>
                        </div>

                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span>Estrellas:</span>
                            <strong
                                class="
                                    d-flex
                                    align-items-center
                                    gap-1
                                    flex-wrap
                                "
                            >
                                ${renderEstrellas(
                                    comentario.estrellas
                                )}
                                <span class="ms-1">
                                    (${comentario.estrellas}/5)
                                </span>
                            </strong>
                        </div>

                    </div>
                </div>

                <div class="order-actions"></div>
            </div>
        </div>
    `;

    return returnHtml
        ? html
        : $('#list-container').append(html);
}

function renderComentarioSkeleton(id) {
    $('#list-container').append(`
        <div
            id="comentario-skeleton-${id}"
            class="product-admin-card product-skeleton"
        >
            <div class="product-admin-header">
                <div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line skeleton-title"></div>
                </div>
            </div>

            <div class="product-admin-body">
                <div class="product-info">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
            </div>
        </div>
    `);
}

function seleccionarComentariosPorIdProducto(idProducto) {
    const offset = (currentPage - 1) * itemsPerPage;
    toggleLoadingIcon('container-comentaries', true, 4, 28);

    $.ajax({
        url: backend + urlComentary,
        type: 'POST',
        data: {
            accion: 'seleccionarPorIdProducto',
            idProducto: idProducto,
            limit: itemsPerPage,
            offset: offset
        },
        success: function(response) {
            try {
                const comentarios = response.datos;
                const total = response.total;
                mostrarTotalRegistros(response.total);
                mostrarComentariosEnProducto(comentarios);
                actualizarPaginacionComentarioPorIdProducto(total);
            } catch (error) {
                toggleLoadingIcon('container-comentaries', false, 4, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            toggleLoadingIcon('container-comentaries', false, 4, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function limpiarFiltrosComentario() {
    $('#Producto').val('');
}