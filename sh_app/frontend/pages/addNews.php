<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateNews = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $newsId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateNews ? 'Actualizar aviso' : 'Agregar aviso';
    $pageIcon = 'bi-bell-fill';
    
    $inputs = [
        [
            'label' => 'El título del aviso',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el título del mensaje.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Mensaje',
            'id' => 'Mensaje',
            'icon' => 'bi bi-info-circle-fill',
            'input' => 'textarea',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el mensaje del aviso.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Imágen referente al aviso',
            'id' => 'imagenAviso',
            'idVista' => 'vistaImagenAviso',
            'idHidden' => 'hiddenImagenAviso',
            'value' => '<%= aviso.imagen %>',
            'icon' => 'bi bi-image-fill',
            'input' => 'image',
            'btnHelp' => true,
            'inputInfo' => "Aquí seleccionas una imagen que quieras mostrar en el mensaje.",
            'spans' => [null, 'Máximo un 1 MB (1000 KB) de tamaño'],
        ]
    ];
    $menuTable = [
        'url' => 'news.php',
        'addMethod' => 'guardarAviso()',
    ];

    $type = 'aviso';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar aviso</h4>
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
        <input type="hidden" id="Id" value="<?php echo isset($newsId) ? $newsId : ''; ?>"> 
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        if (<?php echo $updateNews ? 'true' : 'false'; ?>) {
            buscarAviso(<?php echo $newsId; ?>);
        }
    });
</script>