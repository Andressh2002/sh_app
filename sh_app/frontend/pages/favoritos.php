<?php
    include '../src/components/login/access.php';
    //checkAccess('Cliente');
    $pageTitle = "Tienda";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $nombreCategoria = isset($_GET['nombreCategoria']) ? $_GET['nombreCategoria'] : '';
    $nombreUniverso = isset($_GET['nombreUniverso']) ? $_GET['nombreUniverso'] : '';
    $idCategoria = isset($_GET['idCategoria']) ? $_GET['idCategoria'] : '';
    $idUniverso = isset($_GET['idUniverso']) ? $_GET['idUniverso'] : '';
    $nombreProducto = isset($_GET['nombreProducto']) ? $_GET['nombreProducto'] : '';
    $nombreCategoria = $nombreCategoria ?? '';
    $nombreUniverso = $nombreUniverso ?? '';

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

<div class="container-fluid px-0 mx-0">

    <!-- FILA PRODUCTOS -->
    <div class="row px-0 mx-0">

        <div class="col-12 px-0 mx-0">

            <!-- Productos destacados -->
            <div class="m-0 p-0" id="col-productos-destacados">

                <?php 
                    $title = [
                        'title' => 'Favoritos destacados',
                        'icon' => 'bi bi-lightbulb',
                    ];

                    include '../src/components/titles/titleStore.php';
                ?>

                <div
                    id="contenedor-productos-destacados"
                    class="row my-3 mx-0 px-0"
                >
                    <?php 
                        include '../src/components/loading/loading.php';
                    ?>
                </div>

            </div>

            <!-- Productos -->
            <div class="m-0 p-0" id="col-productos-ordinarios">

                <?php 
                    $title = [
                        'title' => 'Favoritos',
                        'icon' => 'bi bi-brush',
                    ];

                    include '../src/components/titles/titleStore.php';
                ?>

                <div
                    id="contenedor-productos"
                    class="row my-3 mx-0 px-0"
                >
                    <?php 
                        include '../src/components/loading/loading.php';
                    ?>
                </div>

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
        const nombreProducto = <?php echo json_encode($nombreProducto); ?>;
        const nombreCategoria = <?php echo json_encode($nombreCategoria); ?>;
        const nombreUniverso = <?php echo json_encode($nombreUniverso); ?>;
        const cartaProductosFiltrosDefecto = {
            nombre: nombreProducto,
            categorias: <?php echo $idCategoria ? json_encode([$idCategoria]) : json_encode([]); ?>,
            precio: [],
            festividades: [],
            rarezas: [],
            universos: <?php echo $idUniverso ? json_encode([$idUniverso]) : json_encode([]); ?>,
            idCliente: <?php echo json_encode($_SESSION['usuario_id'] ?? ''); ?>,
            modo: 'favoritos',
        };
        obtenerCartasProductos(cartaProductosFiltrosDefecto);
        obtenerListaFiltros('lista-categorias-filtros', 'categorias', nombreCategoria);
        //obtenerListaFiltros('lista-festividades-filtros', 'festividades');
        obtenerListaFiltros('lista-rarezas-filtros', 'rarezas');
        obtenerListaFiltros('lista-universos-filtros', 'universos', nombreUniverso);

        guardarInteraccion({
            usuario: <?php echo json_encode($_SESSION['usuario_id'] ?? ''); ?>,
            accion: `Ir a la página de ${"<?php echo $pageTitle; ?>"}`,
        });
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