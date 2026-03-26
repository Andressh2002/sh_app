function mostrarTablaDashboard(datos, id, orden, isOther) {
    const container = $('#' + id);
    container.empty();

    if (!Array.isArray(datos) || datos.length === 0) {
        container.append('<tr><td class="text-center" colspan="4">No se encontraron productos.</td></tr>');
        return;
    }

    datos.forEach((dato, index) => {
        const html = `
            <tr>
                <td class="align-middle">${isOther == 1 ? (orden == 'ASC' ? index + 1 : dato.puesto) : index + 1}</td>
                <td class="align-middle">${dato.producto}</td>
                <td class="align-middle">${dato.categoria}</td>
                <td class="align-middle">${isOther == 1 ? dato.pedidos : ('₡' + dato.ganancias)}</td>
            </tr>
        `;
        container.append(html);
    });
}

function mostrarIndicadorDashboard(datos, id, totales) {
    let cant = 0;

    switch (id) {
        case 'Ganancias':
            datos.forEach((dato) => {
                cant += parseInt(dato.ganancias);
            });
            mostrarPorAnimacion(cant, '₡', id, 'en ganancias');
            mostrarPorcentajePorAnimacion(cant, id, totales[0].ganancias, 'de las ganancias');
            break;

        case 'Pedidos':
            datos.forEach((dato) => {
                cant += parseInt(dato.pedidos, totales);
            });
            mostrarPorAnimacion(cant, '', id, cant.toString() != '1' ? 'pedidos' : 'pedido');
            mostrarPorcentajePorAnimacion(cant, id, totales[0].pedidos, 'de los pedidos');
            break;
        
        case 'Vendidos':
            datos.forEach((dato) => {
                cant += parseInt(dato.vendidos, totales);
            });
            mostrarPorAnimacion(cant, '', id, cant.toString() != '1' ? 'vendidos' : 'vendido');
            mostrarPorcentajePorAnimacion(cant, id, totales[0].vendidos, 'de los vendidos');
            break;
    
        default:
            container.append('N/A');
            break;
    }
}

function mostrarPorAnimacion(total, html, id, string) {
    let contador = 0;
    const container = $('#cant' + id);
    container.empty();
    container.append(html + contador + ' ' + string);

    let fragmento = Math.max(1, parseInt(parseInt(total) / 25));

    const intervalo = setInterval(() => {
        contador += fragmento;
        container.empty();
        container.append(html + contador + ' ' + string);

        if (parseInt(contador) >= parseInt(total)) {
            clearInterval(intervalo);
            container.empty();
            container.append(html + total + ' ' + string);
        }
    }, 50);
}

function mostrarPorcentajePorAnimacion(total, id, totalTodo, string) {
    const porcentajeTotal = parseInt(total) != 0 || parseInt(totalTodo) != 0 ? Math.round((parseInt(total) / parseInt(totalTodo)) * 100) : 0;

    let contador = 0;
    const container = $('#percent' + id);
    container.empty();
    container.append(contador + '%' + ' ' + string);

    let fragmento = Math.max(1, Math.round(porcentajeTotal / 25));

    const intervalo = setInterval(() => {
        contador += fragmento;
        container.empty();
        container.append(contador + '%' + ' ' + string);

        if (contador >= porcentajeTotal) {
            clearInterval(intervalo);
            container.empty();
            container.append(porcentajeTotal + '%' + ' ' + string);
        }
    }, 50);
}

function buscarElementoDashboard(vect, accion, element, idElement, orden, isOther) {
    switch (element) {
        case 'indicador':
            toggleLoadingIconCard(['cant' + idElement.toString(), 'percent' + idElement.toString()], true, [26, 16]);
            break;

        case 'tabla':
            toggleLoadingIcon(idElement, true, 4, 28);
            break;

        default:
            break;
    }

    $.ajax({
        url: backend + urlDashboard,
        type: 'POST',
        data: {
            accion: accion,
            anioInicial:vect[0],
            anioFinal: vect[3],
            mesInicial: vect[1],
            mesFinal: vect[4],
            diaInicial: vect[2],
            diaFinal: vect[5],
            categoria: vect[6],
            rareza: vect[7],
            universo: vect[8],
            orden: orden
        },
        success: function(response) {
            try {
                const datos = typeof response === 'string' ? JSON.parse(response) : response;

                switch (element) {
                    case 'indicador':
                        buscarTotales(function(totales) {
                            mostrarIndicadorDashboard(datos, idElement, totales);
                        });
                        break;

                    case 'tabla':
                        mostrarTablaDashboard(datos, idElement, orden, isOther);
                        break;

                    case 'grafica':
                        if (isOther == 1) {
                            generarRangoMeses(datos);
                        } else {
                            generarBarrasProductos(datos);
                        }
                        break;

                    default:
                        break;
                }
                
            } catch (error) {
                switch (element) {
                    case 'indicador':
                        toggleLoadingIconCard(['cant' + idElement.toString(), 'percent' + idElement.toString()], false, [26, 16]);
                        break;
            
                    case 'tabla':
                        toggleLoadingIcon(idElement, false, 4, 28);
                        break;
            
                    default:
                        break;
                }
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function() {
            switch (element) {
                case 'indicador':
                    toggleLoadingIconCard(['cant' + idElement.toString(), 'percent' + idElement.toString()], false, [26, 16]);
                    break;
        
                case 'tabla':
                    toggleLoadingIcon(idElement, false, 4, 28);
                    break;
        
                default:
                    break;
            }
            console.error('Error al procesar la solicitud.');
        }
    });
}

function buscarTotales(callback) {
    $.ajax({
        url: backend + urlDashboard,
        type: 'POST',
        data: {
            accion: 'buscarTotales'
        },
        success: function(response) {
            try {
                const datos = typeof response === 'string' ? JSON.parse(response) : response;
                if (callback) callback(datos);
            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
                if (callback) callback(null);
            }
        },
        error: function() {
            console.error('Error al procesar la solicitud.');
            if (callback) callback(null);
        }
    });
}

function actualizarDashboard() {
    const fechaInicioInput = $('#FechaInicio').val();
    const fechaFinalInput = $('#FechaFinal').val();
    let fechaInicio = ['', '', ''];
    let fechaFinal = ['', '', ''];

    if (fechaInicioInput) {
        const [anio, mes, dia] = fechaInicioInput.split('-');
        fechaInicio[0] = anio;
        fechaInicio[1] = mes;
        fechaInicio[2] = dia;
    }
    if (fechaFinalInput) {
        const [anio, mes, dia] = fechaFinalInput.split('-');
        fechaFinal[0] = anio;
        fechaFinal[1] = mes;
        fechaFinal[2] = dia;
    }

    const categoria = $('#Categoria').val();
    const rareza = $('#Rareza').val();
    const universo = $('#Universo').val();
    const elements = [
        fechaInicio[0], fechaInicio[1], fechaInicio[2],
        fechaFinal[0], fechaFinal[1], fechaFinal[2],
        categoria, rareza, universo
    ];
    
    buscarElementoDashboard(elements, 'buscarProductos', 'tabla', 'tableMejoresProductos', 'DESC', 1);
    buscarElementoDashboard(elements, 'buscarProductos', 'tabla', 'tablePeoresProductos', 'ASC', 1);
    buscarElementoDashboard(elements, 'buscarGananciasProductos', 'indicador', 'Ganancias', 'DESC', 1);
    buscarElementoDashboard(elements, 'buscarProductos', 'indicador', 'Pedidos', 'DESC', 1);
    buscarElementoDashboard(elements, 'buscarProductos', 'indicador', 'Vendidos', 'DESC', 1);
    buscarElementoDashboard(elements, 'buscarGananciasTiempo', 'grafica', '', 'DESC', 1);
    buscarElementoDashboard(elements, 'buscarGananciasProductos', 'tabla', 'tableMejoresProductosGanancias', 'DESC', 0);
    //buscarElementoDashboard(elements, 'buscarProductos', 'grafica', '', 'DESC', 0);
}

function obtenerRarezasParaDashboard(select, all) {
    $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        },
        success: function (response) {
            try {
                const rarezas = typeof response === 'string' ? JSON.parse(response) : response;

                rarezas.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (all === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Todos'
                        })
                    );
                }

                rarezas.forEach(function (rareza) {
                    selectElement.append(
                        $('<option>', {
                            value: all ? rareza.nombre : rareza.id,
                            text: rareza.nombre
                        })
                    );
                });

            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}

function obtenerUniversosParaDashboard(select, all) {
    $.ajax({
        url: backend + urlUniverse,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        },
        success: function (response) {
            try {
                const universos = typeof response === 'string' ? JSON.parse(response) : response;

                universos.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (all === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Todos'
                        })
                    );
                }

                universos.forEach(function (universo) {
                    selectElement.append(
                        $('<option>', {
                            value: all ? universo.nombre : universo.id,
                            text: universo.nombre
                        })
                    );
                });

            } catch (error) {
                console.error('Error al procesar la respuesta:', error);
            }
        },
        error: function () {
            console.error('Error al procesar la solicitud.');
        }
    });
}