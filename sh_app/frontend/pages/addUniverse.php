<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateUniverse = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $universeId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateUniverse ? 'Actualizar universo' : 'Agregar universo';
    $pageIcon = 'bi-tools';
    
    $inputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre del universo.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Imágen',
            'id' => 'imagenUniverso',
            'idVista' => 'vistaImagenUniverso',
            'idHidden' => 'hiddenImagenUniverso',
            'value' => '<%= universo.imagen %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'image',
            'btnHelp' => true,
            'inputInfo' => "Aquí seleccionas una imagen para el universo. Esta parte se ve en la página de inicio de la tienda.",
            'spans' => ['Obligatorio', 'Máximo un 1 MB (1000 KB) de tamaño'],
        ],
        [
            'label' => 'Descripcion',
            'id' => 'Descripcion',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe una descripción del universo. Esta parte no se ve en la tienda, pero te puede servir si quieres anotar el motivo por el que creaste este universo.",
            'spans' => [null, null],
        ]
    ];
    $menuTable = [
        'url' => 'universes.php',
        'addMethod' => 'guardarUniverso()',
    ];

    $type = 'universo';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar universo</h4>
            <i class="bi bi-file-earmark-plus fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Información básica</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($inputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="container-fluid mt-3 mb-2">
            <?php include '../src/components/forms/dialogButtons.php'; ?>
        </div>
        <input type="hidden" id="Id" value="<?php echo isset($universeId) ? $universeId : ''; ?>"> 
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        if (<?php echo $updateUniverse ? 'true' : 'false'; ?>) {
            buscarUniverso(<?php echo $universeId; ?>);
        }
    });
</script>