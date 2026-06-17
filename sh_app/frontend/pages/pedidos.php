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
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar producto...',
        ],
        [
            'label' => 'Categoría',
            'id' => 'Categoria',
            'icon' => 'bi bi-tools',
            'input' => 'select',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'select',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'select',
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Color',
            'id' => 'Color',
            'icon' => 'bi bi-paint-bucket',
            'input' => 'text',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar color...',
        ],
        [
            'label' => '¿Está pagado?',
            'id' => 'Pagado',
            'icon' => 'bi bi-cash',
            'input' => 'select',
            'options' => [
                '' => 'Ambos',
                '1' => 'Si',
                '0' => 'No'
            ],
            'onchange' => 'currentPage = 1; aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => 'bi bi-arrow-down-up',
            'input' => 'select',
            'options' => [
                'pe.id' => 'Fecha de pedido',
                'pr.nombre' => 'Producto',
                'ca.nombre' => 'Categoría',
                'co.nombre' => 'Color',
                'pe.progreso' => 'Progreso',
            ],
            'onchange' => 'aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'De forma:',
            'id' => 'Ordenar_en',
            'icon' => 'bi bi-arrow-down-up',
            'input' => 'select',
            'options' => [
                'DESC' => 'Descendente',
                'ASC' => 'Ascendente',
            ],
            'onchange' => 'aplicarFiltrosPedidosCliente(' . $_SESSION["usuario_id"] . ')',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]
    ];
    $menuTable = [
        'url' => '',
        'updateMethod' => 'seleccionarPedidosCliente(' . $_SESSION["usuario_id"] . ''.''.''.''.''.')',
        'clearMethod' => 'limpiarFiltrosPedidosCliente()',
        'showAdd' => false,
        'showUpdate' => true,
        'showInfo' => false,
        'showCount' => true,
    ];
?>

<input type="hidden" id="Cliente" value="<?php echo $_SESSION['usuario_id']; ?>"> 

<div class="row my-3 p-4 mx-0">
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
            <div class="col-12 mb-4">
                <div class="table-menu-responsive">
                    <?php include '../src/components/tables/menuTable.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="orders-container" class="orders-grid"></div>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        seleccionarPedidosCliente(<?php echo $_SESSION['usuario_id']; ?>, '', '', '', '', '', '');
        obtenerCategoriasParaProductos('Categoria', true, false);
        obtenerRarezasParaDashboard('Rareza', true, false);
        obtenerUniversosParaDashboard('Universo', true, false);
    });

    let typingTimer;

    function actualizarPedidosConFiltros(){

        seleccionarPedidosCliente(

            <?php echo $_SESSION['usuario_id']; ?>,

            $('#Producto').val(),
            $('#Categoria').val(),
            $('#Rareza').val(),
            $('#Universo').val(),
            $('#Color').val(),
            $('#Pagado').val()
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function(){

            actualizarPedidosConFiltros();

            obtenerCategoriasParaProductos(
                'Categoria',
                true,
                false
            );

            obtenerRarezasParaDashboard(
                'Rareza',
                true,
                false
            );

            obtenerUniversosParaDashboard(
                'Universo',
                true,
                false
            );

            // INPUTS TEXT CON DELAY

            $('#Producto, #Color').on(
                'input',
                function(){

                    clearTimeout(typingTimer);

                    typingTimer = setTimeout(
                        () => {

                            currentPage = 1;

                            actualizarPedidosConFiltros();

                        },
                        400
                    );
                }
            );

            // SELECTS INMEDIATOS

            $(
                '#Categoria,' +
                '#Rareza,' +
                '#Universo,' +
                '#Pagado,' +
                '#Ordenar_por,' +
                '#Ordenar_en'
            ).on(
                'change',
                function(){

                    currentPage = 1;

                    actualizarPedidosConFiltros();
                }
            );
        }
    );
</script>