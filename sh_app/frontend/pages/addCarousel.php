<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCarousel = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $carouselId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateCarousel ? 'Actualizar carta del carrusel' : 'Agregar carta del carrusel';
    $pageIcon = 'bi-calendar-fill';
    
    $inputs = [
        [
            'label' => 'Título',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Texto',
            'id' => 'Texto',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Imágen',
            'id' => 'imagenCarrusel',
            'idVista' => 'vistaImagenCarrusel',
            'idHidden' => 'hiddenImagenCarrusel',
            'value' => '<%= carrusel.imagen %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'image',
        ],
        [
            'label' => 'Festividad',
            'id' => 'Festividad',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'holiday',
            'title' => 'Tabla de festividades',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Fecha', 'Opción'],
        ]
    ];
    $menuTable = [
        'url' => 'carousel.php',
        'addMethod' => 'guardarCarrusel()',
    ];

    $type = 'carrusel';
?>

<div class="w-100 p-3 rounded-3" style="background-color: #f9fafb;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="mb-0"><?php echo $updateCarousel ? "Actualizar carta del carrusel" : "Agregar carta del carrusel"; ?></h4>
        <i class="bi bi-search fs-4 d-flex align-self-center"></i>
    </div>
    
    <div class="container-fluid p-0">
        <div class="row">
            <?php
                foreach ($inputs as $input) {
                    include '../src/components/inputs/input.php';
                }
            ?>
        </div>
    </div>
    <div class="container-fluid">
        <?php include '../src/components/forms/dialogButtons.php'; ?>
    </div>
    <input type="hidden" id="Id" value="<?php echo isset($carouselId) ? $carouselId : ''; ?>"> 
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        if (<?php echo $updateCarousel ? 'true' : 'false'; ?>) {
            buscarCarrusel(<?php echo $carouselId; ?>);
        }
    });

    $('#modalHolidays').on('shown.bs.modal', function () {
        obtenerFestividadesParaProductos('holidays-data-container', '');
    });
</script>