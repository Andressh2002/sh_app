<?php
    include '../src/components/login/access.php';

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $idProducto = isset($_GET['id']) ? $_GET['id'] : '';
    $nombreProducto = isset($_GET['nombreProducto']) ? $_GET['nombreProducto'] : '';
    $productId = isset($_GET['id']) ? $_GET['id'] : null;
    $idCliente = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : '';

    $pageTitle = $nombreProducto;
?>
<div class="container-fluid p-0 m-0">
    <div class="w-100 container" id="producto-informacion">
        <!-- Contenido de la información del producto se cargará aquí -->
    </div>
</div>


<input type="hidden" id="Id" value="<?php echo isset($productId) ? $productId : ''; ?>"> 
<input type="hidden" id="Color" value=""> 
<input type="hidden" id="AccesoryColor" value=""> 
<input type="hidden" id="NumColor" value=""> 
<input type="hidden" id="NumAccesoryColor" value=""> 
<input type="hidden" id="Sesion" value="<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : '' ?>"> 

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        buscarCartaProducto(<?php echo json_encode($idProducto); ?>, <?php echo json_encode($idCliente); ?>);
    });
</script>