<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Pedidos";
    $pageIcon = 'bi-cart-fill';
    $type = 'pedido';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateOrder = false;

    $inputs = [
        [
            'label' => 'Cliente',
            'id' => 'Cliente',
            'icon' => 'bi bi-person-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Ubicación',
            'id' => 'Ubicacion',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Teléfono',
            'id' => 'Telefono',
            'icon' => 'bi bi-telephone-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Producto',
            'id' => 'Producto',
            'icon' => 'bi bi-palette-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Categoría',
            'id' => 'Categoria',
            'icon' => 'bi bi-tools',
            'input' => 'selectajax',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'selectajax',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'selectajax',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ],
        [
            'label' => 'Color',
            'id' => 'Color',
            'icon' => 'bi bi-paint-bucket',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
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
            'onchange' => 'currentPage = 1; aplicarFiltrosPedido()',
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
                'cl.nombre' => 'Cliente',
                'pr.nombre' => 'Producto',
                'ca.nombre' => 'Categoría',
                'rr.nombre' => 'Rareza',
                'un.nombre' => 'Universo',
                'co.color_familia' => 'Color'
            ],
            'onchange' => 'aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
        ]
    ];
    $menuTable = [
        'url' => '',
        'updateMethod' => 'seleccionarPedidos()',
        'clearMethod' => 'limpiarFiltrosPedido()',
        'pageInfo' => 'pedidos',
        'showAdd' => false,
        'showUpdate' => true,
        'showInfo' => true,
    ];
    $headers = ['#', 'Imagen', 'Producto', 'Cliente', 'Categoría', 'Cantidad', 'Total', '¿Pagado?', 'Fecha de pedido', 'Fecha de pago', 'Opciones'];
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Pedidos</h4>
            <i class="bi bi-search fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
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
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Orden</p>
                </div>
            </div>
            <div class="row px-3 py-1" id="formulario-filtros-orden">
                <?php
                foreach ($orders as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
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
            <div class="col-auto d-flex gap-2 mb-4">
                <?php include '../src/components/tables/menuTable.php'; ?>
            </div>
        </div>
        <div class="border border-1 border-light rounded-2 overflow-hidden">
            <?php include '../src/components/tables/dataTable.php'; ?>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        seleccionarPedidos('', '', '', '', '');
        obtenerCategoriasParaProductos('Categoria', true);
        obtenerRarezasParaDashboard('Rareza', true);
        obtenerUniversosParaDashboard('Universo', true);
    });
</script>