<?php
    include '../src/components/login/access.php';
    $pageTitle = "Apps descargables";
    
    ob_start();
    $showHeader = true;
    $showNavbar = true;
    $showFooter = true;
    $showSidebar = false;
?>

<div class="container-fluid downloads-grid">

    <!-- MOBILE -->
    <div class="download-card">

        <div class="download-icon mobile">
            <i class="bi bi-phone-fill"></i>
        </div>

        <h3>SH APP Mobile</h3>

        <p class="download-description">
            Lleva tus pedidos y colecciones
            desde cualquier lugar.
        </p>

        <div class="download-info mb-4">

            <div>
                <span>Versión</span>
                <strong>v1.0.1</strong>
            </div>

            <div>
                <span>Compatibilidad</span>
                <p class="fw-bolder p-0 m-0">
                    Android 5.0+ (API 21+)
                </p>
            </div>

            <div>
                <span>Tamaño</span>
                <strong>9.33 MB</strong>
            </div>

        </div>

        <a
            href="https://drive.google.com/file/d/1tcstZN4Xm0ev82XkpFsxXYS7lHmY5p5q/view?usp=sharing"
            target="_blank"
            class="store-filter-btn download-btn text-decoration-none"
        >
            <i class="bi bi-download"></i>
            <span>Descargar APK</span>
        </a>

    </div>

    <!-- DESKTOP -->
    <div class="download-card">

        <div class="download-icon desktop">
            <i class="bi bi-laptop-fill"></i>
        </div>

        <h3>SH APP Desktop</h3>

        <p class="download-description">
            La experiencia completa de SH APP
            para escritorio.
        </p>

        <div class="download-info mb-4">

            <div>
                <span>Versión</span>
                <strong>v1.0.0</strong>
            </div>

            <div>
                <span>Compatibilidad</span>
                <p class="fw-bolder p-0 m-0">
                    Windows 11
                </p>
                <p class="fw-bolder p-0 m-0">
                    Windows 10 (64 bits)
                </p>
            </div>

            <div>
                <span>Tamaño</span>
                <strong>91.8 MB</strong>
            </div>

        </div>

        <a
            href="https://drive.google.com/file/d/1yJTdm2xZuJywmnbpqyQNvjtiABWP93we/view?usp=sharing"
            target="_blank"
            class="store-filter-btn download-btn text-decoration-none"
        >
            <i class="bi bi-download"></i>
            <span>Descargar instalador</span>
        </a>

    </div>

</div>

<?php
    $content = ob_get_clean();

    include 'template.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        seleccionarAvisosCliente('');
    });
</script>