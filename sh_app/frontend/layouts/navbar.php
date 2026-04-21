<?php
$currentPage = basename($_SERVER['REQUEST_URI']);
$currentPage = strtok($currentPage, '?');

$navbarItems = [
    [
        'label' => 'Inicio',
        'icon' => 'bi-house',
        'url' => 'store.php',
        'style' => 1
    ],
    [
        'label' => 'Tienda',
        'icon' => 'bi-shop',
        'url' => 'productos.php',
        'style' => 1
    ],
    isset($_SESSION['usuario_id']) ? [
        'label' => 'Pedidos',
        'icon' => 'bi-cart',
        'url' => 'pedidos.php',
        'style' => 1
    ] : null,
    [
        'label' => 'Avisos',
        'icon' => 'bi-bell',
        'url' => 'avisos.php',
        'style' => 1
    ],
    [
        'label' => 'Manual',
        'icon' => 'bi-book',
        'url' => 'manual.php',
        'style' => 1
    ],
    [
        'label' => 'Compartir ideas',
        'icon' => 'bi-clipboard',
        'url' => 'https://forms.gle/gUNCDTWvGTLcYYkTA',
        'style' => 1
    ],
    [
        'label' => 'Descargas',
        'icon' => 'bi-cloud-arrow-down',
        'url' => 'descarga.php',
        'style' => 1
    ],
    /*
    [
        'label' => '¿Quiénes somos?',
        'icon' => 'bi-question-circle',
        'url' => 'manual.php',
        'style' => 1
    ],
    */
    !isset($_SESSION['usuario_id']) ? [
        'label' => 'Iniciar sesión',
        'icon' => 'bi-person',
        'url' => 'login.php',
        'style' => 2
    ] : null,
    isset($_SESSION['usuario_id']) ? [
        'label' => 'Ver usuario',
        'icon' => 'bi-person',
        'url' => 'usuario.php',
        'style' => 1
    ] : null,
    isset($_SESSION['usuario_id']) ? [
        'label' => 'Cerrar sesión',
        'icon' => 'bi-person',
        'url' => 'logout.php',
        'style' => 2
    ] : null,
];

// Filtra los elementos nulos
$navbarItems = array_filter($navbarItems);
?>

<nav class="navbar navbar-expand-lg navbar-bg navbar-border">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent2"
            aria-controls="navbarSupportedContent2" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent2">
            <ul class="navbar-list d-flex flex-wrap justify-content-center my-0 w-100 p-0">
                <?php foreach ($navbarItems as $item): ?>
                    <?php $isActive = ($currentPage === $item['url']) ? 'navbar-item-active' : ''; ?>
                    <?php $style2Class = (2 === $item['style']) ? 'navbar-login-item' : ''; ?>
                    <li class="d-flex align-items-center w-100 navbar-item <?php echo $isActive; ?>" onclick="window.location.href='<?php echo $item['url']; ?>'">
                        <div class="mx-auto">
                            <a id="a-<?php echo $item['url']; ?>" href="<?php echo $item['url']; ?>" class="nav-link text-decoration-none d-flex align-items-center w-100 h-100 text-center px-3 py-1 <?php echo $style2Class; ?>">
                                <i class="me-2 bi <?php echo $item['icon']; ?> d-flex align-self-center me-sm-2 h2 my-auto navbar-item-icon-size"></i>
                                <span class="d-sm-inline fw-bold navbar-item-font-size"><?php echo $item['label']; ?></span>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>  
