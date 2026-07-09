let colores_almacenados = [];
let descuentos_almacenados = [];

function guardarProducto() {
    if ($('#Comida').prop('checked') == true) {
        colores_almacenados.length = 0;
        colores_almacenados.push({
            'id': 0,
            'color1': '#FFFFFF',
            'color2': '#FFFFFF',
            'color3': '#FFFFFF',
            'imagen': $('#hiddenImagen1Producto').val(),
            'familia': 'blanco',
        });
    }
    // Definición de variables desde el DOM
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const precio = $('#Precio').val();
    const fichas = $('#FichasRecompensa').val();
    const categoria = $('#Categorias').val();
    const colores = colores_almacenados.map(item => item.id).join(',');
    const descuentos = descuentos_almacenados.map(item => item.id).join(',');
    const altura = $('#Altura').val();
    const peso = $('#Peso').val();
    const imagen1 = $('#hiddenImagen1Producto').val();
    const imagen2 = $('#hiddenImagen2Producto').val();
    const descripcion = $('#Descripcion').val();
    const advertencia = $('#Advertencia').val();
    const festividad = $('#Festividad').val();
    const rareza = $('#Rareza').val();
    const universo = $('#Universo').val();
    const accesorio = $('#Accesorio').val();
    const vectColores = colores_almacenados.map(color => color.imagen);
    const tiempo = $('#Tiempo').val();
    const comida = $('#Comida').prop('checked');
    const existencia = $('#Existencia').prop('checked');

    // Rellena con cadenas vacías hasta alcanzar un total de 20 elementos
    const imagenColores = Array.from({ length: 20 }, (_, i) => vectColores[i] || '');

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Guardando...", 'Espere un momento...', "bi bi-wifi", false);

    if (!$('#Categorias').val() || !$('#Rareza').val() || !$('#Universo').val()){
        cerrarModal('modalGuardando');
        cambiarMensajeModal("#modalGuardando", "Error", 'Todavía se están cargando los clasificadores.', "bi bi-x-circle", true);
        return;
    }

    let arrayResponse = [];

    // Función para guardar datos del producto
    function guardarDatos() {
        return new Promise((resolve, reject) => {
            const accion = id ? 'actualizar' : 'insertar';
            const data = {
                accion: accion,
                nombre: nombre,
                precio: precio,
                fichas: fichas,
                categoria: categoria,
                colores: colores,
                descuentos: descuentos,
                altura: altura,
                peso: peso,
                festividad: !existencia ? festividad : '',
                descripcion: descripcion,
                rareza: rareza,
                universo: universo,
                accesorio: !comida ? accesorio : '',
                advertencia: advertencia,
                tiempo: !comida ? tiempo : '',
                comida: comida ? 1 : 0,
                existencia: existencia ? 1 : 0,
            };

            if (id) {
                data.id = id;
            }

            $.ajax({
                url: backend + urlProduct,
                type: 'POST',
                data: data,
                success: function (response) {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    arrayResponse = data;
                    if (data.icon === 'bi bi-check-circle' && data.producto_id) {
                        resolve(data.producto_id); // Devuelve el ID del producto
                    } else {
                        reject('Error al guardar el producto: ' + data.text);
                    }
                },
                error: function () {
                    reject('Error en la solicitud AJAX');
                }
            });
        });
    }

    // Función para guardar una imagen
    function guardarImagenProducto(id, imagen, campo){
        return new Promise((resolve,reject)=>{

            $.ajax({
                url: backend + urlProduct,
                type: "POST",
                data:{
                    accion: "insertarImagenProducto",
                    id: id,
                    campo: campo,
                    imagen: imagen
                },

                success:function(response){

                    const data =
                        typeof response === "string"
                        ? JSON.parse(response)
                        : response;

                    if(data.icon === "bi bi-check-circle"){
                        resolve();
                    }else{
                        reject(data.text);
                    }
                },

                error:function(){
                    reject();
                }
            });

        });
    }

    guardarDatos()
    .then(async productId => {

        if(imagen1){
            await guardarImagenProducto(
                productId,
                imagen1,
                "imagen_portada"
            );
        }

        if(imagen2){
            await guardarImagenProducto(
                productId,
                imagen2,
                "imagen_galeria"
            );
        }

        for(let index = 0; index < imagenColores.length; index++){

            const imagen = imagenColores[index];

            if(!imagen){
                continue;
            }

            await guardarImagenProducto(
                productId,
                imagen,
                `imagen_color${index + 1}`
            );
        }

    })
    .then(() => {

        cambiarMensajeModal(
            "#modalGuardando",
            arrayResponse.title,
            arrayResponse.text,
            arrayResponse.icon,
            true
        );

        $('#container-progress-bar').hide();

    })
    .catch(error => {

        console.error(error);

        cambiarMensajeModal(
            "#modalGuardando",
            "Error",
            error,
            "bi bi-x-circle",
            true
        );

    });

    // Llamada a las funciones
    //guardarDatos()
    //    .then(productId => {
    //        const totalColores = imagenColores.length;
//
    //        // Función para procesar las imágenes en serie
    //        const procesarImagenes = async () => {
    //            for (let index = 0; index < totalColores; index++) {
    //                const imagen = imagenColores[index];
    //                if (imagen) { // Solo intenta guardar si hay una imagen presente
    //                    try {
    //                        await guardarImagen(productId, imagen, `imagen_color${index + 1}`);
    //                    } catch (error) {
    //                        console.error(`Error al guardar la imagen ${index + 1}: ${error}`);
    //                    }
    //                }
    //            }
    //        };
//
    //        // Llama a la función para procesar las imágenes
    //        return procesarImagenes();
    //    })
    //    .then(() => {
    //        // Mensaje de éxito después de que se guarden todas las imágenes
    //        cambiarMensajeModal(
    //            "#modalGuardando",
    //            arrayResponse.title,
    //            arrayResponse.text,
    //            arrayResponse.icon,
    //            true,
    //        );
    //        $('#container-progress-bar').hide(); // Oculta la barra de progreso al terminar
    //    })
    //    .catch(error => {
    //        cambiarMensajeModal(
    //            "#modalGuardando",
    //            arrayResponse.title,
    //            arrayResponse.text,
    //            arrayResponse.icon,
    //            true,
    //        );
    //        console.error(error);
    //    });
}

function cargarEstrellasProducto(
    calificacion,
    idElement
){

    // convertir a número
    const rating =
        parseFloat(calificacion) || 0;

    const divRating =
        document.getElementById(
            'container-product-stars' +
            idElement.toString()
        );

    if(!divRating){
        return;
    }

    divRating.innerHTML = '';

    for(let i = 1; i <= 5; i++){

        // estrella completa
        if(rating >= i){

            divRating.innerHTML += `
                <i class="
                    bi bi-star-fill
                    text-star
                "></i>
            `;
        }

        // media estrella
        else if(rating >= i - 0.5){

            divRating.innerHTML += `
                <i class="
                    bi bi-star-half
                    text-star
                "></i>
            `;
        }

        // estrella vacía
        else{

            divRating.innerHTML += `
                <i class="
                    bi bi-star
                    text-star
                "></i>
            `;
        }
    }
}

function buscarImagenProducto(idProducto, idExtra = null) {
    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'buscarImagen',
            id: idProducto,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data[0].imagen_portada && data[0].imagen_portada !== '' ? data[0].imagen_portada : '../src/img/app/no_image.png';
                
                const imgElement = document.getElementById(`img-${idProducto}${idExtra ? (`-${idExtra}`) : ''}`);
                const spinnerElement = document.getElementById(`spinner-${idProducto}`);

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

function buscarProducto(id) {
    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function (response) {
            try {
                const producto = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarProducto(producto);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarProducto(producto) {
    if (producto) {
        $('#Nombre').val(producto.nombre);
        $('#Categorias').val(producto.idCategoria);
        $('#Rareza').val(producto.idRareza);
        $('#Universo').val(producto.idUniverso);
        $('#Festividad').val(producto.idFestividad);
        $('#Accesorio').val(producto.idAccesorio);
        $('#Precio').val(producto.precio);
        $('#FichasRecompensa').val(producto.fichas);
        $('#Altura').val(producto.altura);
        $('#Anchura').val(producto.anchura);
        $('#Peso').val(producto.peso);
        $('#Tiempo').val(producto.tiempo);
        $('#Descripcion').val(producto.descripcion);
        $('#Advertencia').val(producto.advertencia);
        $('#Comida').prop('checked', producto.comida == 1 ? true : false);
        $('#Existencia').prop('checked', producto.existencia == 1 ? true : false);

        if (producto.comida == 1) {
            $('#input-col-Accesorio, #input-col-Tiempo, #col-container-colors').addClass('d-none');
        }
        if (producto.existencia == 1) {
            $('#input-col-Festividad').addClass('d-none');
        }

        cargarImagenGuardada(producto.imagen_portada, '#vistaImagen1Producto');
        cargarImagenGuardada(producto.imagen_galeria, '#vistaImagen2Producto');
        $('#hiddenImagen1Producto').val(producto.imagen_portada);
        $('#hiddenImagen2Producto').val(producto.imagen_galeria);

        colores_almacenados = [];
        descuentos_almacenados = [];

        if (producto.idColores) {
            const vectorIdColores = producto.idColores.split(',').map(Number);
            const colorPromises = vectorIdColores.map((id, index) => {
                const imagenColor = producto[`imagen_color${index + 1}`];
                return buscarColorParaProducto(id, imagenColor);
            });
            Promise.all(colorPromises).then((colores) => {
                colores.forEach(color => {
                    seleccionarColor(color.id, color.color1, color.color2, color.color3, color.imagen, color.familia);
                });
                actualizarColoresSeleccionados();
                setTimeout(function () { setProductLoading(false); }, 250);
            }).catch(error => {
                console.error("Error al cargar colores:", error);
            });
        }
        if (producto.idDescuentos) {
            const vectorIdDescuentos = producto.idDescuentos.split(',').map(Number);
            const descuentoPromises = vectorIdDescuentos.map((id) => {
                return buscarDescuentoParaProducto(id);
            });
            Promise.all(descuentoPromises).then((descuentos) => {
                descuentos.forEach(descuento => {
                    seleccionarDescuento(descuento.id, descuento.nombre, descuento.fecha, descuento.descuento);
                });
                actualizarDescuentosSeleccionados();
                setTimeout(function () { setProductLoading(false); }, 250);
            }).catch(error => {
                console.error("Error al cargar descuentos:", error);
            });
        }

        if (producto.idColores && producto.idDescuentos) {
            setProductLoading(false);
        }
    }
}

function eliminarProducto(id, nombre) {

    eliminarRegistro({
        id,
        nombre,
        entidad: ['producto', 'productos' , 'el producto'],
        url: backend + urlProduct,
        callback: aplicarFiltrosProducto
    });
}

function aplicarFiltrosProducto() {
    const nombre = $('#Nombre').val();
    const categoria = $('#Categorias').val();
    const rareza = $('#Rareza').val();
    const universo = $('#Universo').val();
    seleccionarProductos(nombre, categoria, rareza, universo);
}

// CONTROL GLOBAL
let tokenCargaProductos = 0;

async function seleccionarProductos(
    nombre,
    categoria,
    rareza,
    universo
){

    const currentToken = ++tokenCargaProductos;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    const textElement = ["producto", "productos"];

    try{

        const response = await $.ajax({

            url: backend + urlProduct,

            type: 'POST',

            dataType: 'json',

            data: {
                accion: 'listarIds',
                nombre: nombre || '',
                categoria: categoria || '',
                rareza: rareza || '',
                universo: universo || '',
                orden: order
            }
        });

        if(currentToken !== tokenCargaProductos){
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(
            ids.length,
            ['producto', 'productos']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron productos.
                </div>
            `);

            return;
        }

        await cargarProductosProgresivamente(
            ids,
            currentToken
        );

    }
    catch(error){

        console.error(error);

        if(currentToken === tokenCargaProductos){

            container.html(`
                <div class="orders-empty">
                    Error al cargar productos.
                </div>
            `);
        }
    }
}

async function cargarProductosProgresivamente(
    ids,
    currentToken
){

    const container = $('#list-container');

    container.empty();

    for(const item of ids){

        renderProductoSkeleton(item);

        if(currentToken !== tokenCargaProductos){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlProduct,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if(currentToken !== tokenCargaProductos){
                return;
            }

            const producto = response;

            if(!producto){
                continue;
            }

            const productoFinal =
                typeof producto === 'string'
                    ? JSON.parse(producto)
                    : producto;

            $(`#producto-skeleton-${productoFinal.id}`)
                .replaceWith(
                    renderProductoCard(
                        productoFinal,
                        true
                    )
                );
                
            cargarEstrellasProducto(
                productoFinal.calificacion_estrellas,
                productoFinal.id
            );
            
            buscarImagenProducto(
                productoFinal.id
            );
        }
        catch(error){

            console.error(
                'Error cargando producto',
                item.id,
                error
            );
        }
    }
}

function renderProductoCard(
    producto,
    returnHtml = false
){

    const json = encodeURIComponent(
        JSON.stringify(producto)
    );

    const countColores =
        producto.idColores
        ? producto.idColores.split(',').length
        : 0;

    const totalDescuentos =
        producto.idDescuentos
        ? producto.idDescuentos.split(',').length
        : 0;

    let destacado = false;

    if(producto.fecha_destacado){

        const hoy = new Date();

        const fecha = new Date(
            producto.fecha_destacado.split(' ')[0]
        );

        const dias = Math.ceil(
            Math.abs(hoy - fecha)
            /
            (1000 * 60 * 60 * 24)
        );

        destacado = dias <= 7;
    }

    const html = `

        <div
            class="product-admin-card"
            id="producto-${producto.id}"
        >

            <!-- HEADER -->
            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el ${formatearFechaConHora(producto.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${producto.nombre || 'Sin nombre'}
                    </h5>

                </div>

                <div
                    class="
                        product-status
                        ${
                            producto.visible == 1
                            ? 'product-status-visible'
                            : 'product-status-hidden'
                        }
                    "
                >
                    ${
                        producto.visible == 1
                        ? 'Visible'
                        : 'Oculto'
                    }
                </div>

            </div>

            <!-- BODY -->
            <div class="product-admin-body">

                <!-- IMAGEN -->
                <div class="product-admin-image">

                    <img
                        id="img-${producto.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${producto.nombre}"
                    >

                </div>

                <!-- INFORMACIÓN -->
                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Categoría:</span>
                            <strong>
                                ${producto.categoria || '-'}
                            </strong>
                        </div>

                        <div>
                            <span>Rareza:</span>
                            <strong>
                                ${producto.rareza || '-'}
                            </strong>
                        </div>

                        <div>
                            <span>Universo:</span>
                            <strong>
                                ${producto.universo || '-'}
                            </strong>
                        </div>

                        <div>
                            <span>Precio:</span>
                            <strong>
                                ₡${producto.precio || 0}
                            </strong>
                        </div>

                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start">
                            <span>Fichas:</span>
                            <strong>
                                <div class="d-flex gap-1 ms-1">
                                    <p class="mb-0">${producto.fichas || 0}</p>
                                    <img class="fs-4 my-auto" src="../src/img/app/SH_Ficha.png" alt="sh" style="height: 20px;">
                                </div>
                            </strong>
                        </div>

                        <div>
                            <span>Paletas:</span>
                            <strong>
                                ${countColores}
                            </strong>
                        </div>

                        <div>
                            <span>Descuentos:</span>
                            <strong>
                                ${totalDescuentos}
                            </strong>
                        </div>

                        <div>
                            <span>Existencias:</span>
                            <strong>
                                ${producto.existencia == 0 ? 'Por fabricación' : producto.disponibles}
                            </strong>
                        </div>

                        <div>
                            <span>Destacado:</span>

                            <strong
                                class="
                                    ${
                                        destacado
                                        ? 'text-success'
                                        : 'text-danger'
                                    }
                                "
                            >
                                ${
                                    destacado
                                    ? 'Sí'
                                    : 'No'
                                }
                            </strong>

                        </div>

                        <div>

                            <span>Calificación:</span>

                            <strong
                                id="container-product-stars${producto.id}"
                            ></strong>

                        </div>

                    </div>

                </div>

                <!-- ACCIONES -->
                <div class="order-actions">

                    <a
                        href="addProduct.php?id=${producto.id}&accion=actualizar"
                        class="store-filter-btn px-4 px-md-5 px-lg-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 px-md-5 px-lg-4 justify-content-center text-decoration-none"
                        onclick="
                            cambiarVisibilidadProducto(
                                ${producto.visible == 0 ? 1 : 0},
                                '${producto.id}'
                            )
                        "
                    >
                        <i class="bi ${
                            producto.visible == 0
                            ? 'bi-cloud-arrow-up-fill'
                            : 'bi-lock-fill'
                        }"></i>

                        ${
                            producto.visible == 0
                            ? 'Publicar'
                            : 'Ocultar'
                        }
                    </button>

                    <button
                        class="store-filter-btn px-4 px-md-5 px-lg-4 justify-content-center text-decoration-none"
                        onclick="
                            cambiarDestacacidadProducto(
                                ${destacado ? 0 : 1},
                                '${producto.id}'
                            )
                        "
                    >
                        <i class="bi bi-star-fill"></i>

                        ${
                            destacado
                            ? 'Desestacar'
                            : 'Destacar'
                        }
                    </button>

                    <button
                        class="store-filter-btn px-4 px-md-5 px-lg-4 justify-content-center text-decoration-none"
                        onclick="
                            eliminarProducto(
                                ${producto.id},
                                '${producto.nombre}'
                            )
                        "
                    >
                        <i class="bi bi-trash3-fill"></i>
                        Eliminar
                    </button>

                </div>

            </div>

        </div>
    `;

    if(returnHtml){
        return html;
    }

    $('#list-container').append(html);
}

function renderProductoSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="producto-skeleton-${id}"
        >

            <!-- HEADER -->
            <div class="product-admin-header">

                <div>

                    <div class="skeleton-line skeleton-subtitle"></div>

                    <div class="skeleton-line skeleton-title"></div>

                </div>

                <div class="skeleton-badge"></div>

            </div>

            <!-- BODY -->
            <div class="product-admin-body">

                <!-- IMAGEN -->
                <div class="product-admin-image skeleton-box"></div>

                <!-- INFO -->
                <div class="product-info">

                    <div class="product-info-grid">

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

                <!-- ACCIONES -->
                <div class="product-admin-actions">

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                </div>

            </div>

        </div>

    `);
}

function obtenerCategoriasParaProductos(select, all, isImagen = true, acceptNull = false) {
    return $.ajax({
        url: backend + urlCategory,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: '',
            isImagen
        }
        }).then(
            function (response) {

                const categorias = typeof response === 'string' ? JSON.parse(response) : response;

                categorias.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (acceptNull === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Ninguno'
                        })
                    );
                }

                if (all === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Todos'
                        })
                    );
                }

                categorias.forEach(function (categoria) {
                    selectElement.append(
                        $('<option>', {
                            value: all ? categoria.nombre : categoria.id,
                            text: categoria.nombre
                        })
                    );
                }
            );
        },
    );
}

function cargarFiltrosParaTablaColoresModal(tabla) {
    const nombre = $('#NombreColorModal').val();
    const familia = $('#FamiliaColorModal').val();
    obtenerColoresParaProductos(tabla, nombre, familia);
}

let filtrosColores = ['', ''];

function obtenerColoresParaProductos(table, nombre, familia) {
    filtrosColores[0] = nombre;
    filtrosColores[1] = familia;
    toggleLoadingIcon(table, true, 5, 28);
    $.ajax({
        url: backend + urlColor,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre,
            familia: familia
        },
        success: function (response) {
            try {
                const colores = typeof response === 'string' ? JSON.parse(response) : response;

                const colores_filtrados = colores.filter(color =>
                    !colores_almacenados.some(c => c.id === color.id)
                );

                colores_filtrados.sort((a, b) => a.nombre.localeCompare(b.nombre));

                const tableElement = $('#' + table);
                tableElement.empty();

                if (colores_filtrados.length > 0) {
                    colores_filtrados.forEach((color, index) => {
                        const rowHtml = `
                            <div class="palette-selection-card px-3 px-lg-4 px-xl-5 mb-2">

                                <div class="palette-selection-info">

                                    <strong>${color.nombre}</strong>

                                    <span class="palette-family">
                                        ${color.color_familia}
                                    </span>

                                </div>

                                <div class="d-flex align-items-center gap-3">

                                    <div
                                        class="position-relative btn-palette border border-2 border-dark rounded-2"
                                        style="background:${color.codigo_color_principal};"
                                    >
                                        <div
                                            class="position-absolute btn-palette border border-2 border-dark rounded-2
                                            ${!color.codigo_color_terciario ? 'btn-palette-bg-color-2-A' : 'btn-palette-bg-color-2-B'}"
                                            style="background:${color.codigo_color_secundario};"
                                        ></div>

                                        <div
                                            class="position-absolute btn-palette border border-2 border-dark rounded-2
                                            ${!color.codigo_color_terciario ? 'visually-hidden' : 'btn-palette-bg-color-3'}"
                                            style="background:${color.codigo_color_terciario};"
                                        ></div>
                                    </div>

                                    <button
                                        class="store-btn-secondary"
                                        onclick="seleccionarColor('${color.id}', '${color.codigo_color_principal}', '${color.codigo_color_secundario}', '${color.codigo_color_terciario}', '', '${color.color_familia}')"
                                    >
                                        Agregar
                                    </button>

                                </div>

                            </div>
                        `;

                        tableElement.append(rowHtml);
                    });
                } else {
                    tableElement.append('<tr><td class="text-center" colspan="5">No se encontraron colores.</td></tr>');
                }

            } catch (error) {
                toggleLoadingIcon(table, false, 5, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon(table, false, 5, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function cargarFiltrosParaTablaFestividadesModal(tabla) {
    const nombre = $('#NombreFestividadModal').val();
    obtenerFestividadesParaProductos(tabla, nombre);
}

function cargarFiltrosParaTablaRarezasModal(tabla) {
    const nombre = $('#NombreRarezaModal').val();
    obtenerRarezasParaProductos(tabla, nombre);
}

function cargarFiltrosParaTablaUniversosModal(tabla) {
    const nombre = $('#NombreUniversoModal').val();
    obtenerUniversosParaProductos(tabla, nombre);
}

let filtroDescuento = '';
function cargarFiltrosParaTablaDescuentosModal(tabla) {

    const nombre = $('#NombreDescuentoModal').val();

    filtroDescuento = nombre;

    obtenerDescuentosParaProductos(
        tabla,
        nombre
    );
}

function cargarFiltrosParaTablaAccesoriosModal(tabla) {
    const nombre = $('#NombreAccesorioModal').val();
    obtenerDescuentosParaProductos(tabla, nombre);
}

function obtenerDescuentosParaProductos(table, nombre) {
    toggleLoadingIcon(table, true, 5, 28);
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

                const descuentos_filtrados = descuentos.filter(color =>
                    !descuentos_almacenados.some(c => c.id === color.id)
                );

                descuentos_filtrados.sort((a, b) => a.nombre.localeCompare(b.nombre));

                const tableElement = $('#' + table);
                tableElement.empty();

                if (descuentos_filtrados.length > 0) {
                    descuentos_filtrados.forEach((descuento, index) => {
                        const rowHtml = `
                            <div class="palette-selection-card px-3 px-lg-4 px-xl-5">

                                <div class="discount-info">

                                    <div class="discount-name">
                                        ${descuento.nombre}
                                    </div>

                                    <div class="discount-date">
                                        Del ${formarFecha(descuento.fecha_inicial)}
                                        al
                                        ${formarFecha(descuento.fecha_final)}
                                    </div>

                                </div>

                                <div class="d-flex align-items-center gap-3">

                                    <div class="discount-badge">
                                        ${descuento.descuento}%
                                    </div>

                                    <button
                                        onclick="seleccionarDescuento(
                                            '${descuento.id}',
                                            '${descuento.nombre}',
                                            '${'Del ' + formarFecha(descuento.fecha_inicial) + ' al ' + formarFecha(descuento.fecha_final)}',
                                            '${descuento.descuento}'
                                        )"
                                        class="store-btn-secondary"
                                    >
                                        Seleccionar
                                    </button>

                                </div>

                            </div>
                        `;

                        tableElement.append(rowHtml);
                    });
                } else {
                    tableElement.append('<tr><td class="text-center" colspan="5">No se encontraron descuentos.</td></tr>');
                }

            } catch (error) {
                toggleLoadingIcon(table, false, 5, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon(table, false, 5, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function seleccionarColor(id, color1, color2, color3, imagen, familia) {
    if (colores_almacenados.length < 20) {
        colores_almacenados.push({
            'id': id,
            'color1': color1,
            'color2': color2,
            'color3': color3,
            'imagen': imagen,
            'familia': familia,
        });
        actualizarColoresSeleccionados();
    
        // Actualizar la lista de colores en la tabla
        obtenerColoresParaProductos('colors-data-container', filtrosColores[0], filtrosColores[1]);
    } else {
        alert(
            'Error',
            'No puedes seleccionar más de 20 colores',
            'error',
            'Aceptar'
        );
    }
}

function seleccionarFestividad(id, value) {
    $('#textFestividad').val(value);
    $('#hiddenFestividad').val(id);
    $('#modalHolidays').modal('hide');
}

function seleccionarRareza(id, value) {
    $('#textRareza').val(value);
    $('#hiddenRareza').val(id);
    $('#modalRarities').modal('hide');
}

function seleccionarUniverso(id, value) {
    $('#textUniverso').val(value);
    $('#hiddenUniverso').val(id);
    $('#modalUniverses').modal('hide');
}

function seleccionarDescuento(
    id,
    nombre,
    fecha,
    descuento
) {

    const existe = descuentos_almacenados.some(
        d => d.id == id
    );

    if(existe){
        return;
    }

    descuentos_almacenados.push({
        id,
        nombre,
        fecha,
        descuento
    });

    actualizarDescuentosSeleccionados();

    obtenerDescuentosParaProductos(
        'discounts-data-container',
        filtroDescuento
    );
}

function seleccionarAccesorio(id, value) {
    $('#textAccesorio').val(value);
    $('#hiddenAccesorio').val(id);
    $('#modalAccesories').modal('hide');
}

function eliminarColorSeleccionado(id) {
    colores_almacenados = colores_almacenados.filter(color => color.id !== id);
    actualizarColoresSeleccionados();
    obtenerColoresParaProductos('colors-data-container', '', '');
}

function eliminarDescuentoSeleccionado(id) {
    descuentos_almacenados = descuentos_almacenados.filter(descuento => descuento.id.toString() !== id.toString());
    actualizarDescuentosSeleccionados();
        obtenerDescuentosParaProductos(
        'discounts-data-container',
        filtroDescuento
    );
}

function actualizarColoresSeleccionados() {
    const div = $('#colors-selected-data-container');
    const label = $('#labelColorCant');
    const button = $('#btnColors');
    if (colores_almacenados.length >= 20) {
        button.addClass('bg-secondary');
        button.attr('disabled', true);
        label.addClass('text-danger');
        $('#modalColors').modal('hide');
    } else {
        button.removeClass('bg-secondary');
        button.removeAttr('disabled');
        label.removeClass('text-danger');
    }
    div.empty();
    label.empty();
    label.append('Agregados ' + colores_almacenados.length.toString() + ' de 20');

    colores_almacenados.forEach((color, index) => {
        const html = `
            <div class="color-admin-card mb-2">

                <div class="color-admin-header">

                    <p class="color-admin-title">
                        Paleta #${index + 1}
                    </p>

                    <div class="color-admin-actions">

                        <button
                            onclick="moverColorArriba(${index})"
                            class="btn-details text-white border-0 px-2 py-1"
                            ${index === 0 ? 'disabled' : ''}
                        >
                            <i class="bi bi-arrow-up"></i>
                        </button>

                        <button
                            onclick="moverColorAbajo(${index})"
                            class="btn-details text-white border-0 px-2 py-1"
                            ${index === colores_almacenados.length - 1 ? 'disabled' : ''}
                        >
                            <i class="bi bi-arrow-down"></i>
                        </button>

                        <button
                            onclick="eliminarColorSeleccionado('${color.id}')"
                            class="btn-delete text-white border-0 px-2 py-1"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>

                    </div>

                </div>

                <div class="color-admin-body">

                    <div class="color-admin-preview">

                        <img
                            id="vista${color.id}"
                            class="p-1 p-sm-2 p-md-3"
                            src="${color.imagen || ''}"
                            style="
                                display:${color.imagen ? 'block' : 'none'};
                            "
                        >

                    </div>

                    <input
                        type="file"
                        id="imageInput${color.id}"
                        class="form-control"
                    >

                    <div class="color-admin-footer">

                        <div class="d-flex align-items-center gap-3">

                            <div
                                class="position-relative btn-palette border border-2 border-dark rounded-2"
                                style="background:${color.color1};"
                            >

                                <div
                                    class="position-absolute btn-palette border border-2 border-dark rounded-2
                                    ${!color.color3 ? 'btn-palette-bg-color-2-A' : 'btn-palette-bg-color-2-B'}"
                                    style="background:${color.color2};"
                                ></div>

                                <div
                                    class="position-absolute btn-palette border border-2 border-dark rounded-2
                                    ${!color.color3 ? 'visually-hidden' : 'btn-palette-bg-color-3'}"
                                    style="background:${color.color3};"
                                ></div>

                            </div>

                            <span class="color-family">
                                ${color.familia}
                            </span>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        id="hidden${color.id}"
                        value="${color.imagen || ''}"
                    >

                </div>

            </div>
        `;

        div.append(html);
        
        // Añadir el listener para la previsualización de la imagen
        document.getElementById(`imageInput${color.id}`).addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById(`vista${color.id}`);
            const hiddenField = document.getElementById(`hidden${color.id}`);

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    hiddenField.value = e.target.result;

                    const colorIndex = colores_almacenados.findIndex(c => c.id === color.id);
                    if (colorIndex !== -1) {
                        colores_almacenados[colorIndex].imagen = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                hiddenField.value = '';

                const colorIndex = colores_almacenados.findIndex(c => c.id === color.id);
                if (colorIndex !== -1) {
                    colores_almacenados[colorIndex].imagen = '';
                }
            }
        });

    });
}

function actualizarDescuentosSeleccionados() {
    const div = $('#discounts-selected-data-container');

    $('#labelDiscountCant').text(
        descuentos_almacenados.length +
        ' descuentos seleccionados'
    );

    div.empty();

    if (descuentos_almacenados.length > 1) {
        descuentos_almacenados.sort((a, b) => a.nombre.localeCompare(b.nombre, 'es', { sensitivity: 'base' }));
    }
    
    if (descuentos_almacenados.length > 0) {
        descuentos_almacenados.forEach((descuento, index) => {
            const html = `
                <div class="discount-card mb-2">

                    <div class="discount-info">

                        <div class="discount-name">
                            ${descuento.nombre}
                        </div>

                        <div class="discount-date">
                            ${descuento.fecha}
                        </div>

                    </div>

                    <div class="d-flex align-items-center gap-3">

                        <div class="discount-badge">
                            ${descuento.descuento}%
                        </div>

                        <button
                            onclick="eliminarDescuentoSeleccionado('${descuento.id}')"
                            class="btn-delete text-white border-0 px-3 py-2"
                        >
                            Eliminar
                        </button>

                    </div>

                </div>
            `;
    
            div.append(html);
        });
    } else {
        const html = `
            <tr>
                <td scope="col" colspan="5" class="align-middle text-center card-text fw-normal">Ninguno seleccionado</th>
            </tr>
            `;

        div.append(html);
    }
    
}

function moverColorArriba(index) {
    if(index > 0){
        [
            colores_almacenados[index - 1],
            colores_almacenados[index]
        ] =
        [
            colores_almacenados[index],
            colores_almacenados[index - 1]
        ];
        actualizarColoresSeleccionados();
    }
}

function moverColorAbajo(index) {
    if(index < colores_almacenados.length - 1){
        [
            colores_almacenados[index],
            colores_almacenados[index + 1]
        ] =
        [
            colores_almacenados[index + 1],
            colores_almacenados[index]
        ];
        actualizarColoresSeleccionados();
    }
}

function buscarColorParaProducto(id, imagen) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: backend + urlColor,
            type: 'POST',
            data: {
                accion: 'buscar',
                id: id
            },
            success: function (response) {
                try {
                    const color = typeof response === 'string' ? JSON.parse(response) : response;
                    resolve({
                        id: color.id.toString(),
                        color1: color.codigo_color_principal,
                        color2: color.codigo_color_secundario,
                        color3: color.codigo_color_terciario,
                        imagen: imagen,
                        familia: color.color_familia
                    });
                } catch (error) {
                    console.error('Error al procesar la respuesta:', error);
                    reject(error);
                }
            },
            error: function () {
                console.error('Error al procesar la solicitud.');
                reject(new Error("Error en la solicitud AJAX"));
            }
        });
    });
}

function buscarDescuentoParaProducto(id) {
    return new Promise((resolve, reject) => {
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
                    resolve({
                        id: descuento.id.toString(),
                        nombre: descuento.nombre,
                        fecha: 'Del ' + formarFecha(descuento.fecha_inicial) + ' al ' + formarFecha(descuento.fecha_final),
                        descuento: descuento.descuento,
                    });
                } catch (error) {
                    console.error('Error al procesar la respuesta:', error);
                    reject(error);
                }
            },
            error: function () {
                console.error('Error al procesar la solicitud.');
                reject(new Error("Error en la solicitud AJAX"));
            }
        });
    });
}

function cambiarVisibilidadProducto(estado, id) {
    const accion = 'cambiar';
    const data = {
        accion: accion,
        id: id,
        visible: estado,
    };

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Cambiando visibilidad", "Se está cambiando la visibilidad del producto", "bi bi-arrow-clockwise", false);

    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: data,
        success: function (response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            cambiarMensajeModal("#modalGuardando", "Visibilidad cambiada", "Se ha cambiado la visibilidad del producto", "bi bi-check-circle", true);
            aplicarFiltrosProducto();
        },
        error: function () {
            cambiarMensajeModal("#modalGuardando", "¡Error!", "Ha ocurrido un error al tratar de cambiar la visibilidad del producto", "bi bi-x-circle", true);
        }
    });
}

function cambiarDestacacidadProducto(estado, id) {
    const accion = 'cambiarDestacacidad';
    const data = {
        accion: accion,
        id: id,
        isDestacacidad: estado,
    };

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Cambiando estado de destacamiento", "Se está cambiando el estado de destacamiento del producto", "bi bi-arrow-clockwise", false);

    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: data,
        success: function (response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            cambiarMensajeModal("#modalGuardando", "Estado de destacamiento cambiado", "Se ha cambiado el estado de destacamiento del producto", "bi bi-check-circle", true);
            aplicarFiltrosProducto();
        },
        error: function () {
            cambiarMensajeModal("#modalGuardando", "¡Error!", "Ha ocurrido un error al tratar de cambiar el estado de destacamiento del producto", "bi bi-x-circle", true);
        }
    });
}
