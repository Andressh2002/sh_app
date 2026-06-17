function obtenerFiltrosDashboard(){

    const inicio =
        $('#FechaInicio')
        .val()
        ?.split('-')
        || [];

    const fin =
        $('#FechaFinal')
        .val()
        ?.split('-')
        || [];

    return {

        anioInicial:inicio[0],

        mesInicial:inicio[1],

        diaInicial:inicio[2],

        anioFinal:fin[0],

        mesFinal:fin[1],

        diaFinal:fin[2],

        categoria:
            $('#Categoria').val(),

        rareza:
            $('#Rareza').val(),

        universo:
            $('#Universo').val()
    };
}