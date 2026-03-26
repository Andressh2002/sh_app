function obtenerListaFiltros(idElement, tabla, filtro, categoria) {
    if (!filtro) {
        filtro = null;
    }
    if (!categoria) {
        categoria = null;
    }
    $.ajax({
        url: backend + urlFilter,
        type: 'POST',
        data: {
            accion: 'obtenerLista',
            tabla: tabla
        },
        success: function (response) {
            try {
                const objets = typeof response === 'string' ? JSON.parse(response) : response;
                
                const selectElement = $('#' + idElement);
                selectElement.empty();

                if (objets.length > 0) {
                    objets.sort(function (a, b) {
                        return a.nombre.localeCompare(b.nombre);
                    });

                    if (tabla == 'festividades') {
                        selectElement.append(
                            $(`
                                <li class="list-group-item py-1 filterbar-input-bg filter">
                                    <div class="form-check pb-0">
                                        <input class="form-check-input filter-group-checkbox" type="checkbox" value="0" id="check${tabla.toString()}-1" checked>
                                        <label class="form-check-label w-100" for="check${tabla.toString()}-1">
                                            Ninguna
                                        </label>
                                    </div>
                                </li>
                            `)
                        );
                    }

                    objets.forEach(function (item) {
                        selectElement.append(
                            $(`
                                <li class="list-group-item py-1 filterbar-input-bg">
                                    <div class="form-check pb-0 my-0">
                                        <input class="form-check-input filter-group-checkbox" type="checkbox" value="${item.id}" id="check${tabla.toString()}${item.id}" ${filtro && tabla == 'categorias' ? (item.nombre == filtro || tabla == 'festividades' ? 'checked' : '') : 'checked'}>
                                        <label class="form-check-label w-100" for="check${tabla.toString()}${item.id}">
                                            ${item.nombre}
                                        </label>
                                    </div>
                                </li>
                            `)
                        );
                    });
                } else {
                    selectElement.append(
                        $(`
                            <label class="form-check-label" for="check${tabla.toString()}}0">
                                Sin filtros
                            </label>
                        `)
                    );
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

function obtenerFiltrosActuales() {
    return {
        nombre: $('#nombre').val() || '',
        categorias: obtenerValoresCheckbox('lista-categorias-filtros'),
        precio: [
            $('#precioInicial').val() ? parseFloat($('#precioInicial').val()) : null,
            $('#precioFinal').val() ? parseFloat($('#precioFinal').val()) : null
        ],
        descuento: $('#checkDescount').is(':checked') ? 1 : 0,
        festividades: obtenerValoresCheckbox('lista-festividades-filtros'),
        rarezas: obtenerValoresCheckbox('lista-rarezas-filtros'),
        universos: obtenerValoresCheckbox('lista-universos-filtros'),
    };
}

function obtenerValoresCheckbox(idElement) {
    const values = [];
    $(`#${idElement} input:checked`).each(function () {
        if ($(this).val()) {
            values.push(parseInt($(this).val(), 10));
        }
    });
    return values;
}

try {
    $(document).on('input change', '#nombre, #precioInicial, #precioFinal, #checkDescount', function () {
        const filtros = obtenerFiltrosActuales();
        obtenerCartasProductos(filtros);
    });
    
    $(document).on('change', '#lista-categorias-filtros input, #lista-festividades-filtros input, #lista-rarezas-filtros input, #lista-universos-filtros input', function () {
        const filtros = obtenerFiltrosActuales();
        obtenerCartasProductos(filtros);
    });

/*
    const element1 = document.getElementById('precioInicial');
    const element2 = document.getElementById('precioFinal');
    if (element1) {
        element1.addEventListener("input", function () {
            if (element1.value > element2.value) {
                element2.value = this.value; 
            }
        });
    }
    if (element2) {
        element2.addEventListener("input", function () {
            if (element2.value < element1.value) {
                element1.value = this.value; 
            }
        });
    }
*/
    // Evento para evitar desmarcar todos los checkboxes
    $(document).on('change', '.filter-group-checkbox', function () {
        const checkboxGroup = $(this).closest('ul'); // Encuentra la lista actual de checkboxes
        const allCheckboxes = checkboxGroup.find('.filter-group-checkbox'); // Checkboxes en grupo
        const checkedCheckboxes = checkboxGroup.find('.filter-group-checkbox:checked'); // Checkboxes marcados
        
        if (checkedCheckboxes.length === 0) {
            // Evita desmarcar el último checkbox
            $(this).prop('checked', true);
        } else if (checkedCheckboxes.length === 1) {
            // Deshabilita temporalmente el último checkbox marcado
            checkedCheckboxes.prop('disabled', true);
        } else {
            // Habilita todos los checkboxes si hay más de uno marcado
            allCheckboxes.prop('disabled', false);
        }
    });
} catch (error) {
    //
}
