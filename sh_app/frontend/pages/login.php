<?php
    ob_start();
    $pageTitle = "Iniciar sesión";

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
    <div class="login-card-shadow">
        <div class="login-card">
            <div class="login-header">
                <img
                    src="../src/img/app/SH_Logo.png"
                    class="login-logo"
                    alt="Logo"
                >
                <h2>
                    Iniciar sesión
                </h2>
                <p>
                    Bienvenido nuevamente
                </p>
            </div>
            <form>
                <div class="filter-card px-4">
                    <h6 class="filter-title">
                        <i class="bi bi-person-fill"></i>
                        Usuario
                    </h6>
                    <input
                        type="text"
                        id="nombreUsuario"
                        class="form-control filter-input"
                        placeholder="Escribe tu usuario"
                    >
                </div>
                <div class="filter-card mt-4 px-4">
                    <h6 class="filter-title">
                        <i class="bi bi-lock-fill"></i>
                        Contraseña
                    </h6>

                    <div class="password-container">
                        <input
                            type="password"
                            id="Contrasennia"
                            class="form-control filter-input"
                            placeholder="Escribe tu contraseña"
                        >
                        <button
                            type="button"
                            class="password-btn"
                            onclick="togglePassword()"
                        >
                            <i
                                id="passwordIcon"
                                class="bi bi-eye-fill"
                            ></i>
                        </button>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    <div class="navbar-btn-shadow">
                        <button
                            type="button"
                            class="store-filter-btn slide_from_left"
                            onclick="iniciarSesion()"
                        >
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>
                                Ingresar
                            </span>
                        </button>
                    </div>
                </div>
                <div class="login-register">
                    ¿No tienes una cuenta?
                    <a href="createLogin.php">
                        Crear cuenta
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include 'template.php';
?>

<script>

function togglePassword(){

    const input=
        document.getElementById(
            'Contrasennia'
        );

    const icon=
        document.getElementById(
            'passwordIcon'
        );

    if(input.type==='password'){

        input.type='text';

        icon.className=
            'bi bi-eye-slash-fill';
    }
    else{

        input.type='password';

        icon.className=
            'bi bi-eye-fill';
    }
}

$('#nombreUsuario,#Contrasennia').on(
    'keypress',
    function(e){

        if(e.key==='Enter'){
            iniciarSesion();
        }

    }
);

</script>
