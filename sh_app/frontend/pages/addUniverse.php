<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateUniverse =
        isset($_GET['accion']) &&
        $_GET['accion'] == 'actualizar';

    $universeId =
        isset($_GET['id'])
            ? $_GET['id']
            : null;

    $pageTitle = $updateUniverse
        ? 'Actualizar universo'
        : 'Agregar universo';

    $pageIcon = 'bi-flag-fill';

    $basicInputs = [

        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'btnHelp' => true,
            'inputInfo' => 'Aquí se escribe el nombre del universo.',
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
            'placeholder' => 'Escribir nombre',
            'required' => 'Campo requerido',
        ],
    ];

    $imageInputs = [

        [
            'label' => 'Imagen',
            'id' => 'imagenUniverso',
            'idVista' => 'vistaImagenUniverso',
            'idHidden' => 'hiddenImagenUniverso',
            'value' => '<%= universo.imagen %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'file',
            'btnHelp' => true,
            'inputInfo' => 'Aquí seleccionas una imagen para el universo.',
            'spans' => [
                'Obligatorio',
                'Máximo un 1 MB (1000 KB) de tamaño'
            ],
            'col' => 'col-12 col-lg-6',
            'help' => 'Máximo un 1 MB (1000 KB) de tamaño',
            'required' => 'Campo requerido',
        ],

        [
            'label' => 'Icono',
            'id' => 'logoUniverso',
            'idVista' => 'vistaLogoUniverso',
            'idHidden' => 'hiddenLogoUniverso',
            'value' => '<%= universo.logo %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'file',
            'btnHelp' => true,
            'inputInfo' => 'Aquí seleccionas un icono para el universo.',
            'spans' => [
                'Obligatorio',
                'Máximo un 1 MB (1000 KB) de tamaño'
            ],
            'col' => 'col-12 col-lg-6',
            'help' => 'Máximo un 1 MB (1000 KB) de tamaño',
            'required' => 'Campo requerido',
        ]
    ];

    $menuTable = [
        'url' => 'universes.php',
        'addMethod' => 'guardarUniverso()',
    ];

    $type = 'universo';
?>

<div class="w-100 <?php echo $updateUniverse ? 'product-loading' : ''; ?>" id="universe-form">

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
                    Imágenes del universo
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php
            foreach ($imageInputs as $input) {
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
        value="<?php echo isset($universeId) ? $universeId : ''; ?>"
    >

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>

    function setUniverseLoading(isLoading){
        $('#universe-form').toggleClass(
            'product-loading',
            isLoading
        );
    }

    $(document).ready(function(){
        const isUpdate = <?php echo $updateUniverse ? 'true' : 'false'; ?>;

        if(isUpdate){
            setUniverseLoading(true);
            buscarUniverso(<?php echo $universeId; ?>);
        }
    });

    const maxSizeInKB = 1000;
    const maxSizeInBytes = maxSizeInKB * 1024;

    document
        .querySelectorAll('.id-input-image')
        .forEach(input => {

            input.addEventListener(
                'change',
                function(event){

                    const file =
                        event.target.files[0];

                    if(
                        file &&
                        file.size > maxSizeInBytes
                    ){

                        alert(
                            '¡Error!',
                            'No puedes cargar este archivo porque supera el tamaño máximo permitido (1 MB), busca una más pequeña.',
                            'error',
                            'Cerrar'
                        );

                        event.target.value = '';
                    }
                }
            );
        });

</script>