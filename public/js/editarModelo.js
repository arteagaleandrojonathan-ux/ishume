document.addEventListener("DOMContentLoaded", function () {

    // Campos del formulario
    const precio = document.getElementById("Precio");
    const horizontal = document.getElementById("TamañoHorizontal");
    const vertical = document.getElementById("TamañoVertical");

    // Mensajes de error
    const errorPrecio = document.getElementById("errorPrecio");
    const errorHorizontal = document.getElementById("errorHorizontal");
    const errorVertical = document.getElementById("errorVertical");

    // Botón guardar
    const botonGuardar = document.querySelector(".boton-guardar");
     

    function validarNombreMaterial() {
        const valor = nombreMaterial.value.trim();

        if (valor === "") {

            errorNombreMaterial.textContent =
                "El nombre del material es obligatorio.";

            return false;
        }
        if (/[0-9]/.test(valor)) {

            errorNombreMaterial.textContent =
                "El nombre del material no debe contener números.";

            return false;
        }
        errorNombreMaterial.textContent = "";
        return true;
    }


    function validarColor() {
        const valor = color.value.trim();

        if (valor === "") {

            errorColor.textContent =
                "El color es obligatorio.";

            return false;
        }

        if (/[0-9]/.test(valor)) {

            errorColor.textContent =
                "El color no debe contener números.";
            return false;
        }
        errorColor.textContent = "";
        return true;
    }


    function validarPrecio() {
        const valor = Number(precio.value);

        if (precio.value === "") {
            errorPrecio.textContent = "El precio es obligatorio.";
            return false;
        }

        if (valor <= 0) {
            errorPrecio.textContent = "El precio debe ser mayor que 0.";
            return false;
        }

        errorPrecio.textContent = "";
        return true;
    }


   
    function validarHorizontal() {
        const valor = Number(horizontal.value);

        if (horizontal.value === "") {
            errorHorizontal.textContent = "El ancho es obligatorio.";
            return false;
        }

        if (valor <= 0) {
            errorHorizontal.textContent = "El ancho debe ser mayor que 0.";
            return false;
        }

        errorHorizontal.textContent = "";
        return true;
    }


    function validarVertical() {
        const valor = Number(vertical.value);
        if (vertical.value === "") {
            errorVertical.textContent = "El largo es obligatorio.";
            return false;
        }

        if (valor <= 0) {
            errorVertical.textContent = "El largo debe ser mayor que 0.";
            return false;
        }
        errorVertical.textContent = "";
        return true;
    }


    
    function validarFormulario() {
        const precioValido = validarPrecio();
        const horizontalValido = validarHorizontal();
        const verticalValido = validarVertical();

        const formularioValido =
            precioValido &&
            horizontalValido &&
            verticalValido;

        // Activar/desactivar botón
        botonGuardar.disabled = !formularioValido;
    }


    precio.addEventListener("input", validarFormulario);
    horizontal.addEventListener("input", validarFormulario);
    vertical.addEventListener("input", validarFormulario);


    validarFormulario();

});