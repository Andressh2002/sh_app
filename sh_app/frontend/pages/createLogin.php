<?php
ob_start();

$pageTitle = "Crear cuenta";

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = false;

session_start();

if (isset($_SESSION['usuario_id'])) {

    if ($_SESSION['usuario_rol'] == 'Administrador') {
        header('Location: home.php');
        exit();
    }

    if ($_SESSION['usuario_rol'] == 'Cliente') {
        header('Location: store.php');
        exit();
    }
}
?>

<section class="login-page">
    <div class="register-card-shadow">
        <div class="register-card">
            <div class="login-header">
                <img
                    src="../src/img/app/SH_Logo.png"
                    class="login-logo"
                    alt="Logo"
                >
                <h2>
                    Crear cuenta
                </h2>
                <p>
                    Completa la información requerida
                </p>
            </div>
            <form>
                <!-- DATOS PERSONALES -->
                <div class="filter-card px-4 px-md-5">
                    <h6 class="filter-title">
                        <i class="bi bi-person-fill"></i>
                        Información personal
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <input
                                type="text"
                                id="Nombre"
                                class="form-control filter-input"
                                placeholder="Primer nombre *"
                            >
                        </div>
                        <div class="col-md-6">
                            <input
                                type="text"
                                id="segundoNombre"
                                class="form-control filter-input"
                                placeholder="Segundo nombre"
                            >
                        </div>
                        <div class="col-md-6 mt-3">
                            <input
                                type="text"
                                id="primerApellido"
                                class="form-control filter-input"
                                placeholder="Primer apellido *"
                            >
                        </div>
                        <div class="col-md-6 mt-3">
                            <input
                                type="text"
                                id="segundoApellido"
                                class="form-control filter-input"
                                placeholder="Segundo apellido"
                            >
                        </div>
                    </div>
                </div>
                <!-- UBICACIÓN -->
                <div class="filter-card mt-4 px-4 px-md-5">
                    <h6 class="filter-title">
                        <i class="bi bi-geo-alt-fill"></i>
                        Ubicación
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <input
                                type="text"
                                id="Provincia"
                                class="form-control filter-input"
                                placeholder="Provincia *"
                            >
                        </div>
                        <div class="col-md-4">
                            <input
                                type="text"
                                id="Canton"
                                class="form-control filter-input"
                                placeholder="Cantón *"
                            >
                        </div>
                        <div class="col-md-4">
                            <input
                                type="text"
                                id="Distrito"
                                class="form-control filter-input"
                                placeholder="Distrito *"
                            >
                        </div>
                    </div>
                </div>
                <!-- CUENTA -->
                <div class="filter-card mt-4 px-4 px-md-5">
                    <h6 class="filter-title">
                        <i class="bi bi-key-fill"></i>
                        Datos de acceso
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <input
                                type="text"
                                id="Telefono"
                                class="form-control filter-input"
                                placeholder="Teléfono *"
                            >
                        </div>
                        <div class="col-md-6">
                            <input
                                type="text"
                                id="nombreUsuario"
                                class="form-control filter-input"
                                placeholder="Usuario *"
                            >
                        </div>
                        <div class="col-md-6 mt-3">
                            <input
                                type="password"
                                id="Contrasennia"
                                class="form-control filter-input"
                                placeholder="Contraseña **"
                            >
                        </div>
                        <div class="col-md-6 mt-3">
                            <input
                                type="password"
                                id="Contrasennia2"
                                class="form-control filter-input"
                                placeholder="Confirmar contraseña **"
                            >
                        </div>
                    </div>
                </div>
                <p class="text-light opacity-75 mt-4">
                    (*) Campo obligatorio
                </p>
                <p class="text-light opacity-75 mt-4">
                    (**) Campo obligatorio y con una longitud mínima de 8 caractéres
                </p>
                <!-- BOTONES -->
                <div
                    class="d-flex justify-content-center gap-3 mt-4 flex-wrap"
                >
                    <div class="navbar-btn-shadow">
                        <button
                            type="button"
                            class="store-filter-btn"
                            onclick="registrarUsuario()"
                        >
                            <i class="bi bi-person-plus-fill"></i>
                            <span>
                                Registrar
                            </span>
                        </button>
                    </div>
                    <div class="navbar-btn-shadow">
                        <button
                            type="button"
                            class="store-btn-secondary"
                            onclick="location.href='login.php'"
                        >
                            <i class="bi bi-arrow-left"></i>
                            <span>
                                Volver
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
$content=ob_get_clean();
include 'template.php';
?>