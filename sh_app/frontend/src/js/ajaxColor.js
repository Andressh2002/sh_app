function guardarColor() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const descripcion = $('#Descripcion').val();
    const color1 = $('#Color1').val();
    const color2 = $('#Color2').val();
    const color3 = $('#Color3').val();
    const familia = $('#Familia').val();

    if (!validarCampos(
        [nombre, color1, color2, color3, familia],
        ['el nombre', 'el color principal', 'el color secundario', 'el color terciario', 'la familia de color al que pertenece']
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
            color1: color1,
            color2: color2,
            color3: color3,
            familia: familia
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlColor,
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
                    'Hubo un problema al guardar el color',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function obtenerColores(nombre) {
    $.ajax({
        url: backend + urlColor,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function(response) {
            try {
                const colores = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarColores(colores);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarColores(colores) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    colores = ordenar(colores, order);

    if (!Array.isArray(colores) || colores.length === 0) {
        container.append('<tr><td class="text-center" colspan="5">No se encontraron colores.</td></tr>');
        return;
    }

    colores.forEach((color, index) => {
        const json = encodeURIComponent(JSON.stringify(color));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${color.nombre}</td>
                <td class="align-middle">${color.color_familia}</td>
                <td class="align-middle">
                    <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2" style="background: ${color.codigo_color_principal};">
                        <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.codigo_color_terciario ? 'btn-palette-bg-color-2-A' : 'btn-palette-bg-color-2-B'}" style="background: ${color.codigo_color_secundario};"></div>
                        <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.codigo_color_terciario ? 'visually-hidden' : 'btn-palette-bg-color-3'}" style="background: ${color.codigo_color_terciario};"></div>
                    </div>
                </td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addColor.php?id=${color.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarColor(${color.id}, '${color.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesColor('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function buscarColor(id) {
    $.ajax({
        url: backend + urlColor,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const color = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarColor(color);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarColor(color) {
    if (color) {
        $('#Nombre').val(color.nombre);
        $('#Descripcion').val(color.descripcion);
        $('#Color1').val(color.codigo_color_principal);
        $('#Color2').val(color.codigo_color_secundario);
        $('#Color3').val(color.codigo_color_terciario);
        $('#Familia').val(color.color_familia);
    }
}

function eliminarColor(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar "' + nombre + '" de los colores? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function() {
                eliminarColor(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlColor,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function(response) {
                aplicarFiltrosColor()
                alert(
                    '¡Color eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al eliminar el color.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosColor() {
    const nombre = $('#Nombre').val();
    const familia = $('#Familia').val();
    seleccionarColores(nombre, familia);
}

function verDetallesColor(json) {
    const color = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles del color',
        color,
        ['nombre', 'descripcion', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarColores(nombre, familia) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();
    const colspan = 5;
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
        url: backend + urlColor,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            familia: familia,
            orden: order,
        },
        success: function (response) {
            try {
                const colores = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (colores.length > 0) {
                    procesarColoresSecuencialmente(colores, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron colores.</td></tr>`);
                }
            } catch (error) {
                container.empty();
                container.append(`<tr><td class="text-center" colspan="${colspan}">Ha ocurrido un error al cargar la lista.</td></tr>`);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function (xhr, status) {
            if (status !== 'abort') {
                container.empty();
                container.append(`<tr><td class="text-center" colspan="${colspan}">Ha ocurrido un error al tratar de conseguir la información.</td></tr>`);
                console.error('Error al procesar la solicitud.');
            } else {
                console.log('Solicitud principal cancelada.');
            }
        }
    });
}

function procesarColoresSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const color = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="nombre-${color.id}"></td>
                <td class="align-middle" id="familia-${color.id}"></td>
                <td class="align-middle" id="color-${color.id}"></td>
                <td class="align-middle text-center" id="opciones-${color.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este color no se pudo cargar.</td></tr>`);
    }

    cargarColorSeleccionado(color.id, function () {
        procesarColoresSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarColorSeleccionado(id, callback) {
    const tdNombre = $(`#nombre-${id}`);
    const tdFamilia = $(`#familia-${id}`);
    const tdColor = $(`#color-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    // Guardamos esta solicitud activa
    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
    }

    solicitudAjaxActiva = $.ajax({
        url: backend + urlColor,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            if (cancelarCargaSecuencial) return;

            try {
                const color = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(color));
                
                tdNombre.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${color.nombre || 'Sin nombre'}</li>
                    </ul>
                `);
                tdFamilia.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${color.color_familia || 'Sin asignar'}</li>
                    </ul>
                `);
                tdColor.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">
                            <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2" style="background: ${color.codigo_color_principal};">
                                <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.codigo_color_terciario ? 'btn-palette-bg-color-2-A' : 'btn-palette-bg-color-2-B'}" style="background: ${color.codigo_color_secundario};"></div>
                                <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.codigo_color_terciario ? 'visually-hidden' : 'btn-palette-bg-color-3'}" style="background: ${color.codigo_color_terciario};"></div>
                            </div>
                        </li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton${color.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${color.id}">
                                <li><a class="dropdown-item" href="addColor.php?id=${color.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addColor.php?id=${color.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarColor(${color.id}, '${color.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesColor('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }

            if (typeof callback === 'function') callback(); // Continuar con el siguiente
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            if (typeof callback === 'function') callback(); // Continuar igual aunque haya error
        }
    });
}

function actualizarPaginacionColor(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaColor(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaColor(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaColor(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaColor(pagina) {
    currentPage = pagina;
    seleccionarColores('', '');
}

function limpiarFiltrosColor() {
    $('#Nombre').val('');
    $('#Familia').val('');
}

