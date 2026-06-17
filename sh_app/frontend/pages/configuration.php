<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Configuración";
    $pageIcon = 'bi-gear-fill';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;
?>

<div class="w-100 overflow-hidden p-0">
    <div class="px-0 pb-2">
        <div class="overflow-hidden p-0">
            <div
                id="list-container"
                class="products-admin-grid p-0"
            >

                <div class="product-admin-card">

                    <div class="product-admin-header">
                        <div>
                            <p class="product-number">
                                Configuración rápida
                            </p>

                            <h5 class="product-title">
                                Modo mantenimiento
                            </h5>
                        </div>
                    </div>

                    <div class="product-admin-body">

                        <div class="product-admin-image">
                            <img
                                id="img"
                                class="product-image"
                                src="../src/img/app/no_image.png"
                                alt="${usuario.nombre_usuario}"
                            >
                        </div>

                        <div class="product-info">
                            <div class="product-info-grid">

                                <div>
                                    <span>El modo mantenimiento está actualmente:</span>
                                    <strong>
                                        <span id="estado-mantenimiento" class="text-danger">Cargando...</span>
                                    </strong>
                                </div>

                                <div>
                                    <span class="text-secondary">Este modo hace que si está activo, ningún usuario (excepto usuarios con rol administrador) pueda acceder a la aplicación.</span>
                                </div>
                            
                            </div>
                        </div>

                        <div class="order-actions">
                            <button
                                class="
                                    store-filter-btn
                                    px-4
                                    justify-content-center
                                    text-decoration-none
                                "
                                onclick="cambiarModoManteniento()"
                            >
                                <i class="bi bi-arrow-clockwise"></i>
                                Cambiar
                            </button>
                        </div>

                    </div>
            
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
    $(document).ready(function() {
        buscarConfiguracionModoMantenimiento();
    });
</script>