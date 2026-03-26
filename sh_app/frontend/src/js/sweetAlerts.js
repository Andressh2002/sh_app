function alert(title, text, icon, confirmButtonText) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: confirmButtonText,
        customClass: {
            confirmButton: 'bg-blue',
            denyButton: 'bg-blue',
            cancelButton: 'bg-blue',
        }
    });
}

function alertHTML(title, text, icon, confirmButtonText) {
    Swal.fire({
        title: title,
        html: `
            <p class="text-start">${text}</p>
            `,
        icon: icon,
        confirmButtonText: confirmButtonText,
        customClass: {
            confirmButton: 'bg-blue',
            denyButton: 'bg-blue',
            cancelButton: 'bg-blue',
        }
    });
}

function dialogAlert(title, text, icon, confirmButtonText, cancelButtonText, func) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: confirmButtonText,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        reverseButtons: true,
        customClass: {
            confirmButton: "bg-red",
            cancelButton: "bg-blue"
        },
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Cargando...',
                text: "Espere a que termine el proceso",
                icon: "warning",
                confirmButtonText: 'Esperar',
                showCancelButton: false,
                customClass: {
                    confirmButton: "bg-blue",
                },
            })
            func();
        }
    });
}

function alertLoading(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        html: `
            <div class="w-100 p-0 m-0">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%; background-color: #007bff;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        `,
        showCloseButton: false,
        showCancelButton: false,
        focusConfirm: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            confirmButton: "hidden",
            cancelButton: "hidden"
        },
    });

    $('#container-progress-bar').hide(); // Oculta el contenedor de progreso al inicio
}

function alertLoadingBlocked(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCloseButton: false,
        showCancelButton: false,
        focusConfirm: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        customClass: {
            confirmButton: "hidden",
            cancelButton: "hidden"
        },
    });
}

function alertOption(title, text, icon, func) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: "#4BE800",
        cancelButtonColor: "#e80000",
        denyButtonColor: "#0084F0",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        denyButtonText: "Iniciar sesión",
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            func();
        } else if (result.isDenied) {
            irLogin();
        }
    });
}

function alertDetails(title, object, response, icon, confirmButtonText) {
    let tableRows = '';

    response.forEach(field => {
        if (field === 'imagen_portada' || field === 'imagen_galeria' || field === 'imagen_colores' || field === 'imagen') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">
                        ${
                            field === 'imagen' ? 'Imagen' :
                            field === 'imagen_portada' ? 'Imagen de portada' :
                            field === 'imagen_galeria' ? 'Imagen de galería' :
                            field === 'imagen_colores' ? 'Imagen de colores' : ''
                        }
                    </td>
                    <td scope="col" class="align-middle fw-normal">
                        <img class="p-3" src="${object[field]}" alt="" style="width: 35%; height: auto;">
                    </td>
                </tr>
            `;
        } else if (field === 'codigo_color_principal') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">Color principal</td>
                    <td scope="col" class="align-middle fw-normal">
                        <div class="border border-2 border-dark rounded rounded-2 m-auto" style="background: ${object[field]}; width: 32px; height: 32px;"></div>
                    </td>
                </tr>
            `;
        } else if (field === 'codigo_color_secundario') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">Color secundario</td>
                    <td scope="col" class="align-middle fw-normal">
                        <div class="border border-2 border-dark rounded rounded-2 m-auto" style="background: ${object[field]}; width: 32px; height: 32px;"></div>
                    </td>
                </tr>
            `;
        } else if (field === 'color_familia') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}</td>
                    <td scope="col" class="align-middle fw-normal">${object[field]}</td>
                </tr>
            `;
        } else if (field === 'nombre_usuario') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">Nombre de usuario</td>
                    <td scope="col" class="align-middle fw-normal">${object[field]}</td>
                </tr>
            `;
        } else if (field === 'festividad') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">Festividad</td>
                    <td scope="col" class="align-middle fw-normal">${object[field] != null ? object[field] : 'Ninguna'}</td>
                </tr>
            `;
        } else if (field === 'colores') {
            // Obtener y descomponer los colores
            const coloresArray = object.colores.split('|');
            
            // Generar el HTML para cada conjunto de colores
            let coloresHTML = '';
            coloresArray.forEach(colorSet => {
                const [codigo_color_principal, codigo_color_secundario, color_familia] = colorSet.split(',');

                coloresHTML += `
                    <div class="d-flex flex-column align-items-center my-2">
                        <div class="position-relative border border-2 border-dark rounded rounded-2 mb-1" style="background: ${codigo_color_principal}; width: 32px; height: 32px;">
                            <div class="position-absolute border border-2 border-dark rounded rounded-2" style="background: ${codigo_color_secundario}; width: 32px; height: 32px; clip-path: polygon(100% 40%, 40% 100%, 100% 100%); top: -2px; left: -2px;"></div>
                        </div>
                        <span>${color_familia}</span>
                    </div>
                `;
            });

            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold text-center">Colores</td>
                    <td scope="col" class="align-middle fw-normal text-center">
                        <div class="d-flex flex-column align-items-center">${coloresHTML}</div>
                    </td>
                </tr>
            `;
        } else if (field === 'paleta') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold text-center">Paleta</td>
                    <td scope="col" class="align-middle fw-bold text-center">
                        <div class="d-flex flex-column align-items-center my-2">
                            <div class="position-relative border border-2 border-dark rounded rounded-2 mb-1" style="background: ${object['colorPrincipal']}; width: 32px; height: 32px;">
                                <div class="position-absolute border border-2 border-dark rounded rounded-2" style="background: ${object['colorSecundario']}; width: 32px; height: 32px; clip-path: polygon(100% 40%, 40% 100%, 100% 100%); top: -2px; left: -2px;"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        } else if (field === 'especial') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}</td>
                    <td scope="col" class="align-middle fw-normal">${object[field] == 0 ? 'No' : 'Si'}</td>
                </tr>
            `;
        } else if (field === 'precio' || field === 'total') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}</td>
                    <td scope="col" class="align-middle fw-normal">₡${object[field]}</td>
                </tr>
            `;
        } else if (field === 'categoria') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">Categoría</td>
                    <td scope="col" class="align-middle fw-normal">${object[field]}</td>
                </tr>
            `;
        } else if (field === 'pagado') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">¿Pagado?</td>
                    <td scope="col" class="align-middle fw-normal">${object[field].toString() == '1' ? 'Si' : 'No'}</td>
                </tr>
            `;
        } else if (field === 'fecha_registro') {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">Fecha de registro</td>
                    <td scope="col" class="align-middle fw-normal">${formatearFechaConHora(object[field])}</td>
                </tr>
            `;
        } else {
            tableRows += `
                <tr>
                    <td scope="col" class="align-middle fw-bold">${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}</td>
                    <td scope="col" class="align-middle fw-normal">${object[field]}</td>
                </tr>
            `;
        }
    });

    Swal.fire({
        title: title,
        html: `
            <div class="border border-1 border-light rounded-2 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-striped fs-6">
                        <thead class="text-center">
                            <tr>
                                <th scope="col" class="align-middle text-center fw-bold" style="width: 172px;">Detalle</th>
                                <th scope="col" class="align-middle text-center fw-bold">Información</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
            </div>
        `,
        icon: icon,
        confirmButtonText: confirmButtonText,
        customClass: {
            confirmButton: 'bg-blue',
            denyButton: 'bg-blue',
            cancelButton: 'bg-blue',
        }
    });
}