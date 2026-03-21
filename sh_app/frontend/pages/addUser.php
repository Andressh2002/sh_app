<?php
    include '../src/components/login/access.php';
    checkAccess('Administrador');

    ob_start();

    $showHeader = false;
    $showNavbar = false;
    $showFooter = false;
    $showSidebar = true;

    $updateUser = isset($_GET['accion']) && $_GET['accion'] == 'actualizar';
    $userId = isset($_GET['id']) ? $_GET['id'] : null;

    $pageTitle = $updateUser ? 'Actualizar usuario' : 'Agregar usuario';
    $pageIcon = 'bi-person-fill';
    
    $informationInputs = [
        [
            'label' => 'Nombre',
            'id' => 'Nombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Segundo nombre',
            'id' => 'segundoNombre',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el segundo nombre de la persona usuaria.",
            'spans' => [null, null],
        ],
        [
            'label' => 'Primer apellido',
            'id' => 'primerApellido',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el primer apellido de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Segundo apellido',
            'id' => 'segundoApellido',
            'icon' => 'bi bi-card-text',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el segundo apellido de la persona usuaria.",
            'spans' => [null, null],
        ],
    ];
    $localitationInputs = [
        [
            'label' => 'Provincia',
            'id' => 'Provincia',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe la provincia de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Cantón',
            'id' => 'Canton',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el cantón de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Distrito',
            'id' => 'Distrito',
            'icon' => 'bi bi-geo-alt-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el distrito de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Número teléfono',
            'id' => 'Telefono',
            'icon' => 'bi bi-telephone-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el número telefónico de la persona usuaria.",
            'spans' => [null, null],
        ],
    ];
    $userInputs = [
        [
            'label' => 'Nombre de usuario',
            'id' => 'nombreUsuario',
            'icon' => 'bi bi-key-fill',
            'input' => 'text',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe el nombre de usuario de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Contraseña',
            'id' => 'Contrasennia',
            'icon' => 'bi bi-lock-fill',
            'input' => 'password',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se escribe la contraseña del usuario de la persona usuaria.",
            'spans' => ['Obligatorio', null],
        ],
        [
            'label' => 'Rol',
            'id' => 'Rol',
            'icon' => 'bi bi-person-badge-fill',
            'input' => 'select',
            'onchange' => '',
            'btnHelp' => true,
            'inputInfo' => "Aquí se selecciona el rol del usuario. Si es administrador: puede hacer lo mismo que tú haces en la aplicación, agregar, eliminar, modificar, buscar, entre otras acciones. Si es cliente: entonces solo puede ver y reservar los productos de la tienda.",
            'spans' => ['Obligatorio', null],
            'options' => [
                'Administrador' => 'Administrador',
                'Cliente' => 'Cliente'
            ]
        ],
    ];
    $menuTable = [
        'url' => 'users.php',
        'addMethod' => 'guardarUsuario()',
    ];

    $type = 'usuario';
?>

<div class="w-100 rounded-3 overflow-hidden" style="background-color: #f9fafb;">
    <div class="admin-header-card-bg w-100 px-3 py-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="card-title">Agregar usuario</h4>
            <i class="bi bi-file-earmark-plus fs-4 d-flex align-self-center"></i>
        </div>
    </div>
    <div class="px-3 pb-2">
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Información personal</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($informationInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Localización</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($localitationInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="card rounded-3 overflow-hidden my-2">
            <div class="card-body admin-subheader-card-bg py-1">
                <div class="d-flex align-items-center gap-2">
                    <p class="card-title p-0 m-0">Datos de usuario</p>
                </div>
            </div>
            <div class="row px-3 py-1">
                <?php
                foreach ($userInputs as $input) {
                    include '../src/components/inputs/input.php';
                }
                ?>
            </div>
        </div>
        <div class="container-fluid mt-3 mb-2">
            <?php include '../src/components/forms/dialogButtons.php'; ?>
        </div>
        <input type="hidden" id="Id" value="<?php echo isset($userId) ? $userId : ''; ?>"> 
    </div>
</div>

<?php
    $content = ob_get_clean();
    include 'template.php';
?>

<script>
    $(document).ready(function() {
        if (<?php echo $updateUser ? 'true' : 'false'; ?>) {
            buscarUsuario(<?php echo $userId; ?>);
        }
    });
</script>