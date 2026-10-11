document.addEventListener('DOMContentLoaded', function () {

    const formulario = document.getElementById('formAnularCompra');
    if (!formulario) return;

    const seleccionarTodo = document.getElementById('seleccionarTodo');
    const checks = Array.from(document.querySelectorAll('.check-producto:not(:disabled)'));
    const motivo = document.getElementById('motivoAnulacion');
    const errorMotivo = document.getElementById('errorMotivoAnulacion');
    const resumenSeleccion = document.getElementById('resumenSeleccion');
    const resumenMonto = document.getElementById('resumenMonto');
    const botonConfirmar = document.getElementById('botonConfirmarAnulacion');

    // Si ya no queda ningún producto vigente, no hay nada que seleccionar
    if (checks.length === 0) {
        seleccionarTodo.disabled = true;
    }

    function actualizar() {
        const marcados = checks.filter(function (c) { return c.checked; });

        let monto = 0;
        marcados.forEach(function (c) { monto += Number(c.dataset.subtotal) || 0; });

        resumenSeleccion.textContent = marcados.length === 0
            ? 'Ningún producto seleccionado'
            : marcados.length + ' producto(s) seleccionado(s)';
        resumenMonto.textContent = 'S/ ' + monto.toFixed(2);

        seleccionarTodo.checked = checks.length > 0 && marcados.length === checks.length;

        botonConfirmar.disabled = !(marcados.length > 0 && motivo.value.trim() !== '');
    }

    seleccionarTodo.addEventListener('change', function () {
        checks.forEach(function (c) { c.checked = seleccionarTodo.checked; });
        actualizar();
    });

    checks.forEach(function (c) { c.addEventListener('change', actualizar); });

    motivo.addEventListener('input', function () {
        errorMotivo.textContent = motivo.value.trim() === '' ? 'El motivo es obligatorio.' : '';
        actualizar();
    });

    formulario.addEventListener('submit', function (evento) {
        if (botonConfirmar.disabled) {
            evento.preventDefault();
            return;
        }
        botonConfirmar.disabled = true; // evita doble envío
        botonConfirmar.textContent = 'Cancelando...';
    });

    actualizar();
});
