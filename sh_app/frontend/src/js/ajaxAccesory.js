function guardarAccesorio() {
    // Definición de variables desde el DOM
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const colores = colores_almacenados.map(item => item.id).join(',');
    const vectColores = colores_almacenados.map(color => color.imagen);

    // Rellena con cadenas vacías hasta alcanzar un total de 10 elementos
    const imagenColores = Array.from({ length: 20 }, (_, i) => vectColores[i] || '');

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Guardando...", 'Espere un momento...', "bi bi-wifi", false);

    let arrayResponse = [];

    // Función para guardar datos del producto
    function guardarDatos() {
        return new Promise((resolve, reject) => {
            const accion = id ? 'actualizar' : 'insertar';
            const data = {
                accion: accion,
                nombre: nombre,
                colores: colores,
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
                    if (data.icon === 'bi bi-check-circle') {
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
            cambiarMensajeModal(
                "#modalGuardando",
                arrayResponse.title,
                arrayResponse.text,
                arrayResponse.icon,
                true,
            );
            $('#container-progress-bar').hide(); // Oculta la barra de progreso al terminar
        })
        .catch(error => {
            cambiarMensajeModal(
                "#modalGuardando",
                arrayResponse.title,
                arrayResponse.text,
                arrayResponse.icon,
                true,
            );
            console.error(error);
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

function eliminarAccesorio(id, nombre) {

    eliminarRegistro({
        id,
        nombre,
        entidad: ['accesorio', 'accesorios' , 'el accesorio'],
        url: backend + urlAccesory,
        callback: aplicarFiltrosAccesorio
    });

}

function aplicarFiltrosAccesorio() {
    const nombre = $('#Nombre').val();
    seleccionarAccesorios(nombre);
}

let tokenCargaAccesorios = 0;

async function seleccionarAccesorios(nombre = '') {

    const currentToken = ++tokenCargaAccesorios;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try {

        const response = await $.ajax({

            url: backend + urlAccesory,

            type: 'POST',

            dataType: 'json',

            data: {
                accion: 'listarIds',
                nombre: nombre,
                orden: order
            }
        });

        if (currentToken !== tokenCargaAccesorios) {
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(
            ids.length,
            ['accesorio', 'accesorios']
        );

        if (ids.length === 0) {

            container.html(`
                <div class="orders-empty">
                    No se encontraron accesorios.
                </div>
            `);

            return;
        }

        await cargarAccesoriosProgresivamente(
            ids,
            currentToken
        );

    }
    catch (error) {

        console.error(error);

        if (currentToken === tokenCargaAccesorios) {

            container.html(`
                <div class="orders-empty">
                    Error al cargar accesorios.
                </div>
            `);
        }
    }
}

async function cargarAccesoriosProgresivamente(
    ids,
    currentToken
) {

    const container = $('#list-container');

    container.empty();

    for (const item of ids) {
        renderAccesorioSkeleton(item);

        if (currentToken !== tokenCargaAccesorios) {
            return;
        }

        try {

            const response = await $.ajax({

                url: backend + urlAccesory,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if (currentToken !== tokenCargaAccesorios) {
                return;
            }

            const accesorio = response;

            if (!accesorio) {
                continue;
            }

            const accesorioFinal =
                typeof accesorio === 'string'
                    ? JSON.parse(accesorio)
                    : accesorio;

            $(`#accesorio-skeleton-${accesorioFinal.id}`)
                .replaceWith(
                    renderAccesorioCard(
                        accesorioFinal,
                        true
                    )
                );

            buscarImagenAccesorio(
                accesorioFinal.id
            );
        }
        catch (error) {

            console.error(
                'Error cargando accesorio',
                item.id,
                error
            );
        }
    }
}

function renderAccesorioCard(
    accesorio,
    returnHtml = false
) {

    const json = encodeURIComponent(
        JSON.stringify(accesorio)
    );

    const countColores =
        accesorio.idColores
        ? accesorio.idColores.split(',').length
        : 0;

    const html = `

        <div
            class="product-admin-card"
            id="accesorio-${accesorio.id}"
        >

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el ${formatearFechaConHora(accesorio.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${accesorio.nombre || 'Sin nombre'}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <img
                        id="img-${accesorio.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${accesorio.nombre}"
                    >

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>

                            <span>Paletas:</span>

                            <strong>
                                ${
                                    countColores < 16
                                    ? `${countColores} de 16`
                                    : 'Las 16'
                                }
                            </strong>

                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addAccesory.php?id=${accesorio.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarAccesorio(
                                ${accesorio.id},
                                '${accesorio.nombre}'
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

    if (returnHtml) {
        return html;
    }

    $('#list-container').append(html);
}

function renderAccesorioSkeleton(id) {

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="accesorio-skeleton-${id}"
        >

            <div class="product-admin-header">

                <div>

                    <div class="skeleton-line skeleton-subtitle"></div>

                    <div class="skeleton-line skeleton-title"></div>

                </div>

            </div>

            <div class="product-admin-body">

                <div
                    class="product-admin-image skeleton-box"
                ></div>

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

                    </div>

                </div>

                <div class="order-actions">

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                </div>

            </div>

        </div>

    `);
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
