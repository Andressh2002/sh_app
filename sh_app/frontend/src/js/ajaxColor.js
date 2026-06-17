function guardarColor() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const color1 = $('#Color1').val();
    const color2 = $('#Color2').val();
    const color3 = $('#Color3').val();
    const familia = $('#Familia').val();

    if (!validarCampos(
        [nombre, color1, color2, color3, familia],
        ['el nombre', 'el color principal', 'el color secundario', 'el color terciario', 'la familia de color al que pertenece']
    )) {
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

    guardarDatos();

    function guardarDatos() {
        const accion = id ? 'actualizar' : 'insertar';
        const data = {
            accion: accion,
            nombre: nombre,
            color1: color1,
            color2: color2,
            color3: color3,
            familia: familia
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlColor,
            type: 'POST',
            data: data,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                cambiarMensajeModal(
                    "#modalGuardando",
                    data.title,
                    data.text,
                    data.icon,
                    true
                );
            },
            error: function(error) {
                cambiarMensajeModal(
                    "#modalGuardando",
                    error.title,
                    error.text,
                    error.icon,
                    true
                );
            }
        });
    }
}

function buscarColor(id) {
    $.ajax({
        url: backend + urlColor,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const color = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarColor(color);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarColor(color) {
    if (color) {
        $('#Nombre').val(color.nombre);
        $('#Color1').val(color.codigo_color_principal);
        $('#Color2').val(color.codigo_color_secundario);
        $('#Color3').val(color.codigo_color_terciario);
        $('#Familia').val(color.color_familia);
    }
}

function eliminarColor(id, nombre, eliminar) {
    
    eliminarRegistro({
        id,
        nombre,
        entidad: ['color', 'colores' , 'el color'],
        url: backend + urlColor,
        callback: aplicarFiltrosColor
    });
}

function aplicarFiltrosColor() {
    const nombre = $('#Nombre').val();
    const familia = $('#Familia').val();
    seleccionarColores(nombre, familia);
}

let tokenCargaColores = 0;

async function seleccionarColores(
    nombre = '',
    familia = ''
){

    const currentToken = ++tokenCargaColores;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const ids = await $.ajax({

            url: backend + urlColor,

            type: 'POST',

            dataType: 'json',

            data:{
                accion:'listarIds',
                nombre: nombre || '',
                familia: familia || '',
                orden: order
            }
        });

        if(currentToken !== tokenCargaColores){
            return;
        }

        mostrarTotalRegistros(
            ids.length,
            ['colores','colores']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron colores.
                </div>
            `);

            return;
        }

        await cargarColoresProgresivamente(
            ids,
            currentToken
        );

    }catch(error){

        console.error(error);

    }
}

async function cargarColoresProgresivamente(
    ids,
    currentToken
){

    $('#list-container').empty();

    for(const item of ids){

        renderColorSkeleton(item);

        if(currentToken !== tokenCargaColores){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlColor,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if(currentToken !== tokenCargaColores){
                return;
            }

            const color = response;

            if(!color){
                continue;
            }

            const colorFinal =
                typeof color === 'string'
                    ? JSON.parse(color)
                    : color;

            $(`#color-skeleton-${colorFinal.id}`)
                .replaceWith(
                    renderColorCard(
                        colorFinal,
                        true
                    )
                );
        }
        catch(error){

            console.error(
                'Error cargando color',
                item.id,
                error
            );
        }
    }
}

function renderColorCard(
    color,
    returnHtml = false
){

    const html = `

        <div class="product-admin-card">

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el
                        ${formatearFechaConHora(color.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${color.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <div
                        class="position-relative btn-palette"
                        style="
                            background:${color.codigo_color_principal};
                            width:75%;
                            height:75%;
                            clip-path: polygon(0 0, 96% 0, 100% 100%, 5% 100%);
                        "
                    >

                        <div
                            class="position-absolute"
                            style=" 
                                background:${color.codigo_color_secundario};
                                width:100%;
                                height:100%;
                                clip-path: polygon(100% 5%, 20% 100%, 100% 100%);
                            "
                        ></div>

                        <div
                            class="position-absolute"
                            style=" 
                                background:${color.codigo_color_terciario};
                                width:100%;
                                height:100%;
                                clip-path: polygon(100% 60%, 45% 100%, 100% 100%);
                            "
                        ></div>

                    </div>

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Color:</span>
                            <strong>${color.color_familia}</strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addColor.php?id=${color.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarColor(
                                ${color.id},
                                '${color.nombre}',
                                false
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

    return returnHtml
    ? html
    : $('#list-container').append(html);
}
    

function renderColorSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="color-skeleton-${id}"
        >

            <div class="product-admin-header">

                <div>

                    <div class="skeleton-line skeleton-subtitle"></div>

                    <div class="skeleton-line skeleton-title"></div>

                </div>

            </div>

            <div class="product-admin-body">

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

function limpiarFiltrosColor() {
    $('#Nombre').val('');
    $('#Familia').val('');
}

