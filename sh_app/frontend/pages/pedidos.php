<?php
    include '../src/components/login/access.php';
    $pageTitle = "Pedidos";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;
    $inputs = [
        [
            'label' => 'Nombre del producto',
            'id' => 'Producto',
            'icon' => 'bi bi-palette-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Categoría',
            'id' => 'Categoria',
            'icon' => 'bi bi-tools',
            'input' => 'selectajax',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'selectajax',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'selectajax',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Color',
            'id' => 'Color',
            'icon' => 'bi bi-paint-bucket',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => '¿Está pagado?',
            'id' => 'Pagado',
            'icon' => 'bi bi-money',
            'input' => 'select',
            'options' => [
                '' => 'Ambos',
                '1' => 'Si',
                '0' => 'No'
            ],
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => '',
            'input' => 'select',
            'options' => [
                'pe.id' => 'Fecha de pedido',
                'pr.nombre' => 'Producto',
                'ca.nombre' => 'Categoría',
                'co.nombre' => 'Color'
            ],
            'onchange' => 'aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
        ]
    ];
    $menuTable = [
        'url' => '',
        'updateMethod' => 'seleccionarPedidosCliente(' . $_SESSION["usuario_id"] . ''.''.''.''.''.')',
        'clearMethod' => 'limpiarFiltrosPedidosCliente()',
        'showAdd' => false,
        'showUpdate' => true,
        'showInfo' => false,
    ];
    $headers = ['#', 'Producto', 'Resumen', 'Total', '¿Pagado?', 'Opciones'];
?>

<input type="hidden" id="Cliente" value="<?php echo $_SESSION['usuario_id']; ?>"> 

<div class="row my-3 p-4 mx-0">
    <div class="d-flex align-items-center gap-2 mb-4 px-0">
        <h4 class="mb-0">Tus pedidos</h4>
        <i class="bi bi-cart-fill fs-4 d-flex align-self-center"></i>
    </div>

    <div class="container-fluid p-0">
        <div class="row" id="formulario-filtros">
            <?php
                foreach ($inputs as $input) {
                    include '../src/components/inputs/input.php';
                }
            ?>
        </div>
        <div class="row" id="formulario-filtros-orden">
            <?php
                foreach ($orders as $input) {
                    include '../src/components/inputs/input.php';
                }
            ?>
        </div>
        <div class="row justify-content-between">
            <div class="col-auto">
                <p class="card-text mb-3" id="total-data"></p>
            </div>
        </div>
        <div class="row justify-content-between">
            <div class="col-auto">
                <nav aria-label="Page navigation example">
                    <ul class="pagination"></ul>
                </nav>
            </div>
            <div class="col-auto d-flex gap-0 mb-4">
                <?php include '../src/components/tables/menuTable.php'; ?>
            </div>
        </div>
    </div>
    <div class="border border-1 border-light rounded-2 overflow-hidden px-0">
        <?php include '../src/components/tables/dataTable.php'; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        seleccionarPedidosCliente(<?php echo $_SESSION['usuario_id']; ?>, '', '', '', '', '', '');
        obtenerCategoriasParaProductos('Categoria', true);
        obtenerRarezasParaDashboard('Rareza', true);
        obtenerUniversosParaDashboard('Universo', true);
    });
</script>