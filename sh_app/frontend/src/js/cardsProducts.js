function obtenerCartasProductos(filtros) {
    const contenedorProductos = document.getElementById('contenedor-productos');
    const contenedorProductosDestacados = document.getElementById('contenedor-productos-destacados');
    contenedorProductos.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos productos

    // Mostrar spinner principal en el contenedor
    toggleLoadingIconStoreCard('contenedor-productos', true, [50]);

    // Primera llamada para obtener los productos
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
        },
        success: function (response) {
            try {
                const productos = typeof response === 'string' ? JSON.parse(response) : response;

                contenedorProductosDestacados.innerHTML = '';

                if (productos) {
                    productos.sort((a, b) => {
                        // Función para saber si el producto sigue siendo destacado
                        const isVigente = (producto) => {
                            if (!producto.fecha_destacado || producto.fecha_destacado === '0000-00-00 00:00:00') {
                                return false;
                            }
                            const fechaDestacado = new Date(producto.fecha_destacado);
                            const ahora = new Date();
                            const seisDiasDespues = new Date(fechaDestacado.getTime() + 6 * 24 * 60 * 60 * 1000);
                            return fechaDestacado <= ahora && seisDiasDespues >= ahora;
                        };
                
                        const aVigente = isVigente(a);
                        const bVigente = isVigente(b);
                
                        // 1. Destacados primero
                        if (aVigente && !bVigente) return -1;
                        if (!aVigente && bVigente) return 1;
                
                        // 2. Si los dos son del mismo grupo (ambos vigentes o ambos no vigentes)
                        //  Si son destacados los dejamos en el orden que vienen (opcional)
                        //  Si no son destacados, ordenamos alfabéticamente
                        if (!aVigente && !bVigente) {
                            if (a.nombre.toLowerCase() < b.nombre.toLowerCase()) return -1;
                            if (a.nombre.toLowerCase() > b.nombre.toLowerCase()) return 1;
                            return 0;
                        }
                
                        // 3. Opcionalmente, entre destacados podrías ordenar por fecha_destacado descendente
                        const fechaA = new Date(a.fecha_destacado || 0);
                        const fechaB = new Date(b.fecha_destacado || 0);
                        return fechaB - fechaA;
                    });
                }

                let hayDestacados = false;
                if (productos) {
                    productos.forEach(producto => {
                        if (producto.fecha_destacado != null) {
                            const fechaActual = new Date();
                            const fechaDestacado = new Date(producto.fecha_destacado.split(" ")[0]);
                            const diffTime = Math.abs(fechaActual - fechaDestacado);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            
                            if (diffDays <= 7) {
                                hayDestacados = true;
                            }
                        }
                    })
                }

                // Limpiar el spinner principal
                contenedorProductos.innerHTML = '';

                // Si hay productos, mostrar spinners para cada uno
                if (productos.length > 0) {
                    productos.forEach(producto => {
                        const cardHTML = `
                            <div class="col" id="producto-${producto.id}">
                                <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-sizes">
                                    <div class="card-body card-body-product card-shadow text-decoration-none">
                                        <div class="spinner-border spinner-color" text-primary m-auto" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        if (producto.fecha_destacado != null) {
                            const fechaActual = new Date();
                            const fechaDestacado = new Date(producto.fecha_destacado.split(" ")[0]);
                            const diffTime = Math.abs(fechaActual - fechaDestacado);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            
                            if (diffDays <= 7) {
                                contenedorProductosDestacados.innerHTML += cardHTML;
                            } else {
                                contenedorProductos.innerHTML += cardHTML;
                            }
                        } else {
                            contenedorProductos.innerHTML += cardHTML;
                        }
                    });

                    cargarProductoSecuencial(productos, 0);
                    if (!hayDestacados) {
                        contenedorProductosDestacados.innerHTML = '<p class="card-title w-100 text-center">No hay productos destacados</p>';
                    }
                } else {
                    contenedorProductos.innerHTML = '<p class="card-title w-100 text-center">No hay productos encontrados</p>';
                    contenedorProductosDestacados.innerHTML = '<p class="card-title w-100 text-center">No hay productos destacados</p>';
                }
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                contenedorProductos.innerHTML = '<p class="card-title w-100 text-center">No hay productos encontrados</p>';
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            toggleLoadingIconStoreCard('contenedor-productos', false); // Mostrar error si falla el AJAX
        }
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
                                <a class="card-body p-0 pb-3 card-body-product card-shadow text-decoration-none d-flex flex-column align-items-center justify-content-between" href="../pages/product.php?nombreProducto=${encodeURIComponent(producto.nombre)}&id=${encodeURIComponent(producto.id)}">
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
    const div = document.getElementById('producto-informacion');
    div.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos elementos.

    // Mostrar un spinner en el contenedor mientras se cargan los datos del producto.
    const spinnerHTML = `
        <div class="d-flex justify-content-center my-5">
            <div class="spinner-border spinner-color" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    div.innerHTML = spinnerHTML;

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
                let html = '';

                let coloresHTML = '';

                // Procesar colores principales
                const coloresArray = producto.colores ? producto.colores.split('|') : null;
                const idColores = producto.colores ? producto.idColores.split(',').map(id => parseInt(id)) : null;

                let coloresDict = {};
                if (coloresArray) {
                    coloresArray.forEach(colorSet => {
                        const [id, ...rest] = colorSet.split(',');
                        coloresDict[parseInt(id)] = rest; // Guardar los valores sin el ID
                    });

                    idColores.forEach((id, index) => {
                        if (coloresDict[id]) {
                            const [codigo_color_principal, codigo_color_secundario, codigo_color_terciario, color_familia] = coloresDict[id];

                            coloresHTML += `
                                <div class="d-flex flex-column align-items-center mx-2" style="width: 48px">
                                    <div class="color-preview" style="background: ${codigo_color_principal};" onclick="mostrarColorImagen(${index}, ${id})">
                                        <div class="color-secondary" style="background: ${codigo_color_secundario};"></div>
                                        <div class="color-terciary" style="background: ${codigo_color_terciario};"></div>
                                    </div>
                                    <span class="mt-1">${color_familia}</span>
                                </div>
                            `;
                        }
                    });
                
                    $('#Color').val(idColores[0]); // Seleccionar el primer color en orden
                    $('#NumColor').val(1);
                } else {
                    $('#Color').val(0);
                    $('#NumColor').val(0);
                }

                let coloresAccesorioHTML = '';
                let isAccesory = true;
                if (coloresArray) {
                    try {
                        const coloresAccesorioArray = producto.colores_accesorio.split('|');
                        const idColoresAccesorio = producto.idColoresAccesorio.split(',').map(id => parseInt(id));

                        // Crear un diccionario de colores con el id como clave
                        let coloresAccesorioDict = {};
                        coloresAccesorioArray.forEach(colorSet => {
                            const [id, ...rest] = colorSet.split(',');
                            coloresAccesorioDict[parseInt(id)] = rest; // Guardar los valores sin el ID
                        });

                        // Recorrer idColoresAccesorio para asegurarse de que se respeten los IDs en orden
                        idColoresAccesorio.forEach((id, index) => {
                            if (coloresAccesorioDict[id]) {
                                const [codigo_color_principal, codigo_color_secundario, codigo_color_terciario, color_familia] = coloresAccesorioDict[id];

                                coloresAccesorioHTML += `
                                    <div class="d-flex flex-column align-items-center mx-2" style="width: 48px">
                                        <div class="color-preview" style="background: ${codigo_color_principal};" onclick="mostrarColorImagenAccesorio(${index}, ${id})">
                                            <div class="color-secondary" style="background: ${codigo_color_secundario};"></div>
                                            <div class="color-terciary" style="background: ${codigo_color_terciario};"></div>
                                        </div>
                                        <span class="mt-1">${color_familia}</span>
                                    </div>
                                `;
                            }
                        });

                        $('#AccesoryColor').val(idColoresAccesorio[0]); // Seleccionar el primer color en orden
                        $('#NumAccesoryColor').val(1);
                    } catch (error) {
                        coloresAccesorioHTML = '';
                        isAccesory = false;
                        $('#NumAccesoryColor').val(0);
                    }
                } else {
                    coloresAccesorioHTML = '';
                    isAccesory = false;
                    $('#NumAccesoryColor').val(0);
                }
                
                const fechaActual = new Date();
                const anioActual = fechaActual.getFullYear();

                let priceHTML = '';
                let totalHTML = `<input onchange="calcularTotal(${producto.precio})" type="number" class="form-control w-auto" id="cantidad" value="1" min="1" max="100" />`;
                let editImageHTML = `
                    <div class="px-2 pb-2 card-text preview-product-card-bg">
                        <p class="card-text">Observación:</p>
                        <p class="card-text">Esta imagen fue editada por computadora. Es para indicar los colores aproximados que tendrá el producto al pedirlo con esta paleta.</p>
                    </div>
                    `;
                let editAccesoryImageHTML = `
                    <div class="px-2 pb-2 card-text preview-product-card-bg">
                        <p class="card-text">Observación:</p>
                        <p class="card-text">Esta imagen fue editada por computadora. Es para indicar el color aproximado que tendrá el accesorio al pedirlo con esta paleta.</p>
                    </div>
                    `;
                let discountHTML = '';
                let timeHTML = '';
                let isDiscount = false;
                let descuentoFinal = 0;

                if (producto.descuentos) {
                    const vectDescuentos = (producto.descuentos).split('|');
                    let mejorDescuento = null;
                    vectDescuentos.forEach(indexDescuento => {
                        const vect = indexDescuento.split(',');
                        const [mesDescuentoInicio, diaDescuentoInicio] = vect[1].split('-').map(Number);
                        const [mesDescuentoFinal, diaDescuentoFinal] = vect[2].split('-').map(Number);
                        const rebaja = parseFloat(vect[3]);
                    
                        const fechaInicio = new Date(anioActual, mesDescuentoInicio - 1, diaDescuentoInicio);
                        const fechaFin = new Date(mesDescuentoFinal >= mesDescuentoInicio ? anioActual : anioActual + 1, mesDescuentoFinal - 1, diaDescuentoFinal);
                    
                        if (fechaActual >= fechaInicio && fechaActual <= fechaFin) {
                            if (!mejorDescuento || rebaja > mejorDescuento.rebaja) {
                                mejorDescuento = {
                                    rebaja,
                                    precioConDescuento: producto.precio - (producto.precio * (rebaja / 100)),
                                    mesDescuentoFinal, // Guarda aquí para usar fuera del forEach
                                    diaDescuentoFinal,
                                };
                                isDiscount = true;
                                descuentoFinal = mejorDescuento.precioConDescuento;
                            }
                        }
                    });

                    if (mejorDescuento) {
                        priceHTML = `
                            <div class="d-flex mb-0 gap-2">
                                <p class="card-text text-decoration-line-through">
                                    ₡${producto.precio}
                                </p>
                                <p class="card-text">
                                    ₡${mejorDescuento.precioConDescuento.toFixed(0)}
                                </p>
                            </div>
                        `;
                        discountHTML = `
                            <div class="text-center" style="width: 64px;">
                                <p class="rounded-pill bg-red text-white fw-bold p-1">
                                    -${mejorDescuento.rebaja}%
                                </p>
                            </div>
                            <p class="text-danger p-0 mb-3">
                                El descuento termina el ${formarFecha(`${mejorDescuento.mesDescuentoFinal}-${mejorDescuento.diaDescuentoFinal}`)}
                            </p>
                        `;
                        totalHTML = `<input onchange="calcularTotal(${mejorDescuento.precioConDescuento})" type="number" class="form-control w-auto" id="cantidad" value="1" min="1" max="100" />`;
                    } else {
                        priceHTML = `<p class="card-text">₡${producto.precio}</p>`;
                    }
                } else {
                    priceHTML = `
                        <p class="card-text">₡${producto.precio}</p>
                    `;
                }

                if (!producto.existencia){
                    if (producto.idFestividad) {
                        const [mesFestividadInicio, diaFestividadInicio] = producto.festividad_inicio.split('-').map(Number);
                        const [mesFestividadFinal, diaFestividadFinal] = producto.festividad_final.split('-').map(Number);
                    
                        const fechaInicio = new Date(anioActual, mesFestividadInicio - 1, diaFestividadInicio);
                        const fechaFin = new Date(mesFestividadFinal >= mesFestividadInicio ? anioActual : anioActual + 1, mesFestividadFinal - 1, diaFestividadFinal);
                    
                        if (fechaActual >= fechaInicio && fechaActual <= fechaFin) {
                            timeHTML = `
                                <p class="card-text text-primary">
                                    Producto solo disponible antes del ${formarFecha(`${mesFestividadFinal}-${diaFestividadFinal}`)}
                                </p>
                            `;
                        } else {
                            timeHTML = ``;
                            div.innerHTML = '<p class="text-center m-auto">¡Este producto ya no está disponible!</p>';
                            return;
                        }
                    } else {
                        timeHTML = ``;
                    }
                } else if (producto.existencia == 1) {
                    timeHTML = `
                        <p class="card-text text-primary">
                            Este producto solo está disponible hasta agotar existencias
                        </p>
                    `;
                }

                div.innerHTML = '';  // Limpiar el contenedor del spinner.
                html = `
                    <div class="row my-3 align-items-center gap-3 mx-auto">
                        <div class="col px-3 container" style="max-width: 512px;">
                            <div class="row my-2">
                                <div class="rounded-2 overflow-hidden preview-product-card-bg preview-product-card-border">
                                    <div class="p-4">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <img class="w-auto h-100 product-page-img overflow-hidden" src="${coloresArray ? producto.imagen_color1 : producto.imagen_portada}" alt="" id="product-color-image">
                                            <canvas id="canvas" style="display: none;"></canvas>
                                            <input type="hidden" class="m-0 p-0" id="idProducto" value="${producto.id}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ${isAccesory ? `
                            <div class="row my-2">
                                <div class="rounded-2 overflow-hidden preview-product-card-bg preview-product-card-border">
                                    <div class="p-4">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <img class="w-auto h-100 product-page-img" src="${producto.imagen_accesorio_color1}" alt="" id="accesory-color-image">
                                            <canvas id="canvas" style="display: none;"></canvas>
                                            <input type="hidden" class="m-0 p-0" id="idAccesorio" value="${producto.id}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        <div class="col px-3">
                            <div class="d-flex flex-column gap-3">
                                <div class="rounded-2 overflow-hidden preview-product-card-border">
                                    <div class="preview-product-card-bg p-0 m-0">
                                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                            <h4 class="card-title">${producto.nombre}</h4>
                                        </div>
                                        <div class="px-2 pb-2">
                                            <p class="card-text">${producto.categoria}</p>
                                            ${priceHTML}
                                            ${discountHTML}
                                            ${timeHTML}
                                            <div class="m-auto text-star" id="rating-producto"></div>
                                        </div>
                                    </div>
                                </div>
                                ${coloresArray ? `
                                    <div class="rounded-2 overflow-hidden preview-product-card-border">
                                        <div class="preview-product-card-bg p-0 m-0">
                                            <div class="preview-product-card-bg-header w-100 px-2 py-1 mb-2">
                                                <p class="card-text">${producto.colores_accesorio ? 'Colores del producto:' : 'Colores:' }</p>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 py-2 justify-content-center align-items-center">
                                                ${coloresHTML}
                                            </div>
                                        </div>
                                        <div class="p-0 m-0" id="isImageEdited"></div>
                                    </div>
                                ` : ''}
                                ${isAccesory ? `
                                <div class="rounded-2 overflow-hidden preview-product-card-border">
                                    <div class="preview-product-card-bg p-0 m-0">
                                        <div class="preview-product-card-bg-header w-100 px-2 py-1 mb-2">
                                            <p class="card-text">Colores del accesorio:</p>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 py-2 justify-content-center align-items-center">
                                            ${coloresAccesorioHTML}
                                        </div>
                                    </div>
                                    <div class="p-0 m-0" id="isAccesoryImageEdited"></div>
                                </div>
                                ` : ''}
                                ${coloresArray ? `
                                    <div class="rounded-2 overflow-hidden preview-product-card-border">
                                        <div class="preview-product-card-bg p-0 m-0">
                                            <div class="preview-product-card-bg-header w-100 px-2 py-1 mb-2">
                                                <p class="card-text">Observación</p>
                                            </div>
                                            <div class="px-2 pb-2">
                                            ${isAccesory ? `
                                                Estas imagenes fueron editadas por computadora. Es para indicar los colores aproximados que tendrán tanto el producto como el accesorio al pedirlos con estas paletas.
                                                ` : `
                                                Esta imagen fue editada por computadora. Es para indicar los colores aproximados que tendrá el producto al pedirlo con esta paleta.
                                            `}
                                            </div>
                                        </div>
                                    </div>
                                ` : ''}
                                <div class="rounded-2 overflow-hidden preview-product-card-border">
                                    <div class="preview-product-card-bg p-0 m-0">
                                        <p class="card-text d-flex align-items-center px-2 pt-2">
                                            <span class="me-2">Cantidad:</span>
                                            ${totalHTML}
                                        </p>
                                        <div class="preview-product-card-bg-footer w-100 px-2 py-1">
                                            <p class="card-text p-0 m-0 fw-bolder" id="labelTotal"></p>
                                        </div>
                                        <input type="hidden" class="m-0 p-0" id="total" />
                                        <input type="hidden" class="m-0 p-0" id="precio" value="${isDiscount ? descuentoFinal : producto.precio}" />
                                    </div>
                                </div>
                                <div class="align-items-center">
                                    <button 
                                        onclick="${idCliente 
                                            ? `guardarPedido(${producto.id})` 
                                            : `alertOption(
                                                '¿Seguro?', 
                                                'Aún no has iniciado sesión. Si continúas, no podrás cancelar tu pedido, pero si puedes anotar tu nombre y otros datos para que nosotros sepamos a quién entregarle tu pedido.', 
                                                'warning', 
                                                () => irReservar(${producto.id}, ${producto.idAccesorio ? producto.idAccesorio : 0})
                                            )`}" 
                                        type="button" 
                                        class="btn-details text-white border-0 rounded-2 px-4 py-2 d-flex align-items-center"
                                    >
                                        ${idCliente ? 'Agregar a pedidos' : 'Reservar'}
                                        <i class="bi bi-cart-fill ms-2 d-flex align-self-center"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Información</h4>
                                    </div>
                                    <p class="card-text px-2 pb-2">${producto.descripcion}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${producto.advertencia.length > 0 ? `
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Advertencias</h4>
                                    </div>
                                    <p class="card-text px-2 pb-2">${producto.advertencia}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Características</h4>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 px-0 pb-2">
                                        <div class="d-flex flex-column align-items-center mx-2">
                                            <div class="w-100 py-2 d-flex gap-2">
                                                <label class="form-label m-0">Alto</label>
                                                <i class="bi bi-rulers d-flex align-self-center"></i>
                                            </div>
                                            <p class="form-label m-0">${parseInt(producto.altura) > 0.9 ? "Cerca de " + producto.altura : "Menos de 1"} cm</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-center mx-2">
                                            <div class="w-100 py-2 d-flex gap-2">
                                                <label class="form-label m-0">Ancho</label>
                                                <i class="bi bi-rulers d-flex align-self-center"></i>
                                            </div>
                                            <p class="form-label m-0">${parseInt(producto.anchura) > 0.9 ? "Cerca de " + producto.anchura : "Menos de 1"} cm</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-center mx-2">
                                            <div class="w-100 py-2 d-flex gap-2">
                                                <label class="form-label m-0">Peso</label>
                                                <i class="bi bi-hammer d-flex align-self-center"></i>
                                            </div>
                                            <p class="form-label m-0">${parseInt(producto.peso) > 0.9 ? "Aproximadamente " + producto.peso : "Menos de 1"} kg</p>
                                        </div>
                                        ${producto.tiempo ?
                                            `
                                            <div class="d-flex flex-column align-items-center mx-2">
                                                <div class="w-100 py-2 d-flex gap-2">
                                                    <label class="form-label m-0">Tiempo</label>
                                                    <i class="bi bi-alarm-fill d-flex align-self-center"></i>
                                                </div>
                                                <p class="form-label m-0">${parseInt(producto.tiempo) > 0.9 ? producto.tiempo + " días de fabricación" : "Menos de 1 día de fabricación"}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Galería</h4>
                                    </div>
                                    <p class="card-text d-flex justify-content-center align-items-center px-2 pb-2">
                                        <img class="w-auto h-100 product-page-img overflow-hidden rounded rounded-4" src="${producto.imagen_galeria}" alt="">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Tú calificación de estrellas</h4>
                                    </div>
                                    <div class="d-flex flex-column align-items-center px-2 pb-2">
                                        <div class="preview-product-card-border rounded-2 p-3" style="background-color:rgb(245, 245, 245)">
                                            <div class="d-flex align-items-center gap-2">
                                                <button id="reset-rating" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                    </svg>
                                                </button>
                                                <div class="m-auto mb-2 text-star" id="rating-opinion" data-id="opinion">
                                                    <i class="bi bi-star star-radio-pointer" data-star="1"></i>
                                                    <i class="bi bi-star star-radio-pointer" data-star="2"></i>
                                                    <i class="bi bi-star star-radio-pointer" data-star="3"></i>
                                                    <i class="bi bi-star star-radio-pointer" data-star="4"></i>
                                                    <i class="bi bi-star star-radio-pointer" data-star="5"></i>
                                                </div>
                                            </div>
                                            <button onclick="${idCliente ? 'guardarEstrellas()' : 'irLogin()'}" id="save-rating" type="button" class="btn-details mx-auto text-white border-0 rounded-2 px-2 py-1 mt-2 d-flex align-items-center">
                                                ${idCliente ? 'Guardar calificación' : 'Iniciar sesión'}
                                                <i class="bi bi-${idCliente ? 'pencil-square' : 'person-fill'} ms-2 d-flex align-items-center"></i>
                                            </button>
                                            <input type="hidden" class="m-0 p-0" id="Estrellas" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${idCliente ? `
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Agregue un comentario</h4>
                                    </div>
                                    <div class="px-2 pb-2">
                                        <textarea class="form-control bi-textarea-resize" id="Comentario" cols="999%" rows="3"></textarea>
                                        <button onclick="guardarComentario()" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 mt-2 d-flex align-items-center">
                                            Enviar
                                            <i class="bi bi-pencil-square ms-2 d-flex align-items-center"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    <div class="row my-3 align-items-center">
                        <div class="col px-3">
                            <div class="rounded-2 overflow-hidden preview-product-card-border">
                                <div class="preview-product-card-bg p-0 m-0">
                                    <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                        <h4 class="card-title">Lista de comentarios</h4>
                                    </div>
                                    <div class="px-2 pb-2">
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination"></ul>
                                        </nav>
                                        <div class="container-fluid row p-0 m-0" id="container-comentaries"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                div.innerHTML += html;

                mostrarEstrellas(producto.calificaciones_estrellas);

                // Definir los eventos de clic solo para las estrellas con data-id="opinion"
                $(document).on('click', '[data-id="opinion"] .bi-star, [data-id="opinion"] .bi-star-fill', function () {
                    const selectedStar = $(this).data('star');

                    // Vaciar todas las estrellas primero
                    $('[data-id="opinion"] .bi-star, [data-id="opinion"] .bi-star-fill').removeClass('bi-star-fill').addClass('bi-star');

                    // Rellenar solo las estrellas hasta la seleccionada
                    $('[data-id="opinion"] .bi-star').each(function () {
                        const currentStar = $(this).data('star');

                        // Si la estrella es menor o igual a la seleccionada, se rellena
                        if (currentStar <= selectedStar) {
                            $(this).removeClass('bi-star').addClass('bi-star-fill');
                        }
                    });

                    // Puedes guardar el valor seleccionado o usarlo en algún cálculo
                    $('#Estrellas').val(selectedStar);
                });

                // Evento para el botón de resetear la calificación
                $('#reset-rating').on('click', function () {
                    // Vaciar todas las estrellas
                    $('[data-id="opinion"] .bi-star, [data-id="opinion"] .bi-star-fill').removeClass('bi-star-fill').addClass('bi-star');

                    // Puedes registrar que la calificación se ha eliminado
                    $('#Estrellas').val(0);
                });

                const estrellasDefault = obtenerEstrellasIdCliente(producto.calificaciones_estrellas);

                if (estrellasDefault) {
                    // Vaciar todas las estrellas primero
                    $('[data-id="opinion"] .bi-star, [data-id="opinion"] .bi-star-fill').removeClass('bi-star-fill').addClass('bi-star');

                    // Rellenar las estrellas según el valor de estrellasDefault
                    $('[data-id="opinion"] .bi-star').each(function () {
                        const currentStar = $(this).data('star');

                        // Si la estrella es menor o igual a estrellasDefault, se rellena
                        if (currentStar <= estrellasDefault) {
                            $(this).removeClass('bi-star').addClass('bi-star-fill');
                        }
                    });

                    // Asignar el valor de estrellasDefault al input hidden
                    $('#Estrellas').val(estrellasDefault);
                }

                
                if (isDiscount == false) {
                    calcularTotal(producto.precio);
                } else {
                    calcularTotal(descuentoFinal);
                }

                // Definir la función dentro del ámbito local
                window.mostrarColorImagen = function (index, id) {
                    const colorImage = producto[`imagen_color${index + 1}`];
                    $('#product-color-image').attr('src', colorImage);
                    $('#Color').val(id);
                    $('#NumColor').val(index + 1);
                }

                window.mostrarColorImagenAccesorio = function (index, id) {
                    const colorImage = producto[`imagen_accesorio_color${index + 1}`];
                    $('#accesory-color-image').attr('src', colorImage);
                    $('#AccesoryColor').val(id);
                    $('#NumAccesoryColor').val(index + 1);
                }

                seleccionarComentariosPorIdProducto(producto.id);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                div.innerHTML = 'Error al cargar el producto';
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            div.innerHTML = 'Error al cargar el producto';
        }
    });
}

function mostrarEstrellas(calificaciones) {
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

    const divRating = document.getElementById('rating-producto');
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
                // Limpiamos el spinner principal y cargamos los spinners por cada tarjeta
                contenedor.innerHTML = '';
                categorias.forEach(categoria => {
                    const cardHTML = `
                        <div class="col" id="card-${categoria.id}">
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
                    mostrarCarta(categoria.id);
                });
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                toggleLoadingIconStoreCard('contenedor-categorias', false); // Mostrar error si hay problema
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            toggleLoadingIconStoreCard('contenedor-categorias', false); // Mostrar error si falla el AJAX
        }
    });
}

function mostrarCarta(idCategoria) {
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
                            <a class="card-body p-0 card-body-product card-shadow text-decoration-none d-flex flex-column align-items-center justify-content-between card-categories-body" href="../pages/productos.php?nombreCategoria=${encodeURIComponent(categoria.nombre)}&idCategoria=${encodeURIComponent(idCategoria)}">
                                <div class="position-relative d-flex justify-content-center align-items-center card-img-categories-container m-auto p-2">
                                    <div class="spinner-border spinner-color position-absolute" role="status" style="width: 50px; height: 50px;" id="spinner-${idCategoria}"></div>
                                    <img class="d-none product-img-hover" id="img-${idCategoria}" alt="Imagen de categoría">
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
                document.getElementById(`card-${idCategoria}`).innerHTML = cardHTML;

                // Llamamos para cargar la imagen
                cargarImagenCategoria(idCategoria);

            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                document.getElementById(`card-${idCategoria}`).innerHTML = 'Error al cargar categoría';
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
            document.getElementById(`card-${idCategoria}`).innerHTML = 'Error al cargar categoría';
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
                const imgElement = document.getElementById(`img-${idCategoria}`);
                const spinnerElement = document.getElementById(`spinner-${idCategoria}`);

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
    const id = $('#idProducto').val();
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

    if (isAccesorio != 0) {
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

                if (isAccesorio != 0) {
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