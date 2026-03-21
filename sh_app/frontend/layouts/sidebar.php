<?php 
    // Obtener el nombre de archivo de la página actual
    $currentPage = basename($_SERVER['REQUEST_URI']);  // Esto devolverá algo como 'store.php'

    $sidebarItems = [
        [
            'label' => 'Inicio',
            'url' => null
        ],
        [
            'label' => 'Home',
            'icon' => 'bi-house-fill',
            'url' => 'home.php'
        ],
        [
            'label' => 'Dashboard',
            'icon' => 'bi-speedometer',
            'url' => 'dashboard.php'
        ],
        [
            'label' => 'Mercancía',
            'url' => null
        ],
        [
            'label' => 'Productos',
            'icon' => 'bi-palette-fill',
            'url' => 'products.php'
        ],
        [
            'label' => 'Accesorios',
            'icon' => 'bi-brush-fill',
            'url' => 'accesories.php'
        ],
        [
            'label' => 'Clasificadores',
            'url' => null
        ],
        [
            'label' => 'Categorías',
            'icon' => 'bi-tools',
            'url' => 'categories.php'
        ],
        [
            'label' => 'Rarezas',
            'icon' => 'bi-tag-fill',
            'url' => 'rarities.php'
        ],
        [
            'label' => 'Universos',
            'icon' => 'bi-flag-fill',
            'url' => 'universes.php'
        ],
        [
            'label' => 'Colores',
            'icon' => 'bi-paint-bucket',
            'url' => 'colors.php'
        ],
        [
            'label' => 'Festividades',
            'icon' => 'bi-calendar-fill',
            'url' => 'holidays.php'
        ],
        [
            'label' => 'Descuentos',
            'icon' => 'bi-percent',
            'url' => 'discounts.php'
        ],
        [
            'label' => 'Clientes',
            'url' => null
        ],
        [
            'label' => 'Pedidos',
            'icon' => 'bi-cart-fill',
            'url' => 'orders.php'
        ],
        [
            'label' => 'Usuarios',
            'icon' => 'bi-person-fill',
            'url' => 'users.php'
        ],
        [
            'label' => 'Comentarios',
            'icon' => 'bi-chat-fill',
            'url' => 'comentaries.php'
        ],
        [
            'label' => 'Avisos',
            'icon' => 'bi-bell-fill',
            'url' => 'news.php'
        ],
        [
            'label' => 'Tienda',
            'url' => null
        ],
        [
            'label' => 'Tienda',
            'icon' => 'bi-shop',
            'url' => 'store.php'
        ],
        [
            'label' => 'Sesión',
            'url' => null
        ],
        [
            'label' => 'Salir',
            'icon' => 'bi-door-open-fill',
            'url' => 'logout.php'
        ],
        /* 
        [
            'label' => 'Carrusel',
            'icon' => 'bi-tv-fill',
            'url' => 'carousel.php'
        ] */
    ];
?>

<div class="container-fluid d-flex flex-column mx-0 flex-fill" style="height: 100vh; overflow: hidden;">
    <div class="row">
        <?php include '../layouts/header2.html'; ?>
    </div>
    <div class="row flex-nowrap flex-grow-1 overflow-hidden">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar-bg">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 py-2 text-white h-100 sidebar">
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100 gap-3" id="menu">
                    <?php foreach ($sidebarItems as $item): ?>
                        <?php if ($item['url'] != null) : ?>
                            <?php
                                // Agregar clase 'active' si el nombre del archivo coincide con la página actual
                                $isActive = ($currentPage === $item['url']) ? 'active' : '';
                            ?>
                            <li class="nav-item d-flex align-items-center w-100">
                                <?php if ($isActive): ?>
                                    <!-- Si el item es la página actual, solo mostrar el texto sin enlace -->
                                    <div class="px-3 py-2 rounded-2 sidebar-item d-flex align-items-center w-100 text-center <?php echo $isActive; ?>">
                                        <i class="fs-4 bi <?php echo $item['icon']; ?> d-flex align-self-center me-sm-2"></i> 
                                        <span class="d-none d-sm-inline"><?php echo $item['label']; ?></span>
                                    </div>
                                <?php else: ?>
                                    <!-- Si no es la página actual, generar el enlace -->
                                    <a href="<?php echo $item['url']; ?>" class="px-3 py-2 rounded-2 text-decoration-none sidebar-item d-flex align-items-center w-100 text-center <?php echo $isActive; ?>">
                                        <i class="fs-4 bi <?php echo $item['icon']; ?> d-flex align-self-center me-sm-2"></i> 
                                        <span class="d-none d-sm-inline"><?php echo $item['label']; ?></span>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php else: ?>
                            <label class="d-none d-sm-inline mt-2"><?php echo $item['label']; ?></label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col px-0 mx-0 content-area d-flex flex-column overflow-auto admin-body-bg">
            <?php include '../layouts/title.php'; ?>
            
            <!-- Contenedor limitado para evitar que el contenido se salga del ancho -->
            <div class="flex-grow-1 p-4">
                <?php echo $content; ?>
            </div>
            
            <footer class="mt-auto">
                <?php include '../layouts/footer2.html'; ?>
            </footer>
        </div>
    </div>
</div>
