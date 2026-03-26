function guardarAviso() {
    const id = document.getElementById('Id').value || null;
    const titulo = $('#Titulo').val();
    const mensaje = $('#Mensaje').val();
    const imagen = $('#hiddenImagenAviso').val();

    if (!validarCampos(
        [titulo, mensaje],
        ['el título', 'el mensaje']
    )) {
        return;
    }
    
    
    guardarDatos();

    function guardarDatos() {
        const accion = id ? 'actualizar' : 'insertar';
        const data = {
            accion: accion,
            titulo: titulo,
            mensaje: mensaje,
            imagen: imagen
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlNews,
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
                    'Hubo un problema al guardar el aviso.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function mostrarAvisos(avisos) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    avisos = ordenar(avisos, order);

    if (!Array.isArray(avisos) || avisos.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron avisos.</td></tr>');
        return;
    }

    avisos.forEach((aviso, index) => {
        const json = encodeURIComponent(JSON.stringify(aviso));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${aviso.titulo}</td>
                <td class="align-middle">${aviso.mensaje}</td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addNews.php?id=${aviso.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarAviso(${aviso.id}, '${aviso.titulo}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesAviso('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function mostrarAvisosCliente(avisos) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    avisos = ordenar(avisos, order);

    if (!Array.isArray(avisos) || avisos.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron avisos.</td></tr>');
        return;
    }

    avisos.forEach((aviso, index) => {
        const json = encodeURIComponent(JSON.stringify(aviso));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${aviso.titulo}</td>
                <td class="align-middle">${formatearFecha(aviso.fecha_registro)}</td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='verAviso.php?id=${aviso.id}'" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Ver<i class="bi bi-search ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function buscarAviso(id) {
    $.ajax({
        url: backend + urlNews,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const aviso = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarAviso(aviso);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function buscarAvisoCliente(id) {
    $.ajax({
        url: backend + urlNews,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const aviso = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarAvisoCliente(aviso);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarAviso(aviso) {
    if (aviso) {
        $('#Titulo').val(aviso.titulo);
        $('#Mensaje').val(aviso.mensaje);

        if (aviso.imagen.length > 30) {
            cargarImagenGuardada(aviso.imagen, '#vistaImagenAviso');
            $('#hiddenImagenAviso').val(aviso.imagen);
        }
    }
}

function mostrarAvisoCliente(aviso) {
    if (aviso) {
        const titulo = $('#mensajeCardTitle');
        const mensaje = $('#mensajeCardMenssage');
        const fecha = $('#mensajeCardDate');

        if (aviso.imagen.length > 30) {
            cargarImagenGuardada(aviso.imagen, '#mensajeCardImage');
        }

        titulo.html('');
        mensaje.html('');
        fecha.html('');

        titulo.html(aviso.titulo);
        mensaje.html(aviso.mensaje);
        fecha.html(formatearFecha(aviso.fecha_registro));
    }
}

function eliminarAviso(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar el aviso de "' + nombre + '"? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function() {
                eliminarAviso(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlNews,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function(response) {
                aplicarFiltrosAviso()
                alert(
                    '¡Aviso eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al eliminar el aviso.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosAviso() {
    const titulo = $('#Titulo').val();
    seleccionarAvisos(titulo);
}

function aplicarFiltrosAvisoCliente() {
    const titulo = $('#Titulo').val();
    seleccionarAvisosCliente(titulo);
}

function verDetallesAviso(json) {
    const aviso = JSON.parse(decodeURIComponent(json));
    alertDetails(
        'Detalles del aviso',
        aviso,
        ['titulo', 'mensaje', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarAvisos(titulo) {
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
        url: backend + urlNews,
        type: 'POST',
        data: {
            accion: 'listarIds',
            titulo: titulo,
            orden: order,
        },
        success: function (response) {
            try {
                const avisos = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (avisos.length > 0) {
                    procesarAvisosSecuencialmente(avisos, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron avisos.</td></tr>`);
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

function procesarAvisosSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const aviso = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="titulo-${aviso.id}"></td>
                <td class="align-middle" id="mensaje-${aviso.id}"></td>
                <td class="align-middle text-center" id="opciones-${aviso.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este aviso no se pudo cargar.</td></tr>`);
    }

    cargarAvisoSeleccionado(aviso.id, function () {
        procesarAvisosSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarAvisoSeleccionado(id, callback) {
    const tdTitulo = $(`#titulo-${id}`);
    const tdMensaje = $(`#mensaje-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlNews,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const aviso = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(aviso));
                
                tdTitulo.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${aviso.titulo || 'Sin título'}</li>
                    </ul>
                `);
                tdMensaje.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${aviso.mensaje || 'Sin mensaje'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton${aviso.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${aviso.id}">
                                <li><a class="dropdown-item" href="addNews.php?id=${aviso.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addNews.php?id=${aviso.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarAviso(${aviso.id}, '${aviso.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesAviso('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
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

function seleccionarAvisosCliente(titulo) {
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
        url: backend + urlNews,
        type: 'POST',
        data: {
            accion: 'listarIds',
            titulo: titulo,
            orden: order,
        },
        success: function (response) {
            try {
                const avisos = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (avisos.length > 0) {
                    procesarAvisosClienteSecuencialmente(avisos, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron avisos.</td></tr>`);
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

function procesarAvisosClienteSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const aviso = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="titulo-${aviso.id}"></td>
                <td class="align-middle" id="fecha-${aviso.id}"></td>
                <td class="align-middle text-center" id="opciones-${aviso.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este aviso no se pudo cargar.</td></tr>`);
    }

    cargarAvisoClienteSeleccionado(aviso.id, function () {
        procesarAvisosClienteSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarAvisoClienteSeleccionado(id, callback) {
    const tdTitulo = $(`#titulo-${id}`);
    const tdFecha = $(`#fecha-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlNews,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const aviso = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                
                tdTitulo.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${aviso.titulo || 'Sin título'}</li>
                    </ul>
                `);
                tdFecha.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${formatearFecha(aviso.fecha_registro)}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='verAviso.php?id=${aviso.id}'" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Ver<i class="bi bi-search ms-2"></i>
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

function actualizarPaginacionAviso(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaAviso(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaAviso(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaAviso(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaAviso(pagina) {
    currentPage = pagina;
    limpiarFiltrosAviso('');
}

function limpiarFiltrosAviso() {
    $('#Titulo').val('');
}
