function guardarFestividad() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const diaInicio = $('#DayStartDate').val();
    const diaFinal = $('#DayEndDate').val();
    const mesInicio = $('#MonthStartDate').val();
    const mesFinal = $('#MonthEndDate').val();
    const fechaInicio = mesInicio + '-' + diaInicio;
    const fechaFinal = mesFinal + '-' + diaFinal;

    if (!validarCampos(
        [nombre, diaInicio, diaFinal, mesInicio, mesFinal],
        ['el nombre', 'el día de inicio', 'el día de finalización', 'el mes de inicio', 'el mes de finalización']
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
            fecha_inicial: fechaInicio,
            fecha_final: fechaFinal
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlHoliday,
            type: 'POST',
            data: data,
            success: function (response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                cambiarMensajeModal(
                    "#modalGuardando",
                    data.title,
                    data.text,
                    data.icon,
                    true
                );
            },
            error: function (error) {
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

function buscarFestividad(id) {
    $.ajax({
        url: backend + urlHoliday,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function (response) {
            try {
                const festividad = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarFestividad(festividad);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarFestividad(festividad) {
    if (!festividad) {
        setHolidayLoading(false);
        return;
    }
    const fechaInicio = festividad.fecha_inicial.split('-');
    const fechaFinal = festividad.fecha_final.split('-');
    $('#Nombre').val(festividad.nombre);
    $('#DayStartDate').val(fechaInicio[1]);
    $('#DayEndDate').val(fechaFinal[1]);
    $('#MonthStartDate').val(fechaInicio[0]);
    $('#MonthEndDate').val(fechaFinal[0]);
    setTimeout(function(){ setHolidayLoading(false); }, 250);
}

function eliminarFestividad(id, nombre, eliminar) {
    
    eliminarRegistro({
        id,
        nombre,
        entidad: ['festividad', 'festividades' , 'la festividad'],
        url: backend + urlHoliday,
        callback: aplicarFiltrosFestividad
    });
}

function aplicarFiltrosFestividad() {
    const nombre = $('#Nombre').val();
    seleccionarFestividades(nombre);
}

let tokenCargaFestividades = 0;

async function seleccionarFestividades(
    nombre = ''
){

    const currentToken = ++tokenCargaFestividades;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const ids = await $.ajax({

            url: backend + urlHoliday,

            type: 'POST',

            dataType: 'json',

            data:{
                accion:'listarIds',
                nombre,
                orden: order
            }
        });

        if(currentToken !== tokenCargaFestividades){
            return;
        }

        mostrarTotalRegistros(
            ids.length,
            ['festividad','festividades']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron festividades.
                </div>
            `);

            return;
        }

        await cargarFestividadesProgresivamente(
            ids,
            currentToken
        );

    }catch(error){

        console.error(error);

    }
}

async function cargarFestividadesProgresivamente(
    ids,
    currentToken
){

    $('#list-container').empty();

    for(const item of ids){

        renderFestividadSkeleton(item);

        if(currentToken !== tokenCargaFestividades){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlHoliday,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'buscarPorId',
                    id: item
                }
            });

            if(currentToken !== tokenCargaFestividades){
                return;
            }

            const festividad = response;

            if(!festividad){
                continue;
            }

            const festividadFinal =
                typeof festividad === 'string'
                    ? JSON.parse(festividad)
                    : festividad;

            $(`#festividad-skeleton-${festividadFinal.id}`)
                .replaceWith(
                    renderFestividadCard(
                        festividadFinal,
                        true
                    )
                );
        }
        catch(error){

            console.error(
                'Error cargando festividad',
                item.id,
                error
            );
        }
    }
}

function renderFestividadCard(
    festividad,
    returnHtml = false
){

    const dias = calcularDias(festividad.fecha_inicial, festividad.fecha_final);

    const html = `

        <div class="product-admin-card">

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el
                        ${formatearFechaConHora(festividad.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${festividad.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <img
                        id="img-${festividad.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${festividad.nombre}"
                    >

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Periodo:</span>
                            <strong>
                                ${'Del ' + formarFecha(festividad.fecha_inicial) + ' al ' + formarFecha(festividad.fecha_final)}
                            </strong>
                        </div>

                        <div>
                            <span>Duración:</span>
                            <strong>
                                ${dias} ${dias === 1 ? 'día' : 'días'}
                            </strong>
                        </div>

                        <div>
                            <span>Productos relacionados:</span>
                            <strong>
                                ${festividad.total_productos}
                            </strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addHoliday.php?id=${festividad.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarFestividad(
                                ${festividad.id},
                                '${festividad.nombre}',
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

function renderFestividadSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="festividad-skeleton-${id}"
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

function limpiarFiltrosFestividad() {
    $('#Nombre').val('');
}
