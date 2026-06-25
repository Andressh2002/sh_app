<?php
    include '../src/components/login/access.php';

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $idProducto = isset($_GET['id']) ? $_GET['id'] : '';
    $nombreProducto = isset($_GET['nombreProducto']) ? $_GET['nombreProducto'] : '';
    $productId = isset($_GET['id']) ? $_GET['id'] : null;
    $idCliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : '';

    $pageTitle = $nombreProducto;

    $modalComentario = [
        'id' => 'modalGuardarComentario',
        'title' => 'Guardar comentario',
        'icon' => 'bi bi-chat-left-text-fill',
        'size' => 'modal-lg',
        'btn_close' => true,

        'form' => [

            [
                'label' => 'Comentario',
                'id' => 'Comentario',
                'input' => 'textarea',
                'icon' => 'bi bi-chat-left-text-fill',
                'placeholder' => 'Escribir comentario...',
                'help' => 'Escribe un comentario',
                'required' => 'Campo obligatorio',
            ],

            [
                'input' => 'rating',
                'id' => 'Calificacion',
                'label' => 'Calificación',
                'icon' => 'bi bi-star-half',
                'value' => 0,
                'help' => 'Selecciona una cantidad de estrellas',
            ]

        ],

        'buttons' => [

            [
                'text' => 'Cancelar',
                'icon' => 'bi bi-x-lg',
                'class' => 'store-btn-secondary',
                'dismiss' => true
            ],

            [
                'text' => 'Guardar comentario',
                'icon' => 'bi bi-floppy-fill',
                'class' => 'store-filter-btn',
                'onclick' => 'guardarComentario()'
                //'onclick' => "abrirModal('modalGuardando')"
            ]

        ]
    ];
    $modal = $modalComentario;
    include '../src/components/modal/modal.php';
?>
<div class="container-fluid py-4">
    <div class="container">

        <!-- ========================================= -->
        <!-- SKELETON GENERAL -->
        <!-- ========================================= -->

        <div id="producto-page-skeleton">
            <div class="row g-4">
                <div class="col-12 col-lg-5">
                    <div class="store-skeleton skeleton-image"></div>
                    <div
                        class="store-skeleton skeleton-image mt-4"
                    ></div>
                </div>
                <div class="col-12 col-lg-7">
                    <div class="store-skeleton skeleton-title mb-3"></div>
                    <div class="store-skeleton skeleton-text"></div>
                    <div class="store-skeleton skeleton-text"></div>
                    <div class="store-skeleton skeleton-text"></div>
                    <div
                        class="store-skeleton skeleton-button mt-4"
                    ></div>
                </div>
            </div>
        </div>

        <!-- ========================================= -->
        <!-- CONTENIDO REAL -->
        <!-- ========================================= -->

        <div id="producto-page-content" class="d-none">

            <!-- ========================================= -->
            <!-- PRODUCTO -->
            <!-- ========================================= -->

            <div class="row g-4 align-items-start">

                <!-- IMÁGENES -->
                <div class="col-12 col-lg-5">

                    <!-- Imagen principal -->
                    <div class="store-panel-shadow mb-4">
                        <div class="store-panel">
                            <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                                <i class="bi bi-image-fill"></i>
                                Producto
                            </div>
                            <div class="store-panel-body text-center">

                                <!-- Skeleton -->
                                <div
                                    id="skeleton-image-main"
                                    class="
                                        store-skeleton
                                        skeleton-image
                                    "
                                ></div>
                                <img
                                    id="product-color-image"
                                    class="
                                        product-page-img
                                        d-none
                                        w-100 h-auto
                                    "
                                    src=""
                                    alt=""
                                >

                                <!-- COLORES PRODUCTO -->
                                <div
                                    id="contenedor-colores"
                                    class="
                                        d-flex
                                        flex-wrap
                                        gap-3
                                        justify-content-center
                                        mt-4
                                    "
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Accesorio -->
                    <div
                        id="row-imagen-accesorio"
                        class="store-panel-shadow"
                    >
                        <div class="store-panel">
                            <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                                <i class="bi bi-gem"></i>
                                Accesorio
                            </div>
                            <div
                                class="
                                    store-panel-body
                                    text-center
                                "
                            >
                                <div
                                    id="skeleton-image-accesorio"
                                    class="
                                        store-skeleton
                                        skeleton-image
                                    "
                                ></div>
                                <img
                                    id="accesory-color-image"
                                    class="
                                        product-page-img
                                        d-none
                                        w-100 h-auto
                                    "
                                    src=""
                                    alt=""
                                >

                                <!-- COLORES ACCESORIO -->
                                <div
                                    id="contenedor-colores-accesorio"
                                    class="
                                        d-flex
                                        flex-wrap
                                        gap-3
                                        justify-content-center
                                        mt-4
                                    "
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFORMACIÓN -->
                <div class="col-12 col-lg-7">
                    <div class="d-flex flex-column gap-4">

                        <!-- DATOS -->
                        <div class="store-panel-shadow">
                            <div class="store-panel">
                                <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                                    <i class="bi bi-box-fill"></i>
                                    Información del producto
                                </div>
                                <div class="store-panel-body">

                                    <!-- Skeleton -->
                                    <div id="skeleton-info">
                                        <div class="store-skeleton skeleton-title"></div>
                                        <div class="store-skeleton skeleton-text"></div>
                                        <div class="store-skeleton skeleton-text"></div>
                                        <div class="store-skeleton skeleton-text"></div>
                                    </div>

                                    <!-- Contenido -->
                                    <div id="product-info-content" class="d-none">
                                        <h1 id="nombreProducto" class="mb-3 fw-bold"></h1>
                                        <div class="product-meta">
                                            <p
                                                id="nombreCategoria"
                                            ></p>
                                            <div
                                                id="nombrePrecio"
                                            ></div>
                                            <p
                                                id="descuento"
                                            ></p>
                                            <p
                                                id="tiempoDescuento"
                                            ></p>
                                            <p
                                                id="disponibilidad"
                                            ></p>
                                        </div>
                                        <div
                                            id="estrellas"
                                            class="
                                                text-star
                                                mt-3
                                            "
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADVERTENCIAS -->
                        <div
                            id="row-advertencias"
                            class="store-panel-shadow"
                        >
                            <div class="store-panel">
                                <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                                    <i
                                        class="
                                            bi
                                            bi-exclamation-triangle-fill
                                        "
                                    ></i>
                                    Advertencias
                                </div>
                                <div class="store-panel-body">
                                    <div
                                        id="advertenciasProducto"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- CANTIDAD -->
                        <div class="store-panel-shadow">
                            <div class="store-panel">
                                <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                                    <i class="bi bi-cart-fill"></i>
                                    Pedido
                                </div>
                                <div class="store-panel-body">
                                    <div class="mb-3">
                                        <label class="mb-2">
                                            Cantidad
                                        </label>
                                        <input
                                            type="number"
                                            id="cantidad"
                                            class="filter-input w-auto rounded-0"
                                            value="1"
                                            min="1"
                                            max="100"
                                        >
                                    </div>
                                    <h3
                                        id="labelTotal"
                                        class="product-price"
                                    ></h3>
                                    <div id="fichas-section" class="text-center d-flex gap-1 align-items-center d-none">
                                        <p class="mb-0 fw-bolder" id="text-fichas">Recompensa: ...</p>
                                        <img class="" src="../src/img/app/SH_Ficha.png" alt="sh" style="height: 32px;">
                                    </div>
                                    <div
                                        class="
                                            navbar-btn-shadow
                                            mt-4
                                        "
                                    >
                                        <button
                                            id="btnAccionProducto"
                                            class="
                                                store-filter-btn
                                                slide_from_left
                                            "
                                        >
                                            ...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================= -->
            <!-- DESCRIPCIÓN -->
            <!-- ========================================= -->

            <div class="store-panel-shadow mt-4">
                <div class="store-panel">
                    <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                        <i class="bi bi-card-text"></i>
                        Descripción
                    </div>
                    <div class="store-panel-body">
                        <div
                            id="descripcionProducto"
                            class="lh-lg"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- ========================================= -->
            <!-- CARACTERÍSTICAS -->
            <!-- ========================================= -->

            <div class="store-panel-shadow mt-4">
                <div class="store-panel">
                    <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                        <i class="bi bi-rulers"></i>
                        Características
                    </div>
                    <div class="store-panel-body">
                        <div class="row g-4 text-center">
                            <div class="col-md-4">
                                <h5>Altura</h5>
                                <p id="alturaProducto"></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Peso</h5>
                                <p id="pesoProducto"></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Tiempo</h5>
                                <p id="tiempoProducto"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================= -->
            <!-- GALERÍA -->
            <!-- ========================================= -->

            <div class="store-panel-shadow mt-4">
                <div class="store-panel">
                    <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                        <i class="bi bi-image-fill"></i>
                        Imágen de galería
                    </div>
                    <div class="store-panel-body text-center">

                        <!-- Skeleton -->
                        <div
                            id="skeleton-galeria"
                            class="
                                store-skeleton
                                skeleton-image
                            "
                        ></div>
                        <img
                            id="imagenGaleria"
                            class="
                                product-page-img
                                d-none
                                w-100 h-auto
                            "
                            src=""
                            alt=""
                        >
                    </div>
                </div>
            </div>

            <!-- ========================================= -->
            <!-- COMENTARIOS -->
            <!-- ========================================= -->

            <div class="store-panel-shadow mt-4">
                <div class="store-panel">
                    <div class="store-panel-header px-3 px-sm-3 px-md-4 px-lg-4 px-xl-5">
                        <i
                            class="
                                bi
                                bi-chat-left-text-fill
                            "
                        ></i>

                        Comentarios
                    </div>
                    <div class="store-panel-body">

                        <!-- BOTÓN PARA COMENTAR -->
                        <div class="row px-0 mx-0" id="btn-comentar">
                            <div class="col-12 px-0">
                                <div class="navbar-btn-shadow my-4">
                                    <button
                                        class="store-filter-btn slide_from_left"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalGuardarComentario"
                                    >
                                        <i class="bi bi-chat-left-text"></i>
                                        <span>Comentar</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- LISTA DE COMENTARIOS -->
                        <div
                            id="container-comentaries" class="overflow-y-scroll" style="max-height: 312px"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Elementos hidden -->
<input type="hidden" id="Id" value="<?php echo isset($productId) ? $productId : ''; ?>"> 
<input type="hidden" id="Color" value=""> 
<input type="hidden" id="AccesoryColor" value=""> 
<input type="hidden" id="NumColor" value=""> 
<input type="hidden" id="NumAccesoryColor" value=""> 
<input type="hidden" id="Sesion" value="<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : '' ?>"> 
<input type="hidden" id="Precio" value=""> 
<input type="hidden" id="Fichas" value=""> 
<input type="hidden" id="Total" value=""> 

<input type="hidden" id="FichasCliente" value="">
<input type="hidden" id="FichasRecompensa" value="">
<input type="hidden" id="PrecioBase" value="">

<input type="hidden" id="FichasUsadas">
<input type="hidden" id="FichasGanadas">

<!-- Productos relacionados -->
<?php 
    $title = [
        'title' => 'Productos relacionados',
        'icon' => 'bi bi-search',
    ];
    include '../src/components/titles/titleStore.php';
?>
<div id="contenedor-productos" class="row my-3 mx-0 px-0">
    <?php 
        include '../src/components/loading/loading.php';
    ?>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        buscarCartaProducto(<?php echo json_encode($idProducto); ?>, <?php echo json_encode($idCliente); ?>, <?php echo json_encode($_SESSION['usuario_rol'] ?? ''); ?>);

        guardarInteraccion({
            usuario: <?php echo json_encode($_SESSION['usuario_id'] ?? ''); ?>,
            accion: `Ver el producto ${"<?php echo $pageTitle; ?>"}`,
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.rating-stars').forEach(container => {
            const hiddenInput = document.getElementById(
                container.dataset.target
            );
            const stars = container.querySelectorAll('.rating-star');

            // Pintar estado inicial
            paintStars(stars, parseInt(hiddenInput.value || 0));
            stars.forEach(star => {

                // Hover
                star.addEventListener('mouseenter', function () {
                    const value = parseInt(this.dataset.value);
                    paintStars(stars, value);
                });

                // Click
                star.addEventListener('click', function () {
                    const value = parseInt(this.dataset.value);
                    hiddenInput.value = value;
                    paintStars(stars, value);

                    // Ejecutar onchange si existe
                    const onchange = hiddenInput.getAttribute('data-onchange');
                    if (onchange) {
                        eval(onchange);
                    }
                });
            });

            // Restaurar valor real
            container.addEventListener('mouseleave', function () {
                paintStars(
                    stars,
                    parseInt(hiddenInput.value || 0)
                );
            });
        });
    });

    function paintStars(stars, value) {
        stars.forEach(star => {
            const current = parseInt(star.dataset.value);
            if (current <= value) {
                star.classList.remove('bi-star');
                star.classList.add('bi-star-fill');
            } else {
                star.classList.remove('bi-star-fill');
                star.classList.add('bi-star');
            }
        });
    }
</script>