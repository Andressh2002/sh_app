<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCategory = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $categoryId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateCategory
        ? 'Actualizar categoría'
        : 'Agregar categoría';

    $pageIcon = 'bi-tools';

    $basicInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => 'Aquí se escribe el nombre de la categoría.',
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-4',
            'placeholder' => 'Escribir nombre',
        ],
    ];

    $imageInputs = [
        [
            'label' => 'Imágen',
            'id' => 'imagenCategoria',
            'idVista' => 'vistaImagenCategoria',
            'idHidden' => 'hiddenImagenCategoria',
            'value' => '<%= categoria.imagen %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'file',
            'btnHelp' => true,
            'inputInfo' => 'Aquí seleccionas una imagen para la categoría. Esta imagen se mostrará en la tienda.',
            'spans' => [
                'Obligatorio',
                'Máximo un 1 MB (1000 KB) de tamaño'
            ],
            'col' => 'col-12 col-lg-6',
        ]
    ];

    $menuTable = [
        'url' => 'categories.php',
        'addMethod' => 'guardarCategoria()',
    ];

    $type = 'categoria';
?>

<div class="w-100 <?php echo $updateCategory ? 'product-loading' : ''; ?>" id="category-form">

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
            foreach ($basicInputs as $input) {
                include '../src/components/inputs/input.php';
            }
            ?>
        </div>

    </div>

    <!-- IMAGEN -->
    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">
                    Imagen de la categoría
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
        value="<?php echo isset($categoryId) ? $categoryId : ''; ?>"
    >

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    
    function setCategoryLoading(isLoading){
        $('#category-form').toggleClass(
            'product-loading',
            isLoading
        );
    }

    $(document).ready(function(){
        const isUpdate = <?php echo $updateCategory ? 'true' : 'false'; ?>;

        if(isUpdate){
            setCategoryLoading(true);
            buscarCategoria(<?php echo $categoryId; ?>);
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