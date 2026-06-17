try {
    const ids = ["Precio", "Altura", "Anchura", "Peso", "precioInicial", "precioFinal"]; 

    ids.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener("input", function () {
                this.value = this.value.replace(/[^0123456789.,]/g, "");
            });
        }
    });
} catch (error) {
    console.error("Error:", error);
}

try {
    $('#Comida').change(function () {
        if ($(this).is(':checked')) {
          $('#input-col-Accesorio, #input-col-Tiempo, #col-container-colors').addClass('d-none');
        } else {
            $('#input-col-Accesorio, #input-col-Tiempo, #col-container-colors').removeClass('d-none');
        }
    });
} catch (error) {
    //
}

try {
    $('#Existencia').change(function () {
        if ($(this).is(':checked')) {
          $('#input-col-Festividad').addClass('d-none');
        } else {
            $('#input-col-Festividad').removeClass('d-none');
        }
    });
} catch (error) {
    //
}