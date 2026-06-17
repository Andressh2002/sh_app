<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateAccesory = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $accesoryId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateAccesory ? 'Actualizar accesorio' : 'Agregar accesorio';
    $pageIcon = 'bi-brush-fill';

    $informationInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => 'Aquí se escribe el nombre del accesorio.',
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
            'placeholder' => 'Escribir nombre'
        ]
    ];

    $colorsInputs = [
        [
            'label' => 'Colores',
            'id' => 'Colores',
            'icon' => 'bi bi-palette-fill',
            'input' => 'palettes',
            'title' => 'Tabla de colores',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Familia', 'Color', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => 'Aquí seleccionas las paletas de colores que tendrá el accesorio. Puedes seleccionar un máximo de 16 variantes.',
            'spans' => [
                'Obligatorio (mínimo 1)',
                'Máximo un 1 MB (1000 KB) de tamaño para cada imagen'
            ],
            'col' => 'col-12 col-xl-8',
            'help' => 'Puedes solo seleccionar 20 paletas',
        ]
    ];

    $menuTable = [
        'url' => 'accesories.php',
        'addMethod' => 'guardarAccesorio()',
    ];

    $type = 'accesorio';
?>

<div class="w-100">

    <!-- INFORMACIÓN -->
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
            foreach ($informationInputs as $input) {
                include '../src/components/inputs/input.php';
            }
            ?>

        </div>

    </div>

    <!-- PALETAS -->
    <div
        class="overflow-hidden my-2"
        id="col-container-colors"
    >

        <div class="card-body admin-subheader-card-bg">

            <div class="d-flex align-items-center gap-2">

                <p class="card-title p-0 m-0">
                    Paletas de colores
                </p>

            </div>

        </div>

        <div class="row px-3 py-1">

            <?php
            foreach ($colorsInputs as $input) {
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
        value="<?php echo isset($accesoryId) ? $accesoryId : ''; ?>"
    >

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>

    $(document).ready(function(){

        colores_almacenados = [];

        if(<?php echo $updateAccesory ? 'true' : 'false'; ?>){

            buscarAccesorio(
                <?php echo $accesoryId; ?>
            );
        }
    });

    $('#modalColors').on(
        'shown.bs.modal',
        function(){

            obtenerColoresParaProductos(
                'colors-data-container',
                '',
                ''
            );
        }
    );

    const maxSizeInKB = 1000;
    const maxSizeInBytes = maxSizeInKB * 1024;

    const fileInputs =
        document.querySelectorAll(
            '.id-input-image'
        );

    fileInputs.forEach(input => {

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

    let typingColorTimer;
    function actualizarColoresConFiltros(){
        obtenerColoresParaProductos(
            'colors-data-container',
            $('#NombreColorModal').val(),
            $('#FamiliaColorModal').val()
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function(){
            $('#NombreColorModal, #FamiliaColorModal').on(
                'input',
                function(){
                    clearTimeout(
                        typingColorTimer
                    );
                    typingColorTimer = setTimeout(
                        () => {
                            actualizarColoresConFiltros();
                        },
                        400
                    );
                }
            );

            $('#NombreColorModal, #FamiliaColorModal').on(
                'change',
                function(){
                    actualizarColoresConFiltros();
                }
            );
        }
    );

</script>