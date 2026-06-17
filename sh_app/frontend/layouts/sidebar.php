<?php 
    // Obtener el nombre de archivo de la página actual
    $currentPage = basename($_SERVER['REQUEST_URI']);  // Esto devolverá algo como 'store.php'

    $sidebarGroups = [
        [
            'title' => 'Inicio',
            'items' => [

                [
                    'label' => 'Home',
                    'icon' => 'bi-house-fill',
                    'url' => 'home.php'
                ],

                [
                    'label' => 'Dashboard',
                    'icon' => 'bi-speedometer',
                    'url' => 'dashboard.php'
                ]
            ]
        ],

        [
            'title' => 'Inventario',
            'items' => [

                [
                    'label' => 'Productos',
                    'icon' => 'bi-palette-fill',
                    'url' => 'products.php'
                ],

                [
                    'label' => 'Accesorios',
                    'icon' => 'bi-brush-fill',
                    'url' => 'accesories.php'
                ]
            ]
        ],

        [
            'title' => 'Clasificadores',
            'items' => [

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
                ]
            ]
        ],

        [
            'title' => 'Clientes',
            'items' => [

                [
                    'label' => 'Pedidos',
                    'icon' => 'bi-cart-fill',
                    'url' => 'orders.php'
                ],

                [
                    'label' => 'Usuarios',
                    'icon' => 'bi-brush-fill',
                    'url' => 'users.php'
                ],

                [
                    'label' => 'Comentarios',
                    'icon' => 'bi-chat-fill',
                    'url' => 'comentaries.php'
                ]
            ]
        ],

        [
            'title' => 'Tienda',
            'items' => [

                [
                    'label' => 'Ver tienda',
                    'icon' => 'bi-shop',
                    'url' => 'store.php'
                ],

                [
                    'label' => 'Interacciones',
                    'icon' => 'bi-broadcast',
                    'url' => 'interactions.php'
                ],

                [
                    'label' => 'Configuración',
                    'icon' => 'bi-gear-fill',
                    'url' => 'configuration.php'
                ]
            ]
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

                <?php foreach($sidebarGroups as $index => $group): ?>

                    <?php

                    $groupHasActive = false;

                    foreach($group['items'] as $item){

                        if($currentPage === $item['url']){

                            $groupHasActive = true;
                            break;
                        }
                    }

                    ?>

                    <div class="sidebar-group">

                        <button
                            type="button"
                            class="sidebar-group-btn <?= $groupHasActive ? 'open' : '' ?>"
                            data-target="group-<?= $index ?>"
                        >

                            <span><?= $group['title']; ?></span>

                            <i class="bi bi-chevron-down"></i>

                        </button>

                        <div
                            id="group-<?= $index ?>"
                            class="sidebar-group-content <?= $groupHasActive ? 'show' : '' ?>"
                        >

                            <?php foreach($group['items'] as $item): ?>

                                <?php
                                    $isActive =
                                        ($currentPage === $item['url'])
                                        ? 'active'
                                        : '';
                                ?>

                                <?php if($isActive): ?>

                                    <div class="sidebar-item active">

                                        <i class="bi <?= $item['icon']; ?>"></i>

                                        <span><?= $item['label']; ?></span>

                                    </div>

                                <?php else: ?>

                                    <a
                                        href="<?= $item['url']; ?>"
                                        class="sidebar-item"
                                    >

                                        <i class="bi <?= $item['icon']; ?>"></i>

                                        <span><?= $item['label']; ?></span>

                                    </a>

                                <?php endif; ?>

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