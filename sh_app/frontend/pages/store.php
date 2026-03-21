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
        ['src' => '../src/img/app/carousle_page1.png'],
        ['src' => '../src/img/app/carousle_page2.png'],
        ['src' => '../src/img/app/carousle_page3.png'],
    ]
?>

<div class="row container-sm my-3 mx-auto" id="row-carousel">
    <div class="border border-2 rounded rounded-3 overflow-hidden p-0">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <!-- Indicadores dinámicos -->
            <div class="carousel-indicators">
                <?php foreach ($carousel as $index => $image) : ?>
                    <button type="button" data-bs-target="#carouselExampleIndicators" style="width: 16px; height: 16px;"
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

<div class="bg-clip-path-up-store-section"></div>
<div class="m-0 w-100 bg-store-section pb-3">
    <div class="m-auto row container-sm w-100 m-auto">
        <p class="fw-bold card-category-text-h m-0">Productos de SH</p>
        <p class="card-category-text-p m-0">
            Productos hechos de material reutilizable como cartón, latas, botellas plásticas, fon, entre otros. Y también hechos de una mezcla de tierra, agua y cemento para las artesanías. Hasta tenemos productos mixtos de material reutilizado y tierra.
            <br>
            Son productos muy resistentes, muy elaborados, muy bonitos y en especial muy coloridos. Tienen calidad y belleza nuestro trabajo.
        </p>
    </div>
</div>
<div class="bg-clip-path-down-store-section"></div>

<div id="contenedor-categorias" class="row my-3"></div>

<div class="bg-clip-path-up-store-section"></div>
<div class="m-0 w-100 bg-store-section pb-3 pt-5">
    <div class="row container-fluid mx-auto gap-1">
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">Lógica del negocio</p>
                <i class="bi bi-arrow-repeat fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Ustedes realizan pedidos de nuestros productos y nosotros se los hacemos desde cero.
            </p>
        </div>
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">Productos coloridos</p>
                <i class="bi bi-paint-bucket fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Muchos de nuestros productos tienen opciones de pedirlos en otras paletas de colores.
            </p>
        </div>
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">Duración en elaboración</p>
                <i class="bi bi-alarm fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Depende de la complejidad y tamaño del producto.
            </p>
        </div>
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">Pedir algo fuera de la tienda</p>
                <i class="bi bi-chat-left-dots fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Si, tenemos la opción de hacer pedidos externos que no están en la tienda, es más. incluso ya hecho uno de ellos queda registrado como producto oficial en la tienda. Solo tomar en cuenta el tiempo en el que se nos vamos a dedicar en tratar de hacerlo, ya que es un producto nuevo.
            </p>
        </div>
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">Método de pago</p>
                <i class="bi bi-cash fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Aceptamos efectivo o SINPE.
            </p>
        </div>
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <div class="d-flex align-items-center gap-2">
                <p class="fw-bold card-category-text-h m-0">Dejar comentarios</p>
                <i class="bi bi-chat-dots fs-4 d-flex align-self-center m-0"></i>
            </div>
            <p class="card-category-text-p">
                Ustedes pueden dejarnos comentarios en cualquier lado, tanto en nuestra página de Facebook como en cada uno de los productos de la tienda, incluso permite una calificación de estrellas.
            </p>
        </div>
    </div>
</div>
<div class="bg-clip-path-down-store-section mb-4"></div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        obtenerCartasCategorias('');
    });
</script>