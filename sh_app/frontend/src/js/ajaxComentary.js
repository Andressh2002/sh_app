function guardarComentario() {
    const idCliente = $('#Sesion').val();
    const idProducto = $('#idProducto').val();
    const mensaje = $('#Comentario').val();

    if (!validarCampos(
        [idCliente, idProducto, mensaje],
        ['tu identificador único', 'el identificador del producto', 'por lo menos un caractér o letra en el comentario']
    )) {
        return;
    }
    
    guardarDatos();

    function guardarDatos() {
        const accion = 'insertar';
        const data = {
            accion: accion,
            idCliente: idCliente,
            idProducto: idProducto,
            mensaje: mensaje
        };

        $.ajax({
            url: backend + urlComentary,
            type: 'POST',
            data: data,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al guardar el comentario',
                    'error',
                    'Aceptar'
                );
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

function verDetallesComentario(json) {
    const comentario = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles del comentario',
        comentario,
        ['producto', 'mensaje', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarComentarios(producto) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();
    const colspan = 4;
    container.append(`
        <tr><td class="text-center align-middle" colspan="${colspan}">
            <div class="spinner-border spinner-color" role="status" style="width: 24px; height: 24px;"></div>
        </td></tr>
    `);

    cancelarCargaSecuencial = true;

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
        solicitudAjaxActiva = null;
    }

    cancelarCargaSecuencial = false;

    solicitudAjaxActiva = $.ajax({
        url: backend + urlComentary,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: producto,
            orden: order,
        },
        success: function (response) {
            try {
                const comentarios = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (comentarios.length > 0) {
                    procesarComentariosSecuencialmente(comentarios, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron comentarios.</td></tr>`);
                }
                
            } catch (error) {
                container.empty();
                container.append(`<tr><td class="text-center" colspan="${colspan}">A ocurrido un error al cargar la lista.</td></tr>`);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function (xhr, status) {
            if (status !== 'abort') { // Ignoramos errores si fue por abortar
                container.empty();
                container.append(`<tr><td class="text-center" colspan="${colspan}">Ha ocurrido un error al tratar de conseguir la información.</td></tr>`);
                console.error('Error al procesar la solicitud.');
            } else {
                console.log('Solicitud anterior cancelada.');
            }
        }
    });
}

function procesarComentariosSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const comentario = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="producto-${comentario.id}"></td>
                <td class="align-middle" id="comentario-${comentario.id}"></td>
                <td class="align-middle text-center" id="opciones-${comentario.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este comentario no se pudo cargar.</td></tr>`);
    }

    cargarComentarioSeleccionado(comentario.id, function () {
        procesarComentariosSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarComentarioSeleccionado(id, callback) {
    const tdProducto = $(`#producto-${id}`);
    const tdComentario = $(`#comentario-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlComentary,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const comentario = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(comentario));
                
                tdProducto.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${comentario.producto || 'Sin producto'}</li>
                    </ul>
                `);
                tdComentario.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${comentario.mensaje || 'Sin mensaje'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="verDetallesComentario('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }

            if (typeof callback === 'function') callback();
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            if (typeof callback === 'function') callback();
        }
    });
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

function actualizarPaginacionComentario(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaComentario(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaComentario(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaComentario(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaComentario(pagina) {
    currentPage = pagina;
    seleccionarComentarios('');
}

function limpiarFiltrosComentario() {
    $('#Producto').val('');
}

function actualizarPaginacionComentarioPorIdProducto(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaComentarioPorIdProducto(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaComentarioPorIdProducto(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaComentarioPorIdProducto(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaComentarioPorIdProducto(pagina) {
    currentPage = pagina;
    seleccionarComentarios('');
}