function guardarDescuento() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const descuento = $('#Descuento').val();
    const diaInicio = $('#DayStartDate').val();
    const diaFinal = $('#DayEndDate').val();
    const mesInicio = $('#MonthStartDate').val();
    const mesFinal = $('#MonthEndDate').val();
    const fechaInicio = mesInicio + '-' + diaInicio;
    const fechaFinal = mesFinal + '-' + diaFinal;

    if (!validarCampos(
        [nombre, descuento, diaInicio, diaFinal, mesInicio, mesFinal],
        ['el nombre', 'el descuento', 'el día de inicio', 'el día de finalización', 'el mes de inicio', 'el mes de finalización']
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
            fecha_final: fechaFinal,
            descuento: descuento,
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlDiscount,
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

function buscarDescuento(id) {
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
                mostrarDescuento(descuento);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarDescuento(descuento) {
    if (!descuento) {
        setDiscountLoading(false);
        return;
    }
    const fechaInicio = descuento.fecha_inicial.split('-');
    const fechaFinal = descuento.fecha_final.split('-');
    $('#Nombre').val(descuento.nombre);
    $('#Descuento').val(descuento.descuento);
    $('#DayStartDate').val(fechaInicio[1]);
    $('#DayEndDate').val(fechaFinal[1]);
    $('#MonthStartDate').val(fechaInicio[0]);
    $('#MonthEndDate').val(fechaFinal[0]);
    setTimeout(function(){ setDiscountLoading(false); }, 250);
}

function eliminarDescuento(id, nombre, eliminar) {
    
    eliminarRegistro({
        id,
        nombre,
        entidad: ['descuento', 'descuentos' , 'el descuento'],
        url: backend + urlDiscount,
        callback: aplicarFiltrosDescuento
    });
}

function aplicarFiltrosDescuento() {
    const nombre = $('#Nombre').val();
    seleccionarDescuentos(nombre);
}

let tokenCargaDescuentos = 0;

async function seleccionarDescuentos(
    nombre = ''
){

    const currentToken = ++tokenCargaDescuentos;

    const container = $('#list-container');

    container.empty();

    const order = {
        orden: $('#Ordenar_por').val(),
        forma: $('#Ordenar_en').val()
    };

    try{

        const ids = await $.ajax({

            url: backend + urlDiscount,

            type: 'POST',

            dataType: 'json',

            data:{
                accion:'listarIds',
                nombre,
                orden: order
            }
        });

        if(currentToken !== tokenCargaDescuentos){
            return;
        }

        mostrarTotalRegistros(
            ids.length,
            ['descuento','descuentos']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron descuentos.
                </div>
            `);

            return;
        }

        await cargarDescuentosProgresivamente(
            ids,
            currentToken
        );

    }
    catch(error){

        console.error(error);

    }

}

async function cargarDescuentosProgresivamente(
    ids,
    currentToken
){

    $('#list-container').empty();

    for(const item of ids){

        renderDescuentoSkeleton(item);

        if(currentToken !== tokenCargaDescuentos){
            return;
        }

        try{

            const response = await $.ajax({

                url: backend + urlDiscount,

                type: 'POST',

                dataType: 'json',

                data:{
                    accion:'buscarPorId',
                    id:item
                }

            });

            if(currentToken !== tokenCargaDescuentos){
                return;
            }

            const descuento = response;

            if(!descuento){
                continue;
            }

            const descuentoFinal =
                typeof descuento === 'string'
                    ? JSON.parse(descuento)
                    : descuento;

            $(`#descuento-skeleton-${descuentoFinal.id}`)
                .replaceWith(
                    renderDescuentoCard(
                        descuentoFinal,
                        true
                    )
                );

        }
        catch(error){

            console.error(
                'Error cargando descuento',
                item.id,
                error
            );

        }

    }

}

function renderDescuentoCard(
    descuento,
    returnHtml = false
){

    const dias = calcularDias(descuento.fecha_inicial, descuento.fecha_final);

    const html = `

        <div class="product-admin-card">

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el
                        ${formatearFechaConHora(descuento.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${descuento.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <img
                        id="img-${descuento.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${descuento.nombre}"
                    >

                </div>


                <div class="product-info">

                    <div class="product-info-grid">

                        <div>

                            <span>Porcentaje:</span>

                            <strong>
                                ${descuento.descuento}%
                            </strong>

                        </div>

                        <div>
                            <span>Periodo:</span>
                            <strong>
                                Del
                                ${formarFecha(descuento.fecha_inicial)}
                                al
                                ${formarFecha(descuento.fecha_final)}
                            </strong>
                        </div>

                        <div>
                            <span>Duración:</span>
                            <strong>
                                ${dias} ${dias === 1 ? 'día' : 'días'}
                            </strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="addDiscount.php?id=${descuento.id}&accion=actualizar"
                        class="store-filter-btn px-4 justify-content-center text-decoration-none"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="
                            eliminarDescuento(
                                ${descuento.id},
                                '${descuento.nombre}',
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

function renderDescuentoSkeleton(id){

    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="descuento-skeleton-${id}"
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

function limpiarFiltrosDescuento() {
    $('#Nombre').val('');
}
