function generarRangoMeses(datos) {
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    // Validar que los datos no estén vacíos
    if (!datos || datos.length === 0) {
        console.error("No hay datos para generar el rango de meses.");
        cargarGrafica([], []);
        return;
    }

    // Asegurar que los datos estén ordenados por año y mes
    datos.sort((a, b) => {
        const diffAnio = parseInt(a.anio) - parseInt(b.anio);
        if (diffAnio === 0) {
            return parseInt(a.mes) - parseInt(b.mes);
        }
        return diffAnio;
    });

    // Encontrar el rango de fechas
    const primerDato = datos[0];
    const ultimoDato = datos[datos.length - 1];

    const anioInicio = parseInt(primerDato.anio);
    const mesInicio = parseInt(primerDato.mes);
    const anioFinal = parseInt(ultimoDato.anio);
    const mesFinal = parseInt(ultimoDato.mes);

    const xValues = [];
    const yValues = [];

    // Generar el rango completo de meses entre las fechas
    for (let anio = anioInicio; anio <= anioFinal; anio++) {
        const mesInicioAño = anio === anioInicio ? mesInicio : 1;
        const mesFinalAño = anio === anioFinal ? mesFinal : 12;

        for (let mes = mesInicioAño; mes <= mesFinalAño; mes++) {
            const mesNombre = `${meses[mes - 1]} ${anio}`;
            xValues.push(mesNombre);

            // Buscar si hay datos para este mes y año
            const dato = datos.find(d => parseInt(d.anio) === anio && parseInt(d.mes) === mes);

            // Si hay datos, tomar las ganancias; si no, asignar 0
            yValues.push(dato ? parseInt(dato.ganancias) : 0);
        }
    }

    // Generar la gráfica con los datos
    cargarGrafica(xValues, yValues);
}


let myChart; // Variable global para la gráfica

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
            datasets: [{
                label: "Ganancia",
                fill: false,
                lineTension: 0.0,
                backgroundColor: "rgba(0, 132, 240, 0.2)",
                borderColor: "rgba(0, 132, 240, 0.8)",
                borderWidth: 2,
                pointBorderColor: "rgba(0, 132, 240, 1)",
                pointBackgroundColor: "rgba(0, 132, 240, 1)",
                pointRadius: 4,
                data: yValues
            }]
        },
        options: {
            legend: {display: false},
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₡' + value.toLocaleString(); // Formato moneda
                        },
                        color: "rgba(0, 0, 0, 0.8)",
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: "rgba(0, 0, 0, 0.1)"
                    }
                },
                x: {
                    ticks: {
                        color: "rgba(0, 0, 0, 0.8)",
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: "rgba(0, 0, 0, 0.1)"
                    }
                }
            }
        }
    });
}

function generarBarrasProductos(datos) {
    // Extraer los valores para los ejes de la gráfica
    const xValues = datos.map(item => item.producto);  // Eje X: productos
    const pedidosData = datos.map(item => parseInt(item.pedidos));  // Pedidos (convertidos a número)
    const vendidosData = datos.map(item => parseInt(item.vendidos));  // Vendidos (convertidos a número)

    cargarGraficaBarras(xValues, pedidosData, vendidosData);
}

let myChart2;  // Variable global para la segunda gráfica

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
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,  // Comienza el eje Y desde 0
                            fontColor: "rgb(0, 0, 0)",  // Color de los valores del eje Y
                            fontFamily: "Arial",  // Fuente del eje Y
                            fontSize: 14  // Tamaño de la fuente del eje Y
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, 0.1)"  // Color de las líneas del grid
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontColor: "rgb(0, 0, 0, 0.7)",  // Color de los valores del eje X
                            fontFamily: "Arial",  // Fuente del eje X
                            fontSize: 14  // Tamaño de la fuente del eje X
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, 0.1)"  // Color de las líneas del grid
                        }
                    }]
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
