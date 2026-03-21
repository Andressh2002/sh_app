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

<div class="w-100 rounded-3 overflow-hidden mb-3" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4 mb-2">
        <h4 class="card-title">Pantalla de inicio</h4>
    </div>
    <p class="card-text px-3 pb-2"> Bienvenido a <span class="fw-bold">SH APP</span>, <?php echo $_SESSION["usuario_nombre"] ?></p>
</div>

<div class="w-100 p-2 rounded-3" style="background-color: #f9fafb;">
    <p class="card-text">Use la barra de navegación situado en el lado izquierdo de la pantalla para navegar por las distintas páginas de administración</p>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>