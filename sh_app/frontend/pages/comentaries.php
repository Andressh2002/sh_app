<?php

include '../src/components/login/access.php';

checkAccess('Administrador');

ob_start();

$pageTitle = "Comentarios";
$pageIcon = 'bi-chat-fill';

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = true;

$inputs = [

    [
        'label' => 'Producto',
        'id' => 'Producto',
        'icon' => 'bi bi-search',
        'input' => 'text',
        'btnHelp' => false,
        'placeholder' => 'Buscar producto...',
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3'
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
            'p.nombre' => 'Producto'

        ],

        'btnHelp' => false,
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3'

    ],

    [

        'label' => 'De forma:',
        'id' => 'Ordenar_en',

        'input' => 'select',

        'options' => [

            'DESC' => 'Descendente',
            'ASC' => 'Ascendente'

        ],

        'btnHelp' => false,
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3'

    ]

];

$menuTable = [

    'url' => '',
    'updateMethod' => 'seleccionarComentarios()',
    'clearMethod' => 'limpiarFiltrosComentario()',
    'pageInfo' => 'comentarios',

    'showAdd' => false,
    'showUpdate' => true,
    'showInfo' => false,
    'showCount' => true

];

?>

<div class="w-100 overflow-hidden">

    <div class="row px-0 py-1" id="formulario-filtros">

        <?php
        foreach($inputs as $input){

            include '../src/components/inputs/input.php';

        }
        ?>

    </div>

    <div class="row px-0 py-1">

        <?php
        foreach($orders as $input){

            include '../src/components/inputs/input.php';

        }
        ?>

    </div>

    <div class="px-0">

        <div class="row">

            <div class="col-auto mb-4">

                <?php include '../src/components/tables/menuTable.php'; ?>

            </div>

        </div>

        <div
            id="list-container"
            class="products-admin-grid"
        ></div>

    </div>

</div>

<?php

$content = ob_get_clean();

include 'template.php';

?>

<script>

$(document).ready(function(){

    seleccionarComentarios();

});

let typingTimer;

function actualizarDatosComentarios(){

    seleccionarComentarios(
        $('#Producto').val()
    );

}

$('#Producto').on(
    'input',
    function(){

        clearTimeout(typingTimer);

        typingTimer = setTimeout(
            actualizarDatosComentarios,
            400
        );

    }
);

$('#Ordenar_por, #Ordenar_en').on(
    'change',
    actualizarDatosComentarios
);

</script>