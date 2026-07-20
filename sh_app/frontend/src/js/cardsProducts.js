let idUser = null;

async function obtenerCartasProductos(filtros, random = null) {
    return new Promise((resolve, reject) => {
        idUser = filtros.idCliente ?? null;
        
        const contenedorProductos = document.getElementById('contenedor-productos');
        const contenedorProductosDestacados = document.getElementById('contenedor-productos-destacados');
        const columnaContenedorProductos = document.getElementById('col-productos-ordinarios');
        const columnaContenedorProductosDestacados = document.getElementById('col-productos-destacados');

        const safeHTML = (el, html) => { if (el) el.innerHTML = html; };
        const safeDisplay = (el, value) => { if (el) el.style.display = value; };

        safeHTML(contenedorProductos, '');
        safeHTML(contenedorProductosDestacados, '');

        // Spinner solo si existe contenedor
        if (contenedorProductos) {
            toggleLoadingIconStoreCard('contenedor-productos', true, [50]);
        }

        const esDestacadoVigente = (fecha) => {
            if (!fecha || fecha === '0000-00-00 00:00:00') return false;

            const fechaDestacado = new Date(fecha);
            const ahora = new Date();

            const limite = new Date(fechaDestacado);
            limite.setDate(limite.getDate() + 7);

            return ahora <= limite;
        };

        $.ajax({
            url: backend + urlCard,
            type: 'POST',
            data: {
                accion: 'contarProductos',
                nombre: filtros.nombre,
                idCategorias: filtros.categorias,
                precio: filtros.precio,
                idFestividades: filtros.festividades,
                idRarezas: filtros.rarezas,
                idUniversos: filtros.universos,
                idCliente: idUser,
                modo: filtros.modo,
                limite: random ? random.limite : null,
            },
            success: function (response) {
                try {
                    const productos = typeof response === 'string' ? JSON.parse(response) : response;
                    resolve(productos);
                    safeHTML(contenedorProductos, '');
                    safeHTML(contenedorProductosDestacados, '');

                    if (!productos || productos.length === 0) {
                        //safeDisplay(columnaContenedorProductos, 'none');
                        safeDisplay(columnaContenedorProductosDestacados, 'none');

                        safeHTML(contenedorProductos, '<p class="card-title w-100 text-center">No hay productos encontrados</p>');
                        return;
                    }

                    productos.sort((a, b) => {
                        const aVigente = esDestacadoVigente(a.fecha_destacado);
                        const bVigente = esDestacadoVigente(b.fecha_destacado);

                        if (aVigente && !bVigente) return -1;
                        if (!aVigente && bVigente) return 1;

                        if (!aVigente && !bVigente) {
                            return a.nombre.localeCompare(b.nombre);
                        }

                        const fechaA = new Date(a.fecha_destacado || 0);
                        const fechaB = new Date(b.fecha_destacado || 0);
                        return fechaB - fechaA;
                    });

                    let hayDestacados = false;
                    let hayOrdinarios = false;

                    productos.forEach(producto => {
                        const esDestacado = esDestacadoVigente(producto.fecha_destacado);

                        const cardHTML = `
                            <div
                                class="col-6 col-sm-4 col-md-3 col-xl-2 mb-4"
                                id="producto-${producto.id}"
                            >
                                <div class="product-card-shadow h-100">
                                    <div class="info-card skeleton-card">

                                        <!-- Imagen -->
                                        <div class="card-img-wrapper skeleton-img" style="background: ${producto.color_rareza || '#ffffff'}15;">
                                            <div class="skeleton-shimmer"></div>
                                        </div>

                                        <!-- Body -->
                                        <div class="card-body-wrapper">
                                            <div class="skeleton-title skeleton-block"></div>
                                            <div class="skeleton-category skeleton-block"></div>
                                            <div class="skeleton-stars skeleton-block"></div>
                                            <div class="price-wrapper mt-auto">
                                                <div class="skeleton-price skeleton-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        if (esDestacado && contenedorProductosDestacados) {
                            contenedorProductosDestacados.innerHTML += cardHTML;
                            hayDestacados = true;
                        } else if (contenedorProductos) {
                            contenedorProductos.innerHTML += cardHTML;
                            hayOrdinarios = true;
                        }
                    });

                    safeDisplay(columnaContenedorProductosDestacados, hayDestacados ? '' : 'none');
                    safeDisplay(columnaContenedorProductos, hayOrdinarios ? '' : 'none');

                    if (!hayDestacados && !hayOrdinarios) {
                        safeHTML(contenedorProductos, '<p class="card-title w-100 text-center">No hay productos encontrados</p>');
                    }

                    cargarProductoSecuencial(productos, 0);

                } catch (error) {
                    reject(error);
                    console.error('Error al procesar la respuesta:', error);
                    safeHTML(contenedorProductos, '<p class="card-title w-100 text-center">No hay productos encontrados</p>');
                }
            },
            error: function (error) {
                reject(error);
                console.error('Error en la solicitud AJAX.');
                if (contenedorProductos) {
                    toggleLoadingIconStoreCard('contenedor-productos', false);
                }
            }
        });
    });
}

function cargarProductoSecuencial(productos, indice) {
    // Verifica si hemos alcanzado el último producto
    if (indice >= productos.length) {
        return;
    }

    // Cargar el producto actual
    const producto = productos[indice];
    mostrarCartaProducto(producto.id, () => {
        // Una vez que el producto actual ha cargado, pasa al siguiente
        cargarProductoSecuencial(productos, indice + 1);
    });
}

function mostrarCartaProducto(idProducto, callback) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarProducto',
            id: idProducto,
            idCliente: idUser,
        },
        success: function (response) {
            try {
                const producto = typeof response === 'string' ? JSON.parse(response) : response;

                const fechaActual = new Date();
                const anioActual = fechaActual.getFullYear();

                // Variables para el div especial
                let badgeHTML = '';
                let discountHTML = '';
                let priceHTML = '';
                let mostrarTarjeta = true;

                if (!producto.existencia) {
                    if (producto.festividad_inicio && producto.festividad_final) {
                        const [mesInicio, diaInicio] = producto.festividad_inicio.split('-').map(Number);
                        const [mesFinal, diaFinal] = producto.festividad_final.split('-').map(Number);

                        const fechaInicio = new Date(anioActual, mesInicio - 1, diaInicio);
                        const fechaFin = new Date(mesFinal >= mesInicio ? anioActual : anioActual + 1, mesFinal - 1, diaFinal);

                        if (fechaActual >= fechaInicio && fechaActual <= fechaFin) {
                            badgeHTML = `<div class="badge-lower-right bg-blue card-product-text-b">¡Por tiempo limitado!</div>`;
                        } else {
                            mostrarTarjeta = false;
                        }
                    } else {
                        const fechaRegistro = new Date(producto.fecha_registro.split(" ")[0]);
                        const diffTime = Math.abs(fechaActual - fechaRegistro);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays <= 3) {
                            badgeHTML = `<div class="badge-lower-right bg-green card-product-text-b">¡Nuevo!</div>`;
                        }
                    }
                } else if (producto.existencia == 1) {
                    badgeHTML = `<div class="badge-lower-right bg-blue card-product-text-b">¡Existencias limitadas!</div>`;
                }

                if (producto.descuentos) {
                    let mejorDescuento = null;
                    const vectDescuentos = (producto.descuentos).split('|');
                    vectDescuentos.forEach(indexDescuento => {
                        const vect = indexDescuento.split(',');
                        const [mesDescuentoInicio, diaDescuentoInicio] = vect[1].split('-').map(Number);
                        const [mesDescuentoFinal, diaDescuentoFinal] = vect[2].split('-').map(Number);
                        const rebaja = parseFloat(vect[3]);
                    
                        const fechaInicio = new Date(anioActual, mesDescuentoInicio - 1, diaDescuentoInicio);
                        const fechaFin = new Date(mesDescuentoFinal >= mesDescuentoInicio ? anioActual : anioActual + 1, mesDescuentoFinal - 1, diaDescuentoFinal);
                    
                        if (fechaActual >= fechaInicio && fechaActual <= fechaFin) {
                            if (!mejorDescuento || rebaja > mejorDescuento.rebaja) {
                                mejorDescuento = { rebaja, precioConDescuento: producto.precio - (producto.precio * (rebaja / 100)) };
                            }
                        }
                    });
                    
                    if (mejorDescuento) {
                        priceHTML = `
                            <div class="card-text-product precio-product d-flex mb-0 gap-2">
                                <p class="card-text text-decoration-line-through card-text-product precio-product m-auto">
                                    ₡${producto.precio}
                                </p>
                                <p class="card-text card-text-product precio-product m-auto">
                                    ₡${mejorDescuento.precioConDescuento.toFixed(0)}
                                </p>
                            </div>
                        `;
                        discountHTML = `<div class="badge-lower-left bg-red">-${mejorDescuento.rebaja}%</div>`;
                    } else {
                        priceHTML = `<p class="card-text card-text-product precio-product">₡${producto.precio}</p>`;
                    }
                } else {
                    priceHTML = `
                        <p class="card-text card-text-product precio-product">₡${producto.precio}</p>
                    `;
                }

                const cardContainer = document.getElementById(`producto-${idProducto}`);

                const heartClass = producto.favorito == 1 ? "bi bi-heart-fill" : "bi bi-heart";
                const buttonClass = producto.favorito == 1 ? "favorite-btn active" : "favorite-btn";  

                if (mostrarTarjeta) {
                    // Tarjeta sin imagen inicial, pero con spinner
                    const cardHTML = `
                            <div class="product-card-shadow h-100">
                                <a
                                    id="product-card-${producto.id}"
                                    class="info-card product-card"
                                    href="../pages/product.php?nombreProducto=${encodeURIComponent(producto.nombre)}&id=${encodeURIComponent(producto.id)}&idCategoria=${encodeURIComponent(producto.idCategoria)}"
                                >
                                    <div
                                        class="card-img-wrapper"
                                        style="background: ${producto.color_rareza || '#ffffff'}22;"
                                    >
                                        <div
                                            class="spinner-border spinner-color position-absolute"
                                            role="status"
                                            id="spinner-${idProducto}"
                                        ></div>

                                        ${idUser ? `
                                            <!-- Botón Favorito -->
                                            <button
                                                class="${buttonClass}"
                                                id="favorite-${producto.id}"
                                                onclick="toggleFavorito(event, ${producto.id})"
                                            >
                                                <i
                                                    id="favorite-icon-${producto.id}"
                                                    class="${heartClass}"
                                                ></i>
                                            </button>
                                        ` : ''}

                                        <!-- Logo del universo -->
                                        <img
                                            class="product-universe-logo d-none"
                                            id="logo-product-${idProducto}"
                                            alt="${producto.universo}"
                                        >

                                        <!-- Imagen principal -->
                                        <img
                                            id="img-${idProducto}"
                                            class="product-main-image d-none p-1 p-sm-2 p-md-3 p-lg-4"
                                            alt="${producto.nombre}"
                                        >

                                        ${badgeHTML}
                                        ${discountHTML}
                                    </div>
                                    <div 
                                        class="card-body-wrapper" 
                                        style="background: ${producto.color_rareza || '#ffffff'};"
                                    >
                                        <h5 class="card-title">
                                            ${producto.nombre}
                                        </h5>
                                        <p class="card-category">
                                            ${producto.categoria}
                                        </p>
                                        <div
                                            class="text-center mb-3 card-star-text"
                                            id="rating-producto${producto.id}"
                                        ></div>
                                        <div class="price-wrapper mt-auto">
                                            ${priceHTML}
                                        </div>
                                    </div>
                                </a>
                            </div>
                    `;
                    cardContainer.innerHTML = cardHTML;

                    // Mostrar estrellas
                    mostrarEstrellasCartaProducto(producto.calificacion_estrellas, producto.id);

                    // Cargar la imagen del producto después
                    cargarImagenProducto(idProducto);
                    cargarLogoProducto(idProducto);
                } else {
                    cardContainer.remove();
                }

                // Callback para cargar el siguiente producto
                if (typeof callback === 'function') {
                    callback();
                }
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                document.getElementById(`producto-${idProducto}`).innerHTML = 'Error al cargar producto';
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            document.getElementById(`producto-${idProducto}`).innerHTML = 'Error al cargar producto';
        }
    });
}

function cargarImagenProducto(idProducto) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarImagenProducto',
            id: idProducto,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data.imagen_portada && data.imagen_portada !== '' ? data.imagen_portada : '../src/img/app/no_image.png';

                const imgElement = document.getElementById(`img-${idProducto}`);
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

function cargarLogoProducto(idProducto) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarLogoProducto',
            id: idProducto,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data.logo && data.logo !== '' ? data.logo : '../src/img/app/no_image.png';

                const imgElement = document.getElementById(`logo-product-${idProducto}`);

                imgElement.src = imagenURL;
                imgElement.classList.remove('d-none');

            } catch (error) {
                console.error('Error al procesar la imagen:', error);
            }
        },
        error: function () {
            console.error('Error al cargar la imagen del producto.');
        }
    });
}

function mostrarEstrellasCartaProducto(calificacion, idElement) {
    // convertir a número
    const rating =
        parseFloat(calificacion) || 0;

    const divRating =
        document.getElementById(
            'rating-producto' +
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

function toggleFavorito(event, idProducto){

    event.preventDefault();
    event.stopPropagation();

    $.ajax({
        url: backend + urlFavorite,
        type: "POST",
        data:{
            accion:"toggle",
            idCliente:idUser,
            idProducto:idProducto
        },

        success:function(response){

            const data = typeof response==="string"
                ? JSON.parse(response)
                : response;

            const boton = document.getElementById(`favorite-${idProducto}`);
            const icono = document.getElementById(`favorite-icon-${idProducto}`);

            if(data.favorito){

                boton.classList.add("active");
                icono.className = "bi bi-heart-fill";

            }else{

                boton.classList.remove("active");
                icono.className = "bi bi-heart";

            }

        }

    });

}

let productoActual = null;

function buscarCartaProducto(id, idCliente, userRol = '') {

    // Mostrar skeleton general
    $('#producto-page-skeleton').removeClass('d-none');

    $('#producto-page-content').addClass('d-none');

    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'buscarCartaProducto',
            id: id
        },

        success: function (response) {

            try {

                const producto =
                    typeof response === 'string'
                        ? JSON.parse(response)
                        : response;

                productoActual = producto;
                productoSeleccionado = producto.id;

                // Procesar información
                procesarProducto(producto, idCliente, userRol);

                // Cargar imágenes
                cargarImagenes(producto);

                // Mostrar contenido
                $('#producto-page-skeleton')
                    .addClass('d-none');

                $('#producto-page-content')
                    .removeClass('d-none');

            } catch (error) {

                console.error(
                    'Error al procesar producto:',
                    error
                );
            }
        },

        error: function (xhr, status, error) {

            console.error(
                'Error AJAX:',
                error
            );

            console.error(xhr.responseText);
        }
    });
}

function procesarProducto(producto, idCliente, userRol) {

    const fechaActual = new Date();

    const anioActual =
        fechaActual.getFullYear();

    let precioFinal = parseFloat(producto.precio);

    let mejorDescuento = null;

    // =====================================
    // DESCUENTOS
    // =====================================

    if (
        producto.descuentos &&
        producto.descuentos.trim() !== ''
    ) {

        const descuentos =
            producto.descuentos.split('|');

        descuentos.forEach(d => {

            const partes = d.split(',');

            if (partes.length < 4) return;

            const [
                _,
                inicio,
                fin,
                rebaja
            ] = partes;

            const [mesI, diaI] =
                inicio.split('-').map(Number);

            const [mesF, diaF] =
                fin.split('-').map(Number);

            const fechaInicio =
                new Date(
                    anioActual,
                    mesI - 1,
                    diaI
                );

            const fechaFin =
                new Date(
                    mesF >= mesI
                        ? anioActual
                        : anioActual + 1,
                    mesF - 1,
                    diaF
                );

            if (
                fechaActual >= fechaInicio &&
                fechaActual <= fechaFin
            ) {

                if (
                    !mejorDescuento ||
                    parseFloat(rebaja) >
                    mejorDescuento.rebaja
                ) {

                    mejorDescuento = {
                        rebaja: parseFloat(rebaja),
                        fechaFin: `${mesF}-${diaF}`
                    };
                }
            }
        });

        if (mejorDescuento) {

            precioFinal =
                producto.precio -
                (
                    producto.precio *
                    (
                        mejorDescuento.rebaja / 100
                    )
                );
        }
    }

    // =====================================
    // DATOS BASE (MODAL)
    // =====================================

    const recompensaFinal =
        mejorDescuento
            ? Math.floor(
                parseFloat(
                    producto.fichas
                ) *
                (
                    1 -
                    (
                        mejorDescuento.rebaja /
                        100
                    )
                )
            )
            : parseInt(
                producto.fichas
            ) || 0;
            
    $('#PrecioBase')
        .val(
            precioFinal
        );

    $('#FichasRecompensa')
        .val(
            recompensaFinal
        );

    // No sobreescribir si viene vacío
    if (
        producto.fichasCliente !==
        undefined
    ) {

        $('#FichasCliente')
            .val(
                producto.fichasCliente
            );
    }

    // =====================================
    // DISPONIBILIDAD
    // =====================================

    let mensajeDisponibilidad = '';

    if (!producto.existencia) {

        if (producto.idFestividad) {

            const [mesI, diaI] =
                producto.festividad_inicio
                    .split('-')
                    .map(Number);

            const [mesF, diaF] =
                producto.festividad_final
                    .split('-')
                    .map(Number);

            const fechaFin =
                new Date(
                    mesF >= mesI
                        ? anioActual
                        : anioActual + 1,
                    mesF - 1,
                    diaF
                );

            if (fechaActual > fechaFin) {

                $('#producto-page-content').html(`
                    <div class="container py-5">

                        <div
                            class="
                                product-not-available-card
                            "
                        >

                            <i
                                class="
                                    bi
                                    bi-x-circle-fill
                                "
                            ></i>

                            <h2 class="mt-3">
                                Este producto
                                no está disponible
                            </h2>

                            <p class="mt-2">
                                El producto ya no
                                se encuentra disponible.
                            </p>

                        </div>

                    </div>
                `);

                $('#producto-page-skeleton')
                    .addClass('d-none');

                $('#producto-page-content')
                    .removeClass('d-none');

                return;
            }

            mensajeDisponibilidad =
                `Disponible hasta el ${
                    formarFecha(
                        `${mesF}-${diaF}`
                    )
                }`;

        }

    } else {

        mensajeDisponibilidad =
            'Disponible hasta agotar existencias';
    }

    // =====================================
    // ESTRELLAS
    // =====================================

    renderEstrellas(
        producto.calificacion_estrellas
    );

    const estrellasUsuario =
        obtenerEstrellasIdCliente(
            producto.calificacion_estrellas
        );

    renderEstrellas(
        producto.calificacion_estrellas,
        estrellasUsuario
    );

    // =====================================
    // INFORMACIÓN GENERAL
    // =====================================

    $('#nombreProducto')
        .text(producto.nombre || '');

    $('#nombreCategoria').html(`
        <span class="product-category-badge">
            ${producto.categoria || ''}
        </span>
    `);

    $('#descripcionProducto')
        .text(producto.descripcion || '');

    // =====================================
    // ADVERTENCIAS
    // =====================================

    if (
        producto.advertencia &&
        producto.advertencia.trim() !== ''
    ) {

        $('#row-advertencias').show();

        $('#advertenciasProducto').html(`
            <div class="product-warning-box">

                <i
                    class="
                        bi
                        bi-exclamation-triangle-fill
                        me-2
                    "
                ></i>

                ${producto.advertencia}

            </div>
        `);

    } else {

        $('#row-advertencias').hide();
    }

    // =====================================
    // CARACTERÍSTICAS
    // =====================================

    $('#alturaProducto').html(`
        <span class="product-feature-value">
            Cerca de ${producto.altura} cm
        </span>
    `);

    $('#pesoProducto').html(`
        <span class="product-feature-value">
            Aproximadamente ${producto.peso} kg
        </span>
    `);

    $('#tiempoProducto').html(`
        <span class="product-feature-value">
            Está hecho en
            ${producto.tiempo}
            día${producto.tiempo != 1 ? 's' : ''}
        </span>
    `);

    // =====================================
    // PRECIOS
    // =====================================

    let rebajaFinal = 0;

    if (mejorDescuento) {

        $('#nombrePrecio').html(`
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="product-old-price text-decoration-line-through">
                    ₡${producto.precio}
                </span>
                <span class="product-new-price">
                    ₡${precioFinal.toFixed(0)}
                </span>
                <span class="product-discount-badge">
                    -${mejorDescuento.rebaja}%
                </span>
            </div>
        `);

        rebajaFinal = mejorDescuento.rebaja;

        $('#descuento').text(`
            ${mejorDescuento.rebaja}%
            de descuento aplicado
        `);

        $('#tiempoDescuento').html(`
            <i
                class="
                    bi
                    bi-clock-fill
                    me-1
                "
            ></i>

            Termina el
            ${formarFecha(
                mejorDescuento.fechaFin
            )}
        `);

        $('#Precio').val(precioFinal);

        if (userRol != '') {
            $('#fichas-section').removeClass('d-none');
        }

        $('#text-fichas').html(`Recompensa: ${(producto.fichas * (1 - (mejorDescuento.rebaja / 100))).toFixed(0) || '0'}`);

    } else {

        $('#nombrePrecio').html(`
            <span class="product-normal-price">
                ₡${producto.precio}
            </span>
        `);

        $('#descuento').text('');

        $('#tiempoDescuento').text('');

        if (userRol != '') {
            $('#fichas-section').removeClass('d-none');
        }

        $('#text-fichas').html(`Recompensa: ${producto.fichas || '0'}`);
    }

    // =====================================
    // DISPONIBILIDAD
    // =====================================

    if (
        mensajeDisponibilidad &&
        mensajeDisponibilidad.length > 0
    ) {

        $('#disponibilidad').html(`
            <span class="product-stock-text">

                <i
                    class="
                        bi
                        bi-check-circle-fill
                        me-1
                    "
                ></i>

                ${mensajeDisponibilidad}

            </span>
        `);
    }

    // =====================================
    // TOTAL
    // =====================================

    $('#cantidad')
    .off('change')
    .on('change', function () {

        calcularTotal(
            precioFinal,
            producto.fichas,
            mejorDescuento
                ? mejorDescuento.rebaja
                : 0
        );

        actualizarModalFichas();
    });

    calcularTotal(precioFinal, producto.fichas, rebajaFinal);

    // =====================================
    // COMENTARIOS
    // =====================================

    seleccionarComentariosPorIdProducto(
        producto.id
    );

    if (!idCliente) {
        $("#btn-comentar").addClass("d-none");
    }

    // =====================================
    // BOTÓN PRINCIPAL
    // =====================================

    if (idCliente) {

        $('#btnAccionProducto')
            .html(`
                Agregar a pedidos
                <i
                    class="
                        bi
                        bi-cart-fill
                        ms-2
                    "
                ></i>
            `)
            .off()
            .on(
                'click',
                //() => guardarPedido(producto.id)
                () => abrirModalFichasUsar(producto.id)
            );

    } else {

        $('#btnAccionProducto')
            .html(`
                Reservar
                <i
                    class="
                        bi
                        bi-box-arrow-in-right
                        ms-2
                    "
                ></i>
            `)
            .off()
            .on(
                'click',
                () =>
                    irReservar(
                        producto.id,
                        producto.idAccesorio
                    )
            );
    }

    // =====================================
    // MOSTRAR CONTENIDO
    // =====================================

    $('#skeleton-info').hide();

    $('#product-info-content')
        .removeClass('d-none');

    // =====================================
    // PRODUCTOS RELACIONADOS
    // =====================================

    const filtros = {
        nombre: '',
        categorias: [producto.idCategoria],
        precio: [],
        festividades: [],
        rarezas: [],
        universos: [producto.idUniverso],
        idCliente: idCliente ?? '',
        modo: '',
    };

    obtenerCartasProductos(filtros);
}

function renderColores(producto) {

    if (!producto.colores) {

        $('#contenedor-colores').html('');
        return;
    }

    let html = '';

    const coloresArray = producto.colores.split('|');

    const ids =
        producto.idColores.split(',').map(Number);

    let dict = {};

    // Crear diccionario
    coloresArray.forEach(c => {

        const [id, ...rest] = c.split(',');

        dict[parseInt(id)] = rest;
    });

    // Generar HTML
    ids.forEach((id, index) => {

        if (!dict[id]) return;

        const [
            codigo_color_principal,
            codigo_color_secundario,
            codigo_color_terciario,
            color_familia
        ] = dict[id];

        html += `
            <div
                class="
                    color-option
                    d-flex
                    flex-column
                    align-items-center
                    p-2
                "
                data-color="${id}"
            >
                <div
                    class="color-preview"
                    style="
                        background:${codigo_color_principal};
                        cursor:pointer;
                    "
                    onclick="mostrarColorImagen(${index}, ${id})"
                >
                    <div
                        class="color-secondary"
                        style="
                            background:${codigo_color_secundario};
                        "
                    ></div>
                    <div
                        class="color-terciary"
                        style="
                            background:${codigo_color_terciario};
                        "
                    ></div>
                </div>
            </div>
        `;
    });

    $('#contenedor-colores').html(html);

    // Seleccionar primero
    if (ids.length > 0) {

        $('#Color').val(ids[0]);

        $('#NumColor').val(1);

        $('.color-option').first().addClass('active');
    }
}

function renderColoresAccesorio(producto) {

    if (!producto.colores_accesorio) {

        $('#contenedor-colores-accesorio').html('');
        return;
    }

    let html = '';

    const coloresArray =
        producto.colores_accesorio.split('|');

    const ids =
        producto.idColoresAccesorio.split(',').map(Number);

    let dict = {};

    coloresArray.forEach(c => {

        const [id, ...rest] = c.split(',');

        dict[parseInt(id)] = rest;
    });

    ids.forEach((id, index) => {

        if (!dict[id]) return;

        const [p, s, t, nombre] = dict[id];

        html += `
            <div
                class="
                    accesory-color-option
                    d-flex
                    flex-column
                    align-items-center
                    p-2
                "
                data-color="${id}"
            >
                <div
                    class="color-preview"
                    style="background:${p}; cursor:pointer;"
                    onclick="mostrarColorImagenAccesorio(${index}, ${id})"
                >
                    <div
                        class="color-secondary"
                        style="background:${s};"
                    ></div>

                    <div
                        class="color-terciary"
                        style="background:${t};"
                    ></div>
                </div>
            </div>
        `;
    });

    $('#contenedor-colores-accesorio').html(html);

    if (ids.length > 0) {

        $('#AccesoryColor').val(ids[0]);

        $('#NumAccesoryColor').val(1);

        $('.accesory-color-option')
            .first()
            .addClass('active');
    }
}

function mostrarColorImagen(index, id) {

    $('#product-color-image')
        .attr(
            'src',
            productoActual[`imagen_color${index + 1}`]
        );

    $('.color-option')
        .removeClass('active');

    $(`.color-option[data-color="${id}"]`)
        .addClass('active');

    $('#Color').val(id);

    $('#NumColor').val(index + 1);
}

function mostrarColorImagenAccesorio(index, id) {

    $('#accesory-color-image')
        .attr(
            'src',
            productoActual[
                `imagen_accesorio_color${index + 1}`
            ]
        );

    $('.accesory-color-option')
        .removeClass('active');

    $(`.accesory-color-option[data-color="${id}"]`)
        .addClass('active');

    $('#AccesoryColor').val(id);

    $('#NumAccesoryColor').val(index + 1);
}

function renderEstrellas(data, estrellasUsuario = 0) {

    mostrarEstrellas(data);

    const contenedor = $('[data-id="opinion"]');

    // Limpiar eventos anteriores
    contenedor.off('click', 'i');

    // Click en estrellas
    contenedor.on('click', 'i', function () {

        const selected = $(this).data('star');

        pintarEstrellasUsuario(selected);

        $('#Estrellas').val(selected);
    });

    // Inicializar estrellas usuario
    if (estrellasUsuario > 0) {

        pintarEstrellasUsuario(estrellasUsuario);

        $('#Estrellas').val(estrellasUsuario);
    }

    // Reset
    $('#reset-rating')
        .off('click')
        .on('click', function () {

            pintarEstrellasUsuario(0);

            $('#Estrellas').val(0);
        });
}

function pintarEstrellasUsuario(valor) {

    const estrellas = $('[data-id="opinion"] i');

    estrellas
        .removeClass('bi-star-fill active-star')
        .addClass('bi-star');

    estrellas.each(function () {

        if ($(this).data('star') <= valor) {

            $(this)
                .removeClass('bi-star')
                .addClass('bi-star-fill active-star');
        }
    });
}

function cargarImagenes(producto) {

    // =====================================
    // IMAGEN PRINCIPAL
    // =====================================

    obtenerImagen(
        producto.id,
        'imagen_color1',
        'productos',
        'id',

        function (img) {

            if (!img) return;

            producto.imagen_color1 = img;

            $('#product-color-image')
                .attr('src', img)
                .removeClass('d-none');

            $('#skeleton-image-main')
                .hide();
        }
    );

    // =====================================
    // GALERÍA
    // =====================================

    obtenerImagen(
        producto.id,
        'imagen_galeria',
        'productos',
        'id',

        function (img) {
            
            if (!img) return;

            $('#imagenGaleria')
                .attr('src', img);

            $('#skeleton-galeria')
                .hide();

            $('#imagenGaleria').removeClass("d-none");
        }
    );

    // =====================================
    // IMÁGENES PRODUCTO
    // =====================================

    for (let i = 2; i <= 20; i++) {

        obtenerImagen(
            producto.id,
            `imagen_color${i}`,
            'productos',
            'id',

            function (img) {

                producto[
                    `imagen_color${i}`
                ] = img;
            }
        );
    }

    // =====================================
    // COLORES PRODUCTO
    // =====================================

    renderColores(producto);

    // =====================================
    // ACCESORIO
    // =====================================

    const tieneAccesorio =
        producto.idAccesorio !== null &&
        producto.idAccesorio !== undefined &&
        producto.idAccesorio !== '' &&
        producto.idAccesorio !== '0' &&
        producto.idAccesorio !== 0;

    if (tieneAccesorio) {

        $('#row-imagen-accesorio')
            .show();

        // ==============================
        // IMAGEN ACCESORIO
        // ==============================

        obtenerImagen(
            producto.idAccesorio,
            'imagen_color1',
            'accesorios',
            'id',

            function (img) {

                if (!img) return;

                producto.imagen_accesorio_color1 =
                    img;

                $('#accesory-color-image')
                    .attr('src', img)
                    .removeClass('d-none');

                $('#skeleton-image-accesorio')
                    .hide();
            }
        );

        // ==============================
        // COLORES ACCESORIO
        // ==============================

        for (let i = 2; i <= 16; i++) {

            obtenerImagen(
                producto.idAccesorio,
                `imagen_color${i}`,
                'accesorios',
                'id',

                function (img) {

                    producto[
                        `imagen_accesorio_color${i}`
                    ] = img;
                }
            );
        }

        renderColoresAccesorio(producto);

    } else {

        $('#row-imagen-accesorio')
            .hide();

        $('#row-colores-accesorio')
            .hide();
    }
}

function obtenerImagen(
    id,
    columna,
    tabla,
    campo,
    callback
) {

    $.ajax({

        url: backend + urlImage,

        type: 'POST',

        data: {
            accion: 'buscarImagen',
            id: id,
            columna: columna,
            tabla: tabla,
            campo: campo
        },

        success: function (res) {

            try {

                const data =
                    typeof res === 'string'
                        ? JSON.parse(res)
                        : res;

                if (
                    data.value &&
                    data.value !== 0
                ) {

                    callback(data.value);
                }

            } catch (e) {

                console.error(
                    'Error imagen:',
                    e
                );
            }
        },

        error: function (
            xhr,
            status,
            error
        ) {

            console.error(
                'Error AJAX imagen:',
                error
            );

            console.error(xhr.responseText);
        }
    });
}

function mostrarEstrellas(calificacion) {
    const rating = parseFloat(calificacion) || 0;
    const divRating = document.getElementById('estrellas');
    if(!divRating){
        return;
    }
    divRating.innerHTML = '';
    for(let i = 1; i <= 5; i++){
        if (rating >= i) {
            divRating.innerHTML += `
                <i class="
                    bi bi-star-fill
                    text-star
                "></i>
            `;
        } else if (rating >= i - 0.5) {
            divRating.innerHTML += `
                <i class="
                    bi bi-star-half
                    text-star
                "></i>
            `;
        } else {
            divRating.innerHTML += `
                <i class="
                    bi bi-star
                    text-star
                "></i>
            `;
        }
    }
}

function mostrarComentariosEnProducto(comentarios) {
    const html = document.getElementById('container-comentaries');
    html.innerHTML = '';

    if (comentarios) {
        if (comentarios.length > 0) {
            comentarios.sort((a, b) => b.id - a.id);
            comentarios.forEach(comentario => {

                // Cantidad de estrellas
                const calificacion = parseInt(comentario.estrellas) || 0;

                // Estrellas llenas
                const estrellasLlenas = `
                    <i class="bi bi-star-fill"></i>
                `.repeat(calificacion);

                // Estrellas vacías
                const estrellasVacias = `
                    <i class="bi bi-star"></i>
                `.repeat(5 - calificacion);

                html.innerHTML += `
                    <div class="col-12 mb-3">
                        <div class="store-comment">
                            <div class="fw-bold mb-2">
                                Usuario ${comentario.idCliente}
                            </div>
                            <div class="fw-bold mb-2 text-star">
                                ${estrellasLlenas}
                                ${estrellasVacias}
                            </div>
                            <div>
                                ${comentario.mensaje}
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html.innerHTML += `
                <div class="col-12">
                    <p class="text-center">
                        No hay comentarios
                    </p>
                </div>
            `;
        }
    }
}

function obtenerEstrellasIdCliente(calificaciones) {
    if (!calificaciones) {
        calificaciones = '';
    }
    const cliente = $('#Sesion').val();
    let estrellasMarcadas = 0;
    // Remover los corchetes y luego dividir la cadena en pares clave:valor
    const pares = calificaciones.replace(/[{}]/g, '').split(',');

    // Iterar sobre los pares clave:valor
    pares.forEach(par => {
        const [clave, estrellas] = par.split(':'); // Separar clave y valor
        if (cliente == clave) {
            estrellasMarcadas = estrellas;
        }
    });
    return parseInt(estrellasMarcadas);
}

function mostrarParteImagen(index, totalPartes, imagenSrc, paletaId) {
    $('#Color').val(paletaId);
    const canvas = document.getElementById('canvas');
    const img = new Image();

    img.onload = function () {
        const parteAncho = img.width / totalPartes; // Dividir el ancho
        const ctx = canvas.getContext('2d');
        canvas.width = parteAncho;
        canvas.height = img.height;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, -parteAncho * index, 0); // Cortar la parte correspondiente

        // Convertir el canvas a una imagen y mostrarla
        const imgElement = document.getElementById('product-color-image');
        imgElement.src = canvas.toDataURL();
    };

    img.src = imagenSrc; // Cargar la imagen desde la base de datos
}

function calcularTotal(precio, fichas, descuento) {
    let cantidad = $('#cantidad').val();
    const inputTotal = $('#total');

    try {
        parseInt(cantidad);
        if (cantidad < 1) {
            cantidad = 1;
        } else if (cantidad > 100) {
            cantidad = 100;
        }
        $('#cantidad').val(cantidad.toString());
    } catch (error) {
        cantidad = 1;
    }

    const label = document.getElementById('labelTotal');
    try {
        const total = parseInt(precio) * parseInt(cantidad);
        const string = 'Total: ₡' + total.toString();
        inputTotal.val(total);
        $('#Total').val(total);
        const fichasFinal = ((fichas * (1 - (descuento / 100))).toFixed(0)) * cantidad || 0;
        $('#text-fichas').html(`Recompensa: ${fichasFinal}`);
        $('#Fichas').val(fichasFinal);

        label.innerHTML = string;
    } catch (error) {
        label.innerHTML = 'Total: ₡' + precio.toString();
        inputTotal.val(precio);
    }

}

function obtenerCartasCategorias(nombre) {
    return new Promise((resolve, reject) => {
        const contenedor = document.getElementById('contenedor-categorias');
        contenedor.innerHTML = '';

        // Mostramos los spinners para todas las categorías primero.
        toggleLoadingIconStoreCard('contenedor-categorias', true, [50]);

        // Primera llamada para contar las categorías
        $.ajax({
            url: backend + urlCard,
            type: 'POST',
            data: {
                accion: 'contarCategorias',
                nombre: nombre,
            },
            success: function (response) {
                try {
                    const categorias = typeof response === 'string' ? JSON.parse(response) : response;
                    resolve(categorias);

                    // Limpiamos el spinner principal y cargamos los spinners por cada tarjeta
                    contenedor.innerHTML = '';
                    categorias.forEach(categoria => {
                        const cardHTML = `
                            <div class="col-12 col-md-6 col-xl-4 mb-4" id="card-category-${categoria.id}">
                                <div class="product-card-shadow h-100">
                                    <div class="info-card skeleton-card">
                                        <div class="card-img-wrapper skeleton-img">
                                            <div class="skeleton-shimmer"></div>
                                        </div>
                                        <div class="card-body-wrapper">
                                            <div class="skeleton-title skeleton-block"></div>
                                            <div class="skeleton-category skeleton-block"></div>
                                            <div class="skeleton-category skeleton-block w-75"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        contenedor.innerHTML += cardHTML;
                    });

                    // Ahora buscamos los detalles de cada categoría y reemplazamos el spinner con la tarjeta
                    categorias.forEach(categoria => {
                        mostrarCartaCategoria(categoria.id);
                    });
                } catch (error) {
                    reject(error);
                    console.error('Error al procesar la respuesta:', error);
                    toggleLoadingIconStoreCard('contenedor-categorias', false); // Mostrar error si hay problema
                }
            },
            error: function (error) {
                reject(error);
                console.error('Error al procesar la solicitud.');
                toggleLoadingIconStoreCard('contenedor-categorias', false); // Mostrar error si falla el AJAX
            }
        });
    });
}

function mostrarCartaCategoria(idCategoria) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarCategoria',
            id: idCategoria,
        },
        success: function (response) {
            try {
                const categoria = typeof response === 'string' ? JSON.parse(response) : response;

                // Crear la tarjeta con spinner y espacio para la imagen
                const cardHTML = `
                    <div class="product-card-shadow h-100">
                        <a
                            id="category-card-${idCategoria}"
                            class="info-card category-card"
                            href="../pages/productos.php?nombreCategoria=${encodeURIComponent(categoria.nombre)}&idCategoria=${encodeURIComponent(idCategoria)}"
                        >

                            <!-- Imagen -->
                            <div class="card-img-wrapper category-img-wrapper">
                                <div
                                    class="spinner-border spinner-color position-absolute"
                                    role="status"
                                    id="spinner-category-${idCategoria}"
                                ></div>

                                <img
                                    class="d-none"
                                    id="img-category-${idCategoria}"
                                    alt="${categoria.nombre}"
                                >
                            </div>

                            <!-- Body -->
                            <div class="card-body-wrapper category-body">
                                <h4 class="card-title">
                                    ${categoria.nombre}
                                </h4>
                                <p class="card-category">
                                    ${
                                        categoria.cantidad
                                        ? (parseInt(categoria.cantidad) != 1
                                            ? `Hay ${categoria.cantidad} productos`
                                            : `Solo hay 1 producto`)
                                        : ''
                                    }
                                </p>

                                ${
                                    categoria.tiene_descuentos_activos == 1 ||
                                    categoria.tiene_disponibilidad_limitada == 1 ||
                                    categoria.tiene_existencias_limitadas == 1
                                    ?
                                    `
                                    <div class="category-extra-info">
                                        ${
                                            categoria.tiene_descuentos_activos == 1
                                            ? `<p>¡Hay descuentos!</p>`
                                            : ''
                                        }

                                        ${
                                            categoria.tiene_disponibilidad_limitada == 1
                                            ? `<p>¡Productos por tiempo limitado!</p>`
                                            : ''
                                        }

                                        ${
                                            categoria.tiene_existencias_limitadas == 1
                                            ? `<p>¡Existencias limitadas!</p>`
                                            : ''
                                        }
                                    </div>
                                    `
                                    : ''
                                }
                            </div>
                        </a>
                    </div>
                `;
                document.getElementById(`card-category-${idCategoria}`).innerHTML = cardHTML;

                // Llamamos para cargar la imagen
                cargarImagenCategoria(idCategoria);

            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                document.getElementById(`card-category-${idCategoria}`).innerHTML = 'Error al cargar categoría';
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            document.getElementById(`card-category-${idCategoria}`).innerHTML = 'Error al cargar categoría';
        }
    });
}

function cargarImagenCategoria(idCategoria) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarImagenCategoria',
            id: idCategoria,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data.imagen && data.imagen !== '' ? data.imagen : '../src/img/app/no_image.png';

                // Actualizamos la imagen en el DOM
                const imgElement = document.getElementById(`img-category-${idCategoria}`);
                const spinnerElement = document.getElementById(`spinner-category-${idCategoria}`);

                // Seteamos la URL y mostramos la imagen
                imgElement.src = imagenURL;
                imgElement.classList.remove('d-none'); // Quitamos la clase que oculta la imagen

                // Esperamos a que la imagen cargue completamente antes de quitar el spinner
                imgElement.onload = () => {
                    if (spinnerElement) spinnerElement.remove(); // Eliminamos el spinner
                };

                imgElement.onerror = () => {
                    if (spinnerElement) spinnerElement.remove(); // Eliminamos el spinner si hay error
                    imgElement.src = '../src/img/app/no_image.png'; // Imagen de fallback
                };
            } catch (error) {
                console.error('Error al procesar la imagen:', error);
            }
        },
        error: function () {
            console.error('Error al cargar la imagen de la categoría.');
        }
    });
}

function obtenerCartasUniversos(nombre) {
    return new Promise((resolve, reject) => {
        const contenedor = document.getElementById('contenedor-universos');
        contenedor.innerHTML = '';

        // Mostramos los spinners para todas los universos primero.
        toggleLoadingIconStoreCard('contenedor-universos', true, [50]);

        // Primera llamada para contar los universos
        $.ajax({
            url: backend + urlCard,
            type: 'POST',
            data: {
                accion: 'contarUniversos',
                nombre: nombre,
            },
            success: function (response) {
                try {
                    const universos = typeof response === 'string' ? JSON.parse(response) : response;
                    resolve(universos);
                    // Limpiamos el spinner principal y cargamos los spinners por cada tarjeta
                    contenedor.innerHTML = '';
                    universos.forEach(universo => {
                        const cardHTML = `
                            <div class="col-6 col-md-4 col-xl-3 mb-4" id="card-universo-${universo.id}">
                                <div class="product-card-shadow h-100">
                                    <div class="info-card skeleton-card">
                                        <div class="card-img-wrapper skeleton-img">
                                            <div class="skeleton-shimmer"></div>
                                        </div>
                                        <div class="card-body-wrapper">
                                            <div class="skeleton-title skeleton-block"></div>
                                            <div class="skeleton-category skeleton-block"></div>
                                            <div class="skeleton-category skeleton-block w-75"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        contenedor.innerHTML += cardHTML;
                    });

                    // Ahora buscamos los detalles de cada universo y reemplazamos el spinner con la tarjeta
                    universos.forEach(universo => {
                        mostrarCartaUniverso(universo.id);
                    });
                } catch (error) {
                    reject(error);
                    console.error('Error al procesar la respuesta:', error);
                    toggleLoadingIconStoreCard('contenedor-universos', false); // Mostrar error si hay problema
                }
            },
            error: function (error) {
                reject(error);
                console.error('Error al procesar la solicitud.');
                toggleLoadingIconStoreCard('contenedor-universos', false); // Mostrar error si falla el AJAX
            }
        });
    });
}

function mostrarCartaUniverso(idUniverso) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarUniverso',
            id: idUniverso,
        },
        success: function (response) {
            try {
                const universo = typeof response === 'string' ? JSON.parse(response) : response;

                // Crear la tarjeta con spinner y espacio para la imagen
                const cardHTML = `
                    <div class="product-card-shadow h-100">
                        <a
                            class="info-card universe-card"
                            href="../pages/productos.php?nombreUniverso=${encodeURIComponent(universo.nombre)}&idUniverso=${encodeURIComponent(idUniverso)}"
                        >

                            <!-- Imagen -->
                            <div class="card-img-wrapper universe-img-wrapper">
                                <div
                                    class="spinner-border spinner-color position-absolute"
                                    role="status"
                                    id="spinner-universo-${idUniverso}"
                                ></div>
                                
                                <!-- Logo -->
                                <img
                                    class="universe-logo d-none"
                                    id="logo-universo-${idUniverso}"
                                    alt="Logo ${universo.nombre}"
                                >

                                <!-- Imagen principal -->
                                <img
                                    class="card-main-image d-none"
                                    id="img-universo-${idUniverso}"
                                    alt="${universo.nombre}"
                                >
                            </div>

                            <!-- Body -->
                            <div class="card-body-wrapper universe-body">
                                <h4 class="card-title">
                                    ${universo.nombre}
                                </h4>
                                <p class="card-category">
                                    ${
                                        universo.cantidad
                                        ? (
                                            parseInt(universo.cantidad) != 1
                                            ? `Hay ${universo.cantidad} productos`
                                            : `Solo hay 1 producto`
                                        )
                                        : ''
                                    }
                                </p>
                                ${
                                    universo.tiene_descuentos_activos == 1 ||
                                    universo.tiene_disponibilidad_limitada == 1 ||
                                    universo.tiene_existencias_limitadas == 1
                                    ?
                                    `
                                    <div class="universe-extra-info">
                                        ${
                                            universo.tiene_descuentos_activos == 1
                                            ? `<p>¡Hay descuentos!</p>`
                                            : ''
                                        }
                                        ${
                                            universo.tiene_disponibilidad_limitada == 1
                                            ? `<p>¡Productos por tiempo limitado!</p>`
                                            : ''
                                        }
                                        ${
                                            universo.tiene_existencias_limitadas == 1
                                            ? `<p>¡Existencias limitadas!</p>`
                                            : ''
                                        }
                                    </div>
                                    `
                                    : ''
                                }
                            </div>
                        </a>
                    </div>
                `;
                document.getElementById(`card-universo-${idUniverso}`).innerHTML = cardHTML;

                // Llamamos para cargar la imagen
                cargarImagenUniverso(idUniverso);
                cargarLogoUniverso(idUniverso);

            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                document.getElementById(`card-universo-${idUniverso}`).innerHTML = 'Error al cargar universo';
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            document.getElementById(`card-universo-${idUniverso}`).innerHTML = 'Error al cargar universo';
        }
    });
}

function cargarImagenUniverso(idUniverso) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarImagenUniverso',
            id: idUniverso,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data.imagen && data.imagen !== '' ? data.imagen : '../src/img/app/no_image.png';

                // Actualizamos la imagen en el DOM
                const imgElement = document.getElementById(`img-universo-${idUniverso}`);
                const spinnerElement = document.getElementById(`spinner-universo-${idUniverso}`);

                // Seteamos la URL y mostramos la imagen
                imgElement.src = imagenURL;
                imgElement.classList.remove('d-none'); // Quitamos la clase que oculta la imagen

                // Esperamos a que la imagen cargue completamente antes de quitar el spinner
                imgElement.onload = () => {
                    if (spinnerElement) spinnerElement.remove(); // Eliminamos el spinner
                };

                imgElement.onerror = () => {
                    if (spinnerElement) spinnerElement.remove(); // Eliminamos el spinner si hay error
                    imgElement.src = '../src/img/app/no_image.png'; // Imagen de fallback
                };
            } catch (error) {
                console.error('Error al procesar la imagen:', error);
            }
        },
        error: function () {
            console.error('Error al cargar la imagen de la categoría.');
        }
    });
}

function cargarLogoUniverso(idUniverso) {
    $.ajax({
        url: backend + urlCard,
        type: 'POST',
        data: {
            accion: 'buscarLogoUniverso',
            id: idUniverso,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data.logo && data.logo !== '' ? data.logo : '../src/img/app/no_image.png';

                // Actualizamos la imagen en el DOM
                const imgElement = document.getElementById(`logo-universo-${idUniverso}`);

                // Seteamos la URL y mostramos la imagen
                imgElement.src = imagenURL;
                imgElement.classList.remove('d-none'); // Quitamos la clase que oculta la imagen

            } catch (error) {
                console.error('Error al procesar la imagen:', error);
            }
        },
        error: function () {
            console.error('Error al cargar la imagen de la categoría.');
        }
    });
}

function guardarEstrellas() {
    const id = $('#Id').val();
    const cliente = $('#Sesion').val();
    const estrellas = $('#Estrellas').val();

    guardarDatos();

    function guardarDatos() {
        const accion = 'guardarEstrellas';
        const data = {
            id: id,
            accion: accion,
            dato: '{' + cliente.toString() + ':' + estrellas.toString() + '}'
        };

        $.ajax({
            url: backend + urlCard,
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
                    'Hubo un problema al guardar tu calificación de estrellas',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function buscarPrevisualizacionProducto(id, isAccesorio, numColorProducto, numColorAccesorio) {
    let divProducto = null;
    let divAccesorio = null;

    divProducto = document.getElementById('div-pre-img-producto');
    divProducto.innerHTML = `
        <div class="spinner-border spinner-color m-auto" role="status" id="spinner-producto" style="width: 32px; height: 32px;"></div>
    `;

    if (isAccesorio != 0 && isAccesorio != null) {
        divAccesorio = document.getElementById('div-pre-img-accesorio');
        divAccesorio.innerHTML = `
            <div class="spinner-border spinner-color m-auto" role="status" id="spinner-accesorio" style="width: 32px; height: 32px;"></div>
        `;
    }

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

                const prdImg = producto['imagen_color' + numColorProducto];
                const accImg = producto['imagen_accesorio_color' + numColorAccesorio];
                
                divProducto.innerHTML = `
                    <img id="img-${producto.id}" src="${prdImg}" class="product-img-hover" alt="Imagen" style="max-width: auto; max-height: 128px;">
                `;

                if (isAccesorio != 0 && isAccesorio != null) {
                    divAccesorio.innerHTML = `
                        <img id="img-${producto.idAccesorio}" src="${accImg}" class="product-img-hover" alt="Imagen" style="max-width: auto; max-height: 128px;">
                    `;
                }
                
                $('#span-nombre-producto').html(`${producto.nombre}`);
                $('#span-categoria').html(`${producto.categoria}`);
                $('#span-universo').html(`${producto.universo}`);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}