<?php
    /* Modal de confirmación */
    $modalConfirmacion = [
        'id' => 'modalConfirmacion',
        'title' => 'Confirmar acción',
        'icon' => 'bi bi-exclamation-triangle-fill',
        'size' => 'modal-md',
        'variant' => 'warning',
        'keyboard' => true,
        'btn_close' => false,

        'body' => '
            <div class="text-center py-3">

                <i
                    id="modalConfirmacionIcon"
                    class="bi bi-exclamation-triangle-fill fs-1 text-warning"
                ></i>

                <h5
                    id="modalConfirmacionTitulo"
                    class="mt-3"
                >
                    ¿Estás seguro?
                </h5>

                <p
                    id="modalConfirmacionTexto"
                    class="text-muted mb-0"
                >
                    Esta acción no se puede revertir.
                </p>

            </div>
        ',

        'buttons' => [
            [
                'text' => 'Cancelar',
                'icon' => 'bi bi-x-circle',
                'class' => 'store-btn-secondary',
                'dismiss' => true,
            ],
            [
                'text' => 'Confirmar',
                'icon' => 'bi bi-check-circle-fill',
                'class' => 'store-filter-btn',
                'onclick' => 'ejecutarConfirmacionModal()',
            ]
        ]
    ];

    $modal = $modalConfirmacion;
    include '../src/components/modal/modal.php';


    /* Modal para cambiar contraseña */
    $modalCambiarContrasennia = [
        'id' => 'modalCambiarContrasennia',
        'title' => 'Cambiar contraseña',
        'icon' => 'bi bi-key-fill',
        'size' => 'modal-md',
        'keyboard' => true,

        'body' => '

            <div class="store-modal-body">

                <div class="filter-card px-4 px-sm-5">
                    <p class="filter-title">
                        <i class="bi bi-shield-lock-fill"></i>
                        Contraseña actual
                    </p>
                    <input
                        type="password"
                        class="form-control filter-input"
                        id="ContrasenniaActual"
                        placeholder="Ingrese su contraseña actual"
                    />
                </div>

                <div class="filter-card px-4 px-sm-5">
                    <p class="filter-title">
                        <i class="bi bi-shield-lock-fill"></i>
                        Nueva contraseña
                    </p>
                    <input
                        type="password"
                        class="form-control filter-input"
                        id="ContrasenniaNueva"
                        placeholder="Ingrese la nueva contraseña"
                    />
                </div>

                <div class="filter-card px-4 px-sm-5">
                    <p class="filter-title">
                        <i class="bi bi-shield-lock-fill"></i>
                        Confirmar contraseña
                    </p>
                    <input
                        type="password"
                        class="form-control filter-input"
                        id="ContrasenniaConfirmar"
                        placeholder="Confirme la nueva contraseña"
                    />
                </div>

            </div>
        ',

        'buttons' => [

            [
                'text' => 'Cancelar',
                'icon' => 'bi bi-x-circle',
                'class' => 'store-btn-secondary',
                'dismiss' => true,
            ],

            [
                'text' => 'Actualizar',
                'icon' => 'bi bi-check-circle-fill',
                'class' => 'store-filter-btn',
                'onclick' => 'cambiarContrasenniaUsuario()',
            ]
        ]
    ];

    $modal = $modalCambiarContrasennia;
    include '../src/components/modal/modal.php';


    /* Modal para cambiar contraseña */
    $modalCambiarContrasenniaAdmin = [
        'id' => 'modalCambiarContrasenniaAdmin',
        'title' => 'Cambiar contraseña',
        'icon' => 'bi bi-key-fill',
        'size' => 'modal-md',
        'keyboard' => true,

        'body' => '

            <div class="store-modal-body">

                <div class="filter-card px-4 px-sm-5">
                    <p class="filter-title">
                        <i class="bi bi-shield-lock-fill"></i>
                        Nueva contraseña
                    </p>
                    <input
                        type="password"
                        class="form-control filter-input"
                        id="ContrasenniaNuevaAdmin"
                        placeholder="Ingrese la nueva contraseña"
                    />
                </div>

                <div class="filter-card px-4 px-sm-5">
                    <p class="filter-title">
                        <i class="bi bi-shield-lock-fill"></i>
                        Confirmar contraseña
                    </p>
                    <input
                        type="password"
                        class="form-control filter-input"
                        id="ContrasenniaConfirmarAdmin"
                        placeholder="Confirme la nueva contraseña"
                    />
                </div>

            </div>
        ',

        'buttons' => [

            [
                'text' => 'Cancelar',
                'icon' => 'bi bi-x-circle',
                'class' => 'store-btn-secondary',
                'dismiss' => true,
            ],

            [
                'text' => 'Actualizar',
                'icon' => 'bi bi-check-circle-fill',
                'class' => 'store-filter-btn',
                'onclick' => 'cambiarContrasenniaAdmin()',
            ]
        ]
    ];

    $modal = $modalCambiarContrasenniaAdmin;
    include '../src/components/modal/modal.php';


    /* Modal para actualizar el progreso de avance de un pedido */
    $modalActualizarProgreso = [
        'id' => 'modalActualizarProgreso',
        'title' => 'Actualizar progreso',
        'icon' => 'bi bi-key-fill',
        'size' => 'modal-md',
        'keyboard' => true,

        'body' => '

            <div class="store-modal-body">

                <div class="filter-card px-4 px-sm-5">

                    <p class="filter-title">

                        <i class="bi bi-percent"></i>

                        Avance actual:
                        <span
                            id="texto-progreso"
                            class="fw-bold"
                        >
                            0%
                        </span>

                    </p>

                    <div class="input-group">

                        <input
                            type="range"
                            class="form-range"
                            min="0"
                            max="100"
                            step="1"
                            id="progreso"
                        >

                    </div>

                </div>

            </div>
        ',

        'buttons' => [

            [
                'text' => 'Cancelar',
                'icon' => 'bi bi-x-circle',
                'class' => 'store-btn-secondary',
                'dismiss' => true,
            ],

            [
                'text' => 'Actualizar',
                'icon' => 'bi bi-check-circle-fill',
                'class' => 'store-filter-btn',
                'onclick' => 'actualizarProgresoPedido()',
            ]
        ]
    ];

    $modal = $modalActualizarProgreso;
    include '../src/components/modal/modal.php';
    

    /* Modal de guardando */
    $modalGuardando = [
        'id' => 'modalGuardando',
        'title' => 'Guardando...',
        'icon' => 'bi bi-wifi',
        'size' => 'modal-sm',
        'variant' => 'success',
        'backdrop' => 'static',
        'keyboard' => false,
        'btn_close' => false,

        'body' => '
            <div class="text-center py-4">
                <i class="bi bi-wifi fs-1"></i>

                <p class="text-muted">
                    Espere un momento...
                </p>
            </div>
        ',
    ];
    $modal = $modalGuardando;
    include '../src/components/modal/modal.php';


    /* Modal de iniciando sesión */
    $modalLogueando = [
        'id' => 'modalLogueando',
        'title' => 'Iniciando sesión',
        'icon' => 'bi bi-wifi',
        'size' => 'modal-sm',
        'variant' => 'success',
        'backdrop' => 'static',
        'keyboard' => false,
        'btn_close' => false,

        'body' => '
            <div class="text-center py-4">
                <i class="bi bi-wifi fs-1"></i>

                <p class="text-muted">
                    Espere, estamos tratando de iniciar sesión...
                </p>
            </div>
        ',
    ];
    $modal = $modalLogueando;
    include '../src/components/modal/modal.php';


    /* Modal de validación de campos */
    $modalValidacion = [
        'id' => 'modalValidacion',
        'title' => 'Advertencia',
        'icon' => 'bi bi-exclamation-triangle',
        'size' => 'modal-md',
        'variant' => 'success',
        'keyboard' => true,
        'btn_close' => true,

        'body' => '
            <div class="text-center py-4">
                <i class="bi bi-exclamation-triangle fs-1"></i>

                <p class="text-muted">
                    ...
                </p>
            </div>
        ',
    ];
    $modal = $modalValidacion;
    include '../src/components/modal/modal.php';


    /* Modal para buscar paletas */
    $modalColors = [
        'id' => 'modalColors',
        'title' => 'Seleccionar paletas',
        'icon' => 'bi bi-palette-fill',
        'size' => 'modal-xl',
        'body' => '
            <div class="row mb-3">
                <div class="col-md-6">
                    <input
                        id="NombreColorModal"
                        class="form-control filter-input"
                        placeholder="Buscar nombre"
                    >
                </div>
                <div class="col-md-4">
                    <input
                        id="FamiliaColorModal"
                        class="form-control filter-input"
                        placeholder="Buscar familia"
                    >
                </div>
            </div>
            <div class="table-responsive" id="colors-data-container"></div>
        '
    ];
    $modal = $modalColors;
    include '../src/components/modal/modal.php';


    /* Modal para buscar descuentos */
    $modalDiscounts = [
        'id' => 'modalDiscounts',
        'title' => 'Seleccionar descuentos',
        'icon' => 'bi bi-percent',
        'size' => 'modal-xl',

        'body' => '
            <div class="row">
                <div class="col-6 mb-3">
                    <input
                        type="text"
                        id="NombreDescuentoModal"
                        class="form-control filter-input"
                        placeholder="Buscar descuento"
                    >
                </div>
                <div id="discounts-data-container"
                    class="discount-grid">
                </div>

            </div>
        '
    ];
    $modal = $modalDiscounts;
    include '../src/components/modal/modal.php';
?>
