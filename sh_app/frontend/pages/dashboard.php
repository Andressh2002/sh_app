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
            'input' => 'datepicker',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Hasta la fecha',
            'id' => 'FechaFinal',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'datepicker',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        /* [
            'label' => 'Del año',
            'id' => 'AnioInicio',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'select',
            'options' => [
                '' => 'Todos',
                '2023' => '2023',
                '2024' => '2024',
                '2025' => '2025',
            ],
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Hasta el año',
            'id' => 'AnioFinal',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'select',
            'options' => [
                '' => 'Todos',
                '2023' => '2023',
                '2024' => '2024',
                '2025' => '2025',
            ],
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Del mes',
            'id' => 'MesInicio',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'select',
            'options' => [
                '' => 'Todos',
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ],
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Hasta el mes',
            'id' => 'MesFinal',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'select',
            'options' => [
                '' => 'Todos',
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ],
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ], */
        [
            'label' => 'Categoría',
            'id' => 'Categoria',
            'icon' => 'bi bi-tools',
            'input' => 'selectajax',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'selectajax',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'selectajax',
            'onchange' => 'actualizarDashboard()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
    ];
    $cards = [
        [
            'title' => 'Ganancias',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardGanancias',
            'idCant' => 'cantGanancias',
            'idPercent' => 'percentGanancias',
        ],
        [
            'title' => 'Pedidos',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardPedidos',
            'idCant' => 'cantPedidos',
            'idPercent' => 'percentPedidos',
        ],
        [
            'title' => 'Vendidos',
            'cant' => '0',
            'percent' => '0%',
            'idTitle' => 'cardVendidos',
            'idCant' => 'cantVendidos',
            'idPercent' => 'percentVendidos',
        ]
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
    $tables2 = [
        [
            'title' => 'Ganancias por producto',
            'id' => 'tableMejoresProductosGanancias',
            'headers' => ['#', 'Producto', 'Categoría', 'Ganancia'],
        ]
    ];
    $grafics2 = [
        [
            'title' => 'Mejores productos',
            'id' => 'myChart2',
        ]
    ];
    $menuTable = [
        'url' => '',
        'updateMethod' => 'actualizarDashboard()',
        'clearMethod' => '',
        'pageInfo' => 'dashboard',
        'showAdd' => false,
        'showUpdate' => true,
        'showInfo' => true,
    ];
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Dashboard</h4>
            <i class="bi bi-search fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
        <div class="container-fluid px-0">
            <div class="card rounded-3 overflow-hidden my-2">
                <div class="card-body admin-subheader-card-bg py-1">
                    <div class="d-flex align-items-center gap-2">
                        <p class="card-title p-0 m-0">Filtros</p>
                    </div>
                </div>
                <div class="row px-3 py-1" id="formulario-filtros">
                    <?php
                    foreach ($inputs as $input) {
                        include '../src/components/inputs/input.php';
                    }
                    ?>
                </div>
            </div>
            
            <div class="row justify-content-between">
                <div class="col-auto">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination"></ul>
                    </nav>
                </div>
                <div class="col-auto d-flex gap-2 mb-4">
                    <?php include '../src/components/tables/menuTable.php'; ?>
                </div>
            </div>
            <div class="row container-fluid gap-lg-4 gap-md-3 gap-sm-2 gap-1 m-auto p-0 flex-fill mb-lg-4 mb-md-3 mb-sm-2 mb-1">
                <?php
                foreach ($cards as $cardDash) {
                    include '../src/components/dashboard/card.php';
                }
                ?>
            </div>
            <div class="row container-fluid gap-lg-4 gap-md-3 gap-sm-2 gap-1 m-auto p-0 flex-fill mb-lg-4 mb-md-3 mb-sm-2 mb-1">
                <?php
                foreach ($tables as $table) {
                    include '../src/components/dashboard/table.php';
                }
                ?>
            </div>
            <div class="row container-fluid gap-lg-4 gap-md-3 gap-sm-2 gap-1 m-auto p-0 flex-fill mb-lg-4 mb-md-3 mb-sm-2 mb-1">
                <?php
                foreach ($grafics as $grafic) {
                    include '../src/components/dashboard/grafic.php';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        actualizarDashboard();
        obtenerCategoriasParaProductos('Categoria', true);
        obtenerRarezasParaDashboard('Rareza', true);
        obtenerUniversosParaDashboard('Universo', true);
    });
</script>