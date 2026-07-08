<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $pageTitle = 'Administrar Carrusel';

    $pageIcon = 'bi-images';

    $menuTable = [
        'url' => 'configuration.php',
        'addMethod' => 'guardarCarrusel()',
    ];
?>

<div class="w-100" id="carousel-form">

    <!-- INFORMACIÓN -->
    <div class="overflow-hidden my-2">

        <div class="card-body admin-subheader-card-bg">
            <div class="d-flex align-items-center gap-2">
                <p class="card-title p-0 m-0">
                    Imágenes del carrusel
                </p>
            </div>
        </div>

        <div class="navbar-btn-shadow mt-4">
            <button
                class="store-filter-btn slide_from_left text-decoration-none"
                onclick="agregarSlide()"
            >

            <i class="bi bi-floppy-fill"></i>
            <span>Agregar</span>
        </div>

        <div class="row px-3 py-1" id="carrousel-images-list"></div>

    </div>

    <div class="container-fluid mb-3">
        <?php include '../src/components/forms/dialogButtons.php'; ?>
    </div>

</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    
    $(document).ready(function(){
        cargarCarrusel();
    });

    const maxSizeInKB = 1000;
    const maxSizeInBytes = maxSizeInKB * 1024;

    document
        .querySelectorAll('.id-input-image')
        .forEach(input => {

            input.addEventListener(
                'change',
                function(event){

                    const file =
                        event.target.files[0];

                    if(
                        file &&
                        file.size > maxSizeInBytes
                    ){

                        alert(
                            '¡Error!',
                            'No puedes cargar este archivo porque supera el tamaño máximo permitido (1 MB), busca una más pequeña.',
                            'error',
                            'Cerrar'
                        );

                        event.target.value = '';
                    }
                }
            );
        });

</script>