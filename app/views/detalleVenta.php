<?php
function escaparDetalle($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
$totalPagar = (float) $venta['PrecioTotal'];

$totalPagado = 0;

foreach ($pagos as $pago) {
    $totalPagado += (float) $pago['Monto'];
}

$saldoPendiente = max(0, $totalPagar - $totalPagado);
?>

<section class="detalle-venta">

    <header class="encabezado-detalle">
        <div>
            <h1>Detalle de venta</h1>
            <p>
                Venta <?= escaparDetalle($venta['idVenta']) ?>
                · Contrato <?= escaparDetalle($venta['idContrato']) ?>
            </p>
        </div>

        <a href="index.php?opcion=ventas" class="btn-volver-detalle">
            ← Volver 
        </a>
    </header>

    <!-- DATOS GENERALES -->
    <section class="datos-generales-venta">

        <div class="dato-venta">
            <span>Colegio</span>
            <strong><?= escaparDetalle($venta['NombreColegio']) ?></strong>
        </div>

        <div class="dato-venta">
            <span>Cliente</span>
            <strong>
                <?= escaparDetalle($venta['Nombre'] . ' ' . $venta['Apellidos']) ?>
            </strong>
        </div>

        <div class="dato-venta">
            <span>Evento</span>
            <strong><?= escaparDetalle($venta['Evento']) ?></strong>
        </div>

        <div class="dato-venta">
            <span>Fecha del evento</span>
            <strong>
                <?= date('d/m/Y', strtotime($venta['FechaEvento'])) ?>
            </strong>
        </div>

        <div class="dato-venta">
            <span>Teléfono</span>
            <strong><?= escaparDetalle($venta['Telefono']) ?></strong>
        </div>

        <div class="dato-venta">
            <span>Estado de venta</span>
            <strong class="estado-venta-detalle <?= strtolower(escaparDetalle($venta['EstadoVenta'])) ?>">
                <?= escaparDetalle($venta['EstadoVenta']) ?>
            </strong>
        </div>

        <?php if (!empty($venta['MotivoAnulacion'])): ?>
            <div class="dato-venta">
                <span>Motivo de anulación</span>
                <strong style="overflow-wrap: anywhere"><?= escaparDetalle($venta['MotivoAnulacion']) ?></strong>
            </div>
        <?php endif; ?>

    </section>

    
<!-- RESUMEN DE LA VENTA -->
<section class="resumen-detalle-venta">

    <div>
        <span>Productos solicitados</span>
        <strong><?= (int) $venta['CantidadPedidos'] ?></strong>
    </div>

    <div>
        <span>Total de la venta</span>
        <strong>
            S/ <?= number_format((float) $venta['PrecioTotal'], 2) ?>
        </strong>
    </div>

    <div>
        <span>Fecha de entrega Material</span>
        <strong>
            <?= !empty($venta['FechaEntrega'])
                ? date('d/m/Y', strtotime($venta['FechaEntrega']))
                : 'Por definir' ?>
        </strong>
    </div>

    <div>
        <span>Fecha límite de pago</span>
        <strong>
            <?= !empty($venta['FechaLimitePago'])
                ? date('d/m/Y', strtotime($venta['FechaLimitePago']))
                : 'Por definir' ?>
        </strong>
    </div>

    <!-- BOTÓN PARA EDITAR FECHAS -->
     <?php if (strtoupper(trim($venta['EstadoVenta'])) === 'PENDIENTE'): ?>
        <div class="acciones-fechas-venta">
        
            <button type="button" id="btnEditarFechasVenta"
                onclick="document.getElementById('modalFechasVenta').classList.add('mostrar')">
                ✎ Editar fechas
            </button>
        </div>
    <?php endif; ?>

</section>

<!-- MODAL PARA EDITAR FECHAS -->
<div class="modal-fechas-venta" id="modalFechasVenta">

    <div class="contenido-modal-fechas">

        <div class="encabezado-modal-fechas">
            <div>
                <h2>Editar fechas</h2>
                <p>Actualiza las fechas programadas para esta venta.</p>
            </div>

            <button type="button"
                    class="cerrar-modal-fechas"
                    id="cerrarModalFechas">
                &times;
            </button>
        </div>

        <form id="formEditarFechas">

            <input type="hidden"
                   name="idVenta"
                   value="<?= escaparDetalle($venta['idVenta']) ?>">

            <!-- FECHA DE ENTREGA -->
            <div class="campo-fecha-venta">

                <label for="fechaEntrega">
                    Fecha y hora de entrega
                </label>

                <input
                    type="date"
                    id="fechaEntrega"
                    name="fechaEntrega"
                    min="<?= escaparDetalle($fechaMinima) ?>"
                >

                <small>Indica cuándo se realizará la entrega.</small>

            </div>

            <!-- FECHA LÍMITE DE PAGO -->
            <div class="campo-fecha-venta">

                <label for="fechaLimitePago">
                    Fecha límite de pago
                </label>

                <input
                    type="date"
                    id="fechaLimitePago"
                    name="fechaLimitePago"
                    min="<?= escaparDetalle($fechaMinima) ?>"
                    value="<?= !empty($venta['FechaLimitePago'])
                        ? date('Y-m-d', strtotime($venta['FechaLimitePago']))
                        : '' ?>"
                >

                <small>Indica hasta qué fecha se puede completar el pago.</small>

            </div>

            <!-- BOTONES -->
            <div class="acciones-modal-fechas">

                <button type="button"
                        id="cancelarModalFechas">
                    Cancelar
                </button>

                <button type="submit"
                        id="guardarFechasVenta">
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>

    <!-- PRODUCTOS -->
    <section class="seccion-productos-venta">

        <div class="titulo-productos-venta">
            <div>
                <h2>Productos de la venta</h2>
                <p>Listado de modelos, cantidades y precios registrados.</p>
            </div>
        </div>

        <?php if (empty($detalles)): ?>

            <div class="sin-productos-venta">
                No hay productos registrados en esta venta.
            </div>

        <?php else: ?>

            <div class="tabla-detalle-scroll">
                <table class="tabla-detalle-venta">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td class="producto-detalle">
                                    <strong>
                                        <?= escaparDetalle($detalle['NombreMaterial']) ?>
                                    </strong>
                                </td>

                                <td><?= escaparDetalle($detalle['idModelo']) ?></td>

                                <td><?= (int) $detalle['Cantidad'] ?></td>

                                <td>
                                    S/ <?= number_format((float) $detalle['PrecioUnitario'], 2) ?>
                                </td>

                                <td>
                                    S/ <?= number_format((float) $detalle['Descuento'], 2) ?>
                                </td>

                                <td class="subtotal-detalle">
                                    S/ <?= number_format((float) $detalle['SubTotal'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>

    <!-- PAGOS DE LA VENTA -->
    <section class="seccion-pagos-venta">

        <div class="titulo-productos-venta">
            <div>
                <h2>Pagos registrados</h2>
                <p>Detalle de los pagos realizados para esta venta.</p>
            </div>
        </div>

        <?php if (empty($pagos)): ?>

            <div class="sin-productos-venta">
                No hay pagos registrados en esta venta.
            </div>

        <?php else: ?>

            <div class="tabla-detalle-scroll">
                <table class="tabla-detalle-venta">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Método de pago</th>
                            <th>Monto</th>
                            <th>Número de operación</th>
                            <th>Observación</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($pagos as $pago): ?>
                            <tr>
                                <td>
                                    <?= !empty($pago['FechaPago'])
                                        ? date('d/m/Y H:i', strtotime($pago['FechaPago']))
                                        : '—' ?>
                                </td>

                                <td>
                                    <?= escaparDetalle($pago['NombreMetodo']) ?>
                                </td>

                                <td class="subtotal-detalle">
                                    S/ <?= number_format((float)$pago['Monto'], 2) ?>
                                </td>

                                <td>
                                    <?= escaparDetalle($pago['NumOperacion'] ?: '—') ?>
                                </td>

                                <td>
                                    <?= escaparDetalle($pago['Observacion'] ?: '—') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>
<!-- RESUMEN DE PAGOS -->
<section class="resumen-pagos-venta">

    <div class="titulo-productos-venta">
        <div>
            <h2>Resumen de pagos</h2>
            <p>Estado económico de esta venta.</p>
        </div>
    </div>

    <div class="contenedor-resumen-pagos">

        <div class="dato-resumen-pago">
            <span>Total a pagar</span>
            <strong>
                S/ <?= number_format($totalPagar, 2) ?>
            </strong>
        </div>

        <div class="dato-resumen-pago">
            <span>Monto pagado</span>
            <strong>
                S/ <?= number_format($totalPagado, 2) ?>
            </strong>
        </div>

        <div class="dato-resumen-pago">
            <span>Falta pagar</span>
            <strong>
                S/ <?= number_format($saldoPendiente, 2) ?>
            </strong>
        </div>

    </div>

    <?php if (
        strtoupper(trim($venta['EstadoVenta'])) !== 'ANULADA'
        && $saldoPendiente > 0
    ): ?>

        <div class="acciones-nuevo-pago">

            <button type="button" id="btnAbrirModalPago">
                + Registrar nuevo pago
            </button>

        </div>

    <?php endif; ?>

</section>

<!-- MODAL PARA REGISTRAR NUEVO PAGO -->
<div class="modal-pago-venta" id="modalNuevoPago">

    <div class="contenido-modal-pago">

        <div class="encabezado-modal-pago">

            <div>
                <h2>Registrar nuevo pago</h2>
                <p>Registra un nuevo abono para esta venta.</p>
            </div>

            <button type="button"
                    id="cerrarModalPago"
                    class="cerrar-modal-pago">
                &times;
            </button>

        </div>

        <form id="formNuevoPago">

            <input
                type="hidden"
                name="idVenta"
                value="<?= escaparDetalle($venta['idVenta']) ?>"
            >

            <div class="campo-pago-venta">

                <label for="metodoPagoAdicional">
                    Método de pago
                </label>

                <select
                    id="metodoPagoAdicional"
                    name="idMetodoPago"
                    required
                >

                    <option value="">Selecciona un método</option>

                    <?php foreach ($metodosPago as $metodo): ?>

                        <option value="<?= escaparDetalle($metodo['idMetodoPago']) ?>">
                            <?= escaparDetalle($metodo['NombreMetodo']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="campo-pago-venta">

                <label for="montoPagoAdicional">
                    Monto a pagar
                </label>

                <input
                    type="number"
                    id="montoPagoAdicional"
                    name="monto"
                    min="0.01"
                    max="<?= htmlspecialchars(number_format($saldoPendiente, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                    step="0.01"
                    placeholder="Ejemplo: 100.00"
                    required
                >

                <small>
                    Saldo pendiente: S/ <?= number_format($saldoPendiente, 2) ?>
                </small>

            </div>

            <div class="campo-pago-venta">

                <label for="numeroOperacionAdicional">
                    Número de operación
                </label>

                <input
                    type="text"
                    id="numeroOperacionAdicional"
                    name="numeroOperacion"
                    maxlength="50"
                    placeholder="Número de operación (si corresponde)"
                >

            </div>

            <div class="campo-pago-venta">

                <label for="observacionPagoAdicional">
                    Observación
                </label>

                <textarea
                    id="observacionPagoAdicional"
                    name="observacion"
                    maxlength="500"
                    placeholder="Escribe una observación si es necesario"
                ></textarea>

            </div>

            <div class="acciones-modal-pago">

                <button type="button" id="cancelarModalPago">
                    Cancelar
                </button>

                <button type="submit" id="guardarPagoAdicional">
                    Registrar pago
                </button>

            </div>

        </form>

    </div>

</div>


</section>
