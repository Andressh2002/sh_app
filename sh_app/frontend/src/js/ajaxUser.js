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
    
    alertLoadingBlocked(
        'Guardando usuario',
        'Se está guardando el usuario, espere un momento...',
        'warning',
    );
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
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al guardar el usuario.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function obtenerUsuarios(nombre) {
    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: nombre
        },
        success: function(response) {
            try {
                const usuarios = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarUsuarios(usuarios);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarUsuarios(usuarios) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    usuarios = ordenar(usuarios, order);

    usuarios.forEach((usuario, index) => {
        const json = encodeURIComponent(JSON.stringify(usuario));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">${usuario.nombre}</td>
                <td class="align-middle">${usuario.nombre_usuario}</td>
                <td class="align-middle">${usuario.rol}</td>
                <td class="text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addUser.php?id=${usuario.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarUsuario(${usuario.id}, '${usuario.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesUsuario('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
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
    }
}

function eliminarUsuario(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar a "' + nombre + '" de los usuarios? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function() {
                eliminarUsuario(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlUser,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function(response) {
                aplicarFiltrosUsuario()
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            },
            error: function() {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosUsuario() {
    const nombre = $('#Nombre').val();
    const rol = $('#Rol').val();
    seleccionarUsuarios(nombre, rol);
}

function verDetallesUsuario(json) {
    const usuario = JSON.parse(decodeURIComponent(json));
    alertDetails(
        'Detalles del usuario ' + usuario.nombre,
        usuario,
        ['nombre', 'nombre_usuario', 'rol', 'fecha_registro'],
        'info',
        'Cerrar'
    );
}

function seleccionarUsuarios(nombre, rol) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();
    const colspan = 5;
    container.append(`
        <tr><td class="text-center align-middle" colspan="${colspan}">
            <div class="spinner-border spinner-color" role="status" style="width: 24px; height: 24px;"></div>
        </td></tr>
    `);

    cancelarCargaSecuencial = true;

    if (solicitudAjaxActiva) {
        solicitudAjaxActiva.abort();
        solicitudAjaxActiva = null;
    }

    cancelarCargaSecuencial = false;

    solicitudAjaxActiva = $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'listarIds',
            nombre: nombre,
            rol, rol,
            orden: order,
        },
        success: function (response) {
            try {
                const usuarios = response.datos;
                const total = response.total;
                mostrarTotalRegistros(total.length);
                container.empty();

                if (usuarios.length > 0) {
                    procesarUsuariosSecuencialmente(usuarios, 0, colspan);
                } else {
                    container.empty();
                    container.append(`<tr><td class="text-center" colspan="${colspan}">No se encontraron usuarios.</td></tr>`);
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

function procesarUsuariosSecuencialmente(lista, index, colspan) {
    if (cancelarCargaSecuencial || index >= lista.length) return;

    const usuario = lista[index];
    const container = $('#data-container');

    try {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle" id="nombre-${usuario.id}"></td>
                <td class="align-middle" id="usuario-${usuario.id}"></td>
                <td class="align-middle" id="rol-${usuario.id}"></td>
                <td class="align-middle text-center" id="opciones-${usuario.id}" style="width: 1px;"></td>
            </tr>
        `;
        container.append(html);
    } catch (error) {
        container.append(`<tr><td class="text-center" colspan="${colspan}">Este usuario no se pudo cargar.</td></tr>`);
    }

    cargarUsuarioSeleccionado(usuario.id, function () {
        procesarUsuariosSecuencialmente(lista, index + 1, colspan);
    });
}

function cargarUsuarioSeleccionado(id, callback) {
    const tdNombre = $(`#nombre-${id}`);
    const tdUsuario = $(`#usuario-${id}`);
    const tdRol = $(`#rol-${id}`);
    const tdOpciones = $(`#opciones-${id}`);

    const liClasses = "list-group-item border-0 bg-transparent px-0 py-0";

    $.ajax({
        url: backend + urlUser,
        type: 'POST',
        data: {
            accion: 'buscarPorId',
            id: id
        },
        success: function (response) {
            try {
                const usuario = typeof response.datos[0] === 'string' ? JSON.parse(response.datos[0]) : response.datos[0];
                const json = encodeURIComponent(JSON.stringify(usuario));
                
                tdNombre.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${usuario.nombre || 'Sin nombre'}</li>
                    </ul>
                `);
                tdUsuario.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${usuario.nombre_usuario || 'Sin usuario'}</li>
                    </ul>
                `);
                tdRol.append(`
                    <ul class="list-group border-0 px-0">
                        <li class="${liClasses}">${usuario.rol || 'Sin rol'}</li>
                    </ul>
                `);
                tdOpciones.append(`
                    <div class="d-flex gap-2 justify-content-start">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center" type="button" id="dropdownMenuButton${usuario.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                Editar<i class="bi bi-pencil-square ms-2"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${usuario.id}">
                                <li><a class="dropdown-item" href="addUser.php?id=${usuario.id}&accion=actualizar">En esta pestaña</a></li>
                                <li><a class="dropdown-item" href="addUser.php?id=${usuario.id}&accion=actualizar" target="_blank">En otra pestaña</a></li>
                            </ul>
                        </div>
                        <button onclick="eliminarUsuario(${usuario.id}, '${usuario.nombre}', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesUsuario('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                `);
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

function actualizarPaginacionUsuario(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaUsuario(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaUsuario(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaUsuario(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaUsuario(pagina) {
    currentPage = pagina;
    seleccionarUsuarios('');
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
        [nombre, primerApellido, provincia, canton, distrito, nombreUsuario, contrasennia, contrasennia2],
        ['tu nombre', 'tu primer apellido', 'la provincia', 'el cantón', 'el distrito', 'un nombre de usuario', 'una contraseña', 'la confirmación de la contraseña']
    )) {
        return;
    }

    if (contrasennia !== contrasennia2) {
        alert(
            '¡Las contraseñas digitadas no son iguales!',
            'Vuelve a intentarlo',
            'error',
            'Cerrar'
        );
        return;
    }

    alertLoadingBlocked(
        'Registrando usuario',
        'Se está resitrando el usuario, espere un momento...',
        'warning',
    );
    
    guardarDatos();

    function guardarDatos() {
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
                alert(
                    data.title,
                    data.text,
                    data.icon,
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al agregar el usuario.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}