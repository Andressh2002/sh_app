<?php

include '../src/components/login/access.php';
checkAccess('Administrador');

ob_start();

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = true;

$updateColor =
    isset($_GET['accion']) &&
    $_GET['accion'] == 'actualizar';

$colorId =
    isset($_GET['id'])
        ? $_GET['id']
        : null;

$pageTitle =
    $updateColor
        ? 'Actualizar color'
        : 'Agregar color';

$pageIcon = 'bi-paint-bucket';

$basicInputs = [

    [
        'label' => 'Nombre',
        'id' => 'Nombre',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Nombre de la paleta de colores.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Familia',
        'id' => 'Familia',
        'icon' => 'bi bi-palette-fill',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Familia principal de colores.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

];

$paletteInputs = [

    [
        'label' => 'Color principal',
        'id' => 'Color1',
        'icon' => 'bi bi-eyedropper',
        'input' => 'color',
        'btnHelp' => true,
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-4',
    ],

    [
        'label' => 'Color secundario',
        'id' => 'Color2',
        'icon' => 'bi bi-eyedropper',
        'input' => 'color',
        'btnHelp' => true,
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-4',
    ],

    [
        'label' => 'Color terciario',
        'id' => 'Color3',
        'icon' => 'bi bi-eyedropper',
        'input' => 'color',
        'btnHelp' => true,
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-4',
    ]

];

$menuTable = [
    'url' => 'colors.php',
    'addMethod' => 'guardarColor()',
];

$type = 'color';

?>

<div class="w-100">

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center gap-2">

                <p class="card-title p-0 m-0">
                    Información básica
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php
            foreach ($basicInputs as $input) {
                include '../src/components/inputs/input.php';
            }
            ?>

        </div>

    </div>

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center gap-2">

                <p class="card-title p-0 m-0">
                    Paleta de colores
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php
            foreach ($paletteInputs as $input) {
                include '../src/components/inputs/input.php';
            }
            ?>

        </div>

    </div>

    <div class="container-fluid mb-3">

        <?php include '../src/components/forms/dialogButtons.php'; ?>

    </div>

    <input
        type="hidden"
        id="Id"
        value="<?php echo $colorId; ?>"
    >

</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>

<script>

$(document).ready(function(){

    if(
        <?php echo $updateColor ? 'true' : 'false'; ?>
    ){

        buscarColor(
            <?php echo $colorId; ?>
        );

    }

});

</script>