<?php
include '../src/components/login/access.php';
checkAccess('Administrador');

ob_start();

$pageTitle = "Descuentos";
$pageIcon = 'bi-percent';

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = true;

$type = 'descuento';

$inputs = [

    [
        'label' => 'Nombre',
        'id' => 'Nombre',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'btnHelp' => false,
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3',
        'placeholder' => 'Buscar descuento...'
    ]

];

$orders = [

    [
        'label' => 'Ordenar por:',
        'id' => 'Ordenar_por',
        'icon' => 'bi bi-arrow-down-up',
        'input' => 'select',
        'options' => [
            'id' => 'Fecha de creación',
            'nombre' => 'Nombre',
            'descuento' => 'Porcentaje'
        ],
        'btnHelp' => false,
        'col' => 'col-12 col-md-6 col-xl-3'
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
        'col' => 'col-12 col-md-6 col-xl-3'
    ]

];

$menuTable = [
    'url' => 'addDiscount.php',
    'updateMethod' => 'seleccionarDescuentos('.')',
    'clearMethod' => 'limpiarFiltrosDescuento()',
    'pageInfo' => 'descuentos',
    'showAdd' => true,
    'showUpdate' => true,
    'showInfo' => false,
    'showCount' => true
];
?>

<div class="w-100 overflow-hidden p-0">

    <div class="row px-0 py-1" id="formulario-filtros">

        <?php
        foreach($inputs as $input){
            include '../src/components/inputs/input.php';
        }
        ?>

    </div>

    <div class="row px-0 py-1" id="formulario-filtros-orden">

        <?php
        foreach($orders as $input){
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
    $(document).ready(function() {
        seleccionarDescuentos('');
    });

    let typingTimer;

    function actualizarDatosConFiltros(){

        seleccionarDescuentos(
            $('#Nombre').val()
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

            $('#Ordenar_por, #Ordenar_en').on(
                'change',
                function(){

                    actualizarDatosConFiltros();

                }
            );

        }
    );

</script>