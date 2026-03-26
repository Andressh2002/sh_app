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
    function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
    }

    function getMaxDays(month, year = new Date().getFullYear()) {
        const daysInMonth = [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        return daysInMonth[month - 1] || 31; // default to 31 in case of invalid month
    }

    function adjustDayField(dayField, monthField) {
        const month = parseInt(monthField.value, 10);
        const maxDays = getMaxDays(month);
        let dayValue = parseInt(dayField.value, 10);

        if (dayValue > maxDays) {
            dayField.value = maxDays; // Ajustar al máximo permitido si excede
        } else if (dayValue < 1 || isNaN(dayValue)) {
            dayField.value = 1; // No permitir días menores a 1
        }
    }

    function updateYearIndicator(startMonth, endMonth) {
        const startMonthValue = parseInt(startMonth.value, 10);
        const endMonthValue = parseInt(endMonth.value, 10);
        const yearIndicator = document.getElementById("yearIndicator");

        if (endMonthValue < startMonthValue) {
            yearIndicator.textContent = "El rango seleccionado finaliza hasta el año siguiente.";
        } else {
            yearIndicator.textContent = ""; // Limpiar mensaje si no aplica
        }
    }

    const dayIds = ["DayStartDate", "DayEndDate"];
    const monthIds = ["MonthStartDate", "MonthEndDate"];

    // Eventos para los campos de días
    dayIds.forEach(id => {
        const dayElement = document.getElementById(id);
        dayElement.addEventListener("input", function () {
            const monthField = document.getElementById(id.includes("Start") ? "MonthStartDate" : "MonthEndDate");
            adjustDayField(dayElement, monthField); // Ajustar día basado en el mes seleccionado
        });
    });

    // Eventos para los campos de meses (ahora con select)
    monthIds.forEach(id => {
        const monthElement = document.getElementById(id);
        monthElement.addEventListener("change", function () {
            const dayField = document.getElementById(id.includes("Start") ? "DayStartDate" : "DayEndDate");
            const startMonth = document.getElementById("MonthStartDate");
            const endMonth = document.getElementById("MonthEndDate");

            adjustDayField(dayField, monthElement); // Ajustar día al cambiar el mes
            updateYearIndicator(startMonth, endMonth); // Actualizar mensaje del año
        });
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