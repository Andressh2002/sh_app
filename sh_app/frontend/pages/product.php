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
?>
<div class="container-fluid p-0 m-0">
    <div class="w-100 container" id="producto-informacion">
        <div class="row my-3 align-items-center gap-3 mx-auto">
            <div class="col px-3 container" style="max-width: 512px;">
                <!-- Producto -->
                <div class="row my-2">
                    <div class="rounded-2 overflow-hidden preview-product-card-bg preview-product-card-border">
                        <div class="p-4">
                            <div class="d-flex justify-content-center align-items-center">
                                <?php 
                                    $loadingIcon = [
                                        'id' => 'spinner-imagen-portada',
                                    ];
                                    include '../src/components/loading/loading.php';
                                ?>
                                <img class="w-auto h-100 product-page-img overflow-hidden" src="" alt="[Imagen del producto]" id="product-color-image">
                                <canvas id="canvas" style="display: none;"></canvas>
                                <input type="hidden" class="m-0 p-0" id="idProducto" value="" />
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Accesorio -->
                <div class="row my-2" id="row-imagen-accesorio">
                    <div class="rounded-2 overflow-hidden preview-product-card-bg preview-product-card-border">
                        <div class="p-4">
                            <div class="d-flex justify-content-center align-items-center">
                                <?php 
                                    $loadingIcon = [
                                        'id' => 'spinner-imagen-accesorio',
                                    ];
                                    include '../src/components/loading/loading.php';
                                ?>
                                <img class="w-auto h-100 product-page-img" src="" alt="[Imagen del accesorio]" id="accesory-color-image">
                                <canvas id="canvas" style="display: none;"></canvas>
                                <input type="hidden" class="m-0 p-0" id="idAccesorio" value="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del producto -->
            <div class="col px-3">
                <div class="d-flex flex-column gap-3">
                    <div class="rounded-2 overflow-hidden preview-product-card-border">
                        <div class="preview-product-card-bg p-0 m-0">
                            <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                                <h4 class="card-title" id="nombreProducto">
                                    <?php 
                                        $loadingIcon = [
                                            'width' => '20px',
                                            'height' => '20px'
                                        ];
                                        include '../src/components/loading/loading.php';
                                    ?>
                                </h4>
                            </div>
                            <div class="px-2 pb-2">
                                <p class="card-text" id="nombreCategoria"></p>
                                <p class="card-text" id="nombrePrecio"></p>
                                <p class="card-text" id="descuento"></p>
                                <p class="card-text" id="tiempoDescuento"></p>
                                <p class="card-text" id="disponibilidad"></p>
                                <div class="m-auto text-star" id="estrellas"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Colores -->
                    <div class="rounded-2 overflow-hidden preview-product-card-border">
                        <div class="preview-product-card-bg p-0 m-0">
                            <div class="preview-product-card-bg-header w-100 px-2 py-1 mb-2">
                                <p class="card-text">Colores del producto</p>
                            </div>
                            <div id="contenedor-colores" class="d-flex flex-wrap gap-2 py-2 justify-content-center align-items-center">
                                <?php 
                                    $loadingIcon = [
                                        'width' => '32px',
                                        'height' => '32px'
                                    ];
                                    include '../src/components/loading/loading.php';
                                ?>
                            </div>
                        </div>
                        <div class="p-0 m-0" id="isImageEdited"></div>
                    </div>

                    <!-- Colores del accesorio -->
                    <div class="rounded-2 overflow-hidden preview-product-card-border" id="row-colores-accesorio">
                        <div class="preview-product-card-bg p-0 m-0">
                            <div class="preview-product-card-bg-header w-100 px-2 py-1 mb-2">
                                <p class="card-text">Colores del accesorio</p>
                            </div>
                            <div id="contenedor-colores-accesorio" class="d-flex flex-wrap gap-2 py-2 justify-content-center align-items-center">
                                <?php 
                                    $loadingIcon = [
                                        'width' => '32px',
                                        'height' => '32px'
                                    ];
                                    include '../src/components/loading/loading.php';
                                ?>
                            </div>
                        </div>
                        <div class="p-0 m-0" id="isAccesoryImageEdited"></div>
                    </div>

                    <!-- Observación -->
                    <div class="rounded-2 overflow-hidden preview-product-card-border">
                        <div class="preview-product-card-bg p-0 m-0">
                            <div class="preview-product-card-bg-header w-100 px-2 py-1 mb-2">
                                <p class="card-text">Observación</p>
                            </div>
                            <div class="px-2 pb-2">
                                Las imagenes de las paletas son editadas, con el propósito de mostrar el color aproximado que tendrá el producto con la apariencia seleccionada.
                            </div>
                        </div>
                    </div>

                    <!-- Datos modificables -->
                    <div class="rounded-2 overflow-hidden preview-product-card-border">
                        <div class="preview-product-card-bg p-0 m-0 overflow-hidden">
                            <p class="card-text d-flex align-items-center px-2 pt-2">
                                <span class="me-2">Cantidad:</span>
                                <input type="number" id="cantidad" value="1" min="1" max="100" class="form-control w-auto">
                            </p>
                            <div class="preview-product-card-bg-footer w-100 px-2 py-1 overflow-hidden">
                                <p class="card-text p-0 m-0 fw-bolder" id="labelTotal"></p>
                            </div>
                            <input type="hidden" class="m-0 p-0" id="total" />
                            <input type="hidden" class="m-0 p-0" id="precio" value="" />
                        </div>
                    </div>

                    <!-- Botón de guardar/pedir -->
                    <div class="align-items-center">
                        <button 
                            id="btnAccionProducto"
                            onclick="" 
                            type="button" 
                            class="btn-details text-white border-0 rounded-2 px-4 py-2 d-flex align-items-center"
                        >
                            ...
                            <i class="bi bi-cart-fill ms-2 d-flex align-self-center"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción del producto -->
        <div class="row my-3 align-items-center">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0 overflow-hidden">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Descripción</h4>
                        </div>
                        <p class="card-text px-2 pb-2" id="descripcionProducto">
                            <?php 
                                $loadingIcon = [
                                    'id' => 'spinner-descripcion',
                                    'width' => '32px',
                                    'height' => '32px'
                                ];
                                include '../src/components/loading/loading.php';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Advertencias -->
        <div class="row my-3 align-items-center" id="row-advertencias">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0 overflow-hidden">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Advertencias</h4>
                        </div>
                        <p class="card-text px-2 pb-2" id="advertenciasProducto">
                            <?php 
                                $loadingIcon = [
                                    'id' => 'spinner-advertencias',
                                    'width' => '32px',
                                    'height' => '32px'
                                ];
                                include '../src/components/loading/loading.php';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos Características del producto -->
        <div class="row my-3 align-items-center">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0 overflow-hidden">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Características</h4>
                        </div>
                        <div class="d-flex flex-wrap gap-2 px-0 pb-2">
                            <div class="d-flex flex-column align-items-center mx-2">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label class="form-label m-0">Altura</label>
                                    <i class="bi bi-rulers d-flex align-self-center"></i>
                                </div>
                                <p class="form-label m-0" id="alturaProducto">
                                    <?php 
                                        $loadingIcon = [
                                            'id' => 'spinner-altura',
                                            'width' => '24px',
                                            'height' => '24px'
                                        ];
                                        include '../src/components/loading/loading.php';
                                    ?>
                                </p>
                            </div>
                            <div class="d-flex flex-column align-items-center mx-2">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label class="form-label m-0">Peso</label>
                                    <i class="bi bi-hammer d-flex align-self-center"></i>
                                </div>
                                <p class="form-label m-0" id="pesoProducto">
                                    <?php 
                                        $loadingIcon = [
                                            'id' => 'spinner-peso',
                                            'width' => '24px',
                                            'height' => '24px'
                                        ];
                                        include '../src/components/loading/loading.php';
                                    ?>
                                </p>
                            </div>
                            <div class="d-flex flex-column align-items-center mx-2">
                                <div class="w-100 py-2 d-flex gap-2">
                                    <label class="form-label m-0">Tiempo</label>
                                    <i class="bi bi-alarm-fill d-flex align-self-center"></i>
                                </div>
                                <p class="form-label m-0" id="tiempoProducto">
                                    <?php 
                                        $loadingIcon = [
                                            'id' => 'spinner-tiempo',
                                            'width' => '24px',
                                            'height' => '24px'
                                        ];
                                        include '../src/components/loading/loading.php';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Imágen de galería -->
        <div class="row my-3 align-items-center">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0 overflow-hidden">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Galería</h4>
                        </div>
                        <p class="card-text d-flex justify-content-center align-items-center px-2 pb-2">
                            <?php 
                                $loadingIcon = [
                                    'id' => 'spinner-imagen-galeria'
                                ];
                                include '../src/components/loading/loading.php';
                            ?>
                            <div class="d-flex">
                                <img id="imagenGaleria" class="w-auto h-100 product-page-img overflow-hidden rounded rounded-4 mx-auto" src="" alt="[Imágen de galería]" style="max-height: 196px;">
                            </div>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calificación de estrellas -->
        <div class="row my-3 align-items-center" id="row-calificacion">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Calificación de estrellas</h4>
                        </div>
                        <div class="d-flex flex-column align-items-center px-2 pb-2">
                            <div class="preview-product-card-border rounded-2 p-3" style="background-color:rgb(245, 245, 245)">
                                <div class="d-flex align-items-center gap-2">
                                    <button id="reset-rating" type="button" class="btn-delete text-white border-0 rounded-2 px-2 py-1 d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                        </svg>
                                    </button>
                                    <div class="m-auto mb-2 text-star" id="rating-opinion" data-id="opinion">
                                        <i class="bi bi-star star-radio-pointer" data-star="1"></i>
                                        <i class="bi bi-star star-radio-pointer" data-star="2"></i>
                                        <i class="bi bi-star star-radio-pointer" data-star="3"></i>
                                        <i class="bi bi-star star-radio-pointer" data-star="4"></i>
                                        <i class="bi bi-star star-radio-pointer" data-star="5"></i>
                                    </div>
                                </div>
                                <button onclick="" id="save-rating" type="button" class="btn-details mx-auto text-white border-0 rounded-2 px-2 py-1 mt-2 d-flex align-items-center">
                                    <span id="texto-boton-rating"></span>
                                    <i id="icono-boton-rating" class="ms-2 d-flex align-items-center"></i>
                                </button>
                                <input type="hidden" class="m-0 p-0" id="Estrellas" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Agregar comentario -->
        <div class="row my-3 align-items-center" id="row-comentario">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Agregar un comentario</h4>
                        </div>
                        <div class="px-2 pb-2">
                            <textarea class="form-control bi-textarea-resize" id="Comentario" cols="999%" rows="3"></textarea>
                            <button onclick="guardarComentario()" type="button" class="btn-details text-white border-0 rounded-2 px-2 py-1 mt-2 d-flex align-items-center">
                                Enviar
                                <i class="bi bi-pencil-square ms-2 d-flex align-items-center"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lista de comentarios del producto -->
        <div class="row my-3 align-items-center">
            <div class="col px-3">
                <div class="rounded-2 overflow-hidden preview-product-card-border">
                    <div class="preview-product-card-bg p-0 m-0">
                        <div class="preview-product-card-bg-header w-100 px-2 py-3 mb-2">
                            <h4 class="card-title">Lista de comentarios</h4>
                        </div>
                        <div class="px-2 pb-2">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination"></ul>
                            </nav>
                            <?php 
                                $loadingIcon = [
                                    'id' => 'spinner-comentarios'
                                ];
                                include '../src/components/loading/loading.php';
                            ?>
                            <div class="container-fluid row p-0 m-0" id="container-comentaries"></div>
                        </div>
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
        buscarCartaProducto(<?php echo json_encode($idProducto); ?>, <?php echo json_encode($idCliente); ?>);

        guardarInteraccion({
            usuario: <?php echo json_encode($_SESSION['usuario_id'] ?? ''); ?>,
            accion: `Ver el producto ${"<?php echo $pageTitle; ?>"}`,
        });
    });
</script>