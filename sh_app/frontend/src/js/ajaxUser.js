function guardarUsuario() {
    const id = document.getElementById('Id').value || null;
    const nombre = $('#Nombre').val();
    const nombreUsuario = $('#nombreUsuario').val();
    const contrasennia = $('#Contrasennia').val();
    const rol = $('#Rol').val();

    const segundoNombre = $('#segundoNombre').val();
    const primerApellido = $('#primerApellido').val();
    const segundoApellido = $('#segundoApellido').val();
    const provincia = $('#Provincia').val();
    const canton = $('#Canton').val();
    const distrito = $('#Distrito').val();
    const telefono = $('#Telefono').val();

    if (!id) {
        if (!validarCampos(
            [nombre, primerApellido, provincia, canton, distrito, nombreUsuario, contrasennia, rol],
            ['el nombre', 'el primer apellido', 'la provincia', 'el cantón', 'el distrito', 'el nombre de usuario', 'la contraseña', 'el rol']
        )) {
            if(contrasennia.length < 8){
                abrirModal('modalValidacion');
                $('#modalValidacion-body p').text(
                    'La contraseña debe tener al menos 8 caracteres.'
                );
                return;
            }
            return;
        }
    } else {
        if (!validarCampos(
            [nombre, primerApellido, provincia, canton, distrito, nombreUsuario, rol],
            ['el nombre', 'el primer apellido', 'la provincia', 'el cantón', 'el distrito', 'el nombre de usuario', 'el rol']
        )) {
            return;
        }
    }
    
    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", 'Guardando...', 'Espere un momento...', 'bi bi-wifi', false);
    
    guardarDatos();

    function guardarDatos() {
        const accion = id ? 'actualizar' : 'insertar';
        const data = {
            accion: accion,
            nombre: nombre,
            nombreUsuario: nombreUsuario,
            rol: rol,
            segundoNombre: segundoNombre,
            primerApellido: primerApellido,
            segundoApellido: segundoApellido,
            provincia: provincia,
            canton: canton,
            distrito: distrito,
            telefono: telefono,
        };

        if (id == null) {
            data.contrasennia = contrasennia;
        }

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlUser,
            type: 'POST',
            data: data,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);
            },
            error: function() {
                cambiarMensajeModal("#modalGuardando", '!Error¡', 'Hubo un problema al agregar el usuario.', "bi bi-x-circle", true);
            }
        });
    }
}

function buscarUsuario(id) {
    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const usuario = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarUsuario(usuario);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarUsuario(usuario) {
    if (usuario) {
        $('#Nombre').val(usuario.nombre);
        $('#segundoNombre').val(usuario.segundo_nombre);
        $('#primerApellido').val(usuario.primer_apellido);
        $('#segundoApellido').val(usuario.segundo_apellido);
        $('#nombreUsuario').val(usuario.nombre_usuario);
        $('#Provincia').val(usuario.provincia);
        $('#Canton').val(usuario.canton);
        $('#Distrito').val(usuario.distrito);
        $('#Telefono').val(usuario.telefono);
        $('#Rol').val(usuario.rol);
        $('#Contrasennia').prop('disabled', true);
    }
}

function eliminarUsuario(id, nombre, eliminar) {
    
    eliminarRegistro({
        id,
        nombre,
        entidad: ['usuario', 'usuarios' , 'el usuario'],
        url: backend + urlUser,
        callback: aplicarFiltrosUsuario
    });
}

function aplicarFiltrosUsuario() {
    const nombre = $('#Nombre').val();
    const rol = $('#Rol').val();
    seleccionarUsuarios(nombre, rol);
}

let tokenCargaUsuarios = 0;

async function seleccionarUsuarios(
    nombre = '',
    rol = ''
){

    const token = ++tokenCargaUsuarios;

    const container =
        $('#list-container');

    container.empty();

    try{

        const response =
            await $.ajax({

                url: backend + urlUser,

                type: 'POST',

                dataType: 'json',

                data: {
                    accion: 'listarIdsAdmin',
                    nombre,
                    rol,

                    orden: {
                        orden: $('#Ordenar_por').val(),
                        forma: $('#Ordenar_en').val()
                    }
                }

            });

        if(token !== tokenCargaUsuarios){
            return;
        }

        const ids =
            response || [];

        mostrarTotalRegistros(
            ids.length,
            ['usuario','usuarios']
        );

        if(ids.length === 0){

            container.html(`
                <div class="orders-empty">
                    No se encontraron usuarios.
                </div>
            `);

            return;
        }

        await cargarUsuarios(
            ids,
            token
        );

    }
    catch(error){

        container.html(`
            <div class="orders-empty">
                Error al cargar usuarios.
            </div>
        `);

    }

}

let usuarioSeleccionado = null;

async function cargarUsuarios(
    ids,
    token
){

    $('#list-container').empty();

    for(const item of ids){

        renderUsuarioSkeleton(item);

        if(token !== tokenCargaUsuarios){
            return;
        }

        try{

            const response =
                await $.ajax({

                    url: backend + urlUser,

                    type: 'POST',

                    dataType: 'json',

                    data: {
                        accion: 'buscarPorIdAdmin',
                        id: item
                    }

                });

            if(token !== tokenCargaUsuarios){
                return;
            }

            const usuario = response;

            if(!usuario){
                continue;
            }

            $(
                `#usuario-${usuario.id}`
            ).replaceWith(
                renderUsuarioCard(
                    usuario,
                    true
                )
            );

        }
        catch(error){

            console.error(
                error
            );

        }

    }

}

function renderUsuarioCard(
    usuario,
    returnHtml = false
){

    const html = `

        <div
            class="product-admin-card"
            id="usuario-${usuario.id}"
        >

            <div class="product-admin-header">

                <div>

                    <p class="product-number">
                        Registrado el ${formatearFechaConHora(usuario.fecha_registro)}
                    </p>

                    <h5 class="product-title">
                        ${usuario.nombre}
                    </h5>

                </div>

            </div>

            <div class="product-admin-body">

                <div class="product-admin-image">

                    <img
                        id="img-${usuario.id}"
                        class="product-image"
                        src="../src/img/app/no_image.png"
                        alt="${usuario.nombre_usuario}"
                    >

                </div>

                <div class="product-info">

                    <div class="product-info-grid">

                        <div>
                            <span>Usuario:</span>
                            <strong>
                                ${usuario.nombre_usuario}
                            </strong>
                        </div>

                        <div>
                            <span>Rol:</span>
                            <strong>
                                ${usuario.rol}
                            </strong>
                        </div>

                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start">
                            <span>Fichas SH:</span>
                            <strong>
                                <div class="d-flex gap-1 ms-1">
                                    <p class="mb-0">${usuario.fichas ?? 0}</p>
                                    <img class="fs-4 my-auto" src="../src/img/app/SH_Ficha.png" alt="sh" style="height: 20px;">
                                </div>
                            </strong>
                        </div>

                    </div>

                </div>

                <div class="order-actions">

                    <a
                        href="
                            addUser.php
                            ?id=${usuario.id}
                            &accion=actualizar
                        "
                        class="
                            store-filter-btn
                            px-4
                            justify-content-center
                            text-decoration-none
                        "
                    >
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <button
                        class="
                            store-filter-btn
                            px-4
                            justify-content-center
                            text-decoration-none
                        "
                        onclick="
                            eliminarUsuario(
                                ${usuario.id},
                                '${usuario.nombre}'
                            )
                        "
                    >
                        <i class="bi bi-trash3-fill"></i>
                        Eliminar
                    </button>

                    <button
                        class="
                            store-filter-btn
                            px-4
                            justify-content-center
                            text-decoration-none
                        "
                        onclick="
                            abrirModalCambiarContrasenniaAdmin(
                                '${usuario.id}',
                            )
                        "
                    >
                        <i class="bi bi-lock-fill"></i>
                        Cambiar contraseña
                    </button>

                    <button
                        class="
                            store-filter-btn
                            px-4
                            justify-content-center
                            text-decoration-none
                        "
                        onclick="
                            abrirModalCambiarFichas(
                                '${usuario.id}',
                                '${usuario.fichas}',
                            )
                        "
                    >
                        <i class="bi bi-coin"></i>
                        Cambiar fichas SH
                    </button>

                </div>

            </div>

        </div>
    `;

    if(returnHtml){
        return html;
    }

    $('#list-container')
        .append(html);

}

function renderUsuarioSkeleton(
    id
){
    $('#list-container').append(`

        <div
            class="product-admin-card product-skeleton"
            id="usuario-${id}"
        >

            <div class="product-admin-header">
                <div>
                    <div class="skeleton-line skeleton-subtitle"></div>
                    <div class="skeleton-line skeleton-title"></div>
                </div>
            </div>

            <div class="product-admin-body">
                <div class="product-admin-image skeleton-box">
                </div>

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
                    <div class="skeleton-button"></div>
                    <div class="skeleton-button"></div>
                </div>
            </div>

        </div>

    `);

}

function limpiarFiltrosUsuario() {
    $('#Nombre').val('');
}

function registrarUsuario() {
    const nombre = $('#Nombre').val();
    const nombreUsuario = $('#nombreUsuario').val();
    const contrasennia = $('#Contrasennia').val();
    const contrasennia2 = $('#Contrasennia2').val();

    const segundoNombre = $('#segundoNombre').val();
    const primerApellido = $('#primerApellido').val();
    const segundoApellido = $('#segundoApellido').val();
    const provincia = $('#Provincia').val();
    const canton = $('#Canton').val();
    const distrito = $('#Distrito').val();
    const telefono = $('#Telefono').val();

    if (!validarCampos(
        [nombre, primerApellido, provincia, canton, distrito, telefono, nombreUsuario, contrasennia, contrasennia2],
        ['tu nombre', 'tu primer apellido', 'la provincia', 'el cantón', 'el distrito', 'el teléfono', 'un nombre de usuario', 'una contraseña', 'la confirmación de la contraseña']
    )) {
        return;
    }

    if(contrasennia.length < 8){
        abrirModal('modalValidacion');
        $('#modalValidacion-body p').text(
            'El número de teléfono debe ser de 8 dígitos.'
        );
        return;
    }

    if(contrasennia.length < 8){
        abrirModal('modalValidacion');
        $('#modalValidacion-body p').text(
            'La contraseña debe tener al menos 8 caracteres.'
        );
        return;
    }

    if (contrasennia !== contrasennia2) {
        abrirModal('modalValidacion');
        cambiarMensajeModal("#modalValidacion", "¡Error!", '¡Las contraseñas digitadas no son iguales!', "bi bi-x-circle", true);
        return;
    }
    
    
    guardarDatos();

    function guardarDatos() {
        abrirModal('modalGuardando');
        cambiarMensajeModal("#modalGuardando", 'Guardando...', 'Espere un momento...', 'bi bi-wifi', false);

        const accion = 'insertar';
        const data = {
            accion: accion,
            nombre: nombre,
            nombreUsuario: nombreUsuario,
            contrasennia: contrasennia,
            rol: 'Cliente',
            segundoNombre: segundoNombre,
            primerApellido: primerApellido,
            segundoApellido: segundoApellido,
            provincia: provincia,
            canton: canton,
            distrito: distrito,
            telefono: telefono,
        };

        $.ajax({
            url: backend + urlUser,
            type: 'POST',
            data: data,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                cambiarMensajeModal("#modalGuardando", data.title, data.text, data.icon, true);
            },
            error: function() {
                cambiarMensajeModal("#modalGuardando", '!Error¡', 'Hubo un problema al agregar el usuario.', "bi bi-x-circle", true);
            }
        });
    }
}

function abrirModalCambiarContrasennia(){

    $('#ContrasenniaActual').val('');
    $('#ContrasenniaNueva').val('');
    $('#ContrasenniaConfirmar').val('');

    abrirModal('modalCambiarContrasennia');
}

function cambiarContrasenniaUsuario(){

    const id = $('#Id').val();

    const actual = $('#ContrasenniaActual').val();
    const nueva = $('#ContrasenniaNueva').val();
    const confirmar = $('#ContrasenniaConfirmar').val();

    if(!actual || !nueva || !confirmar){

        abrirModal('modalValidacion');

        $('#modalValidacion-body p').text(
            'Debe completar todos los campos.'
        );

        return;
    }

    if(nueva !== confirmar){

        abrirModal('modalValidacion');

        $('#modalValidacion-body p').text(
            'Las contraseñas no coinciden.'
        );

        return;
    }

    if(nueva.length < 8){

        abrirModal('modalValidacion');

        $('#modalValidacion-body p').text(
            'La nueva contraseña debe tener al menos 8 caracteres.'
        );

        return;
    }

    cerrarModal('modalCambiarContrasennia');

    abrirModal('modalGuardando');

    $.ajax({

        url: backend + urlUser,

        type: 'POST',

        data: {

            accion: 'cambiarContrasennia',

            id: id,

            contrasenniaActual: actual,

            contrasenniaNueva: nueva
        },

        success: function(response){

            const data =
                typeof response === 'string'
                ? JSON.parse(response)
                : response;

            cambiarMensajeModal(
                '#modalGuardando',
                data.title,
                data.text,
                data.icon,
                true
            );
        },

        error: function(){

            cambiarMensajeModal(
                '#modalGuardando',
                'Error',
                'No fue posible actualizar la contraseña.',
                'bi bi-x-circle',
                true
            );
        }
    });
}

function abrirModalCambiarContrasenniaAdmin(id) {
    usuarioSeleccionado = id;
    abrirModal('modalCambiarContrasenniaAdmin');
}

function cambiarContrasenniaAdmin(){
    if(!usuarioSeleccionado){
        return;
    }

    const nueva = $('#ContrasenniaNuevaAdmin').val();
    const confirmar = $('#ContrasenniaConfirmarAdmin').val();

    if(!nueva || !confirmar){
        abrirModal('modalValidacion');
        $('#modalValidacion-body p').text(
            'Debe completar todos los campos.'
        );
        return;
    }

    if(nueva !== confirmar){
        abrirModal('modalValidacion');
        $('#modalValidacion-body p').text(
            'Las contraseñas no coinciden.'
        );
        return;
    }

    if(nueva.length < 8){
        abrirModal('modalValidacion');
        $('#modalValidacion-body p').text(
            'La nueva contraseña debe tener al menos 8 caracteres.'
        );
        return;
    }

    abrirModal('modalGuardando');

    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'cambiarContrasenniaAdmin',
            id: usuarioSeleccionado,
            contrasenniaNueva: nueva
        },

        success: function(response){
            const data =
                typeof response === 'string'
                ? JSON.parse(response)
                : response;
            cambiarMensajeModal(
                '#modalGuardando',
                data.title,
                data.text,
                data.icon,
                true
            );
        },

        error: function(){
            cambiarMensajeModal(
                '#modalGuardando',
                'Error',
                'No fue posible actualizar la contraseña.',
                'bi bi-x-circle',
                true
            );
        }
    });
}

function buscarFichas(id) {
    $('#usuario-fichas-actuales').html('cargando...');
    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'buscarFichas',
            id: id
        },
        success: function(response) {
            try {
                const usuario = typeof response === 'string' ? JSON.parse(response) : response;
                $('#usuario-fichas-actuales').html(`${usuario.fichas}`);
                $('#label-fichas-actuales').html(`Disponibles: ${usuario.fichas}`);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function abrirModalCambiarFichas(id, fichas){
    usuarioSeleccionado = id;
    $('#cambioFichas').val(fichas || 0);
    abrirModal('modalCambiarFichas');
    
}

function cambiarFichas() {
    if(!usuarioSeleccionado){
        return;
    }

    const fichas = $('#cambioFichas').val();

    if (!validarCampos([fichas], ['la cantidad de fichas SH'])) {
        return;
    }

    const accion = 'cambiarFichas';
    const data = {
        accion: accion,
        id: usuarioSeleccionado,
        fichas: fichas,
    };

    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Actulizando fichas", "Se está actualizando la cantidad de fichas de este usuario", "bi bi-arrow-clockwise", false);

    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: data,
        success: function (response) {
            cambiarMensajeModal("#modalGuardando", "Fichas actualizadas", "Se ha actualizado la cantidad de fichas del usuario", "bi bi-check-circle", true);
            aplicarFiltrosUsuario();
        },
        error: function () {
            cambiarMensajeModal("#modalGuardando", "¡Error!", "Ha ocurrido un error al tratar de actualizar la cantidad de fichas del usuario", "bi bi-x-circle", true);
        }
    });
}
