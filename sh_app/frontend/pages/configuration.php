<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();
    $pageTitle = "Configuración";
    $pageIcon = 'bi-gear-fill';

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Elementos configurables</h4>
            <i class="bi bi-search fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Modo mantenimiento</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <div class="align-items-center py-2">
                    <p class="mb-1">
                        El modo mantenimiento está actualmente: 
                        <span id="estado-mantenimiento" class="text-danger">Cargando...</span>
                    </p>
                    <p class="mb-1 text-secondary">Este modo hace que si está activo, ningún usuario (excepto usuarios con rol administrador) pueda acceder a la aplicación.</p>
                    <button 
                        onclick="cambiarModoManteniento()" 
                        type="button" 
                        class="btn-details text-white border-0 rounded-2 px-2 py-1 mt-3 d-flex align-items-center"
                    >
                        Cambiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        buscarConfiguracionModoMantenimiento();
    });
</script>