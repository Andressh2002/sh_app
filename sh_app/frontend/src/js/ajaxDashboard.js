let intervalosDashboard = {};
let intervalosPorcentaje = {};

async function actualizarDashboard(){

    try{

        await Promise.all([

            cargarKPIs(),

            cargarGraficaGanancias(),

            cargarGraficaProductos(),

            cargarTabla(
                'buscarProductos',
                'tableMejoresProductos'
            ),

            cargarTabla(
                'buscarProductos',
                'tablePeoresProductos',
                'ASC'
            ),

            cargarTabla(
                'buscarGananciasProductos',
                'tableMejoresProductosGanancias'
            )
        ]);

    }catch(error){

        console.error(
            'Error dashboard:',
            error
        );
    }
}

function obtenerRarezasParaDashboard(select, all, acceptNull = false) {
    return $.ajax({
        url: backend + urlRarity,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        }
        }).then(
            function (response) {

                const rarezas = typeof response === 'string' ? JSON.parse(response) : response;

                rarezas.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (acceptNull === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Ninguno'
                        })
                    );
                }

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
                }
            );
        },
    );
}

function obtenerUniversosParaDashboard(select, all, acceptNull = false) {
    return $.ajax({
        url: backend + urlUniverse,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        }
        }).then(
            function (response) {

                const universos = typeof response === 'string' ? JSON.parse(response) : response;

                universos.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (acceptNull === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Ninguno'
                        })
                    );
                }

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
                }
            );
        },
    );
}

function obtenerFestividadesParaDashboard(select, all, acceptNull = false) {
    return $.ajax({
        url: backend + urlHoliday,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        }
        }).then(
            function (response) {

                const universos = typeof response === 'string' ? JSON.parse(response) : response;

                universos.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (acceptNull === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Ninguno'
                        })
                    );
                }

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
                }
            );
        },
    );
}

function obtenerAccesoriosParaDashboard(select, all, acceptNull = false) {
    return $.ajax({
        url: backend + urlAccesory,
        type: 'POST',
        data: {
            accion: 'obtener',
            nombre: ''
        }
        }).then(
            function (response) {

                const accesorios = typeof response === 'string' ? JSON.parse(response) : response;

                accesorios.sort(function (a, b) {
                    return a.nombre.localeCompare(b.nombre);
                });

                const selectElement = $('#' + select);
                selectElement.empty();

                if (acceptNull === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Ninguno'
                        })
                    );
                }

                if (all === true) {
                    selectElement.append(
                        $('<option>', {
                            value: '',
                            text: 'Todos'
                        })
                    );
                }

                accesorios.forEach(function (accesorio) {
                    selectElement.append(
                        $('<option>', {
                            value: all ? accesorio.nombre : accesorio.id,
                            text: accesorio.nombre
                        })
                    );
                }
            );
        },
    );
}