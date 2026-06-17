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
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar cliente...',
        ],
        [
            'label' => 'Ubicación',
            'id' => 'Ubicacion',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar ubicación...',
        ],
        [
            'label' => 'Teléfono',
            'id' => 'Telefono',
            'icon' => 'bi bi-telephone-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar número telefónico...',
        ],
        [
            'label' => 'Producto',
            'id' => 'Producto',
            'icon' => 'bi bi-palette-fill',
            'input' => 'text',
            'onchange' => '',
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
            'onchange' => 'aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
            'options' => [
                0 => 'Buscando...',
            ],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'select',
            'onchange' => 'aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
            'options' => [
                0 => 'Buscando...',
            ],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'select',
            'onchange' => 'aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
            'options' => [
                0 => 'Buscando...',
            ],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Color',
            'id' => 'Color',
            'icon' => 'bi bi-paint-bucket',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar color...',
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
            'onchange' => 'aplicarFiltrosPedido()',
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
            'onchange' => 'aplicarFiltrosPedido()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]
    ];
    $menuTable = [
        'url' => '',
        'updateMethod' => 'seleccionarPedidos()',
        'clearMethod' => 'limpiarFiltrosPedido()',
        'pageInfo' => 'pedidos',
        'showAdd' => false,
        'showUpdate' => true,
        'showInfo' => false,
        'showCount' => true,
    ];
?>

<div class="w-100 overflow-hidden p-0">

    <div class="row px-0 py-1" id="formulario-filtros">
        <?php
        foreach ($inputs as $input) {
            include '../src/components/inputs/input.php';
        }
        ?>
    </div>

    <div class="row px-0 py-1" id="formulario-filtros-orden">
        <?php
        foreach ($orders as $input) {
            include '../src/components/inputs/input.php';
        }
        ?>
    </div>

    <div class="px-0 pb-2">

        <div class="row justify-content-between">
            <div class="col-auto d-flex gap-2 mb-4">
                <?php include '../src/components/tables/menuTable.php'; ?>
            </div>
        </div>

        <div class="overflow-hidden p-0">
            <div id="list-container" class="products-admin-grid p-0"></div>
        </div>

    </div>

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function(){

        seleccionarPedidos();

        obtenerCategoriasParaProductos(
            'Categoria',
            true
        );

        obtenerRarezasParaDashboard(
            'Rareza',
            true
        );

        obtenerUniversosParaDashboard(
            'Universo',
            true
        );

    });

    let typingTimer;

    function actualizarPedidos(){

        seleccionarPedidos(
            $('#Cliente').val(),
            $('#Producto').val(),
            $('#Categoria').val(),
            $('#Rareza').val(),
            $('#Universo').val(),
            $('#Color').val(),
            $('#Pagado').val(),
            $('#Ubicacion').val(),
            $('#Telefono').val()
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function(){

            $('#Cliente,#Producto,#Color,#Ubicacion,#Telefono')
            .on(
                'input',
                function(){

                    clearTimeout(typingTimer);

                    typingTimer =
                        setTimeout(
                            actualizarPedidos,
                            400
                        );
                }
            );

            $(
                '#Categoria,' +
                '#Rareza,' +
                '#Universo,' +
                '#Pagado,' +
                '#Ordenar_por'
            )
            .on(
                'change',
                actualizarPedidos
            );

        }
    );

    $(document).on(
        'input',
        '#progreso',
        function(){

            $('#texto-progreso')
                .text(
                    `${this.value}%`
                );

        }
    );
</script>