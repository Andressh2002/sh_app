<?php
    include '../src/components/login/access.php';
    $pageTitle = "Avisos";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;
    $inputs = [
        [
            'label' => 'Título del aviso',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosAvisoCliente()',
            'btnHelp' => false,
            'spans' => [null, null],
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => '',
            'input' => 'select',
            'options' => [
                'fecha_registro' => 'Fecha de creación',
                'titulo' => 'Título'
            ],
            'onchange' => 'aplicarFiltrosAvisoCliente()',
            'btnHelp' => false,
            'spans' => [null, null],
        ]
    ];
    $menuTable = [
        'url' => 'addNews.php',
        'updateMethod' => 'seleccionarAvisosCliente('.')',
        'clearMethod' => 'limpiarFiltrosAviso()',
        'showAdd' => false,
        'showUpdate' => true,
        'btnHelp' => false,
        'showInfo' => false,
        'spans' => [null, null],
    ];
    $headers = ['#', 'Título', 'Fecha', 'Opciones'];
?>

<div class="row my-3 p-4 mx-0">
    <div class="d-flex align-items-center gap-2 mb-4 px-0">
        <h4 class="mb-0">Descarga</h4>
        <i class="bi bi-cloud-arrow-down-fill fs-4 d-flex align-self-center"></i>
    </div>
</div>

<div class="bg-clip-path-up-store-section"></div>
<div class="m-0 w-100 bg-store-section pb-3">
    <div class="m-auto row container-sm w-100 m-auto">
        <div class="d-flex align-items-center gap-2 mb-4 px-0">
            <p class="fw-bold card-category-text-h m-0">SH APP - Móvil</p>
            <i class="bi bi-phone fs-4 d-flex align-self-center mx-0"></i>
        </div>
        
        <p class="card-category-text-p m-0 p-0">
            Descarga <a href="https://drive.google.com/file/d/1fwlYCxoR1AshpU1UpMyOvSCxFRgZ0xQx/view?usp=sharing">aquí</a> la app para móviles
        </p>
    </div>
</div>
<div class="bg-clip-path-down-store-section mb-4"></div>

<div class="bg-clip-path-up-store-section"></div>
<div class="m-0 w-100 bg-store-section pb-3">
    <div class="m-auto row container-sm w-100 m-auto">
        <div class="d-flex align-items-center gap-2 mb-4 px-0">
            <p class="fw-bold card-category-text-h m-0">SH APP - Escritorio</p>
            <i class="bi bi-laptop fs-4 d-flex align-self-center mx-0"></i>
        </div>
        
        <p class="card-category-text-p m-0 p-0">
            Descarga <a href="https://drive.google.com/file/d/1yJTdm2xZuJywmnbpqyQNvjtiABWP93we/view?usp=sharing">aquí</a> la app para computadoras
        </p>
    </div>
</div>
<div class="bg-clip-path-down-store-section mb-4"></div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        seleccionarAvisosCliente('');
    });
</script>