<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $pageTitle = "Colores";
    $pageIcon = 'bi-paint-bucket';
    $type = 'color';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $inputs = [

        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar color...',
        ],

        [
            'label' => 'Familia',
            'id' => 'Familia',
            'icon' => 'bi bi-palette-fill',
            'input' => 'text',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Buscar familia...',
        ]

    ];

    $orders = [

        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => 'bi bi-arrow-down-up',
            'input' => 'select',
            'options' => [
                'c.id' => 'Fecha de creación',
                'c.nombre' => 'Nombre',
                'c.color_familia' => 'Familia'
            ],
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
                'ASC' => 'Ascendente'
            ],
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ]

    ];

    $menuTable = [
        'url' => 'addColor.php',
        'updateMethod' => 'seleccionarColores()',
        'clearMethod' => 'limpiarFiltrosColor()',
        'pageInfo' => 'colores',
        'showAdd' => true,
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

                <?php
                include '../src/components/tables/menuTable.php';
                ?>

            </div>

        </div>

        <div
            id="list-container"
            class="products-admin-grid p-0"
        ></div>

    </div>

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>

$(document).ready(function(){

    seleccionarColores();

});

let typingTimer;

function actualizarDatosConFiltros(){

    seleccionarColores(
        $('#Nombre').val(),
        $('#Familia').val()
    );

}

document.addEventListener(
    'DOMContentLoaded',
    function(){

        $('#Nombre, #Familia').on(
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

        $('#Ordenar_por, #Ordenar_en').on(
            'change',
            function(){

                currentPage = 1;

                actualizarDatosConFiltros();

            }
        );

    }
);

</script>