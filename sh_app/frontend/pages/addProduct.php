<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateProduct = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $productId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateProduct ? 'Actualizar producto' : 'Agregar producto';
    $pageIcon = 'bi-palette-fill';

    $classesInputs = [
        [
            'label' => '¿Es comida?',
            'id' => 'Comida',
            'icon' => 'bi bi-basket-fill',
            'input' => 'check',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se marca si el producto es una fruta o no.",
            'spans' => [null, null],
        ],
        [
            'label' => '¿Existencias límitadas?',
            'id' => 'Existencia',
            'icon' => 'bi bi-hourglass-split',
            'input' => 'check',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se marca si el producto es de agotar existencias.",
            'spans' => [null, null],
        ],
    ];

    $informationInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre del producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Precio',
            'id' => 'Precio',
            'icon' => 'bi bi-cash',
            'input' => 'number',
            'symbol' => '₡',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el precio del producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Accesorio',
            'id' => 'Accesorio',
            'icon' => 'bi bi-palette-fill',
            'input' => 'accesory',
            'title' => 'Tabla de accesorios',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Imagen', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona un producto accesorio. Es para ligar un producto con otro que sea complemento, es decir, un accesorio que forma parte de un producto. Esto permite a los clientes poder escojer un color tanto en el producto como al accesorio.",
            'spans' => [null, 'Solo puedes seleccionar 1'],
        ],
        [
            'label' => 'Descripcion',
            'id' => 'Descripcion',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una descripción del producto. Puede ser de que material está hecho, sus usos, o algún dato que consideres necesario mencionar.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Advertencias    ',
            'id' => 'Advertencia',
            'icon' => 'bi bi-exclamation-triangle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una serie de advertencias sobre el producto. Puede ser que sea frágil, o que probablemente no se vea igual que en la imagen por ser hecho a mano, o también que no es apto para algunas edades, o cualquier cosa que consideres una advertencia.",
            'spans' => [null, null],
        ]
    ];

    $classificationsInputs = [
        [
            'label' => 'Categoría',
            'id' => 'Categorias',
            'icon' => 'bi bi-tools',
            'input' => 'selectajax',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una categoría para el producto. Es para clasificar el producto por algo.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag-fill',
            'input' => 'rarity',
            'title' => 'Tabla de rarezas',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Color', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una rareza para el producto. Es para clasificar tu producto por algo.",
            'spans' => ['Obligatorio', 'Solo puedes seleccionar 1'],
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag-fill',
            'input' => 'universe',
            'title' => 'Tabla de universos',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona un universo para el producto. Es para clasificar por alguna serie tu producto.",
            'spans' => ['Obligatorio', 'Solo puedes seleccionar 1'],
        ],
        [
            'label' => 'Festividad',
            'id' => 'Festividad',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'holiday',
            'title' => 'Tabla de festividades',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Fecha', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una festividad para el producto. Es para hacer visible tu producto en la tienda por un tiempo predeterminado.",
            'spans' => [null, 'Solo puedes seleccionar 1'],
        ],
    ];

    $discountsInputs = [
        [
            'label' => 'Ver descuentos',
            'id' => 'Descuento',
            'icon' => 'bi bi-percent',
            'input' => 'discount',
            'title' => 'Tabla de descuentos',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Descuento', 'Fecha', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una o varias promociones de descuento para el producto. Es para asignar un descuento a tu producto en la tienda por tiempos predeterminados.",
            'spans' => [null, null],
        ],
    ];

    $measuresInputs = [
        [
            'label' => 'Alto',
            'id' => 'Altura',
            'icon' => 'bi bi-rulers',
            'input' => 'long',
            'symbol' => 'cm',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe la altura del producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Peso',
            'id' => 'Peso',
            'icon' => 'bi bi-hammer',
            'input' => 'long',
            'symbol' => 'kg',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el peso del producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Tiempo',
            'id' => 'Tiempo',
            'icon' => 'bi bi-alarm-fill',
            'input' => 'long',
            'symbol' => 'días',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe la duración en la fabricación del producto en días.",
            'spans' => ['Obligatorio', null],
        ],
    ];

    $imagesInputs = [
        [
            'label' => 'Imágen de portada',
            'id' => 'imagen1Producto',
            'idVista' => 'vistaImagen1Producto',
            'idHidden' => 'hiddenImagen1Producto',
            'value' => '<%= producto.imagen_portada %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'image',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona la imagen de portada del producto. Es la imagen del producto que verán los clientes en la tienda.",
            'spans' => ['Obligatorio', 'Máximo un 1 MB (1000 KB) de tamaño'],
        ],
        [
            'label' => 'Imágen de galería',
            'id' => 'imagen2Producto',
            'idVista' => 'vistaImagen2Producto',
            'idHidden' => 'hiddenImagen2Producto',
            'value' => '<%= producto.imagen_galeria %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'image',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona la imagen que se usará como galería del producto.",
            'spans' => ['Obligatorio', 'Máximo un 1 MB (1000 KB) de tamaño'],
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
            'inputInfo' => "Aquí seleccionas las paletas de colores que tendrá el producto. Puedes seleccionar un máximo de 16 variantes.",
            'spans' => ['Obligatorio (mínimo 1)', 'Máximo un 1 MB (1000 KB) de tamaño para cada imagen'],
        ]
    ];

    $menuTable = [
        'url' => 'products.php',
        'addMethod' => 'guardarProducto()',
    ];

    $type = 'producto';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar producto</h4>
            <i class="bi bi-file-earmark-plus fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Tipo del producto</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($classesInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
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
                    <p class="card-title p-0 m-0">Clasificaciones</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($classificationsInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Descuentos</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($discountsInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Medidas</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($measuresInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Imágenes</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($imagesInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="card rounded-3 overflow-hidden my-2" id="col-container-colors">
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
    <input type="hidden" id="Id" value="<?php echo isset($productId) ? $productId : ''; ?>"> 
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        colores_almacenados = [];
        if (<?php echo $updateProduct ? 'true' : 'false'; ?>) {
            buscarProducto(<?php echo $productId; ?>);
        }
        obtenerCategoriasParaProductos('Categorias', false, "false");
    });

    $('#modalColors').on('shown.bs.modal', function () {
        obtenerColoresParaProductos('colors-data-container', '', '');
    });
    $('#modalHolidays').on('shown.bs.modal', function () {
        obtenerFestividadesParaProductos('holidays-data-container', '');
    });
    $('#modalRarities').on('shown.bs.modal', function () {
        obtenerRarezasParaProductos('rarities-data-container', '');
    });
    $('#modalUniverses').on('shown.bs.modal', function () {
        obtenerUniversosParaProductos('universes-data-container', '');
    });
    $('#modalDiscounts').on('shown.bs.modal', function () {
        obtenerDescuentosParaProductos('discounts-data-container', '');
    });
    $('#modalAccesories').on('shown.bs.modal', function () {
        obtenerAccesoriosParaProductos('accesories-data-container', '');
    });

    if (<?php echo $updateProduct ? 'false' : 'true'; ?>) {
        $('#textFestividad').val('Ninguno');
        $('#hiddenFestividad').val('0');
        $('#textRareza').val('Ninguno');
        $('#hiddenRareza').val('0');
        $('#textUniverso').val('Ninguno');
        $('#hiddenUniverso').val('0');
        $('#textAccesorio').val('Ninguno');
        $('#hiddenAccesorio').val('0');
    }

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

