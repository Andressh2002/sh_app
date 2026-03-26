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
    const categoria = $('#Categorias').val();
    const colores = colores_almacenados.map(item => item.id).join(',');
    const descuentos = descuentos_almacenados.map(item => item.id).join(',');
    const altura = $('#Altura').val();
    const anchura = $('#Anchura').val();
    const peso = $('#Peso').val();
    const imagen1 = $('#hiddenImagen1Producto').val();
    const imagen2 = $('#hiddenImagen2Producto').val();
    const descripcion = $('#Descripcion').val();
    const advertencia = $('#Advertencia').val();
    const festividad = $('#hiddenFestividad').val();
    const rareza = $('#hiddenRareza').val();
    const universo = $('#hiddenUniverso').val();
    const accesorio = $('#hiddenAccesorio').val();
    const vectColores = colores_almacenados.map(color => color.imagen);
    const tiempo = $('#Tiempo').val();
    const comida = $('#Comida').prop('checked');
    const existencia = $('#Existencia').prop('checked');

    // Rellena con cadenas vacías hasta alcanzar un total de 20 elementos
    const imagenColores = Array.from({ length: 20 }, (_, i) => vectColores[i] || '');

    alertLoading(
        id ? '¡Cambiando imágenes!' : '¡Guardando imágenes!',
        'Se están guardando las imágenes, espere a que el proceso termine.',
        'info'
    );

    let arrayResponse = [];

    // Función para guardar datos del producto
    function guardarDatos() {
        return new Promise((resolve, reject) => {
            const accion = id ? 'actualizar' : 'insertar';
            const data = {
                accion: accion,
                nombre: nombre,
                precio: precio,
                categoria: categoria,
                colores: colores,
                descuentos: descuentos,
                altura: altura,
                anchura: anchura,
                peso: peso,
                festividad: !existencia ? festividad : '',
                imagen1: imagen1,
                imagen2: imagen2,
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
                    if (data.icon === 'success' && data.producto_id) {
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
    function guardarImagen(productId, imagen, nombreCampo) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('accion', 'insertarImagen');
            formData.append('id', productId);
            formData.append('idImagen', nombreCampo);
            formData.append('imagen', imagen);

            $.ajax({
                url: backend + urlProduct,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.icon === 'success') {
                        resolve(); // La imagen se guardó correctamente
                    } else {
                        reject('Error al guardar la imagen: ' + data.text);
                    }
                },
                error: function () {
                    reject('Error en la solicitud AJAX de imagen');
                }
            });
        });
    }

    // Mostrar y actualizar la barra de progreso
    $('#container-progress-bar').show(); // Asegura que el contenedor sea visible
    $('#container-progress-bar .progress-bar').css('width', '0%'); // Inicializa la barra al 0%

    // Llamada a las funciones y actualización de la barra de progreso
    guardarDatos()
        .then(productId => {
            const totalColores = imagenColores.length;

            // Función para procesar las imágenes en serie
            const procesarImagenes = async () => {
                for (let index = 0; index < totalColores; index++) {
                    const imagen = imagenColores[index];
                    if (imagen) { // Solo intenta guardar si hay una imagen presente
                        try {
                            await guardarImagen(productId, imagen, `imagen_color${index + 1}`);
                            const progressPercentage = ((index + 1) / totalColores) * 100;
                            $('#container-progress-bar .progress-bar').css('width', `${progressPercentage}%`);
                        } catch (error) {
                            console.error(`Error al guardar la imagen ${index + 1}: ${error}`);
                        }
                    }
                }
            };

            // Llama a la función para procesar las imágenes
            return procesarImagenes();
        })
        .then(() => {
            // Mensaje de éxito después de que se guarden todas las imágenes
            alert(
                arrayResponse.title,
                arrayResponse.text,
                arrayResponse.icon,
                'Aceptar'
            );
            $('#container-progress-bar').hide(); // Oculta la barra de progreso al terminar
        })
        .catch(error => {
            alert(
                arrayResponse.title,
                arrayResponse.text,
                arrayResponse.icon,
                'Cerrar'
            );
            console.error(error);
        });
}

function obtenerProductos(nombre) {
    toggleLoadingIcon('data-container', true, 6);
    $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function (response) {
            try {
                const productos = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarProductos(productos);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon('data-container', false, 6);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarProductos(productos) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    // Verificar si currentPage e itemsPerPage están definidos
    if (typeof currentPage === 'undefined' || typeof itemsPerPage === 'undefined') {
        console.error('Error: currentPage o itemsPerPage no están definidos.');
        return;
    }

    const startIndex = (currentPage - 1) * itemsPerPage;

    // Ordenar productos y verificar que sean un array
    try {
        productos = ordenar(productos, order);
    } catch (error) {
        console.error('Error al ordenar los productos:', error);
        return;
    }

    if (!Array.isArray(productos) || productos.length === 0) {
        container.append('<tr><td class="text-center" colspan="6">No se encontraron productos.</td></tr>');
        return;
    }

    productos.forEach((producto, index) => {
        try {
            const json = encodeURIComponent(JSON.stringify(producto));
            const countColores = producto.idColores.split(',').length;
            const html = `
                <tr>
                    <td class="align-middle">${startIndex + index + 1}</td>
                    <td class="align-middle" style="width: 256px;">
                        <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 200px;">
                            <!-- Spinner mientras se carga la imagen -->
                            <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${producto.id}" 
                                style="width: 50px; height: 50px;"></div>
                            <!-- Imagen (oculta por defecto) -->
                            <img id="img-${producto.id}" class="d-none product-img-hover" alt="Imagen">
                        </div>
                    </td>
                    <td class="align-middle">
                        <ul class="list-group border-0 px-0">
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Nombre: </strong>${producto.nombre || 'Sin nombre'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Categoría: </strong>${producto.categoria || 'Sin categoría'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Precio: </strong>₡${producto.precio || 0}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Paletas: </strong>${countColores < 20 || 0 ? countColores + ' de 20' : 'Las 20'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Estrellas: </strong><span id="container-product-stars${producto.id}"></span></li>
                        </ul>
                    </td>
                    <td class="align-middle">
                        <ul class="list-group border-0 px-0">
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Festividad: </strong>${producto.idFestividad == 0 ? 'Ninguna' : producto.festividad || 'Desconocida'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Rareza: </strong>${producto.idRareza == 0 ? 'Ninguna' : producto.rareza || 'Desconocida'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Universo: </strong>${producto.idUniverso == 0 ? 'Ninguno' : producto.universo || 'Desconocido'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Descuentos: </strong>${producto.idDescuentos ? (producto.idDescuentos.split(',').length == 1 ? 'Solo 1 aplicado' : producto.idDescuentos.split(',').length + ' aplicados') : 'No aplicados'}</li>
                        </ul>
                    </td>
                    <td class="align-middle">
                        <ul class="list-group border-0 px-0">
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Pedidos: </strong>${producto.pedidos || 0}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Vendidos: </strong>${producto.vendidos || 0}</li>
                        </ul>
                    </td>
                    <td class="align-middle text-center" style="width: 1px;">
                        <div class="d-flex gap-2 justify-content-start">
                            <button onclick="window.open('addProduct.php?id=${producto.id}&accion=actualizar')" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <button onclick="cambiarVisibilidadProducto(${producto.visible == 0 ? 1 : 0}, '${producto.id}')" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                ${producto.visible == 0 ? 'Publicar' : 'Ocultar'}<i class="bi ${producto.visible == 0 ? 'bi-cloud-arrow-up-fill' : 'bi-lock-fill'} ms-2"></i>
                            </button>
                            <button onclick="eliminarProducto(${producto.id}, '${producto.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Eliminar
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg>
                            </button>
                            <button onclick="verDetallesProducto('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Detalles<i class="bi bi-three-dots ms-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            container.append(html);
            cargarEstrellasProducto(producto.calificaciones_estrellas, producto.id);
            buscarImagenProducto(producto.id);
        } catch (error) {
            console.error(`Error al procesar el producto con id ${producto.id}:`, error);
        }
    });
}

function cargarEstrellasProducto(calificaciones, idElement) {
    if (!calificaciones) {
        calificaciones = '';
    }
    let totalEstrellas = 0;
    let contador = 0;

    const pares = calificaciones.replace(/[{}]/g, '').split(',');

    pares.forEach(par => {
        const [clave, estrellas] = par.split(':');
        totalEstrellas += parseInt(estrellas);
        contador++;
    });

    const promedioEstrellas = contador > 0 ? totalEstrellas / contador : 0;

    const divRating = document.getElementById('container-product-stars' + idElement.toString());
    divRating.innerHTML = '';

    for (let i = 1; i <= 5; i++) {
        if (i <= Math.round(promedioEstrellas)) {
            divRating.innerHTML += `<i class="bi bi-star-fill text-star"></i>`;
        } else {
            divRating.innerHTML += `<i class="bi bi-star text-star"></i>`;
        }
    }
}

function buscarImagenProducto(idProducto) {
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
        $('#Precio').val(producto.precio);
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
        if (producto.festividad) {
            $('#textFestividad').val(producto.festividad);
            $('#hiddenFestividad').val(producto.idFestividad);
        } else {
            $('#textFestividad').val('Ninguno');
        }
        if (producto.rareza) {
            $('#textRareza').val(producto.rareza);
            $('#hiddenRareza').val(producto.idRareza);
        } else {
            $('#textRareza').val('Ninguno');
        }
        if (producto.universo) {
            $('#textUniverso').val(producto.universo);
            $('#hiddenUniverso').val(producto.idUniverso);
        } else {
            $('#textUniverso').val('Ninguno');
        }
        if (producto.accesorio) {
            $('#textAccesorio').val(producto.accesorio);
            $('#hiddenAccesorio').val(producto.idAccesorio);
        } else {
            $('#textAccesorio').val('Ninguno');
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
            }).catch(error => {
                console.error("Error al cargar descuentos:", error);
            });
        }
    }
}

function eliminarProducto(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar "' + nombre + '" de los productos? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function () {
                eliminarProducto(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlProduct,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function (response) {
                aplicarFiltrosProducto()
                alert(
                    '¡Producto eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function () {
                alert(
                    'Error',
                    'Hubo un problema al eliminar el producto.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosProducto() {
    const nombre = $('#Nombre').val();
    const categoria = $('#Categorias').val();
    const rareza = $('#Rareza').val();
    const universo = $('#Universo').val();
    seleccionarProductos(nombre, categoria, rareza, universo);
}

function verDetallesProducto(json) {
    const producto = JSON.parse(decodeURIComponent(json));
    alertDetails(
        'Detalles del producto',
        producto,
        ['nombre', 'categoria', 'precio', 'pedidos', 'vendidos', 'especial', 'festividad', 'descripcion', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarProductos(nombre, categoria, rareza, universo) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    const colspan = 5;

    container.empty();
    container.append(`
        <tr><td class="text-center align-middle" colspan="${colspan}">
            <div class="spinner-border spinner-color" role="status" style="width: 24px; height: 24px;"></div>
        </td></tr>
    `);
    
    if (!nombre) {
        nombre = '';
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

    cancelarCargaSecuencial = true;

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
        solicitudAjaxActiva = null;
    }

    cancelarCargaSecuencial = false;
    
    solicitudAjaxActiva = $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            categoria: categoria,
            rareza: rareza,
            universo: universo,
            orden: order,
        },
        success: function (response) {
            try {
                const productos = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (productos.length > 0) {
                    procesarProductosSecuencialmente(productos, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron productos.</td></tr>`);
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

function procesarProductosSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const producto = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" style="max-width: 256px; min-width: 140px;">
                    <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 140px;">
                        <!-- Spinner mientras se carga la imagen -->
                        <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${producto.id}" style="width: 50px; height: 50px;"></div>
                        <!-- Imagen (oculta por defecto) -->
                        <img id="img-${producto.id}" class="d-none product-img-hover" alt="Imagen">
                    </div>
                </td>
                <td class="align-middle" id="resumen-${producto.id}"></td>
                <td class="align-middle" id="asignaciones-${producto.id}"></td>
                <td class="align-middle text-center" id="opciones-${producto.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este producto no se pudo cargar.</td></tr>`);
    }

    cargarProductoSeleccionado(producto.id, function () {
        procesarProductosSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarProductoSeleccionado(id, callback) {
    const tdResumen = $(`#resumen-${id}`);
    const tdAsignaciones = $(`#asignaciones-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
    }

    solicitudAjaxActiva = $.ajax({
        url: backend + urlProduct,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            if (cancelarCargaSecuencial) return;

            try {
                const producto = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(producto));
                const countColores = producto.idColores.split(',').length;
                const p_comida = producto.comida;

                let isDestacado = false;
                if (producto.fecha_destacado != null) {
                    const fechaActual = new Date();
                    const fechaDestacado = new Date(producto.fecha_destacado.split(" ")[0]);
                    const diffTime = Math.abs(fechaActual - fechaDestacado);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    if (diffDays <= 7) {
                        isDestacado = true;
                    }
                }
                

                let validarComida = false;
                if (p_comida == null || p_comida == 0) {
                    validarComida = true;
                }
                
                tdResumen.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 248px;">
                        <li class="${liClasses}"><strong>Nombre: </strong>${producto.nombre || 'Sin nombre'}</li>
                        <li class="${liClasses}"><strong>Categoría: </strong>${producto.categoria || 'Sin categoría'}</li>
                        <li class="${liClasses}"><strong>Precio: </strong>₡${producto.precio || 0}</li>
                        ${validarComida ? `
                            <li class="${liClasses}"><strong>Paletas: </strong>${countColores < 20 || 0 ? countColores + ' de 20' : 'Las 20'}</li>
                        `:''}
                        <li class="${liClasses}"><strong>Estrellas: </strong><span id="container-product-stars${producto.id}"></span></li>
                    </ul>
                `);
                tdAsignaciones.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 254px;">
                        <li class="${liClasses}"><strong>Festividad: </strong>${producto.idFestividad == 0 ? 'Ninguna' : producto.festividad || 'Desconocida'}</li>
                        <li class="${liClasses}"><strong>Rareza: </strong>${producto.idRareza == 0 ? 'Ninguna' : producto.rareza || 'Desconocida'}</li>
                        <li class="${liClasses}"><strong>Universo: </strong>${producto.idUniverso == 0 ? 'Ninguno' : producto.universo || 'Desconocido'}</li>
                        <li class="${liClasses}"><strong>Descuentos: </strong>${producto.idDescuentos ? (producto.idDescuentos.split(',').length == 1 ? 'Solo 1 aplicado' : producto.idDescuentos.split(',').length + ' aplicados') : 'No aplicados'}</li>
                        <li class="${liClasses} ${producto.visible == 0 ? 'text-danger' :'text-success'}"><strong>Visibilidad: </strong>${producto.visible == 0 ? 'Oculto al público' :'Todo público'}</li>
                        <li class="${liClasses} ${isDestacado == false ? 'text-danger' :'text-success'}"><strong>Destacado: </strong>${isDestacado == false ? 'No' :'Si'}</li>
                    </ul>   
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="addProduct.php?id=${producto.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addProduct.php?id=${producto.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="cambiarVisibilidadProducto(${producto.visible == 0 ? 1 : 0}, '${producto.id}')" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            ${producto.visible == 0 ? 'Publicar' : 'Ocultar'}<i class="bi ${producto.visible == 0 ? 'bi-cloud-arrow-up-fill' : 'bi-lock-fill'} ms-2"></i>
                        </button>
                        <button onclick="cambiarDestacacidadProducto(${isDestacado == false ? 1 : 0}, '${producto.id}')" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            ${isDestacado == false ? 'Destacar' : 'Desestacar'}<i class="bi ${isDestacado == false ? 'bi-cloud-arrow-up-fill' : 'bi-lock-fill'} ms-2"></i>
                        </button>
                        <button onclick="eliminarProducto(${producto.id}, '${producto.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesProducto('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);

                cargarEstrellasProducto(producto.calificaciones_estrellas, producto.id);
                buscarImagenProducto(producto.id);
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

function actualizarPaginacionProducto(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaProducto(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaProducto(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaProducto(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaProducto(pagina) {
    currentPage = pagina;
    seleccionarProductos('', '', '', '');
}

function limpiarFiltrosProducto() {
    $('#Nombre').val('');
}

function obtenerCategoriasParaProductos(select, all) {
    $.ajax({
        url: backend + urlCategory,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        },
        success: function (response) {
            try {
                const categorias = typeof response === 'string' ? JSON.parse(response) : response;

                categorias.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

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
                });

            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
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
                            <tr>
                                <td class="align-middle">${index + 1}</td>
                                <td class="align-middle">${color.nombre}</td>
                                <td class="align-middle">${color.color_familia}</td>
                                <td class="align-middle">
                                    <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2" style="background: ${color.codigo_color_principal};">
                                        <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.codigo_color_terciario ? 'btn-palette-bg-color-2-A' : 'btn-palette-bg-color-2-B'}" style="background: ${color.codigo_color_secundario};"></div>
                                        <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.codigo_color_terciario ? 'visually-hidden' : 'btn-palette-bg-color-3'}" style="background: ${color.codigo_color_terciario};"></div>
                                    </div>
                                </td>
                                ${colores_almacenados.length < 20 ? `
                                    <td class="align-middle text-center" style="width: 1px;">
                                    <button onclick="seleccionarColor('${color.id}', '${color.codigo_color_principal}', '${color.codigo_color_secundario}', '${color.codigo_color_terciario}', '', '${color.color_familia}')" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        Seleccionar
                                    </button>
                                </td>
                                ` : `
                                <td class="align-middle text-center" style="width: 1px;">
                                    Ya tienes las 16 paletas
                                </td>
                                `}
                            </tr>`;

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

function cargarFiltrosParaTablaDescuentosModal(tabla) {
    const nombre = $('#NombreDescuentoModal').val();
    obtenerDescuentosParaProductos(tabla, nombre);
}

function cargarFiltrosParaTablaAccesoriosModal(tabla) {
    const nombre = $('#NombreAccesorioModal').val();
    obtenerDescuentosParaProductos(tabla, nombre);
}

function obtenerFestividadesParaProductos(table, nombre) {
    toggleLoadingIcon(table, true, 4, 28);
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

                festividades.sort((a, b) => a.nombre.localeCompare(b.nombre));

                const tableElement = $('#' + table);
                tableElement.empty();

                if (festividades.length > 0) {
                    festividades.forEach((festividad, index) => {
                        const rowHtml = `
                            <tr>
                                <td class="align-middle">${index + 1}</td>
                                <td class="align-middle">${festividad.nombre}</td>
                                <td class="align-middle">${'Del ' + formarFecha(festividad.fecha_inicial) + ' al ' + formarFecha(festividad.fecha_final)}</td>
                                <td class="align-middle text-center" style="width: 1px;">
                                    <button onclick="seleccionarFestividad('${festividad.id}', '${festividad.nombre}')" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>`;

                        tableElement.append(rowHtml);
                    });
                } else {
                    tableElement.append('<tr><td class="text-center" colspan="4">No se encontraron festividades.</td></tr>');
                }

            } catch (error) {
                toggleLoadingIcon(table, false, 4, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon(table, false, 4, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function obtenerRarezasParaProductos(table, nombre) {
    toggleLoadingIcon(table, true, 4, 28);
    $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function (response) {
            try {
                const rarezas = typeof response === 'string' ? JSON.parse(response) : response;

                rarezas.sort((a, b) => a.nombre.localeCompare(b.nombre));

                const tableElement = $('#' + table);
                tableElement.empty();

                if (rarezas.length > 0) {
                    rarezas.forEach((rareza, index) => {
                        const rowHtml = `
                            <tr>
                                <td class="align-middle">${index + 1}</td>
                                <td class="align-middle">${rareza.nombre}</td>
                                <td class="align-middle">
                                    <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2" style="background: ${rareza.color};"></div>
                                </td>
                                <td class="align-middle text-center" style="width: 1px;">
                                    <button onclick="seleccionarRareza('${rareza.id}', '${rareza.nombre}')" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>`;

                        tableElement.append(rowHtml);
                    });
                } else {
                    tableElement.append('<tr><td class="text-center" colspan="4">No se encontraron rarezas.</td></tr>');
                }

            } catch (error) {
                toggleLoadingIcon(table, false, 4, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon(table, false, 4, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function obtenerUniversosParaProductos(table, nombre) {
    toggleLoadingIcon(table, true, 3, 28);
    $.ajax({
        url: backend + urlUniverse,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function (response) {
            try {
                const universos = typeof response === 'string' ? JSON.parse(response) : response;

                universos.sort((a, b) => a.nombre.localeCompare(b.nombre));

                const tableElement = $('#' + table);
                tableElement.empty();

                if (universos.length > 0) {
                    universos.forEach((universo, index) => {
                        const rowHtml = `
                            <tr>
                                <td class="align-middle">${index + 1}</td>
                                <td class="align-middle">${universo.nombre}</td>
                                <td class="align-middle text-center" style="width: 1px;">
                                    <button onclick="seleccionarUniverso('${universo.id}', '${universo.nombre}')" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>`;

                        tableElement.append(rowHtml);
                        //buscarImagenUniverso(universo.id);
                    });
                } else {
                    tableElement.append('<tr><td class="text-center" colspan="3">No se encontraron universos.</td></tr>');
                }

            } catch (error) {
                toggleLoadingIcon(table, false, 3, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon(table, false, 3, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
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
                            <tr>
                                <td class="align-middle">${index + 1}</td>
                                <td class="align-middle">${descuento.nombre}</td>
                                <td class="align-middle">${descuento.descuento}%</td>
                                <td class="align-middle">${'Del ' + formarFecha(descuento.fecha_inicial) + ' al ' + formarFecha(descuento.fecha_final)}</td>
                                <td class="align-middle text-center" style="width: 1px;">
                                    <button onclick="seleccionarDescuento('${descuento.id}', '${descuento.nombre}', '${'Del ' + formarFecha(descuento.fecha_inicial) + ' al ' + formarFecha(descuento.fecha_final)}', '${descuento.descuento}')" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>`;

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

function obtenerAccesoriosParaProductos(table, nombre) {
    toggleLoadingIcon(table, true, 4, 28);
    $.ajax({
        url: backend + urlAccesory,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function (response) {
            try {
                const accesorios = typeof response === 'string' ? JSON.parse(response) : response;

                accesorios.sort((a, b) => a.nombre.localeCompare(b.nombre));

                const tableElement = $('#' + table);
                tableElement.empty();

                if (accesorios.length > 0) {
                    accesorios.forEach((accesorio, index) => {
                        const rowHtml = `
                            <tr>
                                <td class="align-middle">${index + 1}</td>
                                <td class="align-middle">${accesorio.nombre}</td>
                                <td class="align-middle">
                                    <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 200px;">
                                        <!-- Spinner mientras se carga la imagen -->
                                        <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${accesorio.id + 'accesorio'}" 
                                            style="width: 50px; height: 50px;"></div>
                                        <!-- Imagen (oculta por defecto) -->
                                        <img id="img-${accesorio.id + 'accesorio'}" class="d-none product-img-hover" alt="Imagen">
                                    </div>
                                </td>
                                <td class="align-middle text-center" style="width: 1px;">
                                    <button onclick="seleccionarAccesorio('${accesorio.id}', '${accesorio.nombre}')" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>`;

                        tableElement.append(rowHtml);
                        buscarImagenAccesorio(accesorio.id);
                    });
                } else {
                    tableElement.append('<tr><td class="text-center" colspan="4">No se encontraron accesorios.</td></tr>');
                }

            } catch (error) {
                toggleLoadingIcon(table, false, 4, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon(table, false, 4, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function seleccionarColor(id, color1, color2, color3, imagen, familia) {
    if (colores_almacenados.length <= 20) {
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

function seleccionarDescuento(id, nombre, fecha, descuento) {
    descuentos_almacenados.push({
        'id': id,
        'nombre': nombre,
        'fecha': fecha,
        'descuento': descuento,
    });
    actualizarDescuentosSeleccionados();

    // Actualizar la lista de descuentos en la tabla
    obtenerDescuentosParaProductos('discounts-data-container', '');
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
    obtenerDescuentosParaProductos('discounts-data-container', '');
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
            <div class="col">
                <div class="card mx-auto p-lg-2 p-sm-1 d-flex flex-column align-items-center gap-1" style="min-width: 256px; height: 256px;">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <button onclick="moverColorIzquierda(${index})" type="button" class="btn-details text-white border-0 rounded-pill d-flex align-items-center ${index != 0 ? '' : 'bg-secondary'}" ${index != 0 ? '' : 'disabled'} style="width: 22px; height: 22px;">
                            <i class="bi bi-arrow-left-short d-flex align-self-center m-auto"></i>
                        </button>
                        <button onclick="eliminarColorSeleccionado('${color.id}')" type="button" class="btn-delete text-white border-0 rounded-pill d-flex align-items-center" style="width: 22px; height: 22px;">
                            <i class="bi bi-x d-flex align-self-center m-auto"></i>
                        </button>
                        <button onclick="moverColorDerecha(${index})" type="button" class="btn-details text-white border-0 rounded-pill d-flex align-items-center ${index != colores_almacenados.length - 1 != 0 ? '' : 'bg-secondary'}" ${index != colores_almacenados.length - 1 != 0 ? '' : 'disabled'} style="width: 22px; height: 22px;">
                            <i class="bi bi-arrow-right-short d-flex align-self-center m-auto"></i>
                        </button>
                    </div>
                    <div class="card overflow-hidden rounded-3" style="width: 100%; height: 100%;">
                        <input type="file" class="form-control" id="imageInput${color.id}" />
                        <div class="overflow-x-auto overflow-y-auto w-100 h-100">
                            <img class="p-1" id="vista${color.id}" src="${color.imagen ? color.imagen : ''}" alt="" style="width: 100%; height: auto; display: ${color.imagen ? 'block' : 'none'};">
                        </div>
                        <input type="hidden" id="hidden${color.id}" value="${color.imagen ? color.imagen : ''}">
                    </div>
                    <div class="py-2 d-flex gap-2 m-auto align-items-center">
                        <div class="position-relative btn-palette border border-2 border-dark rounded rounded-2" style="background: ${color.color1};">
                            <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.color3 ? 'btn-palette-bg-color-2-A' : 'btn-palette-bg-color-2-B'}" style="background: ${color.color2};"></div>
                            <div class="position-absolute btn-palette border border-2 border-dark rounded rounded-2 ${!color.color3 ? 'visually-hidden' : 'btn-palette-bg-color-3'}" style="background: ${color.color3};"></div>
                        </div>
                        <p class="m-0">${color.familia}</p>
                    </div>
                </div>
            </div>`;

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
    div.empty();

    if (descuentos_almacenados.length > 1) {
        descuentos_almacenados.sort((a, b) => a.nombre.localeCompare(b.nombre, 'es', { sensitivity: 'base' }));
    }
    
    if (descuentos_almacenados.length > 0) {
        descuentos_almacenados.forEach((descuento, index) => {
            const html = `
                <tr>
                    <td class="align-middle">${index + 1}</td>
                    <td class="align-middle">${descuento.nombre}</td>
                    <td class="align-middle">${descuento.fecha}</td>
                    <td class="align-middle">${descuento.descuento}%</td>
                    <td class="text-center" style="width: 1px;">
                        <div class="d-flex gap-2 justify-content-start">
                            <button onclick="eliminarDescuentoSeleccionado(${descuento.id})" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Eliminar
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
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

function moverColorIzquierda(index) {
    if (index > 0) {
        [colores_almacenados[index - 1], colores_almacenados[index]] =
            [colores_almacenados[index], colores_almacenados[index - 1]];
        actualizarColoresSeleccionados();
    }
}

function moverColorDerecha(index) {
    if (index < colores_almacenados.length - 1) {
        [colores_almacenados[index], colores_almacenados[index + 1]] =
            [colores_almacenados[index + 1], colores_almacenados[index]];
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

    $.ajax({
        url: backend + urlProduct,
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
            aplicarFiltrosProducto();
        },
        error: function () {
            alert(
                'Error',
                'Hubo un problema al cambiar el estado de visibilidad del producto.',
                'error',
                'Aceptar'
            );
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

    $.ajax({
        url: backend + urlProduct,
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
            aplicarFiltrosProducto();
        },
        error: function () {
            alert(
                'Error',
                'Hubo un problema al cambiar la destacacidad del producto.',
                'error',
                'Aceptar'
            );
        }
    });
}
