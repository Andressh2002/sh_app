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
                    <p class="lead fw-bold mb-0 me-3 mb-3">Crear usuario</p>

                    <!-- Grupo de Nombre Completo -->
                    <div class="form-outline mb-2">
                        <div class="w-100 py-2 d-flex gap-2">
                            <label class="form-label m-0" for="Nombre">Nombre completo</label>
                            <i class="bi bi-person-fill d-flex align-self-center"></i>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="text" id="Nombre" class="form-control form-control-md" placeholder="Nombre" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" id="segundoNombre" class="form-control form-control-md" placeholder="Segundo Nombre" />
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="text" id="primerApellido" class="form-control form-control-md" placeholder="Primer Apellido" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" id="segundoApellido" class="form-control form-control-md" placeholder="Segundo Apellido" />
                            </div>
                        </div>
                    </div>

                    <!-- Grupo de Ubicación -->
                    <div class="form-outline mb-2">
                        <div class="w-100 py-2 d-flex gap-2">
                            <label class="form-label m-0" for="Nombre">Ubicación</label>
                            <i class="bi bi-geo-alt-fill d-flex align-self-center"></i>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="text" id="Provincia" class="form-control form-control-md" placeholder="Provincia" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="text" id="Canton" class="form-control form-control-md" placeholder="Canton" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="text" id="Distrito" class="form-control form-control-md" placeholder="Distrito" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nombre de Usuario -->
                    <div class="form-outline mb-2">
                        <div class="w-100 py-2 d-flex gap-2">
                            <label class="form-label m-0" for="Telefono">Teléfono</label>
                            <i class="bi bi-telephone-fill d-flex align-self-center"></i>
                        </div>
                        <input type="phone" id="Telefono" class="form-control form-control-md" placeholder="Número telefónico" />
                    </div>

                    <!-- Nombre de Usuario -->
                    <div class="form-outline mb-2">
                        <div class="w-100 py-2 d-flex gap-2">
                            <label class="form-label m-0" for="nombreUsuario">Un nombre de usuario</label>
                            <i class="bi bi-key-fill d-flex align-self-center"></i>
                        </div>
                        <div class="input-group">
                            <input type="text" id="nombreUsuario" class="form-control form-control-md" placeholder="Nombre de usuario" />
                            <span class="input-group-text input-group-md">*</span>
                        </div>
                    </div>

                    <!-- Contraseñas en una Fila -->
                    <div class="form-outline mb-2">
                        <div class="w-100 py-2 d-flex gap-2">
                            <label class="form-label m-0" for="nombreUsuario">Contraseña</label>
                            <i class="bi bi-lock-fill d-flex align-self-center"></i>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="password" id="Contrasennia" class="form-control form-control-md" placeholder="Contraseña" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <input type="password" id="Contrasennia2" class="form-control form-control-md" placeholder="Repetir Contraseña" />
                                    <span class="input-group-text input-group-md">*</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="card-text text-secondary">Los campos con el símbolo (*) son de digitación obligatoria.</p>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-start gap-3 mt-4 pt-2">
                        <button type="button" class="btn-details btn-lg fs-5 text-white border-0 rounded-2 px-lg-4 py-lg-3 px-md-3 py-md-2 px-sm-2 py-sm-1 px-2" onclick="registrarUsuario()">Registrar</button>
                        <button type="button" class="btn-delete btn-lg fs-5 text-white border-0 rounded-2 px-lg-4 py-lg-3 px-md-3 py-md-2 px-sm-2 py-sm-1 px-2" onclick="location.href='login.php'">Volver</button>
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