<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
$currentPage = strtok($currentPage, '?');

$navbarItems = [
    [
        'label' => 'Inicio',
        'icon' => 'bi-house',
        'url' => 'store.php',
    ],
    [
        'label' => 'Tienda',
        'icon' => 'bi-shop',
        'url' => 'productos.php',
    ],

    isset($_SESSION['usuario_id']) ? [
        'label' => 'Pedidos',
        'icon' => 'bi-cart',
        'url' => 'pedidos.php',
    ] : null,

    //[
    //    'label' => 'Avisos',
    //    'icon' => 'bi-bell',
    //    'url' => 'avisos.php',
    //],

    //[
    //    'label' => 'Manual',
    //    'icon' => 'bi-book',
    //    'url' => 'manual.php',
    //],

    [
        'label' => 'Compartir ideas',
        'icon' => 'bi-clipboard',
        'url' => 'https://forms.gle/gUNCDTWvGTLcYYkTA',
    ],

    [
        'label' => 'Descargas',
        'icon' => 'bi-cloud-arrow-down',
        'url' => 'descarga.php',
    ],

    !isset($_SESSION['usuario_id']) ? [
        'label' => 'Iniciar sesión',
        'icon' => 'bi-person',
        'url' => 'login.php',
    ] : null,

    //isset($_SESSION['usuario_id']) ? [
    //    'label' => 'Ver usuario',
    //    'icon' => 'bi-person',
    //    'url' => 'usuario.php',
    //] : null,

    //isset($_SESSION['usuario_id']) ? [
    //    'label' => 'Cerrar sesión',
    //    'icon' => 'bi-box-arrow-right',
    //    'url' => 'logout.php',
    //] : null,
];

// Filtra los elementos nulos
$navbarItems = array_filter($navbarItems);
?>

<nav class="navbar navbar-expand-lg navbar-bg navbar-border py-3">
    <div class="container-fluid">

        <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent2">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center"
            id="navbarSupportedContent2">

            <ul class="navbar-list d-flex flex-wrap justify-content-center my-0 w-100 p-0">
                <?php foreach ($navbarItems as $item): ?>
                    <?php
                    $isActive = ($currentPage === basename($item['url']));
                    ?>
                    <li class="navbar-item">
                        <div class="navbar-btn-shadow">
                            <a
                                href="<?php echo !$isActive ? $item['url'] : '#'; ?>"
                                class="
                                    nav-link
                                    navbar-btn
                                    slide_from_left

                                    <?php echo $isActive ? 'navbar-btn-active' : ''; ?>
                                "
                                <?php if ($isActive): ?>
                                    aria-current="page"
                                <?php endif; ?>
                            >
                                <i class="bi <?php echo $item['icon']; ?>"></i>
                                <span>
                                    <?php echo $item['label']; ?>
                                </span>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav> 
