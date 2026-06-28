<?php
    include '../src/components/login/access.php';
    checkAccess('Cliente');
    $pageTitle = "Usuario";

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;
?>

<div class="profile-page container-fluid py-4 user-loading" id="user-form">

    <!-- HEADER -->
    <div class="profile-hero">

        <div class="profile-hero-content">

            <div class="profile-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div>
                <h2 class="profile-title">
                    Tu perfil
                </h2>

                <p class="profile-subtitle">
                    Administra tu información personal y los datos de tu usuario.
                </p>
            </div>
        </div>

        <div class="profile-security-card">

            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-lock-fill"></i>

                <strong>
                    Seguridad
                </strong>
            </div>

            <p class="profile-subtitle m-0">
                Tu contraseña se administra desde una sección separada para mayor seguridad.
            </p>

            <div class="navbar-btn-shadow my-4">
                <button
                    class="store-filter-btn slide_from_left"
                    onclick="abrirModalCambiarContrasennia()"
                >
                    <i class="bi bi-key-fill"></i>
                    <span>Cambiar contraseña</span>
                </button>
            </div>
            
        </div>
    </div>

    <!-- FORM -->
    <div class="profile-card">

        <!-- DATOS PERSONALES -->
        <div class="profile-section">

            <div class="profile-section-title">
                <div class="profile-section-icon">
                    <i class="bi bi-person-vcard-fill"></i>
                </div>

                <div>
                    <h5>
                        Información personal
                    </h5>

                    <p>
                        Datos básicos del usuario.
                    </p>
                </div>
            </div>

            <div class="row g-3">

                <div class="col-12 col-md-6">
                    <label class="profile-label">
                        Nombre
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="Nombre"
                            class="form-control"
                            placeholder="Nombre"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="profile-label">
                        Segundo nombre
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>

                        <input
                            type="text"
                            id="segundoNombre"
                            class="form-control"
                            placeholder="Segundo nombre"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="profile-label">
                        Primer apellido
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-fill"></i>
                        </span>

                        <input
                            type="text"
                            id="primerApellido"
                            class="form-control"
                            placeholder="Primer apellido"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="profile-label">
                        Segundo apellido
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>

                        <input
                            type="text"
                            id="segundoApellido"
                            class="form-control"
                            placeholder="Segundo apellido"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- UBICACIÓN -->
        <div class="profile-section">

            <div class="profile-section-title">
                <div class="profile-section-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>

                <div>
                    <h5>
                        Ubicación
                    </h5>

                    <p>
                        Información de residencia.
                    </p>
                </div>
            </div>

            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label class="profile-label">
                        Provincia
                    </label>

                    <input
                        type="text"
                        id="Provincia"
                        class="form-control"
                        placeholder="Provincia"
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label class="profile-label">
                        Cantón
                    </label>

                    <input
                        type="text"
                        id="Canton"
                        class="form-control"
                        placeholder="Cantón"
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label class="profile-label">
                        Distrito
                    </label>

                    <input
                        type="text"
                        id="Distrito"
                        class="form-control"
                        placeholder="Distrito"
                    >
                </div>
            </div>
        </div>

        <!-- CONTACTO -->
        <div class="profile-section">

            <div class="profile-section-title">
                <div class="profile-section-icon">
                    <i class="bi bi-telephone-fill"></i>
                </div>

                <div>
                    <h5>
                        Contacto
                    </h5>

                    <p>
                        Datos para comunicación.
                    </p>
                </div>
            </div>

            <div class="row g-3">

                <div class="col-12 col-md-6">
                    <label class="profile-label">
                        Teléfono
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-phone-fill"></i>
                        </span>

                        <input
                            type="tel"
                            id="Telefono"
                            class="form-control"
                            placeholder="Número telefónico"
                        >
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="profile-label">
                        Usuario
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-at"></i>
                        </span>

                        <input
                            type="text"
                            id="nombreUsuario"
                            class="form-control"
                            placeholder="Nombre de usuario"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="profile-footer">

            <div class="profile-required">
                <i class="bi bi-info-circle-fill"></i>

                <span>
                    Los campos importantes deben mantenerse actualizados.
                </span>
            </div>

            <div class="profile-actions">

                <div class="navbar-btn-shadow my-4">
                    <button
                        class="store-filter-btn slide_from_left"
                        onclick="guardarUsuario()"
                    >
                        <i class="bi bi-floppy-fill"></i>
                        <span>Guardar cambios</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- HIDDEN -->
    <input
        type="hidden"
        id="Rol"
    >

    <input
        type="hidden"
        id="Id"
        value="<?php echo $_SESSION['usuario_id']; ?>"
    >
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    function setUserLoading(isLoading){
        $('#user-form').toggleClass(
            'user-loading',
            isLoading
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function(){

            setUserLoading(true);

            buscarUsuario(
                <?php echo $_SESSION['usuario_id']; ?>
            );

        }
    );
</script>