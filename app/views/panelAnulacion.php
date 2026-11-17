<?php $esVenta = $vista === 'ventas'; ?>
<dialog id="panel-anulacion" class="panel-anulacion" aria-labelledby="titulo-anulacion">
    <form method="post" action="index.php?opcion=<?= $esVenta ? 'anularVentas' : 'cancelarContratoSinVenta' ?>">
        <h2 id="titulo-anulacion"><?= $esVenta ? 'Anular venta' : 'Cancelar contrato' ?></h2>
        <p>Registro <strong id="codigo-anulacion"></strong></p>
        <input type="hidden" name="<?= $esVenta ? 'idVenta' : 'idContrato' ?>" id="registro-anulacion">

        <?php if ($esVenta): ?>
            <p>El contrato también se cancelará y los pagos quedarán en el historial.</p>
            <label for="motivo-anulacion">Motivo de la anulación</label>
            <textarea id="motivo-anulacion" name="motivo" rows="3" maxlength="255" required autofocus></textarea>
        <?php else: ?>
            <p>El contrato pasará a «Cancelados».</p>
        <?php endif; ?>

        <div class="acciones-anulacion">
            <button type="button" id="cerrar-anulacion" <?= $esVenta ? '' : 'autofocus' ?>>Volver</button>
            <button type="submit" class="confirmar-anulacion"><?= $esVenta ? 'Anular venta' : 'Cancelar contrato' ?></button>
        </div>
    </form>
</dialog>

<script>
    const panelAnulacion = document.getElementById('panel-anulacion');
    const motivoAnulacion = document.getElementById('motivo-anulacion');

    document.querySelectorAll('[data-anular-registro]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            panelAnulacion.querySelector('form').reset();
            const codigo = boton.dataset.anularRegistro;
            document.getElementById('registro-anulacion').value = codigo;
            document.getElementById('codigo-anulacion').textContent = codigo;
            panelAnulacion.showModal();
        });
    });

    document.getElementById('cerrar-anulacion').addEventListener('click', function () {
        panelAnulacion.close();
    });

    if (motivoAnulacion) {
        panelAnulacion.querySelector('form').addEventListener('submit', function (evento) {
            if (motivoAnulacion.value.trim() === '') {
                evento.preventDefault();
                motivoAnulacion.value = '';
                motivoAnulacion.reportValidity();
            }
        });
    }
</script>
