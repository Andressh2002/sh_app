<?php
    include '../src/components/login/access.php';
    //checkAccess('Cliente');
    $pageTitle = "Productos";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $nombreCategoria = isset($_GET['nombreCategoria']) ? $_GET['nombreCategoria'] : '';
    $idCategoria = isset($_GET['idCategoria']) ? $_GET['idCategoria'] : '';
    $nombreProducto = isset($_GET['nombreProducto']) ? $_GET['nombreProducto'] : '';
    $nombreCategoria = $nombreCategoria ?? '';

    $listFilters = [
        [
            'label' => 'Nombre',
            'id' => 'nombre',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Precio',
            'idInicio' => 'precioInicial',
            'idFin' => 'precioFinal',
            'input' => 'rangePrice',
            'onchangeInicio' => '',
            'onchangeFin' => '',
        ],
        [
            'label' => 'Categorías',
            'id' => 'lista-categorias-filtros',
            'input' => 'listCheckbox',
        ],
        /*
        [
            'label' => 'Festividades',
            'id' => 'lista-festividades-filtros',
            'input' => 'listCheckbox',
        ], 
        */
        [
            'label' => 'Rarezas',
            'id' => 'lista-rarezas-filtros',
            'input' => 'listCheckbox',
        ], 
        [
            'label' => 'Universos',
            'id' => 'lista-universos-filtros',
            'input' => 'listCheckbox',
        ], 
    ]
?>

<div class="container-fluid row">
    <div class="col-auto py-3 filterBar-responsive" id="filterBar">
        <div class="card position-sticky sticky-top border-0 filterbar-font-size" style="top: 0px;">
            <div class="card-header filterbar-header-bg border-0">Filtros de búsqueda</div>
            <div class="card-body filterbar-body-bg d-flex flex-column gap-3 border-0 rounded-bottom-1 overflow-auto" id="filterBar-container">
                <?php
                    foreach ($listFilters as $input) {
                        include '../src/components/inputs/filters.php';
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="col">
        <!-- Productos destacados -->
        <div class="m-0 p-0" id="col-productos-destacados">
            <div class="bg-clip-path-up-store-section mt-3"></div>
            <div class="m-0 w-100 bg-store-section pb-4">
                <div class="m-auto row container-fluid w-100 m-auto">
                    <p class="fw-bold card-category-text-h m-0">Productos destacados</p>
                </div>
            </div>
            <div class="bg-clip-path-down-store-section"></div>

            <div id="contenedor-productos-destacados" class="row p-0 m-0">
                <div class="spinner-border spinner-color custom-spinner m-auto" role="status" style="width: 50px; height: 50px;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Productos de la tienda -->
        <div class="m-0 p-0" id="col-productos-ordinarios">
            <div class="bg-clip-path-up-store-section mt-3"></div>
            <div class="m-0 w-100 bg-store-section pb-4">
                <div class="m-auto row container-fluid w-100 m-auto">
                    <p class="fw-bold card-category-text-h m-0">Productos de la tienda</p>
                </div>
            </div>
            <div class="bg-clip-path-down-store-section"></div>
        </div>
        <div id="contenedor-productos" class="row p-0 m-0">
            <div class="spinner-border spinner-color custom-spinner m-auto" role="status" style="width: 50px; height: 50px;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div class="bg-clip-path-up-store-section"></div>
    <div class="m-0 w-100 bg-store-section pb-3 pt-5">
        <div class="row container-fluid mx-auto gap-1">
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <div class="d-flex align-items-center gap-2">
                    <p class="fw-bold card-category-text-h m-0">¡Siempre con más productos!</p>
                    <i class="bi bi-tools fs-4 d-flex align-self-center m-0"></i>
                </div>
                <p class="card-category-text-p">
                    Siempre nos encargamos de hacer nuevos productos para que puedan pedirlos en la tienda. Siempre notificamos la llegada de los nuevos productos u otras novedades.
                </p>
            </div>
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                <div class="d-flex align-items-center gap-2">
                    <p class="fw-bold card-category-text-h m-0">¿Cuándo llegan?</p>
                    <i class="bi bi-alarm fs-4 d-flex align-self-center m-0"></i>
                </div>
                <p class="card-category-text-p">
                    No tenemos fechas definidas para la llegada de los nuevos productos, sin embargo, siempre tratamos de hacerlos lo más pronto posible.
                </p>
            </div>
        </div>
    </div>
    <div class="bg-clip-path-down-store-section mb-4"></div>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nombreProducto = <?php echo json_encode($nombreProducto); ?>;
        const nombreCategoria = <?php echo json_encode($nombreCategoria); ?>;
        const cartaProductosFiltrosDefecto = {
            nombre: nombreProducto,
            categorias: <?php echo $idCategoria ? json_encode([$idCategoria]) : json_encode([]); ?>,
            precio: [],
            festividades: [],
            rarezas: [],
            universos: [],
        };
        obtenerCartasProductos(cartaProductosFiltrosDefecto);
        obtenerListaFiltros('lista-categorias-filtros', 'categorias', nombreCategoria);
        //obtenerListaFiltros('lista-festividades-filtros', 'festividades');
        obtenerListaFiltros('lista-rarezas-filtros', 'rarezas');
        obtenerListaFiltros('lista-universos-filtros', 'universos');
    });

    try {
        $(document).on('change', '.select-all', function () {
            const isChecked = $(this).is(':checked');
            const parentList = $(this).closest('ul'); // Encuentra la lista que contiene los checkboxes
            parentList.find('input[type="checkbox"]').not(this).prop('checked', isChecked);
        });

        $(document).on('change', 'ul input[type="checkbox"]:not(.select-all)', function () {
            const parentList = $(this).closest('ul'); // Encuentra la lista que contiene los checkboxes
            const allCheckboxes = parentList.find('input[type="checkbox"]:not(.select-all)');
            const allChecked = allCheckboxes.length === allCheckboxes.filter(':checked').length;
            parentList.find('.select-all').prop('checked', allChecked);
        });
    } catch (error) {
        //
    }
</script>