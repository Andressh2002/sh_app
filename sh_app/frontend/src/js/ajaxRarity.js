function guardarRareza(){

    const id =
        document.getElementById('Id').value || null;

    const nombre = $('#Nombre').val();
    const color = $('#Color').val();

    if(
        !validarCampos(
            [nombre, color],
            ['el nombre', 'el color']
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

    $.ajax({

        url: backend + urlRarity,

        type: 'POST',

        data: {
            accion: id ? 'actualizar' : 'insertar',
            id: id,
            nombre: nombre,
            color: color
        },

        success: function(response){

            const data =
                typeof response === 'string'
                    ? JSON.parse(response)
                    : response;

            cambiarMensajeModal(
                "#modalGuardando",
                data.title,
                data.text,
                data.icon,
                true
            );
        },

        error: function(error){

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

function buscarRareza(id) {
    $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const rareza = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarRareza(rareza);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarRareza(rareza) {
    if (!rareza) {
        setRarityLoading(false);
        return;
    }
    $('#Nombre').val(rareza.nombre);
    $('#Color').val(rareza.color);
    setTimeout(function(){ setRarityLoading(false); }, 250);
}

function eliminarRareza(id, nombre, eliminar) {

    eliminarRegistro({
        id,
        nombre,
        entidad: ['rareza', 'rarezas' , 'la rareza'],
        url: backend + urlRarity,
        callback: aplicarFiltrosRareza
    });
}

function aplicarFiltrosRareza() {
    const nombre = $('#Nombre').val();
    seleccionarRarezas(nombre);
}

let tokenCargaRarezas = 0;

async function seleccionarRarezas(nombre){

    const currentToken = ++tokenCargaRarezas;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const response = await $.ajax({

            url: backend + urlRarity,

            type: 'POST',

            dataType: 'json',

            data: {
                accion: 'listarIds',
                nombre: nombre || '',
                orden: order
            }
        });

        if(currentToken !== tokenCargaRarezas){
            return;
        }

        const ids = response || [];

        mostrarTotalRegistros(
            ids.length,
            ['rarezas', 'rarezas']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron rarezas.
                </div>
            `);

            return;
        }

        await cargarRarezasProgresivamente(
            ids,
            currentToken
        );

    }catch(error){

        console.error(error);

    }

}

async function cargarRarezasProgresivamente(
    ids,
    currentToken
){

    $('#list-container').empty();

    for(const item of ids){

        renderRarezaSkeleton(item);

        if(currentToken !== tokenCargaRarezas){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlRarity,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if(currentToken !== tokenCargaRarezas){
                return;
            }

            const rareza = response;

            if(!rareza){
                continue;
            }

            const rarezaFinal =
                typeof rareza === 'string'
                    ? JSON.parse(rareza)
                    : rareza;

            $(`#rareza-skeleton-${rarezaFinal.id}`)
                .replaceWith(
                    renderRarezaCard(
                        rarezaFinal,
                        true
                    )
                );
        }
        catch(error){

            console.error(
                'Error cargando rareza',
                item.id,
                error
            );
        }
    }
}

function renderRarezaCard(
    rareza,
    returnHtml = false
){

    const html = `

        <div
            class="product-admin-card"
            id="rareza-${rareza.id}"
        >

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el ${formatearFechaConHora(rareza.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${rareza.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <div
                        class="position-relative btn-palette"
                        style="
                            width:75%;
                            height:75%;
                            background:${rareza.color};
                            clip-path: polygon(0 0, 96% 0, 100% 100%, 5% 100%);
                        "
                    ></div>

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Nombre:</span>
                            <strong>${rareza.nombre}</strong>
                        </div>

                        <div>
                            <span>Productos relacionados:</span>
                            <strong>
                                ${rareza.total_productos}
                            </strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addRarity.php?id=${rareza.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarRareza(
                                ${rareza.id},
                                '${rareza.nombre}',
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

function renderRarezaSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="rareza-skeleton-${id}"
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

function limpiarFiltrosRareza() {
    $('#Nombre').val('');
}

