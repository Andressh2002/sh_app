function guardarRareza() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const descripcion = $('#Descripcion').val();
    const color = $('#Color').val();

    if (!validarCampos(
        [nombre, color],
        ['el nombre', 'el color']
    )) {
        return;
    }
    
    
    guardarDatos();

    function guardarDatos() {
        const accion = id ? 'actualizar' : 'insertar';
        const data = {
            accion: accion,
            nombre: nombre,
            descripcion: descripcion,
            color: color
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlRarity,
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
                    'Hubo un problema al guardar la rareza.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function obtenerRarezas(nombre) {
    $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function(response) {
            try {
                const rarezas = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarCategorias(rarezas);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarRarezas(rarezas) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    rarezas = ordenar(rarezas, order);

    if (!Array.isArray(rarezas) || rarezas.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron rarezas.</td></tr>');
        return;
    }

    rarezas.forEach((rareza, index) => {
        const json = encodeURIComponent(JSON.stringify(rareza));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${rareza.nombre}</td>
                <td class="align-middle">
                    <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2" style="background: ${rareza.color};"></div>
                </td>
                <td class="align-middle text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addRarity.php?id=${rareza.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarRareza(${rareza.id}, '${rareza.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesRareza('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function buscarRareza(id) {
    $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const rareza = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarRareza(rareza);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarRareza(rareza) {
    if (rareza) {
        $('#Nombre').val(rareza.nombre);
        $('#Descripcion').val(rareza.descripcion);
        $('#Color').val(rareza.color);
    }
}

function eliminarRareza(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar "' + nombre + '" de las rarezas? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function() {
                eliminarRareza(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlRarity,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function(response) {
                aplicarFiltrosRareza()
                alert(
                    '¡Rareza eliminada!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al eliminar la rareza.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosRareza() {
    const nombre = $('#Nombre').val();
    seleccionarRarezas(nombre);
}

function verDetallesRareza(json) {
    const rareza = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles de la rareza',
        rareza,
        ['nombre', 'descripcion', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarRarezas(nombre) {
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
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            orden: order,
        },
        success: function (response) {
            try {
                const rarezas = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (rarezas.length > 0) {
                    procesarRarezasSecuencialmente(rarezas, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron rarezas.</td></tr>`);
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

function procesarRarezasSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const rareza = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="nombre-${rareza.id}">
                    <div class="spinner-border spinner-color m-auto" role="status" id="spinner-${rareza.id}" style="width: 16px; height: 16px;"></div>
                </td>
                <td class="align-middle" id="colores-${rareza.id}"></td>
                <td class="align-middle text-center" id="opciones-${rareza.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Esta rareza no se pudo cargar.</td></tr>`);
    }

    cargarRarezaSeleccionada(rareza.id, function () {
        procesarRarezasSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarRarezaSeleccionada(id, callback) {
    const tdNombre = $(`#nombre-${id}`);
    const tdColores = $(`#colores-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    tdNombre.empty();
    tdColores.empty();
    tdOpciones.empty();

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const rareza = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(rareza));
                
                tdNombre.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 248px;">
                        <li class="${liClasses}">${rareza.nombre || 'Sin nombre'}</li>
                    </ul>
                `);
                tdColores.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 248px;">
                        <li class="${liClasses}">${rareza.color ? `
                            <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2 align-middle" style="display: inline-block; width: 28px; height: 28px; background: ${rareza.color};"></div>
                        ` : 'Sin asignar'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="addRarity.php?id=${rareza.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addRarity.php?id=${rareza.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarRareza(${rareza.id}, '${rareza.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesRareza('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
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

function actualizarPaginacionRareza(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaRareza(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaRareza(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaRareza(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaRareza(pagina) {
    currentPage = pagina;
    seleccionarRarezas('');
}

function limpiarFiltrosRareza() {
    $('#Nombre').val('');
}

