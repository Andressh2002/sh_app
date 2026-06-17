function mostrarTablaDashboard(
    datos,
    id,
    orden,
    mostrarPedidos = true
) {

    const container = $('#' + id);
    container.empty();

    if (
        !Array.isArray(datos)
        || datos.length === 0
    ) {

        container.append(`
            <tr>
                <td
                    colspan="4"
                    class="text-center align-middle"
                >
                    No se encontraron productos.
                </td>
            </tr>
        `);

        return;
    }

    datos.forEach((dato, index) => {

        container.append(`
            <tr class="align-middle">
                <td>
                    ${
                        orden === 'ASC'
                            ? index + 1
                            : (
                                dato.puesto
                                ?? index + 1
                            )
                    }
                </td>
                <td>
                    ${
                        dato.producto
                        ?? '-'
                    }
                </td>
                <td>
                    ${
                        dato.categoria
                        ?? '-'
                    }
                </td>
                <td>

                    ${
                        mostrarPedidos

                            ? Number(
                                dato.pedidos
                                || 0
                            ).toLocaleString()

                            : '₡' +

                            Number(
                                dato.ganancias
                                || 0
                            ).toLocaleString()
                    }
                </td>
            </tr>
        `);
    });
}

async function cargarTabla(
    accion,
    tabla,
    orden = 'DESC'
) {

    const datos =
        await consultarDashboard(
            accion,
            obtenerFiltrosDashboard(),
            {
                orden
            }
        );

    mostrarTablaDashboard(
        datos,
        tabla,
        orden,
        accion === 'buscarProductos'
    );
}