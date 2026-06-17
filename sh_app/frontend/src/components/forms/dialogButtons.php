<div class="row justify-content-center">
    <div class="col-auto">
        <div class="navbar-btn-shadow">
            <button
                onclick="<?php echo $menuTable['addMethod']; ?>"
                class="store-filter-btn slide_from_left text-decoration-none"
            >
                <i class="bi bi-floppy-fill"></i>
                <span>Guardar</span>
            </button>
        </div>
    </div>
    <div class="col-auto">
        <div class="navbar-btn-shadow">
            <button
                onclick="location.href='<?php echo $menuTable['url']; ?>'"
                class="store-btn-secondary slide_from_left text-decoration-none"
            >
                <i class="bi bi-backspace-fill"></i>
                <span>Retroceder</span>
            </button>
        </div>
    </div>
</div>