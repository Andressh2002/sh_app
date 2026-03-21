<?php
    include '../src/components/login/access.php';
    //checkAccess('Cliente');
    $pageTitle = "Avisos";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $idAviso = isset($_GET['id']) ? $_GET['id'] : null;
?>

<div class="row my-3 p-4">
    <div class="d-flex align-items-center gap-2 mb-4 px-0">
        <h4 class="mb-0">Mensaje del aviso</h4>
        <i class="bi bi-bell-fill fs-4 d-flex align-self-center"></i>
    </div>

    <div class="container-fluid p-0">
        <div class="row">
            <div class="card px-0">
                <div class="card-header" id="mensajeCardTitle"></div>
                <div class="card-body">
                    <p class="card-text" id="mensajeCardMenssage"></p>
                    <img class="p-3 mx-auto" id="mensajeCardImage" src="" alt="" style="display: none; height: 196px; width: auto;">
                </div>
                <div class="card-footer text-muted" id="mensajeCardDate"></div>
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        buscarAvisoCliente(<?php echo $idAviso; ?>);
    });
</script>