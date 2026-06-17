<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $pageTitle = "Categorías";
    $pageIcon = 'bi-tools';
    $type = 'categoria';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCategory = false;

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
            'placeholder' => 'Buscar categoría...',
        ]
    ];

    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => 'bi bi-arrow-down-up',
            'input' => 'select',
            'options' => [
                'c.fecha_registro' => 'Fecha de creación',
                'c.nombre' => 'Nombre',
            ],
            'onchange' => 'aplicarFiltrosCategoria()',
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
            'onchange' => 'aplicarFiltrosCategoria()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]
    ];

    $menuTable = [
        'url' => 'addCategory.php',
        'updateMethod' => 'seleccionarCategorias('.')',
        'clearMethod' => 'limpiarFiltrosCategoria()',
        'pageInfo' => 'categorías',
        'showAdd' => true,
        'showUpdate' => true,
        'showInfo' => false,
        'showCount' => true,
    ];

    $headers = ['#', 'Imagen', 'Nombre', 'Opciones'];
?>

<div class="w-100 overflow-hidden p-0">

    <!-- FILTROS -->
    <div
        class="row px-0 py-1"
        id="formulario-filtros"
    >

        <?php
        foreach ($inputs as $input) {
            include '../src/components/inputs/input.php';
        }
        ?>

    </div>

    <!-- ORDEN -->
    <div
        class="row px-0 py-1"
        id="formulario-filtros-orden"
    >

        <?php
        foreach ($orders as $input) {
            include '../src/components/inputs/input.php';
        }
        ?>

    </div>

    <div class="px-0 pb-2">

        <div class="row justify-content-between">

            <div class="col-auto d-flex gap-2 mb-4">

                <?php
                include '../src/components/tables/menuTable.php';
                ?>

            </div>

        </div>

        <div class="overflow-hidden p-0">

            <div
                id="list-container"
                class="products-admin-grid p-0"
            ></div>

        </div>

    </div>

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>

    $(document).ready(function(){

        seleccionarCategorias('');

    });

    let typingTimer;

    function actualizarDatosConFiltros(){

        seleccionarCategorias(
            $('#Nombre').val()
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function(){

            $('#Nombre').on(
                'input',
                function(){

                    clearTimeout(
                        typingTimer
                    );

                    typingTimer = setTimeout(
                        () => {

                            currentPage = 1;

                            actualizarDatosConFiltros();

                        },
                        400
                    );
                }
            );

            $('#Ordenar_por').on(
                'change',
                function(){

                    currentPage = 1;

                    actualizarDatosConFiltros();
                }
            );
        }
    );

</script>