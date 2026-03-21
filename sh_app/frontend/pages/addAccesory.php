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
            'inputInfo' => "Aquí se escribe el nombre del accesorio.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Descripcion',
            'id' => 'Descripcion',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una descripción del accesorio. Esta parte no se ve en la tienda, sin embargo, te puede servir si quieres anotar algo que te ayude a recordar del por qué creaste este accesorio.",
            'spans' => [null, null],
        ],
    ];

    $colorsInputs = [
        [
            'label' => 'Colores',
            'id' => 'Colores',
            'icon' => 'bi bi-palette-fill',
            'input' => 'colors',
            'title' => 'Tabla de colores',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Familia', 'Color', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí seleccionas las paletas de colores que tendrá el accesorio. Puedes seleccionar un máximo de 16 variantes.",
            'spans' => ['Obligatorio (mínimo 1)', 'Máximo un 1 MB (1000 KB) de tamaño para cada imagen'],
        ]
    ];

    $menuTable = [
        'url' => 'accesories.php',
        'addMethod' => 'guardarAccesorio()',
    ];

    $type = 'accesorio';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar accesorio</h4>
            <i class="bi bi-file-earmark-plus fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Información</p>
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
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Paletas</p>
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
    </div>
    <div class="container-fluid mb-3">
        <?php include '../src/components/forms/dialogButtons.php'; ?>
    </div>
    <input type="hidden" id="Id" value="<?php echo isset($accesoryId) ? $accesoryId : ''; ?>"> 
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        colores_almacenados = [];
        if (<?php echo $updateAccesory ? 'true' : 'false'; ?>) {
            buscarAccesorio(<?php echo $accesoryId; ?>);
        }
    });

    $('#modalColors').on('shown.bs.modal', function () {
        obtenerColoresParaProductos('colors-data-container', '', '');
    });

    const maxSizeInKB = 1000; // Tamaño máximo en KB
    const maxSizeInBytes = maxSizeInKB * 1024; // Convertir a bytes

    // Seleccionar todos los inputs con clase "fileInput"
    const fileInputs = document.querySelectorAll('.id-input-image');

    // Añadir el evento change a cada input
    fileInputs.forEach(input => {
        input.addEventListener('change', function (event) {
            const file = event.target.files[0]; // Obtener el archivo cargado

            // Validar el tamaño del archivo
            if (file && file.size > maxSizeInBytes) {
                alert(
                    '¡Error!',
                    'No puedes cargar este archivo porque supera el tamaño máximo permitido (1 MB), busca una más pequeña.',
                    'error',
                    'Cerrar'
                ); // Mostrar mensaje de error
                event.target.value = ''; // Limpiar el input
            } else {
                errorMsg.style.display = 'none'; // Ocultar mensaje de error
            }
        });
    });
</script>

