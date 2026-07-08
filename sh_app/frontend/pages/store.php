<?php
    include '../src/components/login/access.php';
    //checkAccess('Cliente');
    $pageTitle = "Inicio";

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;
?>

<?php 
    $title = [
        'title' => !isset($_SESSION['usuario_id']) ? 'Bienvenido a SH APP' : '¡Hola ' . $_SESSION['usuario_nombre'] . '!',
        'icon' => 'bi bi-shop',
        'text' => 'Aquí podrás ver nuestros productos. Ofrecemos una amplia variedad de ellos.'
    ];
    include '../src/components/titles/titleStore.php';
?>

<div class="row container-sm my-3 mx-auto" id="row-carousel">
    <div class="carousel-wrapper p-0">
        <div class="carousel-clip overflow-hidden">

            <div
                id="carouselExampleIndicators"
                class="carousel slide"
                data-bs-ride="carousel">

                <div
                    class="carousel-indicators mb-3 mb-lg-4 mb-xl-5"
                    id="carousel-indicators">
                </div>

                <div
                    class="carousel-inner"
                    id="carousel-inner">
                </div>

                <button
                    class="carousel-control-prev"
                    type="button"
                    data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="prev">

                    <span class="carousel-control-prev-icon"></span>

                </button>

                <button
                    class="carousel-control-next"
                    type="button"
                    data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="next">

                    <span class="carousel-control-next-icon"></span>

                </button>

            </div>

        </div>
    </div>
</div>

<div id="contenedor-productos" class="row my-3 mx-0 px-0">
    <?php 
        include '../src/components/loading/loading.php';
    ?>
</div>


<?php 
    $title = [
        'title' => 'Algunos de nuestros tipos de productos',
        'icon' => 'bi bi-tools',
        'text' => 'No solo hay figuras coleccionables, tenemos una gran varidedad en nuestros productos, tanto de decoración como de utilidad.'
    ];
    include '../src/components/titles/titleStore.php';
?>

<div id="contenedor-categorias" class="row my-3 mx-0 px-0">
    <?php 
        include '../src/components/loading/loading.php';
    ?>
</div>

<?php 
    $title = [
        'title' => 'Productos de varios universos',
        'icon' => 'bi bi-flag-fill',
        'text' => 'En la tienda tenemos productos hechos y basados en varios universos. Ideales si eres fan de alguno de ellos.'
    ];
    include '../src/components/titles/titleStore.php';
?>

<div id="contenedor-universos" class="row my-3 mx-0 px-0">
    <?php 
        include '../src/components/loading/loading.php';
    ?>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        obtenerCarrusel()

        const cartaProductosFiltrosDefecto = {
            nombre: '',
            categorias: [],
            precio: [],
            festividades: [],
            rarezas: [],
            universos: [],
        };
        const random = {
            limite: 10,
        }
        await obtenerCartasProductos(cartaProductosFiltrosDefecto, random);
        await obtenerCartasCategorias('');
        await obtenerCartasUniversos('');

        guardarInteraccion({
            usuario: <?php echo json_encode($_SESSION['usuario_id'] ?? ''); ?>,
            accion: `Ir a la página de ${"<?php echo $pageTitle; ?>"}`,
        });
    });
</script>