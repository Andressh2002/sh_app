<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Accesorios";
    $pageIcon = 'bi-brush-fill';
    $type = 'accesorio';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateAccesory = false;

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
            'placeholder' => 'Buscar accesorio...',
        ]
    ];

    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => 'bi bi-arrow-down-up',
            'input' => 'select',
            'options' => [
                'ac.id' => 'Fecha de creación',
                'ac.nombre' => 'Nombre',
            ],
            'onchange' => 'aplicarFiltrosAccesorio()',
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
            'onchange' => 'aplicarFiltrosAccesorio()',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]
    ];

    $menuTable = [
        'url' => 'addAccesory.php',
        'updateMethod' => 'seleccionarAccesorios(' . '' . ')',
        'clearMethod' => 'limpiarFiltrosAccesorio()',
        'pageInfo' => 'accesorios',
        'showAdd' => true,
        'showUpdate' => true,
        'showInfo' => false,
        'showCount' => true,
    ];

    $headers = ['#', 'Imagen', 'Información', 'Opciones'];
?>

<div class="w-100 overflow-hidden p-0">

    <!-- FILTROS -->
    <div class="row px-0 py-1" id="formulario-filtros">
        <?php
        foreach ($inputs as $input) {
            include '../src/components/inputs/input.php';
        }
        ?>
    </div>

    <!-- ORDEN -->
    <div class="row px-0 py-1" id="formulario-filtros-orden">
        <?php
        foreach ($orders as $input) {
            include '../src/components/inputs/input.php';
        }
        ?>
    </div>

    <div class="px-0 pb-2">

        <!-- MENÚ DE ACCIONES -->
        <div class="row justify-content-between">

            <div class="col-auto d-flex gap-2 mb-4">
                <?php include '../src/components/tables/menuTable.php'; ?>
            </div>

        </div>

        <!-- TABLA -->
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
        seleccionarAccesorios('');
    });

    let typingTimer;

    function actualizarDatosConFiltros(){
        seleccionarAccesorios(
            $('#Nombre').val(),
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