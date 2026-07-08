let slidesCarrusel = [];

/* ===========================
 * Carga
 * =========================== */

async function cargarCarrusel() {
    $("#carrousel-images-list").empty();

    try {
        const response = await $.ajax({
            url: backend + urlCarousel,
            type: "POST",
            dataType: "json",
            data: {
                accion: "listar"
            }
        });

        slidesCarrusel = response;
        renderCarrusel();
    } catch (error) {
        console.error(error);
    }

    inicializarImagenesCarrusel();
}

/* ===========================
 * Render
 * =========================== */

function renderCarrusel() {
    actualizarOrden();

    const container = $("#carrousel-images-list");
    container.empty();

    slidesCarrusel.forEach((slide, index) => {

        if (slide.estado == 0) {
            return;
        }

        container.append(
            renderSlide(
                slide,
                index
            )
        );

    });

    inicializarImagenesCarrusel();
}

function renderSlide(slide, index) {
    const total = slidesCarrusel.filter(slide => slide.estado == 1).length;

    return `
        <div
            class="product-admin-card my-2"
            data-index="${index}"
        >
            <div class="product-admin-header">
                <div>
                    <p class="product-number">
                        Slide ${index + 1}
                    </p>
                    <h5 class="product-title">
                        ${slide.id ? "Slide existente" : "Nuevo slide"}
                    </h5>
                </div>
            </div>
            <div class="product-admin-body px-4">
                <div class="admin-image-upload">

                    <input
                        type="file"
                        class="form-control filter-input image-preview-input"
                        id="slide-img-preview${index}"
                        data-preview="vista${index}"
                        data-hidden="hidden${index}"
                    >

                    <div class="admin-image-preview">

                        <img
                            id="vista${index}"
                            src=""
                            alt=""
                            style="display:none;"
                        >

                    </div>

                    <input
                        type="hidden"
                        id="hidden${index}"
                    >

                </div>
                <div class="product-info">
                    <div class="product-info-grid">
                        <div class="filter-card admin-input-card px-4 px-sm-5">
                            <label>URL</label>
                            <input
                                type="text"
                                class="form-control filter-input"
                                value="${slide.url ?? ''}"
                                oninput="cambiarURL(${index}, this.value)"
                            >
                            <small class="admin-input-help">Si escribe algo como "store.php" o "product.php?id=15" entoces navega en la misma pestaña; pero si se escribe "https://facebook.com/...", o "https://instagram.com/..." entonces abre una nueva pestaña</small>
                        </div>
                        <div class="filter-card admin-input-card px-4 px-sm-5">
                            <label>Fecha límite</label>
                            <input
                                type="date"
                                class="form-control filter-input"
                                value="${slide.fecha_limite ?? ''}"
                                onchange="cambiarFecha(${index}, this.value)"
                            >
                            <small class="admin-input-help">Si no se digita una fecha, la imagen queda permanente en el carrusel.</small>
                            <small class="admin-input-help">Si se digita fecha, entonces la hora automáticamente será 23:59.</small>
                        </div>
                    </div>
                </div>

                <div class="order-actions">
                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="moverSlideArriba(${index})"
                        ${index === total + 1 ? "disabled" : ""}
                    >
                        <i class="bi bi-arrow-up"></i>
                        Mover hacia arriba
                    </button>
                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="moverSlideAbajo(${index})"
                        ${index === total - 1 ? "disabled" : ""}
                    >
                        <i class="bi bi-arrow-down"></i>
                        Mover hacia abajo
                    </button>
                    <button
                        class="store-filter-btn px-4 justify-content-center"
                        onclick="eliminarSlide(${index})"
                    >
                        <i class="bi bi-trash3-fill"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
    
}

/* ===========================
 * Orden
 * =========================== */

function moverSlideArriba(index) {
    if (index === 0) return;

    [slidesCarrusel[index], slidesCarrusel[index - 1]] = [
        slidesCarrusel[index - 1],
        slidesCarrusel[index]
    ];
    slidesCarrusel[index].modificado = true;
    slidesCarrusel[index - 1].modificado = true;

    renderCarrusel();
}

function moverSlideAbajo(index) {
    if (index >= slidesCarrusel.length - 1) return;

    [slidesCarrusel[index], slidesCarrusel[index + 1]] = [
        slidesCarrusel[index + 1],
        slidesCarrusel[index]
    ];
    slidesCarrusel[index].modificado = true;
    slidesCarrusel[index + 1].modificado = true;

    renderCarrusel();
}

/* ===========================
 * CRUD local
 * =========================== */

function agregarSlide() {
    slidesCarrusel.push({
        id: null,
        orden: slidesCarrusel.length + 1,
        imagen: "",
        url: "",
        fecha_limite: "",
        estado: 1,
        fecha_registro: null,
        modificado: true,
    });
    renderCarrusel();
}

function eliminarSlide(index){
    slidesCarrusel[index].estado = 0;
    slidesCarrusel[index].modificado = true;
    renderCarrusel();
}

/* ===========================
 * Edición
 * =========================== */

function cambiarURL(index, valor) {
    slidesCarrusel[index].url = valor;
    slidesCarrusel[index].modificado = true;
}

function cambiarFecha(index, valor) {
    slidesCarrusel[index].fecha_limite = valor;
    slidesCarrusel[index].modificado = true;
}

function cambiarImagen(index, imagen) {
    slidesCarrusel[index].imagen = imagen;
    slidesCarrusel[index].modificado = true;
}

/* ===========================
 * Guardar
 * =========================== */

async function guardarCarrusel() {
    abrirModal('modalGuardando');
    cambiarMensajeModal("#modalGuardando", "Guardando...", 'Espere un momento...', "bi bi-wifi", false);

    const validacion = validarCarrusel();

    if (!validacion.ok) {
        cambiarMensajeModal(
            "#modalGuardando",
            "Error",
            validacion.mensaje,
            "bi bi-x-circle",
            true
        );
        return;
    }

    try {
        const slidesGuardar = slidesCarrusel.map(slide => ({
            id: slide.id,
            orden: slide.orden,
            url: slide.url,
            fecha_limite: slide.fecha_limite,
            estado: slide.estado,
            modificado: slide.modificado
        }));
        
        const response = await $.ajax({
            url: backend + urlCarousel,
            type: "POST",
            dataType: "json",
            data: {
                accion: "guardar",
                slides: JSON.stringify(slidesGuardar)
            }
        });

        response.slides.forEach((slide,index)=>{

            slidesCarrusel[index].id = slide.id;
            slidesCarrusel[index].fecha_registro = slide.fecha_registro;

        });

        // Guardar únicamente las imágenes
        for(const slide of slidesCarrusel){

            if(!slide.modificado){
                continue;
            }

            if(!slide.imagen){
                continue;
            }

            await guardarImagen(
                slide.id,
                slide.imagen
            );

        }

        cambiarMensajeModal(
            "#modalGuardando",
            response.title,
            response.text,
            response.icon,
            true
        );
    } catch (error) {
        cambiarMensajeModal(
            "#modalGuardando",
            "Error",
            "No fue posible guardar el carrusel.",
            "bi bi-x-circle",
            true
        );
    }
}

function guardarImagen(id, imagen){
    return new Promise((resolve,reject)=>{
        $.ajax({
            url: backend + urlCarousel,
            type: "POST",
            data:{
                accion:"insertarImagen",
                id:id,
                imagen:imagen
            },

            success:function(response){
                const data =
                    typeof response==="string"
                    ? JSON.parse(response)
                    : response;

                if(data.icon=="bi bi-check-circle"){
                    resolve();
                }else{
                    reject(data.text);
                }
            },

            error:function(){
                reject();
            }
        });
    });
}

function validarCarrusel(){
    for(const slide of slidesCarrusel){
        if(slide.estado==0){
            continue;
        }

        if(!slide.imagen){
            return{
                ok:false,
                mensaje:"Todos los slides deben tener una imagen."
            };
        }
    }

    return{
        ok:true
    };
}

function actualizarOrden() {
    let orden = 1;
    slidesCarrusel.forEach(slide => {
        if (slide.estado == 0) {
            return;
        }
        slide.orden = orden++;
    });
}

function inicializarImagenesCarrusel(){

    document
        .querySelectorAll(".image-preview-input")
        .forEach(input=>{

            input.onchange = function(e){

                const file = e.target.files[0];

                if(!file){
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event){

                    const index = input.id.replace("slide-img-preview","");

                    slidesCarrusel[index].imagen = event.target.result;
                    slidesCarrusel[index].modificado = true;

                    const preview = document.getElementById(
                        input.dataset.preview
                    );

                    const hidden = document.getElementById(
                        input.dataset.hidden
                    );

                    preview.src = event.target.result;
                    preview.style.display = "block";

                    hidden.value = event.target.result;

                };

                reader.readAsDataURL(file);

            };

        });

    cargarImagenesGuardadas();

}

function cargarImagenesGuardadas(){

    slidesCarrusel.forEach((slide, index) => {

        if (slide.estado == 0) {
            return;
        }

        cargarImagenGuardada(
            slide.imagen,
            "#vista" + index
        );

        document.getElementById("hidden" + index).value =
            slide.imagen ?? "";

    });

}

function cargarImagenGuardada(urlImagen, idInput) {
    const preview = document.querySelector(idInput);

    if (urlImagen) {
        preview.src = urlImagen;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

async function obtenerCarrusel(){
    try{
        const slides = await $.ajax({
            url: backend + urlCarousel,
            type: "POST",
            dataType: "json",
            data:{
                accion:"listar"
            }
        });
        renderCarruselTienda(slides);
    }catch(error){
        console.error(error);
    }
}

function renderCarruselTienda(slides){
    const hoy = new Date();

    slides = slides.filter(slide=>{
        if(slide.estado != 1){
            return false;
        }

        if(!slide.fecha_limite){
            return true;
        }

        return new Date(slide.fecha_limite + " 23:59:59") >= hoy;
    });

    construirCarrusel(slides);
}

function construirCarrusel(slides){
    const indicadores = $("#carousel-indicators");
    const contenido = $("#carousel-inner");

    indicadores.empty();
    contenido.empty();

    slides.forEach((slide,index)=>{
        indicadores.append(`
            <button
                type="button"
                data-bs-target="#carouselExampleIndicators"
                data-bs-slide-to="${index}"
                class="${index==0?"active":""}">
            </button>
        `);

        contenido.append(`
            <div class="carousel-item ${index==0?"active":""}">
                ${crearImagenCarrusel(slide)}
            </div>
        `);
    });
}

function crearImagenCarrusel(slide) {
    const clase = slide.url
        ? "carousel-clickable"
        : "";

    return `
        <img
            src="${slide.imagen}"
            class="d-block w-100 ${clase}"
            alt="Carrusel"
            ${slide.url ? `onclick="abrirSlide('${slide.url.replace(/'/g, "\\'")}')"` : ""}
        >
    `;
}

function abrirSlide(url) {

    if (!url) {
        return;
    }

    url = url.trim();

    // URL externa sin protocolo
    if (
        url.startsWith("www.")
    ) {
        url = "https://" + url;
    }

    // URL externa
    if (/^https?:\/\//i.test(url)) {
        window.open(url, "_blank", "noopener");
        return;
    }

    // URL interna
    window.location.href = url;
}