function mostrarTotalRegistros(total, elemento = ["registro", "registros"], showing = false) {
    const containerTotal = $('#total-data');
    containerTotal.empty();
    const html = `
        ${showing ? 'Mostrando' : 'Hay'} ${total == 1 ? `1 ${elemento[0]}` : (total + ` ${elemento[1]}`)}
    `;
    containerTotal.append(html);
}

function toggleLoadingIcon(idElement, option, colspan, size) {
    const container = $('#' + idElement);
    container.empty();

    if (option) {
        container.append(`
            <tr><td class="text-center" colspan="${colspan}">
                <div class="spinner-border spinner-color custom-spinner" role="status" id="spinner" style="width: ${size}px; height: ${size}px;">
                    <span class="visually-hidden"></span>
                </div>
            </td></tr>`
        );
    } else {
        container.append(`
            <tr><td class="text-center" colspan="${colspan}">A ocurrido un error, intentelo nuevamente.</td></tr>`
        );
    }

}

function toggleLoadingIconCard(idElements, option, size) {
    const container1 = $('#' + idElements[0]);
    const container2 = $('#' + idElements[1]);
    container1.empty();
    container2.empty();

    if (option) {
        container1.append(`
                <div class="spinner-border spinner-color custom-spinner" role="status" id="" style="width: ${size[0]}px; height: ${size[0]}px;">
                    <span class="visually-hidden"></span>
                </div>`
        );

        container2.append(`
            <div class="spinner-border spinner-color custom-spinner" role="status" id="" style="width: ${size[1]}px; height: ${size[1]}px;">
                <span class="visually-hidden"></span>
            </div>`
        );
    } else {
        container1.append(`
            ERROR`
        );

        container2.append(`
            ERROR`
        );
    }
}

function toggleLoadingIconStoreCard(idElement, option, size) {
    const container = $('#' + idElement);
    container.empty();

    if (option) {
        // Crear una tarjeta similar a las demás con el spinner en el centro
        container.append(`
            <div class="card mx-auto rounded-0 shadow-sm my-3 card-hover card-categories-sizes">
                <div class="card-body card-body-product d-flex justify-content-center align-items-center">
                    <div class="spinner-border spinner-color custom-spinner" text-primary" role="status" style="width: ${size[0]}px; height: ${size[0]}px;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `);
    } else {
        container.append('ERROR');
    }
}

function searchProductsFilter() {
    const value = $('#btnSearch').val();
    location.href = '../pages/productos.php?nombreProducto=' + encodeURIComponent(value) + '&nombreCategoria=';
}

function formatearFecha(fecha) {
    const fechaObj = new Date(fecha); // Crear un objeto de fecha a partir de la cadena
    const dia = String(fechaObj.getDate()).padStart(2, '0'); // Obtener el día con dos dígitos
    const mes = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Obtener el mes (agregando 1 ya que getMonth() empieza en 0)
    const año = fechaObj.getFullYear(); // Obtener el año

    return `${dia}/${mes}/${año}`; // Formatear en DD/MM/YYYY
}

function formatearFechaConHora(fecha) {
    const fechaObj = new Date(fecha);

    // Obtener el día, mes y año
    const dia = String(fechaObj.getDate()).padStart(2, '0');
    const mes = String(fechaObj.getMonth() + 1).padStart(2, '0'); // Mes comienza en 0
    const año = fechaObj.getFullYear();

    // Obtener la hora, minutos y determinar si es AM o PM
    let horas = fechaObj.getHours();
    const minutos = String(fechaObj.getMinutes()).padStart(2, '0');
    const ampm = horas >= 12 ? 'PM' : 'AM';

    // Convertir a formato de 12 horas
    horas = horas % 12;
    horas = horas ? horas : 12; // El 0 debe ser 12 en el formato de 12 horas
    const horasFormateadas = String(horas).padStart(2, '0'); // Agregar el 0 si es necesario

    return `${dia}/${mes}/${año} ${horasFormateadas}:${minutos} ${ampm}`;
}

function irLogin() {
    location.href = 'login.php';
}

function irReservar(idProducto, idAccesorio) {
    const idColor = $('#Color').val();
    const idColorAccesorio = $('#AccesoryColor').val() == null || $('#AccesoryColor').val().trim() == "" ? "0" : $('#AccesoryColor').val();
    const cantidad = $('#cantidad').val();
    const total = $('#Total').val();
    const numColor = $('#NumColor').val();
    const numColorAccesorio = $('#NumAccesoryColor').val();
    location.href = `guardarPedido.php?idProducto=${idProducto}&idAccesorio=${idAccesorio}&idColor=${idColor}&idColorAccesorio=${idColorAccesorio}&numColor=${numColor}&numColorAccesorio=${numColorAccesorio}&cantidad=${cantidad}&total=${total}`;
}

function formarFecha(date) {
    if (date) {
        const format = date.split('-');
        let mes = '';
        switch (format[0]) {
            case '1':
                mes = 'enero';
                break;
            case '2':
                mes = 'febrero';
                break;
            case '3':
                mes = 'marzo';
                break;
            case '4':
                mes = 'abril';
                break;
            case '5':
                mes = 'mayo';
                break;
            case '6':
                mes = 'junio';
                break;
            case '7':
                mes = 'julio';
                break;
            case '8':
                mes = 'agosto';
                break;
            case '9':
                mes = 'septiembre';
                break;
            case '10':
                mes = 'octubre';
                break;
            case '11':
                mes = 'noviembre';
                break;
            case '12':
                mes = 'diciembre';
                break;
            default:
                break;
        }
        return format[1] + ' de ' + mes;
    }
    return null;
}

function obtenerColorProgreso(progreso){

    // limitar entre 0 y 100
    progreso = Math.max(
        0,
        Math.min(100, progreso)
    );

    // hue:
    // 0   = rojo
    // 60  = amarillo
    // 120 = verde

    const hue =
        (progreso * 120) / 100;

    return `
        hsl(
            ${hue},
            85%,
            48%
        )
    `;
}

function eliminarRegistro({
    id,
    nombre,
    entidad,
    url,
    callback
}) {

    abrirModalConfirmacion({
        titulo: '¿Estás seguro?',
        texto: `¿De verdad quieres eliminar "${nombre}" de ${entidad[1]}? ¡Si lo haces no se podrá revertir!`,
        icono: 'bi bi-trash-fill',

        callback: function () {

            abrirModal('modalGuardando');

            cambiarMensajeModal(
                "#modalGuardando",
                `Eliminando ${entidad[0]}`,
                `Se está eliminando ${entidad[2]}`,
                "bi bi-trash-fill",
                false
            );

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    accion: 'eliminar',
                    id: id
                },

                success: function () {

                    if (callback) {
                        callback();
                    }

                    cambiarMensajeModal(
                        "#modalGuardando",
                        "¡Eliminado!",
                        `${capitalizar(entidad[2])} ha sido eliminado correctamente`,
                        "bi bi-check-circle-fill",
                        true
                    );
                },

                error: function () {

                    cambiarMensajeModal(
                        "#modalGuardando",
                        "¡Error!",
                        `No se pudo eliminar ${entidad}`,
                        "bi bi-x-circle-fill",
                        true
                    );
                }
            });

        }
    });
}

function capitalizar(texto) {
  return texto.charAt(0).toUpperCase() + texto.slice(1);
}

function calcularDias(
    fechaInicial,
    fechaFinal
){

    const [mesInicio, diaInicio] =
        fechaInicial.split('-').map(Number);

    const [mesFinal, diaFinal] =
        fechaFinal.split('-').map(Number);

    const anioBase = 2024; // bisiesto

    const inicio = new Date(
        anioBase,
        mesInicio - 1,
        diaInicio
    );

    let final = new Date(
        anioBase,
        mesFinal - 1,
        diaFinal
    );

    // Cruza de año
    if(final < inicio){

        final = new Date(
            anioBase + 1,
            mesFinal - 1,
            diaFinal
        );
    }

    const diferencia =
        final.getTime() -
        inicio.getTime();

    return Math.floor(
        diferencia / (1000 * 60 * 60 * 24)
    ) + 1;
}
