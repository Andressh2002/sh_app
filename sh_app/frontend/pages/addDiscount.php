<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateDiscount = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $discountId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateDiscount ? 'Actualizar descuento' : 'Agregar descuento';
    $pageIcon = 'bi-percent';
    
    $informationInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre del descuento.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Descuento',
            'id' => 'Descuento',
            'icon' => 'bi bi-percent',
            'input' => 'long',
            'symbol' => '%',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el descuento de la oferta.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Descripcion',
            'id' => 'Descripcion',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una descripción del descuento. Esta parte no se ve en la tienda, pero te puede servir si quieres anotar el motivo por el que creaste esta oferta.",
            'spans' => [null, null],
        ]
    ];
    $datesInputs = [
        [
            'label' => 'Fecha de inicio',
            'id' => 'StartDate',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'day',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona la fecha en la que se inicia la oferta. En otras palabras, a partir de cuando se empieza a aplicar el descuento en la tienda.",
            'spans' => ['Obligatorio', null],
            'options' => [
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio', 
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ],
        ],
        [
            'label' => 'Fecha de finalización',
            'id' => 'EndDate',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'day',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona la fecha en la que se termina la oferta. En otras palabras, a partir de cuando se deja de aplicar el descuento en la tienda.",
            'spans' => ['Obligatorio', null],
            'options' => [
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ],
        ],
        [
            'label' => '',
            'id' => 'yearIndicator',
            'icon' => '',
            'input' => 'infoText',
        ],
    ];
    $menuTable = [
        'url' => 'discounts.php',
        'addMethod' => 'guardarDescuento()',
    ];

    $type = 'descuento';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar descuento</h4>
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
                    <p class="card-title p-0 m-0">Rango de fechas</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($datesInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="container-fluid mt-3 mb-2">
            <?php include '../src/components/forms/dialogButtons.php'; ?>
        </div>
        <input type="hidden" id="Id" value="<?php echo isset($discountId) ? $discountId : ''; ?>"> 
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        if (<?php echo $updateDiscount ? 'true' : 'false'; ?>) {
            buscarDescuento(<?php echo $discountId; ?>);
        }
    });
</script>