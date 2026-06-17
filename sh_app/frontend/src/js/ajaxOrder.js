function guardarPedido(idProduct) {
    const cliente = $('#Sesion').val();
    const producto = idProduct;
    const color = $('#Color').val();
    const colorAccesorio = $('#AccesoryColor').val();
    const cantidad = $('#cantidad').val();
    const precio = $('#Precio').val();
    const total = $('#Total').val();

    guardarDatos();

    function guardarDatos() {
        abrirModal('modalGuardando');
        cambiarMensajeModal("#modalGuardando", 'Guardando...', 'Espere un momento...', 'bi bi-wifi', false);

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
                cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);
            },
            error: function () {
                cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);
            }
        });
    }
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

function eliminarPedido(id, nombre) {
    
    eliminarRegistro({
        id,
        nombre,
        entidad: ['pedido', 'pedidos' , 'el pedido'],
        url: backend + urlOrder,
        callback: aplicarFiltrosPedido
    });
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

let tokenCargaPedidos = 0;

async function seleccionarPedidos(
    cliente = '',
    producto = '',
    categoria = '',
    rareza = '',
    universo = '',
    color = '',
    pagado = '',
    ubicacion = '',
    telefono = ''
){

    const currentToken =
        ++tokenCargaPedidos;

    const container =
        $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const response =
            await $.ajax({

                url: backend + urlOrder,

                type: 'POST',

                dataType: 'json',

                data:{
                    accion:'listarIds',
                    cliente,
                    producto,
                    categoria,
                    rareza,
                    universo,
                    color,
                    pagado,
                    ubicacion,
                    telefono,
                    orden: order
                }
            });

        if(
            currentToken !==
            tokenCargaPedidos
        ){
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(
            ids.length,
            ['pedido','pedidos']
        );

        if(ids.length <= 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron pedidos.
                </div>
            `);

            return;
        }

        await cargarPedidosAdminProgresivamente(
            ids,
            currentToken
        );

    }
    catch(error){

        console.error(error);

        container.html(`
            <div class="orders-empty">
                Error al cargar pedidos.
            </div>
        `);
    }
}

async function cargarPedidosAdminProgresivamente(
    ids,
    currentToken
){

    const container =
        $('#list-container');

    container.empty();

    for(const item of ids){

        renderPedidoAdminSkeleton(item);

        try{

            const response =
                await $.ajax({

                    url: backend + urlOrder,

                    type: 'POST',

                    dataType: 'json',

                    data:{
                        accion:'buscarPorId',
                        id:item
                    }
                });

            if(
                currentToken !==
                tokenCargaPedidos
            ){
                return;
            }

            const pedido = response;

            if(!pedido){
                continue;
            }

            const pedidoFinal =
                typeof pedido === 'string'
                    ? JSON.parse(pedido)
                    : pedido;

            $(
                `#pedido-skeleton-${pedidoFinal.id}`
            ).replaceWith(
                renderPedidoCard(
                    pedidoFinal,
                    true
                )
            );

            const vectColors =
                pedidoFinal.colores.split(',');

            let indexColor = 0;

            vectColors.forEach(
                (color,idColor)=>{
                    if(
                        color ==
                        pedidoFinal.idColor
                    ){
                        indexColor =
                            idColor;
                    }
                }
            );

            buscarImagenPedido(
                pedidoFinal.id,
                indexColor + 1
            );

            if (pedidoFinal.idColorAccesorio != 0) {
                let indexAccesoryColor = 0;

                vectColors.forEach(
                    (color,idColorAccesorio)=>{
                        if(
                            color ==
                            pedidoFinal.idColorAccesorio
                        ){
                            indexAccesoryColor =
                                idColorAccesorio;
                        }
                    }
                );

                buscarImagenAccesorioPedido(
                    pedidoFinal.id,
                    indexAccesoryColor + 1
                );
            }

        }
        catch(error){

            console.error(
                'Error cargando pedido',
                item.id,
                error
            );
        }
    }
}

let pedidoSeleccionado = null;

function renderPedidoCard(
    pedido,
    returnHtml = false
){

    const json =
        encodeURIComponent(
            JSON.stringify(pedido)
        );

    const html = `

    <div
        class="product-admin-card"
        id="pedido-${pedido.id}"
    >

        <div class="product-admin-header">

            <div>

                <p class="product-number">
                    Registrado el ${formatearFechaConHora(pedido.fecha_registro)}
                </p>

                <h5 class="product-title">
                    ${pedido.producto || 'Sin nombre'}
                </h5>

            </div>

            <div
                class="
                    product-status
                    ${
                        pedido.pagado == 1
                        ? 'product-status-visible'
                        : 'product-status-hidden'
                    }
                "
            >

                ${
                    pedido.pagado == 1
                    ? 'Pagado'
                    : 'Pendiente'
                }

            </div>

        </div>

        <div class="product-admin-body">

            <div class="product-admin-image">

                <img
                    id="img-${pedido.id}"
                    class="product-image ${pedido.idColorAccesorio != 0 ? 'w-50' : ''}"
                    src=""
                    alt="${pedido.producto}"
                >

                ${
                    pedido.idColorAccesorio != 0
                    ?
                    `
                    <img
                        id="img-accesory-${pedido.id}"
                        class="product-image w-50"
                        src=""
                        alt="${pedido.producto}"
                    >
                `:''}

            </div>

            <div class="product-info">

                <div class="product-info-grid">

                    <div class="order-progress-wrapper mb-4">
                        <div class="order-progress-header">
                            <strong>
                                ${pedido.progreso || 0}%
                            </strong>
                        </div>
                        <div class="order-progress-bar">
                            <div
                                class="order-progress-fill"
                                style="
                                    width:${pedido.progreso || 0}%;
                                    background:${obtenerColorProgreso( // globalFunctions.js
                                        pedido.progreso || 0
                                    )};
                                "
                            ></div>
                        </div>
                    </div>

                    <div>
                        <span>Cliente:</span>
                        <strong>
                            ${pedido.cliente + ' ' + (pedido.segundo_nombre || '') + pedido.primer_apellido + ' ' + (pedido.segundo_apellido || '')}
                        </strong>
                    </div>

                    <div>
                        <span>Categoría:</span>
                        <strong>
                            ${pedido.categoria || 'Sin registro'}
                        </strong>
                    </div>

                    <div>
                        <span>Rareza:</span>
                        <strong>
                            ${pedido.rareza || 'Sin registro'}
                        </strong>
                    </div>

                    <div>
                        <span>Universo:</span>
                        <strong>
                            ${pedido.universo || 'Sin registro'}
                        </strong>
                    </div>

                    <div>
                        <span>Cantidad:</span>
                        <strong>
                            ${pedido.cantidad || 'Sin registro'}
                        </strong>
                    </div>

                    <div>
                        <span>Total:</span>
                        <strong>
                            ₡${pedido.total || 'Sin registro'}
                        </strong>
                    </div>

                    <div>
                        <span>Ubicación:</span>
                        <strong>
                            ${(pedido.provincia + ', ' || '') + (pedido.canton + ', ' || ' ') + (pedido.distrito + ' ' || ' ')}
                        </strong>
                    </div>

                    <div>
                        <span>Teléfono:</span>
                        <strong>
                            ${pedido.telefono || 'Sin registro'}
                        </strong>
                    </div>

                    <div>
                        <span>Pago:</span>
                        <strong>
                            ${
                                pedido.fecha_registro ==
                                pedido.fecha_pago
                                ? 'Pendiente'
                                : formatearFechaConHora(
                                    pedido.fecha_pago
                                )
                            }
                        </strong>
                    </div>

                </div>

            </div>

            <div class="order-actions">

                ${
                    pedido.pagado == 0
                    ?
                    `
                    <button
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                        onclick="
                            pagarPedido(
                                '${pedido.id}',
                                '${pedido.idProducto}',
                                '${pedido.cantidad}'
                            )
                        "
                    >
                        <i class="bi bi-wallet"></i>
                        Pagar
                    </button>
                    `
                    :
                    ''
                }

                ${
                    pedido.pagado == 0
                    ?
                    `
                    <button
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                        onclick="
                            eliminarPedido(
                                ${pedido.id},
                                '${pedido.producto}'
                            )
                        "
                    >
                        <i class="bi bi-trash3-fill"></i>
                        Eliminar
                    </button>
                    `
                    :
                    ''
                }

                ${
                    pedido.pagado == 0
                    ?
                    `
                    <button
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                        onclick="
                            abrirModalActualizarProgreso(
                                '${pedido.id}',
                                '${pedido.progreso || 0}'
                            )
                        "
                    >
                        <i class="bi bi-arrow-clockwise"></i>
                        Actualizar progreso
                    </button>
                    `
                    :
                    ''
                }

            </div>

        </div>

    </div>
    `;

    if(returnHtml){
        return html;
    }

    $('#list-container')
        .append(html);
}

function renderPedidoAdminSkeleton(id){

    $('#list-container').append(`

        <div
            class="order-admin-card product-skeleton"
            id="pedido-skeleton-${id}"
        >

            <div class="order-admin-header">

                <div>

                    <div class="skeleton-line skeleton-subtitle"></div>

                    <div class="skeleton-line skeleton-title"></div>

                </div>

                <div class="skeleton-badge"></div>

            </div>

            <div class="order-admin-body">

                <div
                    class="
                        order-admin-image
                        skeleton-box
                    "
                ></div>

                <div class="order-info">

                    <div
                        class="
                            order-info-grid
                        "
                    >

                        <div>
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line skeleton-small"></div>
                        </div>

                        <div>
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line skeleton-small"></div>
                        </div>

                        <div>
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line skeleton-small"></div>
                        </div>

                        <div>
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line skeleton-small"></div>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                </div>

            </div>

        </div>

    `);
}

async function seleccionarPedidosCliente(
    cliente,
    producto,
    categoria,
    rareza,
    universo,
    color,
    pagado
){

    // token único
    const currentToken = ++tokenCargaPedidos;

    const container = $('#orders-container');

    container.html(``);

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    }

    const textElement = ["pedido", "pedidos"];

    try{

        const response = await $.ajax({
            url: backend + urlOrder,
            type: 'POST',
            dataType: 'json',
            data: {
                accion: 'listarIdsCliente',
                cliente: cliente || '',
                producto: producto || '',
                categoria: categoria || '',
                rareza: rareza || '',
                universo: universo || '',
                color: color || '',
                pagado: pagado || '',
                orden: order
            }
        });

        // cancelar cargas viejas
        if(currentToken !== tokenCargaPedidos){
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(ids.length, [textElement[0], textElement[1]]);

        container.empty();

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron ${textElement[1]}.
                </div>
            `);

            return;
        }

        await cargarPedidosProgresivamente(
            ids,
            currentToken
        );

    }
    catch(error){

        console.error(error);

        // solo mostrar error si sigue siendo
        // la carga actual
        if(currentToken === tokenCargaPedidos){

            container.html(`
                <div class="orders-empty">
                    Error al cargar los ${textElement[1]}.
                </div>
            `);
        }
    }
}

async function cargarPedidosProgresivamente(
    ids,
    currentToken
){

    // cancelar cargas viejas
    if(currentToken !== tokenCargaPedidos){
        return;
    }

    const container = $('#orders-container');

    container.empty();

    for(const id of ids){

        renderPedidoSkeleton(id);

        if(currentToken !== tokenCargaPedidos){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlOrder,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorIdCliente',
                    id: id
                }
            });

            if(currentToken !== tokenCargaPedidos){
                return;
            }

            const pedido = response;

            if(!pedido){
                continue;
            }

            // evitar duplicados
            if($(`#pedido-${pedido.id}`).length){
                continue;
            }

            $(`#pedido-skeleton-${pedido.id}`).replaceWith(
                renderPedidoCliente(pedido, true)
            );

        }
        catch(error){

            console.error(
                'Error cargando pedido:',
                id,
                error
            );
        }
    }
}

function renderPedidoCliente(pedido, returnHtml = false){

    const container = $('#orders-container');

    const json = encodeURIComponent(
        JSON.stringify(pedido)
    );

    const pagado = pedido.pagado == 1;

    const estadoClass =
        pagado
            ? 'order-status-paid'
            : 'order-status-pending';

    const estadoLabel =
        pagado
            ? 'Pagado'
            : 'Pendiente';

    const html = `

        <div class="order-card" id="pedido-${pedido.id}">

            <!-- HEADER -->
            <div class="order-card-header">
                <div>
                    <p class="order-number">
                        Registrado el ${formatearFechaConHora(pedido.fecha_registro)}
                    </p>
                    <h5 class="order-product">
                        ${pedido.producto}
                    </h5>
                </div>
                <div class="order-status ${estadoClass}">
                    ${estadoLabel}
                </div>
            </div>

            <!-- BODY -->
            <div class="order-card-body">

                <!-- IMÁGENES -->
                <div class="order-product-images">

                    <!-- PRODUCTO -->
                    <div class="order-product-image">
                        <img
                            id="img-${pedido.id}"
                            class="order-image"
                            src="${pedido.imagen_producto || ''}"
                            alt="Producto"
                        >
                    </div>

                    <!-- ACCESORIO -->
                    ${
                        pedido.accesorio
                        ?
                        `
                        <div class="order-product-image">
                            <img
                                class="order-image"
                                src="${pedido.imagen_accesorio || ''}"
                                alt="Accesorio"
                            >
                        </div>
                        `
                        :
                        ''
                    }
                </div>

                <!-- INFO -->
                <div class="order-info">
                    <div class="order-info-grid">
                        <div class="order-progress-wrapper">
                            <div class="order-progress-header">
                                <span>
                                    Progreso
                                </span>
                                <strong>
                                    ${pedido.progreso || 0}%
                                </strong>
                            </div>
                            <div class="order-progress-bar">
                                <div
                                    class="order-progress-fill"
                                    style="
                                        width:${pedido.progreso || 0}%;
                                        background:${obtenerColorProgreso( // globalFunctions.js
                                            pedido.progreso || 0
                                        )};
                                    "
                                ></div>
                            </div>
                        </div>
                        <div>
                            <span>Categoría</span>
                            <strong>
                                ${pedido.categoria || '-'}
                            </strong>
                        </div>
                        <div>
                            <span>Universo</span>
                            <strong>
                                ${pedido.universo || '-'}
                            </strong>
                        </div>
                        <div>
                            <span>Cantidad</span>
                            <strong>
                                ${pedido.cantidad}
                            </strong>
                        </div>
                        <div>
                            <span>Precio unitario</span>
                            <strong>
                                ₡${pedido.precio}
                            </strong>
                        </div>
                        <div>
                            <span>Total</span>
                            <strong class="order-total">
                                ₡${pedido.total}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="order-actions">
                    ${
                        !pagado
                        ?
                        `
                        <button
                            class="store-filter-btn px-4 px-md-5 px-lg-4"
                            onclick="
                                quitarPedido(
                                    ${pedido.id},
                                    ${pedido.idCliente},
                                    '${pedido.producto}',
                                    false
                                )
                            "
                        >
                            <i class="bi bi-x-circle-fill"></i>
                            <span>
                                Cancelar
                            </span>
                        </button>
                        `
                        :
                        ''
                    }
                </div>
            </div>
        </div>
    `;

    if(returnHtml){
        return html;
    }

    container.append(html);
}

function renderPedidoSkeleton(id){

    const container = $('#orders-container');

    container.append(`

        <div
            class="order-card order-skeleton"
            id="pedido-skeleton-${id}"
        >

            <div class="order-card-header">

                <div class="skeleton-line skeleton-title"></div>

                <div class="skeleton-badge"></div>

            </div>

            <div class="order-card-body">

                <div class="order-product-images">

                    <div class="order-product-image skeleton-box"></div>

                </div>

                <div class="order-info">

                    <div class="order-info-grid">

                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>

                    </div>

                </div>

                <div class="order-actions">

                    <div class="skeleton-button"></div>

                </div>

            </div>

        </div>

    `);
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

    abrirModal('modalGuardando');
    cambiarMensajeModal(
        "#modalGuardando",
        "Pagando...",
        "Espere un momento...",
        "bi bi-wifi",
        false
    );

    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: data,
        success: function (response) {
            aplicarFiltrosPedido();
            cambiarMensajeModal(
                "#modalGuardando",
                '¡Pedido pagado!',
                'El pedido se ha pagado correctamente.',
                'bi bi-check-circle',
                true
            );
        },
        error: function () {
            cambiarMensajeModal(
                "#modalGuardando",
                '¡Error!',
                'El pedido no se ha podido pagar.',
                'bi bi-x-circle',
                true
            );
        }
    });
}

let callbackConfirmacion = null;

function quitarPedido(
    id,
    idCliente,
    nombre,
    eliminar
) {

    if (!eliminar) {
        abrirModalConfirmacion({
            titulo: '¿Eliminar pedido?',
            texto:
                `¿Desea eliminar "${nombre}" de tu lista de pedidos?`,
            icono: 'bi bi-trash-fill',
            callback: function () {
                quitarPedido(
                    id,
                    idCliente,
                    nombre,
                    true
                );
            }
        });
        return;
    }

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Eliminado pedido", "Se está eliminado el pedido", "bi bi-trash-fill", false);

    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: {
            accion: 'quitar',
            id: id
        },
        success: function (response) {
            seleccionarPedidosCliente(
                idCliente,
                '',
                '',
                '',
                '',
                '',
                ''
            );
            const data =
                typeof response === 'string'
                    ? JSON.parse(response)
                    : response;
            cambiarMensajeModal("#modalGuardando", "¡Eliminado!", "Se ha eliminado el pedido", "bi bi-x-circle", true);
        },

        error: function () {
            mostrarModalRespuesta({
                title: 'Error',
                text: 'No se pudo eliminar el pedido.',
                icon: 'error'
            });
            cambiarMensajeModal("#modalGuardando", "Eliminado pedido", "Se está eliminado el pedido", "bi bi-x-circle", true);
        }
    });
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

function abrirModalActualizarProgreso(
    id,
    progreso
){

    pedidoSeleccionado = id;

    $('#progreso')
        .val(
            progreso || 0
        );

    $('#texto-progreso')
        .text(
            `${progreso || 0}%`
        );

    abrirModal(
        'modalActualizarProgreso'
    );
}

function actualizarProgresoPedido() {
    if(!pedidoSeleccionado){
        return;
    }

    const progreso =
        $('#progreso').val();

    const accion = 'actualizarProgresoPedido';
    const data = {
        accion: accion,
        id: pedidoSeleccionado,
        progreso: progreso,
    };

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Actulizando avance del pedido", "Se está actualizando el progreso actual del pedido", "bi bi-arrow-clockwise", false);

    $.ajax({
        url: backend + urlOrder,
        type: 'POST',
        data: data,
        success: function (response) {
            cambiarMensajeModal("#modalGuardando", "Progreso actualizado", "Se ha actualizado el progreso del producto", "bi bi-check-circle", true);
            aplicarFiltrosPedido();
        },
        error: function () {
            cambiarMensajeModal("#modalGuardando", "¡Error!", "Ha ocurrido un error al tratar de actualizar el progreso del pedido", "bi bi-x-circle", true);
        }
    });
}