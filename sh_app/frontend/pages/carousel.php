<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Carrusel";
    $pageIcon = 'bi-tools';
    $type = 'carrusel';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCarousel = false;

    $inputs = [
        [
            'label' => 'Título del carrusel',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ],
        [
            'label' => 'Nombre de la festividad',
            'id' => 'Titulo',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => '',
            'input' => 'select',
            'options' => [
                'nombre' => 'Titulo',
                'festividad' => 'Festividad',
                'fecha_registro' => 'Fecha de creación'
            ],
            'onchange' => 'aplicarFiltrosCarrusel()',
        ]
    ];
    $menuTable = [
        'url' => 'addCarousel.php',
        'updateMethod' => 'seleccionarCarruseles('.''.')',
        'clearMethod' => 'limpiarFiltrosCarrusel()',
        'showAdd' => true,
        'showUpdate' => true,
    ];
    $headers = ['#', 'Imagen', 'Título', 'Texto', 'Festividad', 'Opciones'];
?>

<div class="w-100 p-3 rounded-3" style="background-color: #f9fafb;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="mb-0">Lista de cartas del carrusel de la aplicación</h4>
        <i class="bi bi-search fs-4 d-flex align-self-center"></i>
    </div>
    <p class="card-text text-secondary">Aquí puedes ver la lista de cartas del carrusel de la aplicación.</p>
    <p class="card-text text-secondary">El carrusel es un deslizador de imagenes con texto que viene en la página principal de la aplicación desde el cliente, del carrusel, los clientes pueden ver notificaciones rápidas sobre eventos especiales que quieras organizar en la tienda.</p>
    <p class="card-text text-secondary">Las cartas que agregues no necesariamente deben estar ligadas a una festividad, solo toma en cuenta que si le asignas una festividad, esta tarjeta aparecerá en los clientes de forma limitada.</p>
    
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
    <div class="border border-1 border-light rounded-2 overflow-hidden">
        <?php include '../src/components/tables/dataTable.php'; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        seleccionarCarruseles('', '');
    });
</script><?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Carrusel";
    $pageIcon = 'bi-tools';
    $type = 'carrusel';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCarousel = false;

    $inputs = [
        [
            'label' => 'Título del carrusel',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ],
        [
            'label' => 'Nombre de la festividad',
            'id' => 'Titulo',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => '',
            'input' => 'select',
            'options' => [
                'nombre' => 'Titulo',
                'festividad' => 'Festividad',
                'fecha_registro' => 'Fecha de creación'
            ],
            'onchange' => 'aplicarFiltrosCarrusel()',
        ]
    ];
    $menuTable = [
        'url' => 'addCarousel.php',
        'updateMethod' => 'seleccionarCarruseles('.''.')',
        'clearMethod' => 'limpiarFiltrosCarrusel()',
        'showAdd' => true,
        'showUpdate' => true,
    ];
    $headers = ['#', 'Imagen', 'Título', 'Texto', 'Festividad', 'Opciones'];
?>

<div class="w-100 p-3 rounded-3" style="background-color: #f9fafb;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="mb-0">Lista de cartas del carrusel de la aplicación</h4>
        <i class="bi bi-search fs-4 d-flex align-self-center"></i>
    </div>
    <p class="card-text text-secondary">Aquí puedes ver la lista de cartas del carrusel de la aplicación.</p>
    <p class="card-text text-secondary">El carrusel es un deslizador de imagenes con texto que viene en la página principal de la aplicación desde el cliente, del carrusel, los clientes pueden ver notificaciones rápidas sobre eventos especiales que quieras organizar en la tienda.</p>
    <p class="card-text text-secondary">Las cartas que agregues no necesariamente deben estar ligadas a una festividad, solo toma en cuenta que si le asignas una festividad, esta tarjeta aparecerá en los clientes de forma limitada.</p>
    
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
    <div class="border border-1 border-light rounded-2 overflow-hidden">
        <?php include '../src/components/tables/dataTable.php'; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        seleccionarCarruseles('', '');
    });
</script><?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Carrusel";
    $pageIcon = 'bi-tools';
    $type = 'carrusel';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCarousel = false;

    $inputs = [
        [
            'label' => 'Título del carrusel',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ],
        [
            'label' => 'Nombre de la festividad',
            'id' => 'Titulo',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => '',
            'input' => 'select',
            'options' => [
                'nombre' => 'Titulo',
                'festividad' => 'Festividad',
                'fecha_registro' => 'Fecha de creación'
            ],
            'onchange' => 'aplicarFiltrosCarrusel()',
        ]
    ];
    $menuTable = [
        'url' => 'addCarousel.php',
        'updateMethod' => 'seleccionarCarruseles('.''.')',
        'clearMethod' => 'limpiarFiltrosCarrusel()',
        'showAdd' => true,
        'showUpdate' => true,
    ];
    $headers = ['#', 'Imagen', 'Título', 'Texto', 'Festividad', 'Opciones'];
?>

<div class="w-100 p-3 rounded-3" style="background-color: #f9fafb;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="mb-0">Lista de cartas del carrusel de la aplicación</h4>
        <i class="bi bi-search fs-4 d-flex align-self-center"></i>
    </div>
    <p class="card-text text-secondary">Aquí puedes ver la lista de cartas del carrusel de la aplicación.</p>
    <p class="card-text text-secondary">El carrusel es un deslizador de imagenes con texto que viene en la página principal de la aplicación desde el cliente, del carrusel, los clientes pueden ver notificaciones rápidas sobre eventos especiales que quieras organizar en la tienda.</p>
    <p class="card-text text-secondary">Las cartas que agregues no necesariamente deben estar ligadas a una festividad, solo toma en cuenta que si le asignas una festividad, esta tarjeta aparecerá en los clientes de forma limitada.</p>
    
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
    <div class="border border-1 border-light rounded-2 overflow-hidden">
        <?php include '../src/components/tables/dataTable.php'; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        seleccionarCarruseles('', '');
    });
</script><?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Carrusel";
    $pageIcon = 'bi-tools';
    $type = 'carrusel';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateCarousel = false;

    $inputs = [
        [
            'label' => 'Título del carrusel',
            'id' => 'Titulo',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ],
        [
            'label' => 'Nombre de la festividad',
            'id' => 'Titulo',
            'icon' => 'bi bi-calendar-fill',
            'input' => 'text',
            'onchange' => 'currentPage = 1; aplicarFiltrosCarrusel()',
        ]
    ];
    $orders = [
        [
            'label' => 'Ordenar por:',
            'id' => 'Ordenar_por',
            'icon' => '',
            'input' => 'select',
            'options' => [
                'nombre' => 'Titulo',
                'festividad' => 'Festividad',
                'fecha_registro' => 'Fecha de creación'
            ],
            'onchange' => 'aplicarFiltrosCarrusel()',
        ]
    ];
    $menuTable = [
        'url' => 'addCarousel.php',
        'updateMethod' => 'seleccionarCarruseles('.''.')',
        'clearMethod' => 'limpiarFiltrosCarrusel()',
        'showAdd' => true,
        'showUpdate' => true,
    ];
    $headers = ['#', 'Imagen', 'Título', 'Texto', 'Festividad', 'Opciones'];
?>

<div class="w-100 p-3 rounded-3" style="background-color: #f9fafb;">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="mb-0">Lista de cartas del carrusel de la aplicación</h4>
        <i class="bi bi-search fs-4 d-flex align-self-center"></i>
    </div>
    <p class="card-text text-secondary">Aquí puedes ver la lista de cartas del carrusel de la aplicación.</p>
    <p class="card-text text-secondary">El carrusel es un deslizador de imagenes con texto que viene en la página principal de la aplicación desde el cliente, del carrusel, los clientes pueden ver notificaciones rápidas sobre eventos especiales que quieras organizar en la tienda.</p>
    <p class="card-text text-secondary">Las cartas que agregues no necesariamente deben estar ligadas a una festividad, solo toma en cuenta que si le asignas una festividad, esta tarjeta aparecerá en los clientes de forma limitada.</p>
    
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
    <div class="border border-1 border-light rounded-2 overflow-hidden">
        <?php include '../src/components/tables/dataTable.php'; ?>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        seleccionarCarruseles('', '');
    });
</script>