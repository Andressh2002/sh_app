async function consultarDashboard(
    accion,
    filtros = {},
    extra = {}
){

    try{

        const payload = {

            accion,

            ...filtros,

            ...extra
        };

        return await $.ajax({

            url:
                backend +
                urlDashboard,

            type:'POST',

            data:payload
        });

    }catch(error){

        console.error(
            error
        );

        return [];
    }
}