<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateColor = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $colorId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateColor ? 'Actualizar color' : 'Agregar color';
    $pageIcon = 'bi-paint-bucket';
    
    $informationInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre de la paleta de colores.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Descripcion',
            'id' => 'Descripcion',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una descripción del color. Esta parte no se ve en la tienda, pero te puede servir si quieres anotar el motivo por el que creaste esta color.",
            'spans' => [null, null],
        ]
    ];
    $colorsInputs = [
        [
            'label' => 'Color principal',
            'id' => 'Color1',
            'icon' => 'bi bi-eyedropper',
            'input' => 'color',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona el color principal de la paleta. El color que más se destaca en el producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Segundo color',
            'id' => 'Color2',
            'icon' => 'bi bi-eyedropper',
            'input' => 'color',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona el segundo color de la paleta. El color que más o menos se destaca en el producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Tercer color',
            'id' => 'Color3',
            'icon' => 'bi bi-eyedropper',
            'input' => 'color',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona el tercer color de la paleta. El color que menos se destaca en el producto.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Familia de colores',
            'id' => 'Familia',
            'icon' => 'bi bi-palette-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe un nombre genérico de la paleta, puede ser como ejemplo: rojo, azul, amarillo, verde, entre otros colores.",
            'spans' => ['Obligatorio', null],
        ]
    ];
    $menuTable = [
        'url' => 'colors.php',
        'addMethod' => 'guardarColor()',
    ];

    $type = 'color';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar color</h4>
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
                    <p class="card-title p-0 m-0">Paleta</p>
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
        <div class="container-fluid mt-3 mb-2">
            <?php include '../src/components/forms/dialogButtons.php'; ?>
        </div>
        <input type="hidden" id="Id" value="<?php echo isset($colorId) ? $colorId : ''; ?>"> 
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        if (<?php echo $updateColor ? 'true' : 'false'; ?>) {
            buscarColor(<?php echo $colorId; ?>);
        }
    });
</script>