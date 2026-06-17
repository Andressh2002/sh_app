let myChart; // Variable global para la gráfica
let myChart2;  // Variable global para la segunda gráfica

async function cargarGraficaGanancias(){

    const datos =
        await consultarDashboard(
            'buscarGananciasTiempo',
            obtenerFiltrosDashboard()
        );

    const g =
        transformarGananciasTiempo(
            datos
        );

    cargarGrafica(
        g.meses,
        g.valores
    );
}

async function cargarGraficaProductos() {

    const datos =
        await consultarDashboard(
            'buscarProductos',
            obtenerFiltrosDashboard()
        );

    generarBarrasProductos(
        datos
    );
}

function transformarGananciasTiempo(
    datos
){

    const meses = [];

    const valores = [];

    datos.forEach(

        d=>{

            meses.push(
                `${d.mes_nombre} ${d.anio}`
            );

            valores.push(
                Number(
                    d.ganancias
                )
            );
        }
    );

    return {
        meses,
        valores
    };
}

function cargarGrafica(xValues, yValues) {
    // Si ya existe una gráfica, actualizamos sus datos
    if (myChart) {
        myChart.data.labels = xValues;
        myChart.data.datasets[0].data = yValues;
        myChart.update();
        return;
    }

    // Si no existe, creamos una nueva gráfica
    myChart = new Chart("myChart", {
        type: "line",
        data: {
            labels: xValues,
            datasets:[
            {
                label:'Ganancias',

                data:yValues,

                tension:0,

                fill:true,

                borderWidth:4,

                borderColor:'#00df00',

                backgroundColor:
                    'rgba(0,217,111,.12)',

                pointRadius:5,

                pointHoverRadius:8,

                pointBackgroundColor:
                    '#00df00',

                pointBorderWidth:3,

                pointBorderColor:
                    '#ffffff'
            }
            ]
        },
        options: {
            legend: {display: false},
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction:{
                intersect:false,
                mode:'index'
            },
            elements:{
                line:{
                    borderJoinStyle:
                        'round'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₡' + value.toLocaleString(); // Formato moneda
                        },
                        color:'#666',
                        font: {
                            size: 16
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,.06)'
                    }
                },
                x: {
                    ticks: {
                        color:'#666',
                        font: {
                            size: 16
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,.06)'
                    }
                }
            }
        }
    });
}

function cargarGraficaBarras(xValues, pedidos, vendidos) {
    if (myChart2) {
        // Actualiza los datos de la gráfica si ya existe
        myChart2.data.labels = xValues;  // Actualiza las etiquetas (eje X)
        myChart2.data.datasets[0].data = pedidos;  // Actualiza los datos de pedidos
        myChart2.data.datasets[1].data = vendidos;  // Actualiza los datos de vendidos
        myChart2.update();  // Aplica los cambios
    } else {
        // Si no existe, crea una nueva gráfica
        myChart2 = new Chart("myChart2", {
            type: "bar",  // Gráfica de barras
            data: {
                labels: xValues,
                datasets: [
                    {
                        label: "Pedidos",
                        backgroundColor: "rgba(0, 132, 240, 0.5)",  // Color de las barras para pedidos
                        borderColor: "rgba(0, 132, 240, 0.5)",  // Color del borde de las barras
                        borderWidth: 3,
                        data: pedidos  // Datos de pedidos
                    },
                    {
                        label: "Vendidos",
                        backgroundColor: "rgba(75, 232, 0, 0.5)",  // Color de las barras para vendidos
                        borderColor: "rgba(75, 232, 0, 0.5)",  // Color del borde de las barras
                        borderWidth: 3,
                        data: vendidos  // Datos de vendidos
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₡' + value.toLocaleString(); // Formato moneda
                            },
                            color:'#666',
                            font: {
                                size: 16
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,.06)'
                        }
                    },
                    x: {
                        ticks: {
                            color:'#666',
                            font: {
                                size: 16
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,.06)'
                        }
                    }
                },
                legend: {
                    display: false,  // Muestra la leyenda para identificar cada conjunto de barras
                    labels: {
                        fontColor: 'rgb(0, 0, 255)',  // Color del texto de la leyenda
                        fontFamily: 'Arial',  // Fuente de la leyenda
                        fontSize: 16  // Tamaño de la fuente de la leyenda
                    }
                }
            }
        });
    }
}

function generarBarrasProductos(datos) {
    // Extraer los valores para los ejes de la gráfica
    const xValues = datos.map(item => item.producto);  // Eje X: productos
    const pedidosData = datos.map(item => parseInt(item.pedidos));  // Pedidos (convertidos a número)
    const vendidosData = datos.map(item => parseInt(item.vendidos));  // Vendidos (convertidos a número)

    cargarGraficaBarras(xValues, pedidosData, vendidosData);
}