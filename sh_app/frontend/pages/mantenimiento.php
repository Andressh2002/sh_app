<?php
    ob_start();
    $pageTitle = "Configuración";
    $pageIcon = 'bi-gear-fill';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = false;
?>

<section class="vh-100">
    <div class="container h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 text-center">
                <img src="../src/img/app/SH_Logo.png" class="img-fluid m-auto" alt="Sample image">
                <h1>SH APP en modo mantenimiento</h1>
                <p>Estamos trabajando en el sistema. Intenta más tarde.</p>
            </div>
        </div>
    </div>
</section>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>