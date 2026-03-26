function validarCampos(campos, textosErrores) {
    let errores = [];

    campos.forEach((campo, index) => {
        if (!Array.isArray(campo)) {
            if (!campo.trim()) {
                errores.push(textosErrores[index]);
            }
        } else {
            if (campo.length < 1) {
                errores.push(textosErrores[index]);
            }
        }
        
    });

    if (errores.length > 0) {
        let mensaje = 'Te falta anotar ';
        
        if (errores.length === 1) {
            mensaje += errores[0];
        } else {
            mensaje += errores.slice(0, -1).join(', ') + ' y ' + errores[errores.length - 1];
        }

        alert(
            '¡Error!',
            mensaje,
            'error',
            'Aceptar'
        );
        return false;
    }

    return true;
}