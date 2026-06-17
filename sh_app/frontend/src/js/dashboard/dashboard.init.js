$(async function(){

    await Promise.all([

        obtenerCategoriasParaProductos(
            'Categoria',
            true
        ),

        obtenerRarezasParaDashboard(
            'Rareza',
            true
        ),

        obtenerUniversosParaDashboard(
            'Universo',
            true
        )
    ]);

    actualizarDashboard();
});