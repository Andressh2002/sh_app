<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Productos";
    $pageIcon = 'bi-palette-fill';
    $type = 'producto';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateProduct = false;

    $inputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar producto...',
        ],
        [
            'label' => 'Categoría',
            'id' => 'Categorias',
            'icon' => 'bi bi-tools',
            'input' => 'select',
            'options' => [
                0 => 'Buscando...',
            ],
            'onchange' => 'aplicarFiltrosProducto()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag',
            'input' => 'select',
            'options' => [
                0 => 'Buscando...',
            ],
            'onchange' => 'aplicarFiltrosProducto()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag',
            'input' => 'select',
            'options' => [
                0 => 'Buscando...',
            ],
            'onchange' => 'aplicarFiltrosProducto()',
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
                'p.id' => 'Fecha de creación',
                'p.nombre' => 'Nombre',
                'ct.nombre' => 'Categoría',
                'rr.nombre' => 'Rareza',
                'un.nombre' => 'Universo',
                'p.precio' => 'Precio',
            ],
            'onchange' => 'aplicarFiltrosProducto()',
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
            'onchange' => 'aplicarFiltrosProducto()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]
    ];
    $menuTable = [
        'url' => 'addProduct.php',
        'updateMethod' => 'seleccionarProductos('.''.')',
        'clearMethod' => 'limpiarFiltrosProducto()',
        'pageInfo' => 'productos',
        'showAdd' => true,
        'showUpdate' => true,
        'showInfo' => false,
        'showCount' => true,
    ];
    $headers = ['#', 'Imagen', 'Resumen', 'Asiganciones', 'Opciones'];
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
    $(document).ready(function() {
        seleccionarProductos('', '', '', '');
        obtenerCategoriasParaProductos('Categorias', true, "false");
        obtenerRarezasParaDashboard('Rareza', true);
        obtenerUniversosParaDashboard('Universo', true, "false");
    });

    let typingTimer;

    function actualizarDatosConFiltros(){
        seleccionarProductos(
            $('#Nombre').val(),
            $('#Categoria').val(),
            $('#Rareza').val(),
            $('#Universo').val(),
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function(){
            $('#Nombre').on(
                'input',
                function(){
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(
                        () => {
                            currentPage = 1;
                            actualizarDatosConFiltros();
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
                '#Ordenar_por,' +
                '#Ordenar_en'
            ).on(
                'change',
                function(){

                    currentPage = 1;

                    actualizarDatosConFiltros();
                }
            );
        }
    );
</script>