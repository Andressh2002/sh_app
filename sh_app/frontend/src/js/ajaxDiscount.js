function guardarDescuento() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const descuento = $('#Descuento').val();
    const descripcion = $('#Descripcion').val();
    const diaInicio = $('#DayStartDate').val();
    const diaFinal = $('#DayEndDate').val();
    const mesInicio = $('#MonthStartDate').val();
    const mesFinal = $('#MonthEndDate').val();
    const fechaInicio = mesInicio + '-' + diaInicio;
    const fechaFinal = mesFinal + '-' + diaFinal;

    if (!validarCampos(
        [nombre, descuento, diaInicio, diaFinal, mesInicio, mesFinal],
        ['el nombre', 'el descuento', 'el día de inicio', 'el día de finalización', 'el mes de inicio', 'el mes de finalización']
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
            fecha_final: fechaFinal,
            descuento: descuento,
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlDiscount,
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
                    'Hubo un problema al guardar el descuento.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function obtenerDescuentos(nombre) {
    $.ajax({
        url: backend + urlDiscount,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function (response) {
            try {
                const descuentos = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarDescuentos(descuentos);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarDescuentos(descuentos) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    descuentos = ordenar(descuentos, order);

    if (!Array.isArray(descuentos) || descuentos.length === 0) {
        container.append('<tr><td class="text-center" colspan="5">No se encontraron descuentos.</td></tr>');
        return;
    }

    descuentos.forEach((descuento, index) => {
        const json = encodeURIComponent(JSON.stringify(descuento));
        function formarFecha(date) {
            const format = date.split('-');
            let mes = '';
            switch (format[0]) {
                case '1':
                    mes = 'enero';
                    break;
                case '2':
                    mes = 'febrero';
                    break;
                case '3':
                    mes = 'marzo';
                    break;
                case '4':
                    mes = 'abril';
                    break;
                case '5':
                    mes = 'mayo';
                    break;
                case '6':
                    mes = 'junio';
                    break;
                case '7':
                    mes = 'julio';
                    break;
                case '8':
                    mes = 'agosto';
                    break;
                case '9':
                    mes = 'septiembre';
                    break;
                case '10':
                    mes = 'octubre';
                    break;
                case '11':
                    mes = 'noviembre';
                    break;
                case '12':
                    mes = 'diciembre';
                    break;
                default:
                    break;
            }
            return format[1] + ' de ' + mes;
        }
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${descuento.nombre}</td>
                <td class="align-middle">${descuento.descuento}%</td>
                <td class="align-middle">${'Del ' + formarFecha(descuento.fecha_inicial) + ' al ' + formarFecha(descuento.fecha_final)}</td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addDiscount.php?id=${descuento.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarDescuento(${descuento.id}, '${descuento.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesDescuento('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function buscarDescuento(id) {
    $.ajax({
        url: backend + urlDiscount,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function (response) {
            try {
                const descuento = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarDescuento(descuento);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarDescuento(descuento) {
    if (descuento) {
        const fechaInicio = descuento.fecha_inicial.split('-');
        const fechaFinal = descuento.fecha_final.split('-');
        $('#Nombre').val(descuento.nombre);
        $('#Descuento').val(descuento.descuento);
        $('#Descripcion').val(descuento.descripcion);
        $('#DayStartDate').val(fechaInicio[1]);
        $('#DayEndDate').val(fechaFinal[1]);
        $('#MonthStartDate').val(fechaInicio[0]);
        $('#MonthEndDate').val(fechaFinal[0]);
    }
}

function eliminarDescuento(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar "' + nombre + '" de las promociones de descuento? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function () {
                eliminarDescuento(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlDiscount,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function (response) {
                aplicarFiltrosDescuento()
                alert(
                    '¡Descuento eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function () {
                alert(
                    'Error',
                    'Hubo un problema al eliminar el descuento.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosDescuento() {
    const nombre = $('#Nombre').val();
    seleccionarDescuentos(nombre);
}

function verDetallesDescuento(json) {
    const descuento = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles de la promoción',
        descuento,
        ['nombre', 'descuento', 'descripcion', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarDescuentos(nombre) {
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
        url: backend + urlDiscount,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            orden: order,
        },
        success: function (response) {
            try {
                const descuentos = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (descuentos.length > 0) {
                    procesarDescuentoSecuencialmente(descuentos, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron descuentos.</td></tr>`);
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

function procesarDescuentoSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const descuento = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="nombre-${descuento.id}"></td>
                <td class="align-middle" id="descuento-${descuento.id}"></td>
                <td class="align-middle" id="tiempo-${descuento.id}"></td>
                <td class="align-middle text-center" id="opciones-${descuento.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este descuento no se pudo cargar.</td></tr>`);
    }

    cargarDescuentoSeleccionado(descuento.id, function () {
        procesarDescuentoSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarDescuentoSeleccionado(id, callback) {
    const tdNombre = $(`#nombre-${id}`);
    const tdDescuento = $(`#descuento-${id}`);
    const tdTiempo = $(`#tiempo-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlDiscount,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const descuento = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(descuento));
                
                tdNombre.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${descuento.nombre || 'Sin nombre'}</li>
                    </ul>
                `);
                tdDescuento.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${descuento.descuento + '%' || 'Sin porcentaje'}</li>
                    </ul>
                `);
                tdTiempo.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${descuento.fecha_inicial && descuento.fecha_final ? 'Del ' + formarFecha(descuento.fecha_inicial) + ' al ' + formarFecha(descuento.fecha_final) : 'Sin tiempos asignados'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton${descuento.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${descuento.id}">
                                <li><a class="dropdown-item" href="addDiscount.php?id=${descuento.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addDiscount.php?id=${descuento.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarDescuento(${descuento.id}, '${descuento.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesDescuento('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
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

function actualizarPaginacionDescuento(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaDescuento(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaDescuento(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaDescuento(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaDescuento(pagina) {
    currentPage = pagina;
    seleccionarDescuentos('');
}

function limpiarFiltrosDescuento() {
    $('#Nombre').val('');
}
