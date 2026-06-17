<?php

include '../src/components/login/access.php';
checkAccess('Administrador');

ob_start();

$pageTitle = 'Usuarios';
$pageIcon = 'bi-person-fill';
$type = 'usuario';

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
        'onchange' => '',
        'btnHelp' => false,
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3',
        'placeholder' => 'Buscar usuario...',
    ],

    [
        'label' => 'Rol',
        'id' => 'Rol',
        'icon' => 'bi bi-person-badge-fill',
        'input' => 'select',
        'onchange' => '',
        'btnHelp' => false,
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-3',

        'options' => [
            '' => 'Todos',
            'Administrador' => 'Administrador',
            'Cliente' => 'Cliente'
        ]
    ]
];

$orders = [

    [
        'label' => 'Ordenar por:',
        'id' => 'Ordenar_por',
        'icon' => 'bi bi-arrow-down-up',
        'input' => 'select',

        'options' => [
            'fecha_registro' => 'Fecha creación',
            'nombre' => 'Nombre',
            'nombre_usuario' => 'Usuario',
            'rol' => 'Rol'
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
    'url' => 'addUser.php',
    'updateMethod' => 'seleccionarUsuarios()',
    'clearMethod' => 'limpiarFiltrosUsuario()',
    'pageInfo' => 'usuarios',
    'showAdd' => true,
    'showUpdate' => true,
    'showInfo' => false,
    'showCount' => true,
];

?>

<div class="w-100 overflow-hidden p-0">

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

    seleccionarUsuarios();

});

let typingTimer;

function actualizarUsuarios(){

    currentPage = 1;

    seleccionarUsuarios(
        $('#Nombre').val(),
        $('#Rol').val()
    );
}

$('#Nombre').on(
    'input',
    function(){

        clearTimeout(typingTimer);

        typingTimer = setTimeout(
            actualizarUsuarios,
            400
        );
    }
);

$('#Rol, #Ordenar_por, #Ordenar_en').on(
    'change',
    actualizarUsuarios
);

</script>