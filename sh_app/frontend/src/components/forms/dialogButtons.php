<div class="row justify-content-center">
    <div class="col-auto">
        <button type="button" onclick="<?php echo $menuTable['addMethod']; ?>"
            class="btn-details text-white border-0 rounded-2 px-3 py-2 d-flex gap-2 justify-content-center align-items-center">
            Guardar
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-floppy2-fill d-flex align-self-center" viewBox="0 0 16 16">
                <path d="M12 2h-2v3h2z" />
                <path
                    d="M1.5 0A1.5 1.5 0 0 0 0 1.5v13A1.5 1.5 0 0 0 1.5 16h13a1.5 1.5 0 0 0 1.5-1.5V2.914a1.5 1.5 0 0 0-.44-1.06L14.147.439A1.5 1.5 0 0 0 13.086 0zM4 6a1 1 0 0 1-1-1V1h10v4a1 1 0 0 1-1 1zM3 9h10a1 1 0 0 1 1 1v5H2v-5a1 1 0 0 1 1-1" />
            </svg>
        </button>
    </div>
    <div class="col-auto">
        <button type="button" onclick="location.href='<?php echo $menuTable['url']; ?>'"
            class="btn-delete text-white border-0 rounded-2 px-3 py-2 d-flex gap-2 justify-content-center align-items-center">
            Retroceder<i class="bi bi-backspace-fill d-flex align-self-center"></i>
        </button>
    </div>
</div>