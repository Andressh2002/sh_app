<?php
include '../src/components/login/access.php';
checkAccess('Administrador');

ob_start();

$pageTitle = "Configuración";
$pageIcon = "bi-gear-fill";

$showHeader = false;
$showNavbar = false;
$showFooter = false;
$showSidebar = true;

$configurationList = [
    [
        'type' => 'Configuración rápida',
        'title' => 'Modo mantenimiento',
        'image' => '../src/img/app/no_image.png',

        'body' => '
            <div>
                <span>El modo mantenimiento está actualmente:</span>
                <strong>
                    <span
                        id="estado-mantenimiento"
                        class="text-danger"
                    >
                        Cargando...
                    </span>
                </strong>
            </div>
        ',

        'description' =>
            'Este modo impide que los clientes puedan acceder a SH APP. Únicamente los administradores podrán ingresar.',

        'options' => [
            [
                'text' => 'Cambiar',
                'icon' => 'bi bi-arrow-repeat',
                'function' => 'cambiarModoMantenimiento()'
            ]
        ]
    ],

    [
        'type' => 'Configuración avanzada',
        'title' => 'Carrusel de la tienda',
        'image' => '../src/img/app/no_image.png',

        'body' =>
            '<span>Gestiona las imágenes que aparecen en la portada de la tienda.</span>',

        'description' =>
            'Permite agregar, eliminar y cambiar el orden de las imágenes del carrusel.',

        'options' => [
            [
                'text' => 'Administrar',
                'icon' => 'bi bi-images',
                'function' => "location.href='configurateSlides.php'"
            ]
        ]
    ]
];
?>

<div class="container-fluid px-0">

    <?php foreach ($configurationList as $item): ?>

        <div class="mb-4">
            <div class="product-admin-card">

                <div class="product-admin-header">
                    <div>

                        <?php if (!empty($item['type'])): ?>
                            <p class="product-number">
                                <?= $item['type']; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($item['title'])): ?>
                            <h5 class="product-title">
                                <?= $item['title']; ?>
                            </h5>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="product-admin-body">

                    <div class="product-admin-image">
                        <img
                            class="product-image"
                            src="<?= $item['image']; ?>"
                            alt="<?= $item['title']; ?>"
                        >
                    </div>

                    <div class="product-info">
                        <div class="product-info-grid">

                            <?php
                            if (!empty($item['body'])) {
                                echo $item['body'];
                            }
                            ?>

                            <?php if (!empty($item['description'])): ?>
                                <div>
                                    <span class="text-secondary">
                                        <?= $item['description']; ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="order-actions">

                        <?php foreach ($item['options'] as $option): ?>

                            <button
                                type="button"
                                class="
                                    store-filter-btn
                                    px-4
                                    justify-content-center
                                    text-decoration-none
                                "
                                onclick="<?= $option['function']; ?>"
                            >

                                <?php if (!empty($option['icon'])): ?>
                                    <i class="<?= $option['icon']; ?>"></i>
                                <?php endif; ?>

                                <?= $option['text']; ?>

                            </button>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>
        </div>

    <?php endforeach; ?>

</div>

<?php
$content = ob_get_clean();

include 'template.php';
?>

<script>
$(function () {
    buscarConfiguracionModoMantenimiento();
});

function abrirConfiguracionCarrusel() {
    console.log("Abrir configuración del carrusel");
}
</script>