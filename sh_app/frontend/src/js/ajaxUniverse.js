function guardarUniverso() {

    const id =
        document.getElementById('Id').value || null;

    const nombre = $('#Nombre').val();
    const imagen = $('#hiddenImagenUniverso').val();
    const logo = $('#hiddenLogoUniverso').val();

    if(
        !validarCampos(
            [nombre, imagen.length > 30 ? 'A' : '', logo.length > 30 ? 'A' : ''],
            ['el nombre', 'la imagen', 'el logo']
        )
    ){
        return;
    }

    abrirModal('modalGuardando');

    cambiarMensajeModal(
        "#modalGuardando",
        "Guardando...",
        "Espere un momento...",
        "bi bi-wifi",
        false
    );

    let arrayResponse = [];

    function guardarDatos() {
        return new Promise((resolve, reject) => {
            const accion = id ? 'actualizar' : 'insertar';
            const data = {
                accion: accion,
                nombre: nombre
            };

            if (id) {
                data.id = id;
            }

            $.ajax({
                url: backend + urlUniverse,
                type: 'POST',
                data: data,
                success: function(response){
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    arrayResponse = data;

                    if (data.icon === 'bi bi-check-circle' && data.universo_id) {
                        resolve(data.universo_id); // Devuelve el ID del producto
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

    function guardarImagenUniverso(id, imagen, campo){
        return new Promise((resolve,reject)=>{

            $.ajax({
                url: backend + urlUniverse,
                type: "POST",
                data:{
                    accion: "insertarImagen",
                    id: id,
                    campo: campo,
                    imagen: imagen
                },

                success:function(response){
                    const data = typeof response === "string" ? JSON.parse(response) : response;

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
    .then(async universeId => {
        if(imagen){
            await guardarImagenUniverso(
                universeId,
                imagen,
                "imagen"
            );
        }

        if(logo){
            await guardarImagenUniverso(
                universeId,
                logo,
                "logo"
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
    })
    .catch(error => {
        cambiarMensajeModal(
            "#modalGuardando",
            "Error",
            error,
            "bi bi-x-circle",
            true
        );
    });
}

function buscarImagenUniverso(id) {
    $.ajax({
        url: backend + urlUniverse,
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
            console.error('Error al cargar la imagen del universo.');
        }
    });
}

function buscarLogoUniverso(id) {
    $.ajax({
        url: backend + urlUniverse,
        type: 'POST',
        data: {
            accion: 'buscarLogo',
            id: id,
        },
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                const imagenURL = data[0].logo && data[0].logo !== '' ? data[0].logo : '../src/img/app/no_image.png';

                const imgElement = document.getElementById(`img-logo-${id}`);
                const spinnerElement = document.getElementById(`spinner-logo-${id}`);

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
                console.error('Error al procesar el logo:', error);
            }
        },
        error: function () {
            console.error('Error al cargar el logo del universo.');
        }
    });
}

function buscarUniverso(id) {
    $.ajax({
        url: backend + urlUniverse,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const universo = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarUniverso(universo);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarUniverso(universo) {
    if (!universo) {
        setUniverseLoading(false);
        return;
    }
    $('#Nombre').val(universo.nombre);
    cargarImagenGuardada(universo.imagen, '#vistaImagenUniverso');
    $('#hiddenImagenUniverso').val(universo.imagen);
    cargarImagenGuardada(universo.logo, '#vistaLogoUniverso');
    $('#hiddenLogoUniverso').val(universo.logo);
    setTimeout(function(){ setUniverseLoading(false); }, 250);
}

function eliminarUniverso(id, nombre) {

    eliminarRegistro({
        id,
        nombre,
        entidad: ['universo', 'universos' , 'el universo'],
        url: backend + urlUniverse,
        callback: aplicarFiltrosUniverso
    });

}

function aplicarFiltrosUniverso() {
    const nombre = $('#Nombre').val();
    seleccionarUniversos(nombre);
}

let tokenCargaUniversos = 0;

async function seleccionarUniversos(nombre){

    const currentToken = ++tokenCargaUniversos;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const response = await $.ajax({

            url: backend + urlUniverse,

            type: 'POST',

            dataType: 'json',

            data: {
                accion: 'listarIds',
                nombre: nombre || '',
                orden: order
            }
        });

        if(currentToken !== tokenCargaUniversos){
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(
            ids.length,
            ['universos', 'universos']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron universos.
                </div>
            `);

            return;
        }

        await cargarUniversosProgresivamente(
            ids,
            currentToken
        );

    }
    catch(error){

        console.error(error);

        if(currentToken === tokenCargaUniversos){

            container.html(`
                <div class="orders-empty">
                    Error al cargar universo.
                </div>
            `);
        }
    }
}

async function cargarUniversosProgresivamente(
    ids,
    currentToken
){

    $('#list-container').empty();

    for(const item of ids){

        renderUniversoSkeleton(item);

        if(currentToken !== tokenCargaUniversos){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlUniverse,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if(currentToken !== tokenCargaUniversos){
                return;
            }

            const universo = response;

            if(!universo){
                continue;
            }

            const universoFinal =
                typeof universo === 'string'
                    ? JSON.parse(universo)
                    : universo;

            $(`#universo-skeleton-${universoFinal.id}`)
                .replaceWith(
                    renderUniversoCard(
                        universoFinal,
                        true
                    )
                );

            buscarImagenUniverso(
                universoFinal.id
            );

            buscarLogoUniverso(
                universoFinal.id
            );
        }
        catch(error){

            console.error(
                'Error cargando universo',
                item.id,
                error
            );
        }
    }
}

function renderUniversoCard(
    universo,
    returnHtml = false
){

    const json = encodeURIComponent(
        JSON.stringify(universo)
    );

    const html = `

        <div
            class="product-admin-card"
            id="universo-${universo.id}"
        >

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el ${formatearFechaConHora(universo.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${universo.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <div class="w-100 d-block">
                        <img
                        id="img-${universo.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${universo.nombre}"
                        >
                    </div>
                    
                    <div class="w-100 d-block p-3">
                        <img
                            id="img-logo-${universo.id}"
                            class="product-image"
                            src="../src/img/app/no_image.png"
                            alt="${universo.nombre}"
                        >
                    </div>

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Nombre:</span>
                            <strong>
                                ${universo.nombre}
                            </strong>
                        </div>

                        <div>
                            <span>Productos relacionados:</span>
                            <strong>
                                ${universo.total_productos}
                            </strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addUniverse.php?id=${universo.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarUniverso(
                                ${universo.id},
                                '${universo.nombre}'
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

function renderUniversoSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="universo-skeleton-${id}"
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

function limpiarFiltrosUniverso() {
    $('#Nombre').val('');
}
