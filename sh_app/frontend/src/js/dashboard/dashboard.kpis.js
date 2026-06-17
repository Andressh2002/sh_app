function mostrarPorAnimacion(total, prefijo, id, texto){

    const container =
        $('#cant' + id);

    clearInterval(
        intervalosDashboard[id]
    );

    let contador = 0;

    const fragmento =
        Math.max(
            1,
            Math.ceil(total / 25)
        );

    intervalosDashboard[id] =
        setInterval(
            () => {

                contador += fragmento;

                if(
                    contador >= total
                ){

                    contador = total;

                    clearInterval(
                        intervalosDashboard[id]
                    );
                }

                container.text(
                    `${prefijo}${contador.toLocaleString()} ${texto}`
                );

            },
            22
        );
}

async function cargarKPIs(){

    const res =
        await consultarDashboard(
            'buscarKPIs',
            obtenerFiltrosDashboard()
        );

    const k = res?.[0] || {};

        console.log(k);

    const config=[

        ['Ganancias',
            Number(k.ganancias)||0,
            '₡'
        ],

        ['Pedidos',
            Number(k.pedidos)||0,
            ''
        ],

        ['Vendidos',
            Number(k.vendidos)||0,
            ''
        ],

        ['TicketPromedio',
            Number(k.ticketPromedio)||0,
            '₡'
        ]

    ];

    config.forEach(

        ([id,valor,p])=>

            mostrarPorAnimacion(
                valor,
                p,
                id,
                ''
            )
    );

    $('#cantProductosDistintosVendidos')
        .text(
            k.productosVendidos
        );

    $('#cantConversiónVenta')
        .text(
            k.conversion
            +
            '%'
        );

    $('#cantProductoTop')
        .text(
            k.productoTop || '-'
        );

    $('#cantCrecimientoMensual')
        .text(
            (
                Number(
                    k.crecimientoMensual
                ) || 0
            ) + '%'
        );
}