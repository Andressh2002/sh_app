<?php
    include '../src/components/login/access.php';
    //checkAccess('Cliente');
    $pageTitle = "Inicio";

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $carousel = [
        ['src' => '../src/img/app/carrusel_pagina1.png'],
        ['src' => '../src/img/app/carrusel_pagina2.png'],
        ['src' => '../src/img/app/carrusel_pagina3.png'],
        ['src' => '../src/img/app/carrusel_pagina4.png'],
    ]
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
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicadores dinámicos -->
                <div class="carousel-indicators mb-3 mb-lg-4 mb-xl-5">
                    <?php foreach ($carousel as $index => $image) : ?>
                        <button type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide-to="<?= $index ?>" 
                                class="<?= $index === 0 ? 'active' : '' ?>" 
                                aria-current="<?= $index === 0 ? 'true' : 'false' ?>" 
                                aria-label="Slide <?= $index + 1 ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Imágenes del carrusel -->
                <div class="carousel-inner">
                    <?php foreach ($carousel as $index => $image) : ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= $image['src'] ?>" class="d-block w-100" alt="Slide <?= $index + 1 ?>" style="width: 100%; height: auto">
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controles del carrusel -->
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
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