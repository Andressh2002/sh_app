function guardarFestividad() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const descripcion = $('#Descripcion').val();
    const diaInicio = $('#DayStartDate').val();
    const diaFinal = $('#DayEndDate').val();
    const mesInicio = $('#MonthStartDate').val();
    const mesFinal = $('#MonthEndDate').val();
    const fechaInicio = mesInicio + '-' + diaInicio;
    const fechaFinal = mesFinal + '-' + diaFinal;

    if (!validarCampos(
        [nombre, diaInicio, diaFinal, mesInicio, mesFinal],
        ['el nombre', 'el día de inicio', 'el día de finalización', 'el mes de inicio', 'el mes de finalización']
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
            fecha_inicial: fechaInicio,
            fecha_final: fechaFinal
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlHoliday,
            type: 'POST',
            data: data,
            success: function (response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            },
            error: function () {
                alert(
                    'Error',
                    'Hubo un problema al guardar la festividad.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function obtenerFestividades(nombre) {
    $.ajax({
        url: backend + urlHoliday,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function (response) {
            try {
                const festividades = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarFestividades(festividades);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarFestividades(festividades) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    festividades = ordenar(festividades, order);

    if (!Array.isArray(festividades) || festividades.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron festividades.</td></tr>');
        return;
    }

    festividades.forEach((festividad, index) => {
        const json = encodeURIComponent(JSON.stringify(festividad));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${festividad.nombre}</td>
                <td class="align-middle">${'Del ' + formarFecha(festividad.fecha_inicial) + ' al ' + formarFecha(festividad.fecha_final)}</td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addHoliday.php?id=${festividad.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarFestividad(${festividad.id}, '${festividad.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesFestividad('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function buscarFestividad(id) {
    $.ajax({
        url: backend + urlHoliday,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function (response) {
            try {
                const festividad = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarFestividad(festividad);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarFestividad(festividad) {
    if (festividad) {
        const fechaInicio = festividad.fecha_inicial.split('-');
        const fechaFinal = festividad.fecha_final.split('-');
        $('#Nombre').val(festividad.nombre);
        $('#Descripcion').val(festividad.descripcion);
        $('#DayStartDate').val(fechaInicio[1]);
        $('#DayEndDate').val(fechaFinal[1]);
        $('#MonthStartDate').val(fechaInicio[0]);
        $('#MonthEndDate').val(fechaFinal[0]);
    }
}

function eliminarFestividad(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar "' + nombre + '" de las festividad? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function () {
                eliminarFestividad(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlHoliday,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function (response) {
                aplicarFiltrosFestividad()
                alert(
                    '¡Festividad eliminada!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function () {
                alert(
                    'Error',
                    'Hubo un problema al eliminar la festividad.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosFestividad() {
    const nombre = $('#Nombre').val();
    seleccionarFestividades(nombre);
}

function verDetallesFestividad(json) {
    const festividad = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles de la festibidad',
        festividad,
        ['nombre', 'descripcion', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarFestividades(nombre) {
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
        url: backend + urlHoliday,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            orden: order,
        },
        success: function (response) {
            try {
                const festividades = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (festividades.length > 0) {
                    procesarFestividadSecuencialmente(festividades, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron festividades.</td></tr>`);
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

function procesarFestividadSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const festividad = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="nombre-${festividad.id}"></td>
                <td class="align-middle" id="tiempo-${festividad.id}"></td>
                <td class="align-middle text-center" id="opciones-${festividad.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Esta festividad no se pudo cargar.</td></tr>`);
    }

    cargarFestividadSeleccionado(festividad.id, function () {
        procesarFestividadSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarFestividadSeleccionado(id, callback) {
    const tdNombre = $(`#nombre-${id}`);
    const tdTiempo = $(`#tiempo-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlHoliday,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const festividad = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(festividad));
                
                tdNombre.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${festividad.nombre || 'Sin nombre'}</li>
                    </ul>
                `);
                tdTiempo.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${festividad.fecha_inicial && festividad.fecha_final ? 'Del ' + formarFecha(festividad.fecha_inicial) + ' al ' + formarFecha(festividad.fecha_final) : 'Sin tiempos asignados'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton${festividad.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${festividad.id}">
                                <li><a class="dropdown-item" href="addHoliday.php?id=${festividad.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addHoliday.php?id=${festividad.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarFestividad(${festividad.id}, '${festividad.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesFestividad('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
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

function actualizarPaginacionFestividad(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaFestividad(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaFestividad(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaFestividad(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaFestividad(pagina) {
    currentPage = pagina;
    seleccionarFestividades('');
}

function limpiarFiltrosFestividad() {
    $('#Nombre').val('');
}
