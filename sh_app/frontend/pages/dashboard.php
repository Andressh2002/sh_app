<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Dashboard";
    $pageIcon = 'bi-speedometer';
    $type = 'dashboard';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $inputs = [
        [
            'label' => 'De la fecha',
            'id' => 'FechaInicio',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'date',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Hasta la fecha',
            'id' => 'FechaFinal',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'date',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Categoría',
            'id' => 'Categoria',
            'icon' => 'bi bi-tools',
            'input' => 'select',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'select',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'select',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
    ];

    $cards = [
        [
            'title' => 'Ganancias',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardGanancias',
            'idCant' => 'cantGanancias',
            'help' => 'Ingresos totales',
        ],
        [
            'title' => 'Pedidos',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardPedidos',
            'idCant' => 'cantPedidos',
            'help' => 'Pedidos recibidos',
        ],
        [
            'title' => 'Vendidos',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardVendidos',
            'idCant' => 'cantVendidos',
            'help' => 'Productos vendidos',
        ],
        [
            'title' => 'Ticket Promedio',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardTicketPromedio',
            'idCant' => 'cantTicketPromedio',
            'help' => 'Ganancia promedio',
        ],
        [
            'title' => 'Productos distintos vendidos',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardProductosDistintosVendidos',
            'idCant' => 'cantProductosDistintosVendidos',
            'help' => 'Variedad',
        ],
        [
            'title' => 'Conversión venta',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardConversiónVenta',
            'idCant' => 'cantConversiónVenta',
            'help' => 'Pedidos pagados',
        ],
        [
            'title' => 'Producto top',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardProductoTop',
            'idCant' => 'cantProductoTop',
            'help' => 'Mejor producto',
        ],
        [
            'title' => 'Crecimiento mensual',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardCrecimientoMensual',
            'idCant' => 'cantCrecimientoMensual',
            'help' => 'Crece o baja',
        ],
    ];

    $tables = [
        [
            'title' => 'Productos más pedidos',
            'id' => 'tableMejoresProductos',
            'headers' => ['#', 'Producto', 'Categoría', 'Cantidad'],
        ],
        [
            'title' => 'Productos menos pedidos',
            'id' => 'tablePeoresProductos',
            'headers' => ['#', 'Producto', 'Categoría', 'Cantidad'],
        ],
        [
            'title' => 'Ganancias por producto',
            'id' => 'tableMejoresProductosGanancias',
            'headers' => ['#', 'Producto', 'Categoría', 'Ganancia'],
        ]
    ];

    $grafics = [
        [
            'title' => 'Ganancias por tiempo',
            'id' => 'myChart',
        ]
    ];

    $menuTable = [
        'url' => '',
        'updateMethod' => 'actualizarDashboard()',
        'clearMethod' => '',
        'pageInfo' => 'dashboard',
        'showAdd' => false,
        'showUpdate' => true,
        'showInfo' => false,
        'showCount' => false,
    ];

    $dashboard = [
        'inputs'=>$inputs,
        'cards'=>$cards,
        'tables'=>$tables,
        'charts'=>[
            [
                'title'=>'Ganancias por tiempo',
                'id'=>'myChart'
            ],
            [
                'title'=>'Productos más pedidos y vendidos',
                'id'=>'myChart2'
            ]
        ]
    ];
?>

<div class="w-100 overflow-hidden p-0">

    <!-- FILTROS -->
    <section class="dashboard-filters dashboard-shadow mb-4 mx-2">
        <div class="dashboard-panel">

            <div class="dashboard-panel-header">
                <i class="bi bi-funnel-fill"></i>
                <span>Filtros del dashboard</span>
            </div>

            <div class="dashboard-panel-body">

                <div
                    class="row"
                    id="formulario-filtros"
                >
                    <?php
                    foreach ($inputs as $input) {

                        include '../src/components/inputs/input.php';
                    }
                    ?>
                </div>

            </div>

        </div>
    </section>

    <!-- MENÚ -->
    <section class="mb-4">

        <div class="table-menu-responsive">
            <?php include '../src/components/tables/menuTable.php'; ?>
        </div>

    </section>

    <!-- KPI -->
    <section class="dashboard-kpis mb-4 mx-2">
        <?php foreach ($dashboard['cards'] as $cardDash): ?>
            <?php include '../src/components/dashboard/card.php'; ?>
        <?php endforeach; ?>
    </section>

    <!-- GRÁFICA -->
    <section class="dashboard-main-grid mb-4 mx-2">
        <?php foreach ($dashboard['charts'] as $grafic): ?>
            <?php include '../src/components/dashboard/grafic.php'; ?>
        <?php endforeach; ?>
    </section>

    <!-- TABLAS -->
    <section class="dashboard-tables mx-2">
        <?php foreach ($dashboard['tables'] as $table): ?>
            <?php include '../src/components/dashboard/table.php'; ?>
        <?php endforeach; ?>
    </section>

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        $(async function(){
            await Promise.all([
                obtenerCategoriasParaProductos('Categoria', true),
                obtenerRarezasParaDashboard('Rareza', true),
                obtenerUniversosParaDashboard('Universo', true)
            ]);

            actualizarDashboard();
        });
    });
</script>