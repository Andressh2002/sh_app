const productColors = [];
let idColors = 0;

function agregarColor() {
    idColors ++;
    const color = $('#colorProducto').val();
    productColors.push({
        id: idColors,
        color: color,
    });
    actualizarTablaColores(productColors);
}

function actualizarTablaColores(colores) {
    const container = $('#colores-container');
    container.empty();

    if (colores.length === 0) {
        container.append('<tr><td class="text-center" colspan="3">Sin colores seleccionados</td></tr>');
        return;
    }
    colores.forEach((color, index) => {
        const html = `
            <tr>
                <td class="align-middle">${index + 1}</td>
                <td class="align-middle">
                    <div class="border border-2 rounded-pill border-dark" id="color${index + 1}" src="" alt="" style="height: 32px; width: 32px; background: ${color.color};"></div>
                </td>
                <td class="d-grid gap-2">
                    <button onclick="editarColor(${color.id})" type="button" class="btn-edit text-white border-0 rounded-2 p-1 d-flex gap-2 justify-content-center align-items-center">
                        Editar<i class="bi bi-pencil-square"></i>
                    </button>
                    <button onclick="eliminarColor(${color.id})" type="button" class="btn-delete text-white border-0 rounded-2 p-1 d-flex gap-2 justify-content-center align-items-center">
                        Eliminar<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                        </svg>
                    </button>
                </td>
            </tr>
        `;
        container.append(html);
    });
}

actualizarTablaColores(productColors);