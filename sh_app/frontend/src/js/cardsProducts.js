async function obtenerCartasProductos(filtros, random = null) {
    return new Promise((resolve, reject) => {
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
                            <div class="col" id="producto-${producto.id}">
                                <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-sizes">
                                    <div class="card-body card-body-product card-shadow text-decoration-none">
                                        <div class="spinner-border spinner-color m-auto" role="status">
                                            <span class="visually-hidden">Loading...</span>
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

                if (mostrarTarjeta) {
                    // Tarjeta sin imagen inicial, pero con spinner
                    const cardHTML = `
                        <div class="col">
                            <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-sizes position-relative" style="background: ${producto.color_rareza ? producto.color_rareza + '23' : ''}">
                                <a id="product-card-${producto.id}" class="card-body p-0 pb-3 card-body-product card-shadow text-decoration-none d-flex flex-column align-items-center justify-content-between" href="../pages/product.php?nombreProducto=${encodeURIComponent(producto.nombre)}&id=${encodeURIComponent(producto.id)}">
                                    <div class="card-header-bg w-100 pt-2 px-1 pb-0 pt-sm-4 px-sm-2 pb-sm-0" style="background: ${producto.color_rareza ? producto.color_rareza : ''}">
                                        <h4 class="card-product-text-h text-center">${producto.nombre}</h4>
                                        <p class="card-product-text-p">${producto.categoria}</p>
                                    </div>
                                    <div class="m-auto text-star" id="rating-producto${producto.id}"></div>
                                    <div class="position-relative d-flex justify-content-center align-items-center card-img-product-container">
                                        <!-- Spinner mientras se carga la imagen -->
                                        <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${idProducto}" 
                                            style="width: 50px; height: 50px;"></div>
                                        <!-- Imagen del producto (oculta por defecto) -->
                                        <img id="img-${idProducto}" class="d-none product-img-hover" alt="Imagen del Producto">
                                    </div>
                                    ${priceHTML}
                                </a>
                                ${badgeHTML}
                                ${discountHTML}
                            </div>
                        </div>
                    `;
                    cardContainer.innerHTML = cardHTML;

                    // Mostrar estrellas
                    mostrarEstrellasCartaProducto(producto.calificaciones_estrellas, producto.id);

                    // Cargar la imagen del producto después
                    cargarImagenProducto(idProducto);
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

function mostrarEstrellasCartaProducto(calificaciones, idElement) {
    if (!calificaciones) {
        calificaciones = '';
    }
    let totalEstrellas = 0;
    let contador = 0;

    // Remover los corchetes y luego dividir la cadena en pares clave:valor
    const pares = calificaciones.replace(/[{}]/g, '').split(',');

    // Iterar sobre los pares clave:valor
    pares.forEach(par => {
        const [clave, estrellas] = par.split(':'); // Separar clave y valor
        totalEstrellas += parseInt(estrellas); // Sumar las estrellas (valor)
        contador++; // Incrementar el contador
    });

    // Calcular el promedio de estrellas
    const promedioEstrellas = contador > 0 ? totalEstrellas / contador : 0;

    const divRating = document.getElementById('rating-producto' + idElement.toString());
    divRating.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevas estrellas

    // Rellenar las estrellas según el promedio (redondeado)
    for (let i = 1; i <= 5; i++) {
        // Rellenar las estrellas en función del promedio redondeado
        if (i <= Math.round(promedioEstrellas)) {
            divRating.innerHTML += `<i class="bi bi-star-fill"></i>`;
        } else {
            divRating.innerHTML += `<i class="bi bi-star"></i>`;
        }
    }
}

function buscarCartaProducto(id, idCliente) {
    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'buscarCartaProducto',
            id: id
        },
        success: function (response) {
            try {
                const producto = typeof response === 'string' ? JSON.parse(response) : response;

                // pintar datos
                procesarProducto(producto, idCliente);

                // cargar imágenes
                cargarImagenes(producto);

            } catch (error) {
                console.error('Error al procesar:', error);
            }
        },
        error: function () {
            console.error('Error AJAX');
        }
    });
}

function procesarProducto(producto, idCliente) {
    const fechaActual = new Date();
    const anioActual = fechaActual.getFullYear();

    let precioFinal = producto.precio;
    let mejorDescuento = null;

    // Calcular descuentos
    if (producto.descuentos) {
        const vectDescuentos = producto.descuentos.split('|');

        vectDescuentos.forEach(d => {
            const [_, inicio, fin, rebaja] = d.split(',');

            const [mesI, diaI] = inicio.split('-').map(Number);
            const [mesF, diaF] = fin.split('-').map(Number);

            const fechaInicio = new Date(anioActual, mesI - 1, diaI);
            const fechaFin = new Date(
                mesF >= mesI ? anioActual : anioActual + 1,
                mesF - 1,
                diaF
            );

            if (fechaActual >= fechaInicio && fechaActual <= fechaFin) {
                if (!mejorDescuento || rebaja > mejorDescuento.rebaja) {
                    mejorDescuento = {
                        rebaja: parseFloat(rebaja),
                        fechaFin: `${mesF}-${diaF}`
                    };
                }
            }
        });

        if (mejorDescuento) {
            precioFinal = producto.precio - (producto.precio * (mejorDescuento.rebaja / 100));
        }
    }

    // Validad disponibilidad del producto
    let mensajeDisponibilidad = '';

    if (!producto.existencia) {
        if (producto.idFestividad) {
            const [mesI, diaI] = producto.festividad_inicio.split('-').map(Number);
            const [mesF, diaF] = producto.festividad_final.split('-').map(Number);

            const fechaInicio = new Date(anioActual, mesI - 1, diaI);
            const fechaFin = new Date(
                mesF >= mesI ? anioActual : anioActual + 1,
                mesF - 1,
                diaF
            );

            if (fechaActual > fechaFin) {
                mostrarNoDisponible();
                return;
            }

            mensajeDisponibilidad = `Disponible hasta el ${formarFecha(`${mesF}-${diaF}`)}`;
        }
    } else {
        mensajeDisponibilidad = 'Disponible hasta agotar existencias';
    }

    // Estrellas
    renderEstrellas(producto.calificaciones_estrellas);
    const estrellasUsuario = obtenerEstrellasIdCliente(producto.calificaciones_estrellas);

    renderEstrellas(producto.calificaciones_estrellas, estrellasUsuario);

    // Rellenar textos
    $('#nombreProducto').text(producto.nombre);
    $('#nombreCategoria').text(producto.categoria);
    $('#descripcionProducto').text(producto.descripcion);
    document.getElementById("spinner-descripcion").style.display = "none";
    if (producto.advertencia.trim() != "") {
        $('#advertenciasProducto').text(producto.advertencia);
        document.getElementById("spinner-advertencias").style.display = "none";
    } else {
        document.getElementById("row-advertencias").style.display = "none";
    }
    $('#alturaProducto').text(`Cerca de ${producto.altura} cm`);
    document.getElementById("spinner-altura").style.display = "none";
    $('#pesoProducto').text(`Aproximadamente ${producto.peso} kg`);
    document.getElementById("spinner-peso").style.display = "none";
    $('#tiempoProducto').text(`Está hecho en ${producto.tiempo} día${producto.tiempo != 1 ? "s" : ""}`);
    document.getElementById("spinner-tiempo").style.display = "none";

    // Calucar precios
    if (mejorDescuento) {
        $('#nombrePrecio').html(`
            <span style="text-decoration:line-through">₡${producto.precio}</span>
            <span>₡${precioFinal.toFixed(0)}</span>
        `);

        $('#descuento').text(`-${mejorDescuento.rebaja}% de descuento`);
        $('#tiempoDescuento').text(`El descuento termina hasta el ${formarFecha(mejorDescuento.fechaFin)}`);
    } else {
        $('#nombrePrecio').text(`₡${producto.precio}`);
        $('#descuento').text('');
        $('#tiempoDescuento').text('');
    }

    // Mostrar disponibilidad
    $('#disponibilidad').text(mensajeDisponibilidad);

    // Total
    $('#cantidad').off('change').on('change', function () {
        calcularTotal(precioFinal);
    });

    calcularTotal(precioFinal);

    // Comentarios
    seleccionarComentariosPorIdProducto(producto.id);
    document.getElementById("spinner-comentarios").style.display = "none";

    if (idCliente == "") {
        document.getElementById("row-comentario").style.display = "none";
    }

    // Evento de paletas de colores
    window.mostrarColorImagen = function (index, id) {
        $('#product-color-image').attr('src', producto[`imagen_color${index + 1}`]);
        $('#Color').val(id);
        $('#NumColor').val(index + 1);
    }

    window.mostrarColorImagenAccesorio = function (index, id) {
        $('#accesory-color-image').attr('src', producto[`imagen_accesorio_color${index + 1}`]);
        $('#AccesoryColor').val(id);
        $('#NumAccesoryColor').val(index + 1);
    }

    // Acción de guardar pedido
    if (idCliente) {
        $('#btnAccionProducto').text('Agregar a pedidos')
            .off().on('click', () => guardarPedido(producto.id));
    } else {
        $('#btnAccionProducto').text('Reservar')
            .off().on('click', () => irReservar(producto.id, producto.idAccesorio));
    }

    // Botón de guardar calificación de estrellas
    const btn = $('#save-rating');
    const texto = $('#texto-boton-rating');
    const icono = $('#icono-boton-rating');

    btn.off('click'); // limpiar eventos anteriores

    if (idCliente) {
        // Usuario logueado
        texto.text('Guardar calificación');
        icono.attr('class', 'bi bi-pencil-square ms-2 d-flex align-items-center');

        btn.on('click', function () {
            guardarEstrellas();
        });

    } else {
        // Usuario NO logueado
        texto.text('Iniciar sesión');
        icono.attr('class', 'bi bi-person-fill ms-2 d-flex align-items-center');

        btn.on('click', function () {
            irLogin();
        });
    }

    if (idCliente == "") {
        document.getElementById("row-calificacion").style.display = "none";
    }

    //Buscar productos relacionados
    const cartaProductosFiltrosDefecto = {
        nombre: '',
        categorias: [],
        precio: [],
        festividades: [],
        rarezas: [],
        universos: [producto.idUniverso],
    };
    obtenerCartasProductos(cartaProductosFiltrosDefecto);
}

function renderColores(producto) {
    if (!producto.colores) {
        $('#contenedor-colores').html('');
        return;
    }

    let html = '';

    const coloresArray = producto.colores.split('|');
    const ids = producto.idColores.split(',').map(Number);

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
            <div class="d-flex flex-column align-items-center mx-2" style="width: 48px">
                
                <div class="color-preview"
                     style="background: ${codigo_color_principal}; cursor:pointer;"
                     onclick="mostrarColorImagen(${index}, ${id})">
                     
                    <div class="color-secondary"
                         style="background: ${codigo_color_secundario};">
                    </div>
                    
                    <div class="color-terciary"
                         style="background: ${codigo_color_terciario};">
                    </div>
                </div>

                <span class="mt-1 text-center">${color_familia}</span>
            </div>
        `;
    });

    $('#contenedor-colores').html(html);

    // Seleccionar el primero automáticamente
    if (ids.length > 0) {
        $('#Color').val(ids[0]);
        $('#NumColor').val(1);
    }
}

function renderColoresAccesorio(producto) {
    if (!producto.colores_accesorio) {
        $('#contenedor-colores-accesorio').html('');
        return;
    }

    let html = '';

    const coloresArray = producto.colores_accesorio.split('|');
    const ids = producto.idColoresAccesorio.split(',').map(Number);

    let dict = {};

    coloresArray.forEach(c => {
        const [id, ...rest] = c.split(',');
        dict[parseInt(id)] = rest;
    });

    ids.forEach((id, index) => {
        if (!dict[id]) return;

        const [p, s, t, nombre] = dict[id];

        html += `
            <div class="d-flex flex-column align-items-center mx-2" style="width: 48px">
                
                <div class="color-preview"
                     style="background: ${p}; cursor:pointer;"
                     onclick="mostrarColorImagenAccesorio(${index}, ${id})">
                     
                    <div class="color-secondary" style="background: ${s};"></div>
                    <div class="color-terciary" style="background: ${t};"></div>
                </div>

                <span class="mt-1 text-center">${nombre}</span>
            </div>
        `;
    });

    $('#contenedor-colores-accesorio').html(html);

    if (ids.length > 0) {
        $('#AccesoryColor').val(ids[0]);
        $('#NumAccesoryColor').val(1);
    }
}

function renderEstrellas(data, estrellasUsuario = 0) {
    mostrarEstrellas(data);

    const contenedor = $('[data-id="opinion"]');

    // Limpiar eventos anteriores
    contenedor.off('click', 'i');

    // Click en estrellas (IMPORTANTE: star y star-fill)
    contenedor.on('click', 'i', function () {
        const selected = $(this).data('star');

        pintarEstrellasUsuario(selected);
        $('#Estrellas').val(selected);
    });

    // Inicializar con estrellas del usuario
    if (estrellasUsuario > 0) {
        pintarEstrellasUsuario(estrellasUsuario);
        $('#Estrellas').val(estrellasUsuario);
    }

    // Botón reset
    $('#reset-rating').off('click').on('click', function () {
        pintarEstrellasUsuario(0);
        $('#Estrellas').val(0);
    });
}

function pintarEstrellasUsuario(valor) {
    const estrellas = $('[data-id="opinion"] i');

    estrellas
        .removeClass('bi-star-fill')
        .addClass('bi-star');

    estrellas.each(function () {
        if ($(this).data('star') <= valor) {
            $(this).removeClass('bi-star').addClass('bi-star-fill');
        }
    });
}

function mostrarNoDisponible() {
    $('#producto-informacion').html(`
        <p class="text-center">Este producto no está disponible</p>
    `);
}

function cargarImagenes(producto) {
    // Imagen principal
    obtenerImagen(producto.id, 'imagen_color1', 'productos', 'id', function (img) {
        producto["imagen_color1"] = img;

        $('#product-color-image').attr('src', img);
        $('#spinner-imagen-portada').hide();
    });

    // Imagen galería
    obtenerImagen(producto.id, 'imagen_galeria', 'productos', 'id', function (img) {
        $('#imagenGaleria').attr('src', `${img}`);
        $('#spinner-imagen-galeria').hide();
    });

    // Colores producto
    for (let i = 2; i <= 20; i++) {
        obtenerImagen(producto.id, `imagen_color${i}`, 'productos', 'id', function (img) {
            producto[`imagen_color${i}`] = `${img}`;
        });
    }
    renderColores(producto);

    // Accesorio
    if (producto.idAccesorio && producto.idAccesorio != "" && producto.idAccesorio != "0") {
        // primera imagen
        obtenerImagen(producto.idAccesorio, 'imagen_color1', 'accesorios', 'id', function (img) {
            producto.imagen_accesorio_color1 = img;

            $('#accesory-color-image').attr('src', img);
            $('#spinner-imagen-accesorio').hide();
        });

        // demás imagenes
        for (let i = 2; i <= 16; i++) {
            obtenerImagen(producto.idAccesorio, `imagen_color${i}`, 'accesorios', 'id', function (img) {
                producto[`imagen_accesorio_color${i}`] = img;
            });
        }

        renderColoresAccesorio(producto);
    } else {
        $('#row-imagen-accesorio').hide();
        $('#row-colores-accesorio').hide();
    }
}

function obtenerImagen(id, columna, tabla, campo, callback) {
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
                const data = typeof res === 'string' ? JSON.parse(res) : res;

                if (data.value && data.value !== 0) {
                    callback(data.value);
                }

            } catch (e) {
                console.error('Error imagen:', e);
            }
        }
    });
}

function mostrarEstrellas(calificaciones) {
    if (!calificaciones || calificaciones === '{}') {
        calificaciones = '';
    }

    let total = 0;
    let count = 0;

    const pares = calificaciones.replace(/[{}]/g, '').split(',');

    pares.forEach(par => {
        if (!par.includes(':')) return;

        const [, estrellas] = par.split(':');
        total += parseInt(estrellas || 0);
        count++;
    });

    const promedio = count > 0 ? total / count : 0;

    const div = document.getElementById('estrellas');
    div.innerHTML = '';

    for (let i = 1; i <= 5; i++) {
        div.innerHTML += i <= Math.round(promedio)
            ? `<i class="bi bi-star-fill"></i>`
            : `<i class="bi bi-star"></i>`;
    }
}

function mostrarComentariosEnProducto(comentarios) {
    const html = document.getElementById('container-comentaries');
    html.innerHTML = '';

    if (comentarios) {
        if (comentarios.length > 0) {
            comentarios.forEach(comentario => {
                html.innerHTML += `
                    <div class="col-auto px-0 mx-auto">
                        <div class="card bg-light">
                            <p class="card-header fw-medium">Usuario ${comentario.idCliente}</p>
                            <div class="card-body">
                                <p class="card-text">${comentario.mensaje}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html.innerHTML += `
                <div class="col-auto px-0 mx-auto w-100">
                    <p class="card-text">No hay comentarios</p>
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

function calcularTotal(precio) {
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
                            <div class="col" id="card-category-${categoria.id}">
                                <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-categories-sizes">
                                    <div class="card-body card-body-product card-shadow text-decoration-none">
                                        <div class="spinner-border spinner-color text-primary m-auto" role="status" style="width: 50px; height: 50px;">
                                            <span class="visually-hidden">Loading...</span>
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
                    <div class="col">
                        <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-categories-sizes position-relative">
                            <a id="category-card-${idCategoria}" class="card-body p-0 card-body-product card-shadow text-decoration-none d-flex flex-column align-items-center justify-content-between card-categories-body" href="../pages/productos.php?nombreCategoria=${encodeURIComponent(categoria.nombre)}&idCategoria=${encodeURIComponent(idCategoria)}">
                                <div class="position-relative d-flex justify-content-center align-items-center card-img-categories-container m-auto p-2">
                                    <div class="spinner-border spinner-color position-absolute" role="status" style="width: 50px; height: 50px;" id="spinner-category-${idCategoria}"></div>
                                    <img class="d-none product-img-hover" id="img-category-${idCategoria}" alt="Imagen de categoría">
                                </div>
                                <div class="w-100 p-0">
                                    <div class="card-categories-footer p-0 clip-path-height-card-category" style="margin-bottom: -1px; calc(100% + 8px)"></div>
                                    <div class="card-categories-footer-container px-1 pb-0 pt-0 px-sm-2 pb-sm-2 pt-sm-0">
                                        <h4 class="card-category-text-h text-center p-0 m-0">${categoria.nombre}</h4>
                                        <p class="card-category-text-p text-start p-0 m-0">${categoria.cantidad ? (parseInt(categoria.cantidad) != 1 ? "Hay " + categoria.cantidad + " productos" : "Solo hay 1 producto") : ""}</p>
                                    </div>
                                    ${categoria.tiene_descuentos_activos == 1 || categoria.tiene_disponibilidad_limitada == 1 || categoria.tiene_existencias_limitadas == 1 ? `
                                        <div class="px-1 py-1 px-sm-1 py-sm-1 px-md-1 py-md-1 px-lg-2 py-lg-2 px-xl-2 py-xl-2 px-xxl-2 py-xxl-2 card-categories-footer-extras">
                                            ${categoria.tiene_descuentos_activos == 1 ? `
                                                <p class="card-category-text-p text-start text-white fw-bolder p-0 m-0">¡Hay descuentos!</p>
                                            ` : ""}
                                            ${categoria.tiene_disponibilidad_limitada == 1 ? `
                                                <p class="card-category-text-p text-start text-white fw-bolder p-0 m-0">¡Hay productos por tiempo limitado!</p>
                                            ` : ""}
                                            ${categoria.tiene_existencias_limitadas == 1 ? `
                                                <p class="card-category-text-p text-start text-white fw-bolder p-0 m-0">¡Hay productos con existencia limitada!</p>
                                            ` : ""}
                                        </div>
                                    ` : ""}
                                </div>
                            </a>
                        </div>
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
                            <div class="col" id="card-universo-${universo.id}">
                                <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-categories-sizes">
                                    <div class="card-body card-body-product card-shadow text-decoration-none">
                                        <div class="spinner-border spinner-color text-primary m-auto" role="status" style="width: 50px; height: 50px;">
                                            <span class="visually-hidden">Loading...</span>
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
                    <div class="col">
                        <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-categories-sizes position-relative">
                            <a class="card-body p-0 card-body-product card-shadow text-decoration-none d-flex flex-column align-items-center justify-content-between card-categories-body" href="../pages/productos.php?nombreUniverso=${encodeURIComponent(universo.nombre)}&idUniverso=${encodeURIComponent(idUniverso)}">
                                <div class="position-relative d-flex justify-content-center align-items-center card-img-categories-container m-auto p-2">
                                    <div class="spinner-border spinner-color position-absolute" role="status" style="width: 50px; height: 50px;" id="spinner-universo-${idUniverso}"></div>
                                    <img class="d-none product-img-hover" id="img-universo-${idUniverso}" alt="Imagen de universo">
                                </div>
                                <div class="w-100 p-0">
                                    <div class="card-categories-footer p-0 clip-path-height-card-category" style="margin-bottom: -1px; calc(100% + 8px)"></div>
                                    <div class="card-categories-footer-container px-1 pb-0 pt-0 px-sm-2 pb-sm-2 pt-sm-0">
                                        <h4 class="card-category-text-h text-center p-0 m-0">${universo.nombre}</h4>
                                        <p class="card-category-text-p text-start p-0 m-0">${universo.cantidad ? (parseInt(universo.cantidad) != 1 ? "Hay " + universo.cantidad + " productos" : "Solo hay 1 producto") : ""}</p>
                                    </div>
                                    ${universo.tiene_descuentos_activos == 1 || universo.tiene_disponibilidad_limitada == 1 || universo.tiene_existencias_limitadas == 1 ? `
                                        <div class="px-1 py-1 px-sm-1 py-sm-1 px-md-1 py-md-1 px-lg-2 py-lg-2 px-xl-2 py-xl-2 px-xxl-2 py-xxl-2 card-categories-footer-extras">
                                            ${universo.tiene_descuentos_activos == 1 ? `
                                                <p class="card-category-text-p text-start text-white fw-bolder p-0 m-0">¡Hay descuentos!</p>
                                            ` : ""}
                                            ${universo.tiene_disponibilidad_limitada == 1 ? `
                                                <p class="card-category-text-p text-start text-white fw-bolder p-0 m-0">¡Hay productos por tiempo limitado!</p>
                                            ` : ""}
                                            ${universo.tiene_existencias_limitadas == 1 ? `
                                                <p class="card-category-text-p text-start text-white fw-bolder p-0 m-0">¡Hay productos con existencia limitada!</p>
                                            ` : ""}
                                        </div>
                                    ` : ""}
                                </div>
                            </a>
                        </div>
                    </div>
                `;
                document.getElementById(`card-universo-${idUniverso}`).innerHTML = cardHTML;

                // Llamamos para cargar la imagen
                cargarImagenUniverso(idUniverso);

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
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}