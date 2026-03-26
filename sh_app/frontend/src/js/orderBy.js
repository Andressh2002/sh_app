function ordenar(list, order) {
    return list.sort((a, b) => {
        let fieldA = a[order];
        let fieldB = b[order];
    
        // Manejo especial para fechas
        if (order === 'fecha_registro') {
            fieldA = new Date(fieldA).getTime();
            fieldB = new Date(fieldB).getTime();
        }
    
        // Manejo para números
        if (!isNaN(fieldA) && !isNaN(fieldB)) {
            fieldA = parseFloat(fieldA);
            fieldB = parseFloat(fieldB);
            // Ordenar de mayor a menor
            return fieldB - fieldA; // Descendente
        } else {
            // Manejo para cadenas
            if (typeof fieldA === 'string') {
                fieldA = fieldA.toLowerCase();
            }
            if (typeof fieldB === 'string') {
                fieldB = fieldB.toLowerCase();
            }
    
            // Ordenar de menor a mayor para cadenas
            if (fieldA < fieldB) {
                return -1;
            }
            if (fieldA > fieldB) {
                return 1;
            }
        }
        return 0;
    });
}