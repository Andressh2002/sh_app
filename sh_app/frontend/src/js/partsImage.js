/*
try {
    // Escuchar el input de archivo
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const image = new Image();
            image.src = e.target.result;
            
            image.onload = function() {
                // Obtener número de partes
                const partes = parseInt(document.getElementById('partes').value);
                // Llamar a la función para cortar la imagen
                const imagenesCortadas = cortarImagen(image, partes);
                mostrarPartes(imagenesCortadas);
            }
        };

        reader.readAsDataURL(file);
    });
} catch (error) {
    //
}


// Función para cortar la imagen en 'n' partes
function cortarImagen(image, partes) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = image.width;
    canvas.height = image.height;

    // Dibujar la imagen completa en el canvas
    ctx.drawImage(image, 0, 0);

    const anchoParte = image.width / partes;
    const partesCanvas = [];

    for (let i = 0; i < partes; i++) {
        const canvasParte = document.createElement('canvas');
        const ctxParte = canvasParte.getContext('2d');
        canvasParte.width = anchoParte;
        canvasParte.height = image.height;

        ctxParte.drawImage(canvas, i * anchoParte, 0, anchoParte, image.height, 0, 0, anchoParte, image.height);
        
        partesCanvas.push(canvasParte);  // Guardar la parte en el array
    }

    return partesCanvas;  // Retorna el array de canvases
}

// Función para mostrar las partes de la imagen en el contenedor
function mostrarPartes(partesCanvas) {
    const container = document.getElementById('imageContainer');
    container.innerHTML = '';  // Limpiar cualquier contenido anterior

    partesCanvas.forEach(canvas => {
        container.appendChild(canvas);  // Mostrar cada parte
    });
}
    */

function mostrarParteColorProducto(index, totalPartes, imagenSrc, canva, result) {
    const canvas = document.getElementById(canva);
    const img = new Image();
    
    img.onload = function() {
        const parteAncho = img.width / totalPartes; // Dividir el ancho
        const ctx = canvas.getContext('2d');
        canvas.width = parteAncho;
        canvas.height = img.height;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, -parteAncho * index, 0); // Cortar la parte correspondiente

        // Convertir el canvas a una imagen y mostrarla
        const imgElement = document.getElementById(result);
        imgElement.src = canvas.toDataURL();
    };

    img.src = imagenSrc; // Cargar la imagen desde la base de datos
}
