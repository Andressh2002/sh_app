function guardarAccesorio() {
    // Definición de variables desde el DOM
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const colores = colores_almacenados.map(item => item.id).join(',');
    const descripcion = $('#Descripcion').val();
    const vectColores = colores_almacenados.map(color => color.imagen);

    // Rellena con cadenas vacías hasta alcanzar un total de 10 elementos
    const imagenColores = Array.from({ length: 16 }, (_, i) => vectColores[i] || '');

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
                colores: colores,
                descripcion: descripcion
            };

            if (id) {
                data.id = id;
            }

            $.ajax({
                url: backend + urlAccesory,
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
                url: backend + urlAccesory,
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

function obtenerAccesorios(nombre) {
    toggleLoadingIcon('data-container', true, 9);
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
                mostrarAccesorios(accesorios);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            toggleLoadingIcon('data-container', false, 9);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarAccesorios(accesorios) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    // Verificar si currentPage e itemsPerPage están definidos
    if (typeof currentPage === 'undefined' || typeof itemsPerPage === 'undefined') {
        console.error('Error: currentPage o itemsPerPage no están definidos.');
        return;
    }

    const startIndex = (currentPage - 1) * itemsPerPage;

    // Ordenar accesorios y verificar que sean un array
    try {
        accesorios = ordenar(accesorios, order);
    } catch (error) {
        console.error('Error al ordenar los accesorios:', error);
        return;
    }

    if (!Array.isArray(accesorios) || accesorios.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron accesorios.</td></tr>');
        return;
    }

    accesorios.forEach((accesorio, index) => {
        try {
            const json = encodeURIComponent(JSON.stringify(accesorio));
            const countColores = accesorio.idColores.split(',').length;
            const html = `
                <tr>
                    <td class="align-middle">${startIndex + index + 1}</td>
                    <td class="align-middle" style="width: 256px;">
                        <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 200px;">
                            <!-- Spinner mientras se carga la imagen -->
                            <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${accesorio.id + 'accesorio'}" 
                                style="width: 50px; height: 50px;"></div>
                            <!-- Imagen (oculta por defecto) -->
                            <img id="img-${accesorio.id + 'accesorio'}" class="d-none product-img-hover" alt="Imagen">
                        </div>
                    </td>
                    <td class="align-middle">
                        <ul class="list-group border-0 px-0">
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Nombre: </strong>${accesorio.nombre || 'Sin nombre'}</li>
                            <li class="list-group-item border-0 bg-transparent px-0"><strong>Paletas: </strong>${countColores < 16 || 0 ? countColores + ' de 16' : 'Las 16'}</li>
                        </ul>
                    </td>
                    <td class="align-middle text-center" style="width: 1px;">
                        <div class="d-flex gap-2 justify-content-start">
                            <button onclick="location.href='addAccesory.php?id=${accesorio.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <button onclick="eliminarAccesorio(${accesorio.id}, '${accesorio.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Eliminar
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg>
                            </button>
                            <button onclick="verDetallesAccesorio('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                Detalles<i class="bi bi-three-dots ms-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            container.append(html);
            buscarImagenAccesorio(accesorio.id);
        } catch (error) {
            console.error(`Error al procesar el accesorio con id ${accesorio.id}:`, error);
        }
    });
}

function buscarImagenAccesorio(idProducto) {
    $.ajax({
        url: backend + urlAccesory,
        type: 'POST',
        data: {
            accion: 'buscarImagen',
            id: idProducto,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data[0].imagen_color1 && data[0].imagen_color1 !== '' ? data[0].imagen_color1 : '../src/img/app/no_image.png';

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

function buscarAccesorio(id) {
    $.ajax({
        url: backend + urlAccesory,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function (response) {
            try {
                const accesorio = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarAccesorio(accesorio);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarAccesorio(accesorio) {
    if (accesorio) {
        $('#Nombre').val(accesorio.nombre);
        $('#Descripcion').val(accesorio.descripcion);

        const arr = accesorio.idColores.split(',');
        colores_almacenados = [];

        // Obtener IDs y preparar las promesas
        const vectorIdColores = accesorio.idColores.split(',').map(Number);
        const colorPromises = vectorIdColores.map((id, index) => {
            const imagenColor = accesorio[`imagen_color${index + 1}`];
            return buscarColorParaAccesorio(id, imagenColor);
        });

        // Procesar las promesas en el orden original
        Promise.all(colorPromises).then((colores) => {
            colores.forEach(color => {
                seleccionarColor(color.id, color.color1, color.color2, color.color3, color.imagen, color.familia);
            });
            actualizarColoresSeleccionados();
        }).catch(error => {
            console.error("Error al cargar colores:", error);
        });
    }
}

function eliminarAccesorio(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar "' + nombre + '" de los accesorios? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function () {
                eliminarAccesorio(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlAccesory,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function (response) {
                aplicarFiltrosAccesorio()
                alert(
                    '¡Accesorio eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function () {
                alert(
                    'Error',
                    'Hubo un problema al eliminar el accesorio.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosAccesorio() {
    const nombre = $('#Nombre').val();
    seleccionarAccesorios(nombre);
}

function verDetallesAccesorio(json) {
    const accesorio = JSON.parse(decodeURIComponent(json));
    alertDetails(
        'Detalles del accesorio',
        accesorio,
        ['nombre', 'descripcion', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarAccesorios(nombre) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();
    const colspan = 4;
    container.append(`
        <tr><td class="text-center align-middle" colspan="${colspan}">
            <div class="spinner-border spinner-color" role="status" style="width: 24px; height: 24px;"></div>
        </td></tr>
    `);
    
    if (!nombre) {
        nombre = '';
    }
    
    cancelarCargaSecuencial = true;

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
        solicitudAjaxActiva = null;
    }

    cancelarCargaSecuencial = false;
    
    solicitudAjaxActiva = $.ajax({
        url: backend + urlAccesory,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            orden: order,
        },
        success: function (response) {
            try {
                const accesorios = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (accesorios.length > 0) {
                    procesarAccesoriosSecuencialmente(accesorios, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron accesorios.</td></tr>`);
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

function procesarAccesoriosSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const accesorio = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" style="max-width: 256px; min-width: 140px;">
                    <div class="position-relative d-flex justify-content-center align-items-center" style="width: 100%; height: 140px;">
                        <!-- Spinner mientras se carga la imagen -->
                        <div class="spinner-border spinner-color position-absolute" role="status" id="spinner-${accesorio.id}" style="width: 50px; height: 50px;"></div>
                        <!-- Imagen (oculta por defecto) -->
                        <img id="img-${accesorio.id}" class="d-none product-img-hover" alt="Imagen">
                    </div>
                </td>
                <td class="align-middle" id="informacion-${accesorio.id}"></td>
                <td class="align-middle text-center" id="opciones-${accesorio.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este accesorio no se pudo cargar.</td></tr>`);
    }

    cargarAccesorioSeleccionado(accesorio.id, function () {
        procesarAccesoriosSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarAccesorioSeleccionado(id, callback) {
    const tdInformacion = $(`#informacion-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlAccesory,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const accesorio = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(accesorio));
                const countColores = accesorio.idColores.split(',').length;
                
                tdInformacion.append(`
                    <ul class="list-group border-0 px-0" style="min-width: 248px;">
                        <li class="${liClasses}"><strong>Nombre: </strong>${accesorio.nombre || 'Sin nombre'}</li>
                        <li class="${liClasses}"><strong>Paletas: </strong>${countColores < 16 || 0 ? countColores + ' de 16' : 'Las 16'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="addAccesory.php?id=${accesorio.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addAccesory.php?id=${accesorio.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarAccesorio(${accesorio.id}, '${accesorio.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesAccesorio('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);

                buscarImagenAccesorio(accesorio.id);
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

function actualizarPaginacionAccesorio(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaAccesorio(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaAccesorio(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaAccesorio(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaAccesorio(pagina) {
    currentPage = pagina;
    seleccionarProductos('');
}

function limpiarFiltrosAccesorio() {
    $('#Nombre').val('');
}

function buscarColorParaAccesorio(id, imagen) {
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
