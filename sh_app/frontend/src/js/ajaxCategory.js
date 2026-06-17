function guardarCategoria() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const imagen = $('#hiddenImagenCategoria').val();

    if (!validarCampos(
        [nombre, imagen.length > 30 ? 'A' : ''],
        ['el nombre', 'la imagen de portada']
    )) {
        return;
    }
    
    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Guardando...", 'Espere un momento...', "bi bi-wifi", false);

    guardarDatos();

    function guardarDatos() {
        const accion = id ? 'actualizar' : 'insertar';
        const data = {
            accion: accion,
            nombre: nombre,
            imagen: imagen
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlCategory,
            type: 'POST',
            data: data,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                cambiarMensajeModal(
                    "#modalGuardando",
                    data.title,
                    data.text,
                    data.icon,
                    true,
                );
            },
            error: function(error) {
                cambiarMensajeModal(
                    "#modalGuardando",
                    error.title,
                    error.text,
                    error.icon,
                    true,
                );
            }
        });
    }
}

function buscarImagenCategoria(id) {
    $.ajax({
        url: backend + urlCategory,
        type: 'POST',
        data: {
            accion: 'buscarImagen',
            id: id,
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

function buscarCategoria(id) {
    $.ajax({
        url: backend + urlCategory,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const categoria = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarCategoria(categoria);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarCategoria(categoria) {
    if (categoria) {
        $('#Nombre').val(categoria.nombre);

        cargarImagenGuardada(categoria.imagen, '#vistaImagenCategoria');
        $('#hiddenImagenCategoria').val(categoria.imagen);
    }
}

function eliminarCategoria(id, nombre) {

    eliminarRegistro({
        id,
        nombre,
        entidad: ['categoría', 'categorías' , 'la categoría'],
        url: backend + urlCategory,
        callback: aplicarFiltrosCategoria
    });
}

function aplicarFiltrosCategoria() {
    const nombre = $('#Nombre').val();
    seleccionarCategorias(nombre);
}

let tokenCargaCategorias = 0;

async function seleccionarCategorias(nombre){

    const currentToken = ++tokenCargaCategorias;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const response = await $.ajax({

            url: backend + urlCategory,

            type: 'POST',

            dataType: 'json',

            data: {
                accion: 'listarIds',
                nombre: nombre || '',
                orden: order
            }
        });

        if(currentToken !== tokenCargaCategorias){
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(
            ids.length,
            ['categoría', 'categorías']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron categorías.
                </div>
            `);

            return;
        }

        await cargarCategoriasProgresivamente(
            ids,
            currentToken
        );

    }
    catch(error){

        console.error(error);

        if(currentToken === tokenCargaCategorias){

            container.html(`
                <div class="orders-empty">
                    Error al cargar categorías.
                </div>
            `);
        }
    }
}

async function cargarCategoriasProgresivamente(
    ids,
    currentToken
){

    $('#list-container').empty();

    for(const item of ids){

        renderCategoriaSkeleton(item);

        if(currentToken !== tokenCargaCategorias){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlCategory,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if(currentToken !== tokenCargaCategorias){
                return;
            }

            const categoria = response;

            if(!categoria){
                continue;
            }

            const categoriaFinal =
                typeof categoria === 'string'
                    ? JSON.parse(categoria)
                    : categoria;

            $(`#categoria-skeleton-${categoriaFinal.id}`)
                .replaceWith(
                    renderCategoriaCard(
                        categoriaFinal,
                        true
                    )
                );

            buscarImagenCategoria(
                categoriaFinal.id
            );
        }
        catch(error){

            console.error(
                'Error cargando categoría',
                item.id,
                error
            );
        }
    }
}

function renderCategoriaCard(
    categoria,
    returnHtml = false
){

    const json = encodeURIComponent(
        JSON.stringify(categoria)
    );

    const html = `

        <div
            class="product-admin-card"
            id="categoria-${categoria.id}"
        >

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el ${formatearFechaConHora(categoria.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${categoria.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <img
                        id="img-${categoria.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${categoria.nombre}"
                    >

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Nombre:</span>
                            <strong>
                                ${categoria.nombre}
                            </strong>
                        </div>

                        <div>
                            <span>Productos relacionados:</span>
                            <strong>
                                ${categoria.total_productos}
                            </strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addCategory.php?id=${categoria.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarCategoria(
                                ${categoria.id},
                                '${categoria.nombre}'
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

function renderCategoriaSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="categoria-skeleton-${id}"
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

                    </div>

                </div>

                <div class="product-admin-actions">

                    <div class="skeleton-button"></div>

                    <div class="skeleton-button"></div>

                </div>

            </div>

        </div>

    `);
}

function limpiarFiltrosCategoria() {
    $('#Nombre').val('');
}

// Asignar el mismo manejador de eventos a todos los filtros
//$('#Nombre').on('change', aplicarFiltrosCategoria);
