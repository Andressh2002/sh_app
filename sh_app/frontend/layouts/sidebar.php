<?php

$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$sidebarGroups = [
    [
        'title' => 'Inicio',
        'icon'  => 'bi-grid-fill',
        'items' => [
            [
                'label' => 'Dashboard',
                'icon'  => 'bi-speedometer',
                'url'   => 'dashboard.php',
            ],
            [
                'label' => 'Ver tienda',
                'icon'  => 'bi-shop',
                'url'   => 'store.php',
            ],
            [
                'label' => 'Home',
                'icon'  => 'bi-house-fill',
                'url'   => 'home.php',
            ],
        ],
    ],

    [
        'title' => 'Catálogo',
        'icon'  => 'bi-box-fill',
        'items' => [
            [
                'label' => 'Productos',
                'icon'  => 'bi-palette-fill',
                'url'   => 'products.php',
            ],
            [
                'label' => 'Accesorios',
                'icon'  => 'bi-brush-fill',
                'url'   => 'accesories.php',
            ],
            [
                'label' => 'Categorías',
                'icon'  => 'bi-tools',
                'url'   => 'categories.php',
            ],
            [
                'label' => 'Universos',
                'icon'  => 'bi-flag-fill',
                'url'   => 'universes.php',
            ],
            [
                'label' => 'Rarezas',
                'icon'  => 'bi-tag-fill',
                'url'   => 'rarities.php',
            ],
            [
                'label' => 'Colores',
                'icon'  => 'bi-paint-bucket',
                'url'   => 'colors.php',
            ],
        ],
    ],

    [
        'title' => 'Promociones',
        'icon'  => 'bi-stars',
        'items' => [
            [
                'label' => 'Descuentos',
                'icon'  => 'bi-percent',
                'url'   => 'discounts.php',
            ],
            [
                'label' => 'Festividades',
                'icon'  => 'bi-calendar-fill',
                'url'   => 'holidays.php',
            ],
        ],
    ],

    [
        'title' => 'Ventas',
        'icon'  => 'bi-cart-fill',
        'items' => [
            [
                'label' => 'Pedidos',
                'icon'  => 'bi-cart-fill',
                'url'   => 'orders.php',
            ],
            [
                'label' => 'Usuarios',
                'icon'  => 'bi-people-fill',
                'url'   => 'users.php',
            ],
            [
                'label' => 'Comentarios',
                'icon'  => 'bi-chat-fill',
                'url'   => 'comentaries.php',
            ],
            [
                'label' => 'Interacciones',
                'icon'  => 'bi-broadcast',
                'url'   => 'interactions.php',
            ],
        ],
    ],

    [
        'title' => 'Sistema',
        'icon'  => 'bi-gear-fill',
        'items' => [
            [
                'label' => 'Configuración',
                'icon'  => 'bi-gear-fill',
                'url'   => 'configuration.php',
            ],
        ],
    ],
];

?>

<div class="container-fluid d-flex flex-column mx-0 flex-fill" style="height: 100vh; overflow: hidden;">
    <div class="row">
        <?php include '../layouts/header2.php'; ?>
    </div>
    <div class="row flex-nowrap flex-grow-1 overflow-hidden">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar-bg">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 py-2 text-white h-100 sidebar">

                <div class="sidebar-groups">

                    <?php foreach ($sidebarGroups as $index => $group): ?>

                        <?php
                        $groupHasActive = array_reduce(
                            $group['items'],
                            fn ($carry, $item) => $carry || $currentPage === $item['url'],
                            false
                        );
                        ?>

                        <div class="sidebar-group">

                            <button
                                class="sidebar-group-btn <?= $groupHasActive ? 'open active-group' : '' ?>"
                                data-target="group-<?= $index ?>"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi <?= $group['icon'] ?>"></i>
                                    <span><?= $group['title'] ?></span>
                                </div>

                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div
                                id="group-<?= $index ?>"
                                class="sidebar-group-content <?= $groupHasActive ? 'show' : '' ?>"
                            >

                                <?php foreach ($group['items'] as $item): ?>

                                    <a
                                        href="<?= $item['url'] ?>"
                                        class="sidebar-item <?= $currentPage === $item['url'] ? 'active' : '' ?>"
                                    >
                                        <i class="bi <?= $item['icon'] ?>"></i>

                                        <span><?= $item['label'] ?></span>

                                        <?php if (isset($item['badge'])): ?>
                                            <span class="sidebar-badge">
                                                <?= $item['badge'] ?>
                                            </span>
                                        <?php endif; ?>

                                    </a>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>
        </div>
        <div class="col px-0 mx-0 content-area d-flex flex-column overflow-auto admin-body-bg">

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

<script>

    document
    .querySelectorAll('.sidebar-group-btn')
    .forEach(btn => {

        btn.addEventListener('click', () => {

            const target =
                document.getElementById(
                    btn.dataset.target
                );

            btn.classList.toggle('open');

            target.classList.toggle('show');
        });

    });

</script>