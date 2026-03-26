function guardarCarrusel() {
    const id = document.getElementById('Id').value || null;
    const idFestividad = $('#hiddenFestividad').val();
    const titulo = $('#Titulo').val();
    const texto = $('#Texto').val();
    const imagen = $('#hiddenImagenCarrusel').val();

    if (!validarCampos(
        [titulo, texto, imagen.length > 30 ? 'A' : ''],
        ['el título', 'el texto', 'la imagen']
    )) {
        return;
    }
    
    guardarDatos();

    function guardarDatos() {
        const accion = id ? 'actualizar' : 'insertar';
        const data = {
            accion: accion,
            idFestividad: idFestividad,
            titulo: titulo,
            texto: texto,
            imagen: imagen
        };

        if (id) {
            data.id = id;
        }

        $.ajax({
            url: backend + urlCarousel,
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
                    'Hubo un problema al guardar la carta del carrusel.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function mostrarCarruseles(carruseles) {
    const container = $('#data-container');
    const order = $('#Ordenar_por').val();
    container.empty();

    const startIndex = (currentPage - 1) * itemsPerPage;

    carruseles = ordenar(carruseles, order);

    if (!Array.isArray(carruseles) || carruseles.length === 0) {
        container.append('<tr><td class="text-center" colspan="6">No se encontraron cartas del carrusel.</td></tr>');
        return;
    }

    carruseles.forEach((carrusel, index) => {
        console.log(carrusel);
        const json = encodeURIComponent(JSON.stringify(carrusel));
        const html = `
            <tr>
                <td class="align-middle">${startIndex + index + 1}</td>
                <td class="align-middle">
                    <canvas id="canva${startIndex + index + 1}" style="display:none;"></canvas>
                    <img id="result${startIndex + index + 1}" src="${carrusel.imagen}" style="width: 128px; height: auto;" />
                </td>
                <td class="align-middle">${carrusel.titulo}</td>
                <td class="align-middle">${carrusel.texto}</td>
                <td class="align-middle">${carrusel.idFestividad == 0 ? 'Ninguna' : carrusel.festividad}</td>
                <td class="align-middle text-center" style="width: 1px;">
                    <div class="d-flex gap-2 justify-content-start">
                        <button onclick="location.href='addCarousel.php?id=${carrusel.id}&accion=actualizar'" type="button" class="btn-edit text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Editar<i class="bi bi-pencil-square ms-2"></i>
                        </button>
                        <button onclick="eliminarCarrusel(${carrusel.id}, '', false)" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Eliminar
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill ms-2" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        <button onclick="verDetallesCarrusel('${json}')" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                            Detalles<i class="bi bi-three-dots ms-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

function buscarCarrusel(id) {
    $.ajax({
        url: backend + urlCarousel,
        type: 'POST',
        data: {
            accion: 'buscar',
            id: id
        },
        success: function(response) {
            try {
                const categoria = typeof response === 'string' ? JSON.parse(response) : response;
                mostrarCarrusel(categoria);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function mostrarCarrusel(carrusel) {
    if (carrusel) {
        $('#Titulo').val(carrusel.titulo);
        $('#Texto').val(carrusel.texto);
        $('#textFestividad').val(carrusel.festividad);
        $('#hiddenFestividad').val(carrusel.idFestividad);

        cargarImagenGuardada(carrusel.imagen, '#vistaImagenCarrusel');
        $('#hiddenImagenCarrusel').val(carrusel.imagen);
    }
}

function eliminarCarrusel(id, nombre, eliminar) {
    if (!eliminar) {
        dialogAlert(
            '¿Estás seguro?',
            '¿De verdad quiere eliminar esta carta del carrusel? ¡Si lo haces no se podrá revertir!',
            'warning',
            'Si, estoy seguro',
            'No',
            function() {
                eliminarCarrusel(id, '', true);
            }
        );
    } else {
        $.ajax({
            url: backend + urlCarousel,
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            success: function(response) {
                aplicarFiltrosCarrusel()
                alert(
                    '¡Carta del carrusel eliminado!',
                    response,
                    'success',
                    'Aceptar'
                );
            },
            error: function() {
                alert(
                    'Error',
                    'Hubo un problema al eliminar la carta del carrusel.',
                    'error',
                    'Aceptar'
                );
            }
        });
    }
}

function aplicarFiltrosCarrusel() {
    const titulo = $('#Titulo').val();
    const festividad = $('#Festividad').val();
    seleccionarCarruseles(titulo, festividad);
}

function verDetallesCarrusel(json) {
    const carrusel = JSON.parse(decodeURIComponent(json));
    alertDetails(
        'Detalles de la carta del carrusel',
        carrusel,
        ['titulo', 'text', 'festividad', 'imagen'],
        'info',
        'Cerrar'
    );
}

function seleccionarCarruseles(titulo, festividad) {
    const offset = (currentPage - 1) * itemsPerPage;
    toggleLoadingIcon('data-container', true, 6, 28);

    $.ajax({
        url: backend + urlCarousel,
        type: 'POST',
        data: {
            accion: 'seleccionar',
            titulo: titulo,
            festividad: festividad,
            limit: itemsPerPage,
            offset: offset
        },
        success: function(response) {
            try {
                const carruseles = response.datos;
                const total = response.total;
                mostrarTotalRegistros(response.total);
                mostrarCarruseles(carruseles);
                actualizarPaginacionCarrusel(total);
            } catch (error) {
                toggleLoadingIcon('data-container', false, 6, 28);
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            toggleLoadingIcon('data-container', false, 6, 28);
            console.error('Error al procesar la solicitud.');
        }
    });
}

function actualizarPaginacionCarrusel(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    const paginationContainer = $('.pagination');
    paginationContainer.empty();

    if (totalPages !== 0) {
        paginationContainer.append(`
            <li class="page-item ${currentPage === 1 ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Previous" onclick="cambiarPaginaCarrusel(${currentPage - 1})">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPaginaCarrusel(${i})">${i}</a>
                </li>
            `);
        }

        paginationContainer.append(`
            <li class="page-item ${currentPage === totalPages ? 'btn-details-disabled' : ''}">
                <a class="page-link" href="#" aria-label="Next" onclick="cambiarPaginaCarrusel(${currentPage + 1})">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
    }
}

function cambiarPaginaCarrusel(pagina) {
    currentPage = pagina;
    seleccionarCarruseles('', '');
}

function limpiarFiltrosCarrusel() {
    $('#Titulo').val('');
    $('#Festividad').val('');
}

function obtenerCartasParaCarrusel(idElement) {
    $.ajax({
        url: backend + urlCarousel,
        type: 'POST',
        data: {
            accion: 'obtener'
        },
        success: function (response) {
            try {
                const carruseles = typeof response === 'string' ? JSON.parse(response) : response;

                const indicators = $('#carousel-indicators');
                const items = $('#carousel-items');
                indicators.empty();
                items.empty();

                // Obtiene la fecha actual en formato "M-D"
                const hoy = new Date();
                const mesHoy = hoy.getMonth() + 1;  // Los meses en JavaScript son 0-indexados, por eso sumamos 1
                const diaHoy = hoy.getDate();
                const fechaHoy = `${mesHoy}-${diaHoy}`;

                // Filtra las cartas en función de la fecha
                carruseles.forEach(function (carta, index) {
                    const fechaInicio = carta.fechaInicio;
                    const fechaFinal = carta.fechaFinal;

                    // Verifica si la fecha actual está en rango o si alguna fecha es null
                    const enRango = (!fechaInicio || !fechaFinal) ||  // Mostrar si alguna de las fechas es null
                                    (fechaInicio <= fechaHoy && fechaHoy <= fechaFinal) ||
                                    (fechaInicio > fechaFinal && (fechaHoy >= fechaInicio || fechaHoy <= fechaFinal));

                    if (enRango) {
                        // Agrega los indicadores y los elementos si están en rango o tienen fecha null
                        indicators.append(
                            $(`<button type="button" data-bs-target="#${idElement}" data-bs-slide-to="${index}" class="${index === 0 ? 'active' : ''} border border-secondary border-1 rounded" aria-current="true" aria-label="Slide ${index}"></button>`)
                        );

                        items.append(
                            $(`<div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <img src="${carta.imagen}" class="d-block w-100 overflow-hidden" alt="">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 style="text-shadow: 0 0 3px #000;">${carta.titulo}</h5>
                                    <p style="text-shadow: 0 0 3px #000;">${carta.texto}</p>
                                </div>
                            </div>`)
                        );
                    }
                });

                if (carruseles.length > 0) {
                    $('#row-carousel').removeClass('visually-hidden');
                }
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}