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
            'input' => 'checkbox',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se marca si el producto es una fruta o no.",
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => '¿Comida?',
        ],
        [
            'label' => '¿Existencias límitadas?',
            'id' => 'Existencia',
            'icon' => 'bi bi-hourglass-split',
            'input' => 'checkbox',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se marca si el producto es de agotar existencias.",
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => '¿Tiene existencias?',
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
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Escribir nombre',
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
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Escribir precio',
        ],
        [
            'label' => 'Accesorio',
            'id' => 'Accesorio',
            'icon' => 'bi bi-palette-fill',
            'input' => 'select',
            'title' => 'Tabla de accesorios',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Imagen', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona un producto accesorio. Es para ligar un producto con otro que sea complemento, es decir, un accesorio que forma parte de un producto. Esto permite a los clientes poder escojer un color tanto en el producto como al accesorio.",
            'spans' => [null, 'Solo puedes seleccionar 1'],
            'col' => 'col-12 col-md-6 col-xl-3',
            'options' => [
                0 => 'Buscando...',
            ]
        ],
        [
            'label' => 'Descripcion',
            'id' => 'Descripcion',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una descripción del producto. Puede ser de que material está hecho, sus usos, o algún dato que consideres necesario mencionar.",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-lg-6',
            'placeholder' => 'Escribir detalles o datos útiles',
        ],
        [
            'label' => 'Advertencias',
            'id' => 'Advertencia',
            'icon' => 'bi bi-exclamation-triangle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una serie de advertencias sobre el producto. Puede ser que sea frágil, o que probablemente no se vea igual que en la imagen por ser hecho a mano, o también que no es apto para algunas edades, o cualquier cosa que consideres una advertencia.",
            'spans' => [null, null],
            'col' => 'col-12 col-lg-6',
            'placeholder' => 'Escribir consideraciones o cuidados especiales',
        ]
    ];

    $classificationsInputs = [
        [
            'label' => 'Categoría',
            'id' => 'Categorias',
            'icon' => 'bi bi-tools',
            'input' => 'select',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una categoría para el producto. Es para clasificar el producto por algo.",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'options' => [
                0 => 'Buscando...',
            ]
        ],
        [
            'label' => 'Rareza',
            'id' => 'Rareza',
            'icon' => 'bi bi-tag-fill',
            'input' => 'select',
            'title' => 'Tabla de rarezas',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Color', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una rareza para el producto. Es para clasificar tu producto por algo.",
            'spans' => ['Obligatorio', 'Solo puedes seleccionar 1'],
            'col' => 'col-12 col-md-6 col-xl-3',
            'options' => [
                0 => 'Buscando...',
            ]
        ],
        [
            'label' => 'Universo',
            'id' => 'Universo',
            'icon' => 'bi bi-flag-fill',
            'input' => 'select',
            'title' => 'Tabla de universos',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona un universo para el producto. Es para clasificar por alguna serie tu producto.",
            'spans' => ['Obligatorio', 'Solo puedes seleccionar 1'],
            'col' => 'col-12 col-md-6 col-xl-3',
            'options' => [
                0 => 'Buscando...',
            ]
        ],
        [
            'label' => 'Festividad',
            'id' => 'Festividad',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'select',
            'title' => 'Tabla de festividades',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Fecha', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una festividad para el producto. Es para hacer visible tu producto en la tienda por un tiempo predeterminado.",
            'spans' => [null, 'Solo puedes seleccionar 1'],
            'col' => 'col-12 col-md-6 col-xl-3',
            'options' => [
                0 => 'Buscando...',
            ]
        ],
    ];

    $discountsInputs = [
        [
            'label' => 'Ver descuentos',
            'id' => 'Descuento',
            'icon' => 'bi bi-percent',
            'input' => 'discounts',
            'title' => 'Tabla de descuentos',
            'body' => 'Hola!',
            'header' => ['#', 'Nombre', 'Descuento', 'Fecha', 'Opción'],
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona una o varias promociones de descuento para el producto. Es para asignar un descuento a tu producto en la tienda por tiempos predeterminados.",
            'spans' => [null, null],
            'col' => 'col-12 col-md-10 col-xl-8',
            'options' => [
                0 => 'Buscando...',
            ]
        ],
    ];

    $measuresInputs = [
        [
            'label' => 'Alto',
            'id' => 'Altura',
            'icon' => 'bi bi-rulers',
            'input' => 'text',
            'symbol' => 'cm',
            'onchange' => '',
            'btnHelp' => true,
            'help' => "Medido en cm",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Escribir altura',
        ],
        [
            'label' => 'Peso',
            'id' => 'Peso',
            'icon' => 'bi bi-hammer',
            'input' => 'text',
            'symbol' => 'kg',
            'onchange' => '',
            'btnHelp' => true,
            'help' => "Medido en kg",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Escribir peso',
        ],
        [
            'label' => 'Tiempo',
            'id' => 'Tiempo',
            'icon' => 'bi bi-alarm-fill',
            'input' => 'text',
            'symbol' => 'días',
            'onchange' => '',
            'btnHelp' => true,
            'help' => "Medido en días aproximados de fabricación",
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'placeholder' => 'Escribir tiempo',
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
            'input' => 'file',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona la imagen de portada del producto. Es la imagen del producto que verán los clientes en la tienda.",
            'spans' => ['Obligatorio', 'Máximo un 1 MB (1000 KB) de tamaño'],
            'col' => 'col-12 col-lg-6',
        ],
        [
            'label' => 'Imágen de galería',
            'id' => 'imagen2Producto',
            'idVista' => 'vistaImagen2Producto',
            'idHidden' => 'hiddenImagen2Producto',
            'value' => '<%= producto.imagen_galeria %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'file',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona la imagen que se usará como galería del producto.",
            'spans' => ['Obligatorio', 'Máximo un 1 MB (1000 KB) de tamaño'],
            'col' => 'col-12 col-lg-6',
        ],
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
            'inputInfo' => "Aquí seleccionas las paletas de colores que tendrá el producto. Puedes seleccionar un máximo de 16 variantes.",
            'spans' => ['Obligatorio (mínimo 1)', 'Máximo un 1 MB (1000 KB) de tamaño para cada imagen'],
            'col' => 'col-12 col-xl-8',
            'help' => 'Puedes solo seleccionar 20 paletas',
        ]
    ];

    $menuTable = [
        'url' => 'products.php',
        'addMethod' => 'guardarProducto()',
    ];

    $type = 'producto';
?>

<div class="w-100">
    <div class="overflow-hidden my-2">
        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">Tipo de producto</p>
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
    <div class="overflow-hidden my-2">
        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">Información básica</p>
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
    <div class="overflow-hidden my-2">
        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">Clasificadores</p>
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
    <div class="overflow-hidden my-2">
        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">Descuentos aplicados</p>
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
    <div class="overflow-hidden my-2">
        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">Características</p>
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
    <div class="overflow-hidden my-2">
        <div class="card-body admin-subheader-card-bg">
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
    <div class="overflow-hidden my-2" id="col-container-colors">
        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">Paletas de colores</p>
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
        obtenerRarezasParaDashboard('Rareza', false, "false");
        obtenerUniversosParaDashboard('Universo', false, "false");
        obtenerFestividadesParaDashboard('Festividad', false, true);
        obtenerAccesoriosParaDashboard('Accesorio', false, true);
    });

    $('#modalColors').on('shown.bs.modal', function () {
        obtenerColoresParaProductos('colors-data-container', '', '');
    });
    $('#modalDiscounts').on('shown.bs.modal', function () {
        obtenerDescuentosParaProductos('discounts-data-container', '');
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

    // Para colores
    let typingColorTimer;
    function actualizarColoresConFiltros(){
        obtenerColoresParaProductos(
            'colors-data-container',
            $('#NombreColorModal').val(),
            $('#FamiliaColorModal').val(),
        );
    }
    document.addEventListener(
        'DOMContentLoaded',
        function(){
            $('#NombreColorModal, #FamiliaColorModal').on(
                'input',
                function(){
                    clearTimeout(typingColorTimer);
                    typingColorTimer = setTimeout(
                        () => {
                            actualizarColoresConFiltros();
                        },
                        400
                    );
                }
            );
            $(
                '#NombreColorModal,' +
                '#FamiliaColorModal,' 
            ).on(
                'change',
                function(){
                    actualizarColoresConFiltros();
                }
            );
        }
    );

    // Para descuentos
    let typingDiscountTimer;
    function actualizarDescuentosConFiltros(){
        obtenerDescuentosParaProductos(
            'discounts-data-container',
            $('#NombreDescuentoModal').val(),
        );
    }
    document.addEventListener(
        'DOMContentLoaded',
        function(){
            $('#NombreDescuentoModal').on(
                'input',
                function(){
                    clearTimeout(typingDiscountTimer);
                    typingDiscountTimer = setTimeout(
                        () => {
                            actualizarDescuentosConFiltros();
                        },
                        400
                    );
                }
            );
            $(
                '#NombreDescuentoModal,'
            ).on(
                'change',
                function(){
                    actualizarDescuentosConFiltros();
                }
            );
        }
    );
</script>

