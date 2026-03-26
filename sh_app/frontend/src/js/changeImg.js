function cargarImagenGuardada(urlImagen, idInput) {
    const preview = document.querySelector(idInput);
    if (urlImagen) {
        preview.src = urlImagen;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none'; // Ocultar si no hay imagen
    }
}

try {
    document.getElementById('imagen1Producto').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const hiddenImagen1File = document.getElementById('hiddenImagen1Producto');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Mostrar la vista previa de la imagen
                const preview = document.getElementById('vistaImagen1Producto');
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Actualizar el campo oculto con el nuevo contenido de la imagen
                hiddenImagen1File.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Si no se selecciona ningún archivo, ocultar la vista previa
            document.getElementById('vistaImagen1Producto').style.display = 'none';
            hiddenImagen1File.value = ''; // Limpiar el campo oculto si no hay imagen
        }
    });
} catch (error) {
    //
}

try {
    document.getElementById('imagen2Producto').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const hiddenImagen2File = document.getElementById('hiddenImagen2Producto');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Mostrar la vista previa de la imagen
                const preview = document.getElementById('vistaImagen2Producto');
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Actualizar el campo oculto con el nuevo contenido de la imagen
                hiddenImagen2File.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Si no se selecciona ningún archivo, ocultar la vista previa
            document.getElementById('vistaImagen2Producto').style.display = 'none';
            hiddenImagen2File.value = ''; // Limpiar el campo oculto si no hay imagen
        }
    });
} catch (error) {
    //
}

try {
    document.getElementById('imagen3Producto').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const hiddenImagen2File = document.getElementById('hiddenImagen3Producto');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Mostrar la vista previa de la imagen
                const preview = document.getElementById('vistaImagen3Producto');
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Actualizar el campo oculto con el nuevo contenido de la imagen
                hiddenImagen2File.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Si no se selecciona ningún archivo, ocultar la vista previa
            document.getElementById('vistaImagen3Producto').style.display = 'none';
            hiddenImagen2File.value = ''; // Limpiar el campo oculto si no hay imagen
        }
    });
} catch (error) {
    //
}

try {
    document.getElementById('imagenCategoria').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const hiddenImagen1File = document.getElementById('hiddenImagenCategoria');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Mostrar la vista previa de la imagen
                const preview = document.getElementById('vistaImagenCategoria');
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Actualizar el campo oculto con el nuevo contenido de la imagen
                hiddenImagen1File.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Si no se selecciona ningún archivo, ocultar la vista previa
            document.getElementById('vistaImagenCategoria').style.display = 'none';
            hiddenImagen1File.value = ''; // Limpiar el campo oculto si no hay imagen
        }
    });
} catch (error) {
    //
}

try {
    document.getElementById('imagenCarrusel').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const hiddenImagen1File = document.getElementById('hiddenImagenCarrusel');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Mostrar la vista previa de la imagen
                const preview = document.getElementById('vistaImagenCarrusel');
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Actualizar el campo oculto con el nuevo contenido de la imagen
                hiddenImagen1File.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Si no se selecciona ningún archivo, ocultar la vista previa
            document.getElementById('vistaImagenCarrusel').style.display = 'none';
            hiddenImagen1File.value = ''; // Limpiar el campo oculto si no hay imagen
        }
    });
} catch (error) {
    //
}

try {
    document.getElementById('imagenAviso').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const hiddenImagen1File = document.getElementById('hiddenImagenAviso');
    
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Mostrar la vista previa de la imagen
                const preview = document.getElementById('vistaImagenAviso');
                preview.src = e.target.result;
                preview.style.display = 'block';
                
                // Actualizar el campo oculto con el nuevo contenido de la imagen
                hiddenImagen1File.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            // Si no se selecciona ningún archivo, ocultar la vista previa
            document.getElementById('vistaImagenAviso').style.display = 'none';
            hiddenImagen1File.value = ''; // Limpiar el campo oculto si no hay imagen
        }
    });
} catch (error) {
    //
}

