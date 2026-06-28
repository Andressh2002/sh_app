<?php

    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateRarity =
        isset($_GET['accion']) &&
        $_GET['accion'] == 'actualizar';

    $rarityId =
        isset($_GET['id'])
            ? $_GET['id']
            : null;

    $pageTitle =
        $updateRarity
            ? 'Actualizar rareza'
            : 'Agregar rareza';

    $pageIcon = 'bi-tag-fill';

    $basicInputs = [

        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'btnHelp' => true,
            'inputInfo' => 'Aquí se escribe el nombre de la rareza.',
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
            'placeholder' => 'Escribir nombre',
        ],

        [
            'label' => 'Color',
            'id' => 'Color',
            'icon' => 'bi bi-eyedropper',
            'input' => 'color',
            'btnHelp' => true,
            'inputInfo' => 'Color identificativo de la rareza.',
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
        ],
    ];

    $menuTable = [
        'url' => 'rarities.php',
        'addMethod' => 'guardarRareza()',
    ];

    $type = 'rareza';
?>

<div class="w-100 <?php echo $updateRarity ? 'product-loading' : ''; ?>" id="rarity-form">

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

    <div class="container-fluid mb-3">

        <?php include '../src/components/forms/dialogButtons.php'; ?>

    </div>

    <input
        type="hidden"
        id="Id"
        value="<?php echo isset($rarityId) ? $rarityId : ''; ?>"
    >

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>

    function setRarityLoading(isLoading){
        $('#rarity-form').toggleClass(
            'product-loading',
            isLoading
        );
    }

    $(document).ready(function(){
        const isUpdate = <?php echo $updateRarity ? 'true' : 'false'; ?>;

        if(isUpdate){
            setRarityLoading(true);
            buscarRareza(<?php echo $rarityId; ?>);
        }
    });

</script>