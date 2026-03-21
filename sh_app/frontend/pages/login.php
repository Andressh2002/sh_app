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
      } elseif ($_SESSION['usuario_rol'] == 'Cliente') {
          header('Location: store.php');
          exit();
      }
  }
?>

<section class="vh-100">
  <div class="container h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5 d-flex align-middle align-content-center">
        <img src="../src/img/app/SH_Logo.png" class="img-fluid m-auto" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form>
          <p class="lead fw-bold mb-0 me-3 mb-3">Iniciar sesión</p>

          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <div class="w-100 py-2 d-flex gap-2">
              <label class="form-label m-0" for="nombreUsuario">Nombre de usuario</label>
              <i class="bi bi-key-fill d-flex align-self-center"></i>
            </div>
            <input type="email" id="nombreUsuario" class="form-control form-control-lg" />
          </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <div class="w-100 py-2 d-flex gap-2">
              <label class="form-label m-0" for="Contrasennia">Contraseña</label>
              <i class="bi bi-lock-fill d-flex align-self-center"></i>
            </div>
            <input type="password" id="Contrasennia" class="form-control form-control-lg" />
          </div>

          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="button" class="btn-details btn-lg fs-5 text-white border-0 rounded-2 px-4 py-3 d-flex align-items-center" style="padding-left: 2.5rem; padding-right: 2.5rem;" onclick="iniciarSesion()">Ingresar</button>
            <p class="small fw-bold mt-2 pt-1 mb-0">¿No tienes una cuenta aquí? <a href="createLogin.php" class="link-danger">¡Registrala!</a></p>
          </div>

        </form>
      </div>
    </div>
  </div>
</section>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
  try {
    $(document).on('keydown', function(event) {
    if (event.key === "Enter") {
      iniciarSesion();
    }
    });
  } catch (error) {
    //
  }
</script>