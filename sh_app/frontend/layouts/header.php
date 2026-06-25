<div class="top-header d-flex align-items-center p-0" style="background-color: black;">
    
    <!-- Grupo izquierdo: logo + texto -->
    <div class="d-flex align-items-center p-0 h-100">
        <div class="top-header-logo d-flex"> 
            <img class="nav-logo my-auto me-2 me-sm-3 p-1 p-sm-0" src="../src/img/app/SH_Logo.png" alt="sh" style="height: 54px;">
        </div>
        
        <h1 class="ms-2 text-white my-auto">
            <?php echo $pageTitle; ?>
        </h1>
    </div>

    <!-- Indicador de fichas -->
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <div class="header-coins text-center d-flex align-items-center justify-content-center gap-2 ms-auto me-3">
        <p id="usuario-fichas-actuales" class="mb-0 text-white fw-bolder">cargando...</p>
        <img class="fs-4" src="../src/img/app/SH_Ficha.png" alt="sh" style="height: 54px;">
    </div>

    <?php endif; ?>

    <!-- Botón usuario (derecha) -->
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <div class="dropdown user-menu-container me-3">
        <button
            class="user-menu-btn dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
        >
            <span class="user-menu-bg"></span>
            <div class="user-menu-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-menu-info d-none d-md-flex">
                <span class="user-menu-label">
                    <?php echo $_SESSION['usuario_nombre']; ?>
                </span>
            </div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end user-menu-dropdown rounded-0">
            <li>
                <a
                    class="dropdown-item"
                    href="usuario.php"
                >
                    <i class="bi bi-person-vcard-fill"></i>
                    <?php echo $_SESSION['usuario_rol'] == 'Cliente' ? 'Mi perfil' : 'Volver a administración' ?>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a
                    class="dropdown-item user-menu-logout"
                    href="logout.php"
                >
                    <i class="bi bi-box-arrow-right"></i>
                    Cerrar sesión
                </a>
            </li>
        </ul>
    </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        buscarFichas(<?php echo json_encode($_SESSION['usuario_id'] ?? ''); ?>);
    });
</script>