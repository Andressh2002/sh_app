function guardarPedido(idProduct) {
    const cliente = $('#Sesion').val();
    const producto = idProduct;
    const color = $('#Color').val();
    const colorAccesorio = $('#AccesoryColor').val();
    const cantidad = $('#cantidad').val();
    const precio = $('#precio').val();
    const total = $('#total').val();

    alertLoadingBlocked(
        'Guardando pedido',
        'Se está guardando el pedido, espere un momento...',
        'warning',
    );
    guardarDatos();

    function guardarDatos() {
        const accion = 'insertar';
        const data = {
            accion: accion,
            cliente: cliente,
            producto: producto,
            color: color,
            colorAccesorio: colorAccesorio,
            cantidad: cantidad,
            precio: precio, 
            total: total
        };

        $.ajax({
            url: backend + urlOrder,
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
                    'Hubo un problema al intentar guardar el pedido.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function obtenerPedidos() {
    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'obtener',
        },
        success: function (response) {
            try {
                const pedidos = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarPedidos(pedidos);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarPedidos(pedidos) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    pedidos = ordenar(pedidos, order);

    if (!Array.isArray(pedidos) || pedidos.length === 0) {
        container.append('<tr><td class="text-center" colspan="12">No se encontraron pedidos.</td></tr>');
        return;
    }

    pedidos.forEach((pedido, index) => {
        const json = encodeURIComponent(JSON.stringify(pedido));
        const vectColors = (pedido.colores).split(',');

        let indexColor = 0;
        let indexAccesoryColor = 0;
        let isColorAccesory = false;

        for (let index = 0; index < vectColors.length; index++) {
            if (vectColors[index] == pedido.idColor) {
                indexColor = index;
            }
        }

        try {
            const vectAccesoryColors = (pedido.coloresAccesorio).split(',');
            isColorAccesory = true;

            for (let index = 0; index < vectAccesoryColors.length; index++) {
                if (vectAccesoryColors[index] == pedido.idColorAccesorio) {
                    indexAccesoryColor = index;
                }
            }
        } catch (error) {
            //
        }

        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle" style="width: 256px;">
                    <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 200px;">
                        <!-- Spinner mientras se carga la imagen -->
                        <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${pedido.id}" 
                            style="width: 50px; height: 50px;"></div>
                        <!-- Imagen (oculta por defecto) -->
                        <img id="img-${pedido.id}" class="d-none w-auto h-100" alt="Imagen">
                        ${isColorAccesory ? `
                            <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-accesory-${pedido.id}" 
                            style="width: 50px; height: 50px;"></div>
                            <img id="img-accesory-${pedido.id}" class="d-none w-auto h-100" alt="Imagen">
                        ` : ''}
                    </div>
                </td>
                <td class="align-middle">${pedido.producto}</td>
                <td class="align-middle">
                    <ul class="list-group border-0 px-0">
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Nombre: </strong>${pedido.cliente + (pedido.segundo_nombre.length > 1 ? pedido.segundo_nombre + ' ' : ' ') + (pedido.primer_apellido.length > 1 ? pedido.primer_apellido + ' ' : ' ') + (pedido.segundo_apellido.length > 1 ? pedido.segundo_apellido : '')}</li>
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Ubicación: </strong>${(pedido.provincia.length > 1 ? pedido.provincia : '') + (pedido.canton.length > 1 ? ', ' + pedido.canton : '') + (pedido.distrito.length > 1 ? ', ' + pedido.distrito : '')}</li>
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Teléfono: </strong>${pedido.telefono.length > 1 ? pedido.telefono : 'Dato no registrado'}</li>
                    </ul>
                </td>
                <td class="align-middle">${pedido.categoria}</td>
                <td class="align-middle">${pedido.cantidad}</td>
                <td class="align-middle">₡${pedido.total}</td>
                <td class="align-middle">${pedido.pagado == 0 ? 'No' : 'Si'}</td>
                <td class="align-middle">${formatearFechaConHora(pedido.fecha_registro)}</td>
                <td class="align-middle">${pedido.fecha_registro == pedido.fecha_pago ? 'No se ha pagado' : formatearFechaConHora(pedido.fecha_pago)}</td>
                <td class="align-middle text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-end">
                        ${pedido.pagado == 0 ? `
                            <button onclick="pagarPedido('${pedido.id}', '${pedido.idProducto}', '${pedido.cantidad}')" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Pagar<i class="bi bi-wallet ms-2"></i>
                            </button>
                            ` : ``}
                        <button onclick="verDetallesPedido('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
        buscarImagenPedido(pedido.id, indexColor + 1);
        try {
            if (isColorAccesory) {
                buscarImagenAccesorioPedido(pedido.id, indexAccesoryColor + 1);
            }
        } catch (error) {
            //
        }
        //mostrarParteColorProducto(indexColor, vectColors.length, pedido.imagen, ('canva' + (startIndex + index + 1).toString()), ('result' + (startIndex + index + 1).toString()));
    });
}

function buscarImagenPedido(id, colorIndex) {
    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'buscarImagen',
            id: id,
            idColor: colorIndex
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data[0].imagen && data[0].imagen !== '' ? data[0].imagen : '../src/img/app/no_image.png';

                const imgElement = document.getElementById(`img-${id}`);
                const spinnerElement = document.getElementById(`spinner-${id}`);

                imgElement.src = imagenURL;
                imgElement.classList.remove('d-none');

                imgElement.onload = () => {
                    if (spinnerElement) spinnerElement.remove();
                };

                imgElement.onerror = () => {
                    if (spinnerElement) spinnerElement.remove();
                    imgElement.src = '../src/img/app/no_image.png';
                };
            } catch (error) {
                console.error('Error al procesar la imagen:', error);
            }
        },
        error: function () {
            console.error('Error al cargar la imagen del producto.');
        }
    });
}

function buscarImagenAccesorioPedido(id, colorIndex) {
    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'buscarImagenAccesorio',
            id: id,
            idColor: colorIndex
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data[0].imagen && data[0].imagen !== '' ? data[0].imagen : '../src/img/app/no_image.png';

                const imgElement = document.getElementById(`img-accesory-${id}`);
                const spinnerElement = document.getElementById(`spinner-accesory-${id}`);

                imgElement.src = imagenURL;
                imgElement.classList.remove('d-none');

                imgElement.onload = () => {
                    if (spinnerElement) spinnerElement.remove();
                };

                imgElement.onerror = () => {
                    if (spinnerElement) spinnerElement.remove();
                    imgElement.src = '../src/img/app/no_image.png';
                };
            } catch (error) {
                console.error('Error al procesar la imagen:', error);
            }
        },
        error: function () {
            console.error('Error al cargar la imagen del producto.');
        }
    });
}

function buscarPedido(id) {
    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function (response) {
            try {
                const pedido = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarPedido(pedido);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarPedido(color) {
    if (color) {
        $('#Nombre').val(color.nombre);
        $('#Descripcion').val(color.descripcion);
        $('#Color1').val(color.codigo_color_principal);
        $('#Color2').val(color.codigo_color_secundario);
        $('#Familia').val(color.color_familia);
    }
}

function eliminarPedido(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar este pedido? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function () {
                eliminarPedido(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlOrder,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function (response) {
                aplicarFiltrosPedido()
                alert(
                    '¡Pedido eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function () {
                alert(
                    'Error',
                    'Hubo un problema al eliminar el pedido.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosPedido() {
    const cliente = $('#Cliente').val();
    const producto = $('#Producto').val();
    const categoria = $('#Categoria').val();
    const rareza = $('#Rareza').val();
    const universo = $('#Universo').val();
    const color = $('#Color').val();
    const pagado = $('#Pagado').val();
    const ubicacion = $('#Ubicacion').val();
    const telefono = $('#Telefono').val();
    seleccionarPedidos(cliente, producto, categoria, rareza, universo, color, pagado, ubicacion, telefono);
}

function aplicarFiltrosPedidosCliente(cliente) {
    const producto = $('#Producto').val();
    const categoria = $('#Categoria').val();
    const rareza = $('#Rareza').val();
    const universo = $('#Universo').val();
    const color = $('#Color').val();
    const pagado = $('#Pagado').val();
    seleccionarPedidosCliente(cliente, producto, categoria, rareza, universo, color, pagado);
}

function verDetallesPedido(json) {
    const pedido = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles del pedido',
        pedido,
        ['cliente', 'producto', 'color', 'paleta', 'cantidad', 'total', 'pagado', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarPedidos(cliente, producto, categoria, rareza, universo, color, pagado, ubicacion, telefono) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();
    const colspan = 11;
    container.append(`
        <tr><td class="text-center align-middle" colspan="${colspan}">
            <div class="spinner-border spinner-color" role="status" style="width: 24px; height: 24px;"></div>
        </td></tr>
    `);

    if (!cliente) {
        cliente = '';
    }
    if (!producto) {
        producto = '';
    }
    if (!categoria) {
        categoria = '';
    }
    if (!rareza) {
        rareza = '';
    }
    if (!universo) {
        universo = '';
    }
    if (!color) {
        color = '';
    }
    if (!pagado) {
        pagado = '';
    }
    if (!ubicacion) {
        ubicacion = '';
    }
    if (!telefono) {
        telefono = '';
    }
    const offset = (currentPage - 1) * itemsPerPage;
    let isPagado;
    if (pagado != '') {
        if (pagado == '1') {
            isPagado = '1';
        } else {
            isPagado = '0';
        }
    } else {
        isPagado = '';
    }
    
    cancelarCargaSecuencial = true;

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
        solicitudAjaxActiva = null;
    }

    cancelarCargaSecuencial = false;

    solicitudAjaxActiva = $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'listarIds',
            cliente: cliente,
            producto: producto,
            categoria: categoria,
            rareza: rareza,
            universo: universo,
            color: color,
            pagado: isPagado,
            ubicacion: ubicacion,
            telefono: telefono,
            orden: order,
        },
        success: function (response) {
            try {
                const pedidos = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (pedidos.length > 0) {
                    procesarPedidosSecuencialmente(pedidos, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron pedidos.</td></tr>`);
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

function procesarPedidosSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const pedido = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" style="width: 200px; min-width: 70px;">
                    <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 145px;">
                        <!-- Spinner mientras se carga la imagen -->
                        <div class="spinner-border spinner-color" role="status" id="spinner-${pedido.id}" style="width: 50px; height: 50px;"></div>
                        <!-- Imagen (oculta por defecto) -->
                        <img id="img-${pedido.id}" class="d-none w-auto h-100 w-auto" alt="Imagen">
                        ${pedido.coloresAccesorio != null ? `
                            <div class="spinner-border spinner-color" role="status" id="spinner-accesory-${pedido.id}" style="width: 50px; height: 50px;"></div>
                            <img id="img-accesory-${pedido.id}" class="d-none w-auto h-75 w-auto" alt="Imagen">
                        ` : ''}
                    </div>
                </td>
                <td class="align-middle" id="producto-${pedido.id}"></td>
                <td class="align-middle" id="cliente-${pedido.id}"></td>
                <td class="align-middle" id="categoria-${pedido.id}"></td>
                <td class="align-middle" id="cantidad-${pedido.id}"></td>
                <td class="align-middle" id="total-${pedido.id}"></td>
                <td class="align-middle" id="pagado-${pedido.id}"></td>
                <td class="align-middle" id="fecha_pedido-${pedido.id}"></td>
                <td class="align-middle" id="fecha_pago-${pedido.id}"></td>
                <td class="align-middle text-center" id="opciones-${pedido.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este pedido no se pudo cargar.</td></tr>`);
        console.error(error);
    }

    cargarPedidosSeleccionado(pedido.id, function () {
        procesarPedidosSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarPedidosSeleccionado(id, callback) {
    const tdProducto = $(`#producto-${id}`);
    const tdCliente = $(`#cliente-${id}`);
    const tdCategoria = $(`#categoria-${id}`);
    const tdCantidad = $(`#cantidad-${id}`);
    const tdTotal = $(`#total-${id}`);
    const tdPagado = $(`#pagado-${id}`);
    const tdFechaPedido = $(`#fecha_pedido-${id}`);
    const tdFechaPago = $(`#fecha_pago-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const pedido = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(pedido));

                const vectColors = (pedido.colores).split(',');

                let indexColor = 0;
                let indexAccesoryColor = 0;
                let isColorAccesory = false;

                for (let index = 0; index < vectColors.length; index++) {
                    if (vectColors[index] == pedido.idColor) {
                        indexColor = index;
                    }
                }

                try {
                    const vectAccesoryColors = (pedido.coloresAccesorio).split(',');
                    isColorAccesory = true;

                    for (let index = 0; index < vectAccesoryColors.length; index++) {
                        if (vectAccesoryColors[index] == pedido.idColorAccesorio) {
                            indexAccesoryColor = index;
                        }
                    }
                } catch (error) {
                    //
                }
                
                tdProducto.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 256px;">
                        <li class="${liClasses}">${pedido.producto || 'Sin producto'}</li>
                    </ul>
                `);
                tdCliente.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 324px;">
                        <li class="${liClasses}"><strong>Nombre: </strong>${pedido.cliente + (pedido.segundo_nombre.length > 1 ? pedido.segundo_nombre + ' ' : ' ') + (pedido.primer_apellido.length > 1 ? pedido.primer_apellido + ' ' : ' ') + (pedido.segundo_apellido.length > 1 ? pedido.segundo_apellido : '')}</li>
                        <li class="${liClasses}"><strong>Ubicación: </strong>${(pedido.provincia.length > 1 ? pedido.provincia : '') + (pedido.canton.length > 1 ? ', ' + pedido.canton : '') + (pedido.distrito.length > 1 ? ', ' + pedido.distrito : '')}</li>
                        <li class="${liClasses}"><strong>Teléfono: </strong>${pedido.telefono.length > 1 ? pedido.telefono : 'Dato no registrado'}</li>
                    </ul>
                `);
                tdCategoria.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${pedido.categoria || 'Sin categoría'}</li>
                    </ul>
                `);
                tdCantidad.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${pedido.cantidad || 'Sin cantidad'}</li>
                    </ul>
                `);
                tdTotal.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">₡${pedido.total || 'Sin total'}</li>
                    </ul>
                `);
                tdPagado.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${pedido.pagado == 0 ? 'No' : 'Si'}</li>
                    </ul>
                `);
                tdFechaPedido.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 100px;">
                        <li class="${liClasses}">${formatearFechaConHora(pedido.fecha_registro)}</li>
                    </ul>
                `);
                tdFechaPago.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 100px;">
                        <li class="${liClasses}">${pedido.fecha_registro == pedido.fecha_pago ? 'No se ha pagado' : formatearFechaConHora(pedido.fecha_pago)}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        ${pedido.pagado == 0 ? `
                            <button onclick="pagarPedido('${pedido.id}', '${pedido.idProducto}', '${pedido.cantidad}')" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Pagar<i class="bi bi-wallet ms-2"></i>
                            </button>
                            ` : ``}
                        ${pedido.pagado == 0 ? `
                            <button onclick="eliminarPedido(${pedido.id}, ${pedido.idCliente}, '${pedido.producto}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Eliminar<i class="bi bi-x ms-2"></i>
                            </button>
                            ` : ''}
                        <button onclick="verDetallesPedido('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);

                buscarImagenPedido(pedido.id, indexColor + 1);
                try {
                    if (isColorAccesory) {
                        buscarImagenAccesorioPedido(pedido.id, indexAccesoryColor + 1);
                    }
                } catch (error) {
                    console.log(error);
                }
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

function seleccionarPedidosCliente(cliente, producto, categoria, rareza, universo, color, pagado) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();
    const colspan = 11;
    container.append(`
        <tr><td class="text-center align-middle" colspan="${colspan}">
            <div class="spinner-border spinner-color" role="status" style="width: 24px; height: 24px;"></div>
        </td></tr>
    `);

    if (!cliente) {
        cliente = '';
    }
    if (!producto) {
        producto = '';
    }
    if (!categoria) {
        categoria = '';
    }
    if (!rareza) {
        rareza = '';
    }
    if (!universo) {
        universo = '';
    }
    if (!color) {
        color = '';
    }
    if (!pagado) {
        pagado = '';
    }

    const offset = (currentPage - 1) * itemsPerPage;
    let isPagado;
    if (pagado != '') {
        if (pagado == '1') {
            isPagado = '1';
        } else {
            isPagado = '0';
        }
    } else {
        isPagado = '';
    }
    
    cancelarCargaSecuencial = true;

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
        solicitudAjaxActiva = null;
    }

    cancelarCargaSecuencial = false;

    solicitudAjaxActiva = $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'listarIdsCliente',
            cliente: cliente,
            producto: producto,
            categoria: categoria,
            rareza: rareza,
            universo: universo,
            color: color,
            pagado: isPagado,
            orden: order,
        },
        success: function (response) {
            try {
                const pedidos = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (pedidos.length > 0) {
                    procesarPedidosClienteSecuencialmente(pedidos, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron pedidos.</td></tr>`);
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

function procesarPedidosClienteSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const pedido = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" style="width: 200px; min-width: 70px;">
                    <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 145px;">
                        <!-- Spinner mientras se carga la imagen -->
                        <div class="spinner-border spinner-color" role="status" id="spinner-${pedido.id}" style="width: 50px; height: 50px;"></div>
                        <!-- Imagen (oculta por defecto) -->
                        <img id="img-${pedido.id}" class="d-none w-auto h-100 w-auto" alt="Imagen">
                        ${pedido.coloresAccesorio != null ? `
                            <div class="spinner-border spinner-color" role="status" id="spinner-accesory-${pedido.id}" style="width: 50px; height: 50px;"></div>
                            <img id="img-accesory-${pedido.id}" class="d-none w-auto h-75 w-auto" alt="Imagen">
                        ` : ''}
                    </div>
                </td>
                <td class="align-middle" id="resumen-${pedido.id}"></td>
                <td class="align-middle" id="total-${pedido.id}"></td>
                <td class="align-middle" id="pagado-${pedido.id}"></td>
                <td class="align-middle text-center" id="opciones-${pedido.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este pedido no se pudo cargar.</td></tr>`);
        console.error(error);
    }

    cargarPedidosClienteSeleccionado(pedido.id, function () {
        procesarPedidosClienteSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarPedidosClienteSeleccionado(id, callback) {
    const tdResumen = $(`#resumen-${id}`);
    const tdTotal = $(`#total-${id}`);
    const tdPagado = $(`#pagado-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'buscarPorIdCliente',
            id: id
        },
        success: function (response) {
            try {
                const pedido = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(pedido));

                const vectColors = (pedido.colores).split(',');

                let indexColor = 0;
                let indexAccesoryColor = 0;
                let isColorAccesory = false;

                for (let index = 0; index < vectColors.length; index++) {
                    if (vectColors[index] == pedido.idColor) {
                        indexColor = index;
                    }
                }

                try {
                    const vectAccesoryColors = (pedido.coloresAccesorio).split(',');
                    isColorAccesory = true;

                    for (let index = 0; index < vectAccesoryColors.length; index++) {
                        if (vectAccesoryColors[index] == pedido.idColorAccesorio) {
                            indexAccesoryColor = index;
                        }
                    }
                } catch (error) {
                    //
                }
                
                tdResumen.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 324px;">
                        <li class="${liClasses}"><strong>Producto: </strong>${pedido.producto}</li>
                        <li class="${liClasses}"><strong>Categoría: </strong>${pedido.categoria}</li>
                        <li class="${liClasses}"><strong>Precio: </strong>₡${pedido.precio}</li>
                        <li class="${liClasses}"><strong>Cantidad: </strong>${pedido.cantidad} ${pedido.cantidad.toString() != '1' ? 'unidades' : 'unidad'}</li>
                    </ul>
                `);
                tdTotal.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">₡${pedido.total || 'Sin total'}</li>
                    </ul>
                `);
                tdPagado.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${pedido.pagado == 0 ? 'No' : 'Si'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-end">
                        ${pedido.pagado == 0 ? `
                        <button onclick="quitarPedido(${pedido.id}, ${pedido.idCliente}, '${pedido.producto}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Anular<i class="bi bi-x ms-2"></i>
                        </button>
                        ` : ''}
                        <button onclick="verDetallesPedidoCliente('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);

                buscarImagenPedido(pedido.id, indexColor + 1);
                try {
                    if (isColorAccesory) {
                        buscarImagenAccesorioPedido(pedido.id, indexAccesoryColor + 1);
                    }
                } catch (error) {
                    console.log(error);
                }
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

function mostrarPedidosCliente(pedidos) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    pedidos = ordenar(pedidos, order);

    if (!Array.isArray(pedidos) || pedidos.length === 0) {
        container.append('<tr><td class="text-center" colspan="11">No se encontraron pedidos.</td></tr>');
        return;
    }

    pedidos.forEach((pedido, index) => {
        const json = encodeURIComponent(JSON.stringify(pedido));
        const vectColors = (pedido.colores).split(',');

        let indexColor = 0;
        let indexAccesoryColor = 0;
        let isColorAccesory = false;

        for (let index = 0; index < vectColors.length; index++) {
            if (vectColors[index] == pedido.idColor) {
                indexColor = index;
            }
        }

        try {
            const vectAccesoryColors = (pedido.coloresAccesorio).split(',');
            isColorAccesory = true;

            for (let index = 0; index < vectAccesoryColors.length; index++) {
                if (vectAccesoryColors[index] == pedido.idColorAccesorio) {
                    indexAccesoryColor = index;
                }
            }
        } catch (error) {
            //
        }

        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle" style="width: 256px;">
                    <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 200px;">
                        <!-- Spinner mientras se carga la imagen -->
                        <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${pedido.id}" 
                            style="width: 50px; height: 50px;"></div>
                        <!-- Imagen (oculta por defecto) -->
                        <img id="img-${pedido.id}" class="d-none w-auto h-100" alt="Imagen">
                        ${isColorAccesory ? `
                            <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-accesory-${pedido.id}" 
                            style="width: 50px; height: 50px;"></div>
                            <img id="img-accesory-${pedido.id}" class="d-none w-auto h-100" alt="Imagen">
                        ` : ''}
                    </div>
                </td>
                <td class="align-middle">
                    <ul class="list-group border-0 px-0">
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Producto: </strong>${pedido.producto}</li>
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Categoría: </strong>${pedido.categoria}</li>
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Precio: </strong>₡${pedido.precio}</li>
                        <li class="list-group-item border-0 bg-transparent px-0"><strong>Cantidad: </strong>${pedido.cantidad} ${pedido.cantidad.toString() != '1' ? 'unidades' : 'unidad'}</li>
                    </ul>
                </td>
                <td class="align-middle">₡${pedido.total}</td>
                <td class="align-middle ${pedido.pagado == 0 ? 'text-danger' : 'text-success'}">${pedido.pagado == 0 ? 'No' : 'Si'}</td>
                <td class="align-middle text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-end">
                        ${pedido.pagado == 0 ? `
                        <button onclick="quitarPedido(${pedido.id}, ${pedido.idCliente}, '${pedido.producto}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Anular<i class="bi bi-x ms-2"></i>
                        </button>
                        ` : ''}
                        <button onclick="verDetallesPedidoCliente('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
        buscarImagenPedido(pedido.id, indexColor + 1);
        try {
            if (isColorAccesory) {
                buscarImagenAccesorioPedido(pedido.id, indexAccesoryColor + 1);
            }
        } catch (error) {
            //
        }
        //mostrarParteColorProducto(indexColor, vectColors.length, pedido.imagen, ('canva' + (startIndex + index + 1).toString()), ('result' + (startIndex + index + 1).toString()));
    });
}

function verDetallesPedidoCliente(json) {
    const pedido = JSON.parse(decodeURIComponent(json)); // Decodificar y parsear el JSON
    alertDetails(
        'Detalles del pedido',
        pedido,
        ['producto', 'cantidad', 'total', 'pagado', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function actualizarPaginacionPedido(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaPedido(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaPedido(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaPedido(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function actualizarPaginacionPedidoCliente(totalItems, cliente) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaPedidoCliente(${currentPage - 1}, ${cliente})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaPedidoCliente(${i}, ${cliente})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaPedidoCliente(${currentPage + 1}, ${cliente})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaPedido(pagina) {
    currentPage = pagina;
    aplicarFiltrosPedido();
}

function cambiarPaginaPedidoCliente(pagina, cliente) {
    currentPage = pagina;
    aplicarFiltrosPedidosCliente(cliente);
}

function limpiarFiltrosPedido() {
    $('#Cliente').val('');
    $('#Producto').val('');
    $('#Categoria').val('');
    $('#Rareza').val('');
    $('#Universo').val('');
    $('#Color').val('');
    $('#Pagado').val('');
    $('#Ubicacion').val('');
    $('#Telefono').val('');
}

function limpiarFiltrosPedidosCliente() {
    $('#Producto').val('');
    $('#Categoria').val('');
    $('#Rareza').val('');
    $('#Universo').val('');
    $('#Color').val('');
    $('#Pagado').val('');
}

function pagarPedido(id, idProduct, cant) {
    const accion = 'pagar';
    const data = {
        accion: accion,
        id: id,
        producto: idProduct,
        cantidad: cant
    };

    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: data,
        success: function (response) {
            seleccionarPedidos('', '', '', '', '', '', '', '', '');
            alert(
                '¡Pedido pagado!',
                response,
                'success',
                'Aceptar'
            );
        },
        error: function () {
            alert(
                'Error',
                'Hubo un problema al intentar pagar el pedido.',
                'error',
                'Aceptar'
            );
        }
    });
}

function quitarPedido(id, idCliente, nombre, eliminar) {
    const accion = 'quitar';
    const data = {
        accion: accion,
        id: id,
    };

    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar el producto "' + nombre + '" de tu lista de pedidos? ¡Si lo haces no se podrá revertir al menos que lo vuelva a reservar en la tienda!',
            'warning',
            'Si, estoy seguro',
            'No',
            function () {
                quitarPedido(id, idCliente, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlOrder,
            type: 'POST',
            data: data,
            success: function (response) {
                seleccionarPedidosCliente(idCliente, '', '', '', '', '', '');
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            },
            error: function () {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            }
        });
    }
}

function guardarPedidoSinUsuario(idProduct, cant, total) {
    const cliente = {
        nombre: $('#Nombre').val(),
        segundoNombre: $('#segundoNombre').val(),
        primerApellido: $('#primerApellido').val(),
        segundoApellido: $('#segundoApellido').val(),
        provincia: $('#Provincia').val(),
        canton: $('#Canton').val(),
        distrito: $('#Distrito').val(),
        telefono: $('#Telefono').val(),
    };

    const clienteNombre = cliente.nombre;
    const clienteSegundoNombre = cliente.segundoNombre;
    const clientePrimerApellido = cliente.primerApellido;
    const clienteSegundoApellido = cliente.segundoApellido;
    const clienteProvincia = cliente.provincia;
    const clienteCanton = cliente.canton;
    const clienteDistrito = cliente.distrito;
    const clienteTelefono = cliente.telefono;

    const producto = idProduct;
    const color = $('#Color').val();
    const colorAccesorio = $('#AccesoryColor').val();
    const cantidad = cant;
    const precio = $('#precio').val();

    if (!validarCampos(
        [clienteNombre, clientePrimerApellido, clienteProvincia, clienteCanton, clienteDistrito],
        ['tú nombre', 'tú primer apellido', 'la provincia', 'el cantón', 'el distrito']
    )) {
        return;
    }

    alertLoadingBlocked(
        'Enviando pedido',
        'Se está enviando el pedido, espere un momento...',
        'warning',
    );
    guardarDatos();

    function guardarDatos() {
        const accion = 'insertarSinUsuario';
        const data = {
            accion: accion,
            clienteNombre: clienteNombre,
            clienteSegundoNombre: clienteSegundoNombre,
            clientePrimerApellido: clientePrimerApellido,
            clienteSegundoApellido: clienteSegundoApellido,
            clienteProvincia: clienteProvincia,
            clienteCanton: clienteCanton,
            clienteDistrito: clienteDistrito,
            clienteTelefono: clienteTelefono,
            producto: producto,
            color: color,
            colorAccesorio: colorAccesorio,
            cantidad: cantidad,
            precio: precio, 
            total: total
        };
        console.log(data);

        $.ajax({
            url: backend + urlOrder,
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
                    'Hubo un problema al intentar guardar el pedido.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}