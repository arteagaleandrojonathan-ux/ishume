document.addEventListener('DOMContentLoaded', function () {

    const botonesBaja = document.querySelectorAll('.boton-baja');
    const modal = document.getElementById('modalBaja');
    const cerrarModal = document.getElementById('cerrarModalBaja');
    const cancelarBaja = document.getElementById('cancelarBaja');

    const idModeloInput = document.getElementById('idModeloBaja');
    const nombreModelo = document.getElementById('nombreModeloBaja');
    const stockActual = document.getElementById('stockActualBaja');

    const cantidadInput = document.getElementById('cantidadBaja');
    const motivoInput = document.getElementById('motivoBaja');

    const errorCantidad = document.getElementById('errorCantidadBaja');
    const errorMotivo = document.getElementById('errorMotivoBaja');

    botonesBaja.forEach(function (boton) {

        boton.addEventListener('click', function () {

            const idModelo = this.dataset.id;
            const nombre = this.dataset.nombre;
            const stock = this.dataset.stock;

            idModeloInput.value = idModelo;
            nombreModelo.textContent = nombre;
            stockActual.textContent = stock;

            cantidadInput.value = '';
            motivoInput.value = '';

            errorCantidad.textContent = '';
            errorMotivo.textContent = '';

            modal.classList.add('mostrar');

        });

    });

    function cerrar() {
        modal.classList.remove('mostrar');
    }

    cerrarModal.addEventListener('click', cerrar);
    cancelarBaja.addEventListener('click', cerrar);

    modal.addEventListener('click', function (evento) {

        if (evento.target === modal) {
            cerrar();
        }

    });

});