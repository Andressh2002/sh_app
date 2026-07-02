<?php
    include '../src/components/login/access.php';
    $pageTitle = "Pedido";

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $saveIdProducto = isset($_GET['idProducto']) ? $_GET['idProducto'] : null;
    $saveIdColor = isset($_GET['idColor']) ? $_GET['idColor'] : null;
    $saveIdAccesorio = $_GET['idAccesorio'] ?? null;
    if ($saveIdAccesorio === "undefined" || $saveIdAccesorio === "") {
        $saveIdAccesorio = null;
    }
    $saveIdColorAccesorio = isset($_GET['idColorAccesorio']) ? $_GET['idColorAccesorio'] : null;
    $saveCantidad = isset($_GET['cantidad']) ? $_GET['cantidad'] : null;
    $saveTotal = isset($_GET['total']) ? $_GET['total'] : null;
    $saveNumColorProducto = isset($_GET['numColor']) ? $_GET['numColor'] : null;
    $saveNumColorAccesorio = isset($_GET['numColorAccesorio']) ? $_GET['numColorAccesorio'] : null;

    $personalInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'required' => 'Valor requerido',
        ],
        [
            'label' => 'Segundo nombre',
            'id' => 'segundoNombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
        [
            'label' => 'Primer apellido',
            'id' => 'primerApellido',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'required' => 'Valor requerido',
        ],
        [
            'label' => 'Segundo apellido',
            'id' => 'segundoApellido',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => [null, null],
            'col' => 'col-12 col-md-6 col-xl-3',
        ],
    ];

    $locationInputs = [
        [
            'label' => 'Provincia',
            'id' => 'Provincia',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'required' => 'Valor requerido',
        ],
        [
            'label' => 'Cantón',
            'id' => 'Canton',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'required' => 'Valor requerido',
        ],
        [
            'label' => 'Distrito',
            'id' => 'Distrito',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-md-6 col-xl-3',
            'required' => 'Valor requerido',
        ],
        [
            'label' => 'Lugar de entrega',
            'id' => 'Direccion_',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'textarea',
            'onchange' => '',
            'btnHelp' => false,
            'spans' => ['Obligatorio', null],
            'col' => 'col-12 col-lg-6',
            'required' => 'Valor requerido',
        ],
    ];

    $informationInputs = [
        [
            'label' => 'Número teléfono',
            'id' => 'Telefono',
            'icon' => 'bi bi-telephone-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'spans' => [null, null],
            'inputInfo' => "No es obligatorio, pero es para poder contactarlo.",
            'col' => 'col-12 col-md-6 col-xl-3',
            'required' => 'Valor requerido',
        ],
    ];

    $menuTable = [
        'url' => 'products.php',
        'addMethod' => 'guardarProducto()',
    ];
?>

<div class="row my-3 p-4 mx-0">
    <div class="container-fluid p-0">

        <div class="d-flex align-items-center gap-2 px-0 mb-2">
            <h4 class="mb-0">Resumen del pedido</h4>
            <i class="bi bi-binoculars-fill fs-4 d-flex align-self-center"></i>
        </div>

        <div class="row">
            <div class="product-admin-card">
                    <div class="product-admin-header">
                        <div>
                            <p class="product-number">
                                Vista previa
                            </p>
                            <h5 class="product-title" id="span-nombre-producto">
                                ...
                            </h5>
                        </div>
                    </div>

                    <div class="product-admin-body">
                        <div>
                            <div class="product-admin-image mb-2">
                                <div class="row p-3" id="div-pre-img-producto"></div>
                            </div>

                            <?php if ($saveIdAccesorio !== null && $saveIdAccesorio != 0) : ?>
                                <div class="product-admin-image">
                                    <div class="row p-3" id="div-pre-img-accesorio"></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <div class="product-info-grid">
                                <div>
                                    <span>Categoría:</span>
                                    <strong id="span-categoria">
                                        ...
                                    </strong>
                                </div>
                                <div>
                                    <span>Universo:</span>
                                    <strong id="span-universo">
                                        ...
                                    </strong>
                                </div>
                                <div>
                                    <span>Cantidad:</span>
                                    <strong>
                                        <?php echo $_GET['cantidad'] ?? '' ?>
                                    </strong>
                                </div>
                                <div class="mt-2">
                                    <span>Precio total:</span>
                                    <strong>
                                        <?php echo $_GET['total'] ?? '' ?>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="order-actions"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 px-0 mt-4">
            <h4 class="mb-0">Información personal</h4>
            <i class="bi bi-person-fill fs-4 d-flex align-self-center"></i>
        </div>

        <div class="w-100">
            <div class="overflow-hidden my-2">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <p class="card-title p-0 m-0">Nombre</p>
                    </div>
                </div>
                <div class="row px-3 py-1">
                    <?php
                    foreach ($personalInputs as $input) {
                        include '../src/components/inputs/input.php';
                    }
                    ?>
                </div>
            </div>
            <div class="overflow-hidden my-2">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <p class="card-title p-0 m-0">Ubicación</p>
                    </div>
                </div>
                <div class="row px-3 py-1">
                    <?php
                    foreach ($locationInputs as $input) {
                        include '../src/components/inputs/input.php';
                    }
                    ?>
                </div>
            </div>
            <div class="overflow-hidden my-2">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <p class="card-title p-0 m-0">Contacto</p>
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
        </div>
    </div>

    <input type="hidden" id="Product" value="<?php echo isset($saveIdProducto) ? $saveIdProducto : ''; ?>"> 
    <input type="hidden" id="Color" value="<?php echo isset($saveIdColor) ? $saveIdColor : ''; ?>"> 
    <input type="hidden" id="AccesoryColor" value="<?php echo isset($saveIdColorAccesorio) ? $saveIdColorAccesorio : ''; ?>"> 
    <input type="hidden" id="Cant" value="<?php echo isset($saveCantidad) ? $saveCantidad : ''; ?>"> 
    <input type="hidden" id="Total" value="<?php echo isset($saveTotal) ? $saveTotal : ''; ?>"> 
</div>

<div class="bg-clip-path-up-store-section"></div>
<div class="m-0 w-100 bg-store-section pb-3 pt-5">
    <div class="row container-fluid mx-auto gap-1">
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">¡Opción de crear un usuario!</p>
                <i class="bi bi-person fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Al crear un usuario puedes gestionar y ver tus pedidos. Incluso puedes valorar por estrellas y comentar nuestros productos.
            </p>
        </div>
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">¡Cuidado con falsa información!</p>
                <i class="bi bi-bug fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Si recibimos un pedido con nombre de cliente, teléfono o cualquier otro dato falso, ese pedido no será tomado en cuenta.
            </p>
        </div>
    </div>
</div>
<div class="bg-clip-path-down-store-section mb-4"></div>

<div class="container-fluid mb-3">
    <div class="row justify-content-center">
        <div class="col-auto">
            <div class="navbar-btn-shadow">
                <button
                    onclick='guardarPedidoSinUsuario(<?php echo json_encode($saveIdProducto); ?>, <?php echo json_encode($saveCantidad); ?>, <?php echo json_encode($saveTotal); ?>)'
                    class="store-filter-btn slide_from_left text-decoration-none"
                >
                    <i class="bi bi-floppy-fill"></i>
                    <span>Realizar pedido</span>
                </button>
            </div>
        </div>
    </div>
</div>


<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        buscarPrevisualizacionProducto(<?php echo json_encode($saveIdProducto); ?>, <?php echo json_encode($saveIdAccesorio)?>, <?php echo json_encode($saveNumColorProducto); ?>, <?php echo json_encode($saveNumColorAccesorio); ?>);
    });
</script>