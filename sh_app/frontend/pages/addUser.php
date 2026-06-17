<?php

include '../src/components/login/access.php';

checkAccess('Administrador');

ob_start();

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = true;

$updateUser =
    isset($_GET['accion'])
    && $_GET['accion'] === 'actualizar';

$userId =
    $_GET['id']
    ?? null;

$pageTitle =
    $updateUser
        ? 'Actualizar usuario'
        : 'Agregar usuario';

$pageIcon = 'bi-person-fill';

$informationInputs = [

    [
        'label' => 'Nombre',
        'id' => 'Nombre',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el nombre de la persona usuaria.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Segundo nombre',
        'id' => 'segundoNombre',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el segundo nombre de la persona usuaria.',
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Primer apellido',
        'id' => 'primerApellido',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el primer apellido de la persona usuaria.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Segundo apellido',
        'id' => 'segundoApellido',
        'icon' => 'bi bi-card-text',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el segundo apellido de la persona usuaria.',
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ]

];

$localitationInputs = [

    [
        'label' => 'Provincia',
        'id' => 'Provincia',
        'icon' => 'bi bi-geo-alt-fill',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe la provincia de la persona usuaria.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Cantón',
        'id' => 'Canton',
        'icon' => 'bi bi-geo-alt-fill',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el cantón de la persona usuaria.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Distrito',
        'id' => 'Distrito',
        'icon' => 'bi bi-geo-alt-fill',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el distrito de la persona usuaria.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Número teléfono',
        'id' => 'Telefono',
        'icon' => 'bi bi-telephone-fill',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el número telefónico de la persona usuaria.',
        'spans' => [null, null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ]

];

$userInputs = [

    [
        'label' => 'Nombre de usuario',
        'id' => 'nombreUsuario',
        'icon' => 'bi bi-key-fill',
        'input' => 'text',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe el nombre de usuario.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Contraseña',
        'id' => 'Contrasennia',
        'icon' => 'bi bi-lock-fill',
        'input' => 'password',
        'btnHelp' => true,
        'inputInfo' => 'Aquí se escribe la contraseña.',
        'spans' => ['Obligatorio', null],
        'col' => 'col-12 col-md-6 col-xl-4',
    ],

    [
        'label' => 'Rol',
        'id' => 'Rol',
        'icon' => 'bi bi-person-badge-fill',
        'input' => 'select',

        'options' => [
            'Administrador' => 'Administrador',
            'Cliente' => 'Cliente'
        ],

        'btnHelp' => true,

        'inputInfo' =>
            'Administrador: acceso completo al sistema. Cliente: únicamente puede navegar y reservar productos.',

        'spans' => ['Obligatorio', null],

        'col' => 'col-12 col-md-6 col-xl-4',
    ]

];

$menuTable = [
    'url' => 'users.php',
    'addMethod' => 'guardarUsuario()',
];

$type = 'usuario';

?>

<div class="w-100">

    <!-- INFORMACIÓN PERSONAL -->

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center">

                <p class="card-title m-0">
                    Información personal
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php

            foreach ($informationInputs as $input) {

                include '../src/components/inputs/input.php';

            }

            ?>

        </div>

    </div>

    <!-- LOCALIZACIÓN -->

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center">

                <p class="card-title m-0">
                    Localización
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php

            foreach ($localitationInputs as $input) {

                include '../src/components/inputs/input.php';

            }

            ?>

        </div>

    </div>

    <!-- DATOS DE USUARIO -->

    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center">

                <p class="card-title m-0">
                    Datos de usuario
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php

            foreach ($userInputs as $input) {

                include '../src/components/inputs/input.php';

            }

            ?>

        </div>

    </div>

    <div class="container-fluid mb-3">

        <?php
        include '../src/components/forms/dialogButtons.php';
        ?>

    </div>

    <input
        type="hidden"
        id="Id"
        value="<?= $userId ?>"
    >

</div>

<?php

$content =
    ob_get_clean();

include 'template.php';

?>

<script>

$(document).ready(function() {

    if (
        <?php echo $updateUser ? 'true' : 'false'; ?>
    ) {

        buscarUsuario(
            <?php echo $userId; ?>
        );

        $('#input-col-Contrasennia')
            .hide();
    }

});

</script>