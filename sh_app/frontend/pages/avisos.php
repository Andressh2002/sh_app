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
        <h4 class="mb-0">Avisos</h4>
        <i class="bi bi-bell-fill fs-4 d-flex align-self-center"></i>
    </div>

    <div class="container-fluid p-0">
        <div class="row" id="formulario-filtros">
            <?php
                foreach ($inputs as $input) {
                    include '../src/components/inputs/input.php';
                }
            ?>
        </div>
        <div class="row" id="formulario-filtros-orden">
            <?php
                foreach ($orders as $input) {
                    include '../src/components/inputs/input.php';
                }
            ?>
        </div>
        <div class="row justify-content-between">
            <div class="col-auto">
                <p class="card-text mb-3" id="total-data"></p>
            </div>
        </div>
        <div class="row justify-content-between">
            <div class="col-auto">
                <nav aria-label="Page navigation example">
                    <ul class="pagination"></ul>
                </nav>
            </div>
            <div class="col-auto d-flex gap-0 mb-4">
                <?php include '../src/components/tables/menuTable.php'; ?>
            </div>
        </div>
    </div>
    <div class="border border-1 border-light rounded-2 overflow-hidden px-0">
        <?php include '../src/components/tables/dataTable.php'; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        seleccionarAvisosCliente('');
    });
</script>