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

function seleccionarInteracciones(accionInteraccion) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();

    container.empty();

    container.append(`
        <tr><td class="text-center" colspan="4">
            <div class="spinner-border spinner-color"></div>
        </td></tr>
    `);

    solicitudAjaxActiva = $.ajax({
        url: backend + urlInteraction,
        type: 'POST',
        data: {
            accion: 'listarIds',
            filtros: {
                accion: accionInteraccion,
                ordenarPor: order,
                orden: order != "id" ? 'ASC' : 'DESC'
            }
        },
        success: function (response) {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;

                const lista = res.list || [];

                container.empty();

                if (lista.length > 0) {
                    procesarInteraccionesSecuencialmente(lista, 0, 4);
                } else {
                    container.append(`<tr><td colspan="4">No hay datos</td></tr>`);
                }

            } catch (error) {
                console.error(error);
            }
        }
    });
}

function procesarInteraccionesSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const interaccion = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="accion-${interaccion}"></td>
                <td class="align-middle" id="url-${interaccion}"></td>
                <td class="align-middle" id="fecha-${interaccion}"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Esta interacción no se pudo cargar.</td></tr>`);
    }
    
    cargarInteraccionSeleccionada(interaccion, function () {
        procesarInteraccionesSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarInteraccionSeleccionada(id, callback) {
    $.ajax({
        url: backend + urlInteraction,
        type: 'POST',
        data: {
            accion: 'obtener',
            id: id
        },
        success: function (response) {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                const interaccion = res.data;

                if (!interaccion) return;

                $(`#accion-${id}`).html(interaccion.accion);
                $(`#url-${id}`).html(interaccion.url);
                $(`#fecha-${id}`).html(interaccion.fecha_registro);

            } catch (error) {
                console.error(error);
            }

            if (callback) callback();
        }
    });
}

function limpiarFiltrosInteraccion() {
    $('#Accion').val('');
}

