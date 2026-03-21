<?php
    include '../src/components/login/access.php';
    checkAccess('Cliente');
    $pageTitle = "Usuario";

    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;

    $inputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Segundo nombre',
            'id' => 'segundoNombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Primer apellido',
            'id' => 'primerApellido',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Segundo apellido',
            'id' => 'segundoApellido',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Provincia',
            'id' => 'Provincia',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Cantón',
            'id' => 'Canton',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Distrito',
            'id' => 'Distrito',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Número teléfono',
            'id' => 'Telefono',
            'icon' => 'bi bi-telephone-fill',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Nombre de usuario',
            'id' => 'nombreUsuario',
            'icon' => 'bi bi-key-fill',
            'input' => 'text',
            'onchange' => '',
        ],
        [
            'label' => 'Contraseña',
            'id' => 'Contrasennia',
            'icon' => 'bi bi-lock-fill',
            'input' => 'password',
            'onchange' => '',
        ],
        [
            'label' => 'Rol',
            'id' => 'Rol',
            'icon' => 'bi bi-person-badge-fill',
            'input' => 'select',
            'onchange' => '',
            'options' => [
                'Administrador' => 'Administrador',
                'Cliente' => 'Cliente'
            ]
        ],
    ];
?>

<div class="row my-3 p-4">
    <div class="d-flex align-items-center gap-2 mb-4 px-0">
        <h4 class="mb-0">Tu usuario</h4>
        <i class="bi bi-person-fill fs-4 d-flex align-self-center"></i>
    </div>
    <div class="border border-2 rounded rounded-3 overflow-hidden">
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
                    <input type="text" id="Provincia" class="form-control form-control-md" placeholder="Provincia" />
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" id="Canton" class="form-control form-control-md" placeholder="Canton" />
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" id="Distrito" class="form-control form-control-md" placeholder="Distrito" />
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

        <p class="card-text text-secondary pb-2">Los campos con el símbolo (*) son de digitación obligatoria.</p>
    </div>

    <div class="d-flex justify-content-start gap-3 mt-4 py-2">
        <button type="button" class="btn-details btn-lg fs-5 text-white border-0 rounded-2 px-lg-4 py-lg-3 px-md-3 py-md-2 px-sm-2 py-sm-1 px-2 mx-auto" onclick="guardarUsuario()">Guardar</button>
    </div>
    <input type="hidden" id="Rol" class="hidden" />
    <input type="hidden" id="Id" class="hidden" value="<?php echo $_SESSION['usuario_id']; ?>" />
</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        buscarUsuario(<?php echo $_SESSION['usuario_id']; ?>);
    });
</script>