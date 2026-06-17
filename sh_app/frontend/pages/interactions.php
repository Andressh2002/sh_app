<?php

include '../src/components/login/access.php';

checkAccess('Administrador');

ob_start();

$pageTitle = "Interacciones";
$pageIcon = 'bi-broadcast-pin';
$type = 'interacción';

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = true;

$inputs = [
    [
        'label' => 'Acción',
        'id' => 'Accion',
        'icon' => 'bi bi-broadcast',
        'input' => 'text',
        'btnHelp' => false,
        'spans' => [null,null],
        'col' => 'col-12 col-md-6 col-xl-3',
        'placeholder' => 'Buscar interacción...'
    ]
];

$orders = [

    [
        'label' => 'Ordenar por:',
        'id' => 'Ordenar_por',
        'icon' => 'bi bi-arrow-down-up',
        'input' => 'select',
        'options' => [
            'i.fecha_registro' => 'Fecha de registro',
            'i.accion' => 'Acción'
        ],
        'btnHelp' => false,
        'spans' => [null,null],
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
        'spans' => [null,null],
        'col' => 'col-12 col-md-6 col-xl-3'
    ],

    [
        'label' => 'Mostrar:',
        'id' => 'Limite',
        'icon' => 'bi bi-list-ol',
        'input' => 'select',
        'options' => [
            '10' => '10 registros',
            '20' => '20 registros',
            '50' => '50 registros',
            '100' => '100 registros',
            'todos' => 'Todos'
        ],
        'btnHelp' => false,
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3',
    ]
];

$menuTable = [
    'url' => '',
    'updateMethod' => 'seleccionarInteracciones()',
    'clearMethod' => 'limpiarFiltrosInteraccion()',
    'pageInfo' => 'interacciones',
    'showAdd' => false,
    'showUpdate' => true,
    'showInfo' => false,
    'showCount' => true
];

?>

<div class="w-100 overflow-hidden p-0">

    <div
        class="row px-0 py-1"
        id="formulario-filtros"
    >

        <?php
        foreach(
            $inputs
            as $input
        ){
            include '../src/components/inputs/input.php';
        }
        ?>

    </div>

    <div
        class="row px-0 py-1"
        id="formulario-filtros-orden"
    >

        <?php
        foreach(
            $orders
            as $input
        ){
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

$content =
    ob_get_clean();

include 'template.php';

?>

<script>

$(document).ready(function(){

    seleccionarInteracciones();

});

let typingTimer;

$('#Accion').on(
    'input',
    function(){

        clearTimeout(
            typingTimer
        );

        typingTimer =
            setTimeout(
                () => {

                    seleccionarInteracciones(
                        $(this).val()
                    );

                },
                400
            );

    }
);

$('#Ordenar_por,#Ordenar_en,#Limite')
.on(
    'change',
    function(){

        seleccionarInteracciones(
            $('#Accion').val()
        );

    }
);

</script>