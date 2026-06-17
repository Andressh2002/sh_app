<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Home";
    $pageIcon = 'bi-house-fill';

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
                                Bienvenido a <span class="fw-bold">SH APP</span>, <?php echo $_SESSION["usuario_nombre"] ?></p>
                            </p>

                            <h5 class="product-title">
                                Esta es la pantalla de inicio
                            </h5>
                        </div>
                    </div>

                    <div class="product-admin-body">

                        <div class="product-admin-image">
                            <img
                                id="img"
                                class="product-image"
                                src="../src/img/app/SH_Logo.png"
                                alt="${usuario.nombre_usuario}"
                            >
                        </div>

                        <div class="product-info">
                            <div class="product-info-grid">

                                <div>
                                    <span>Usa la barra de navegación (lado izquierdo de la pantalla) para navegar por los diferentes módulos de administración.</span>
                                </div>
                            
                            </div>
                        </div>

                        <div class="order-actions"></div>

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