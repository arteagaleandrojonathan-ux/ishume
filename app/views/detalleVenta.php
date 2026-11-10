<?php
function escaparDetalle($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
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

        <a href="index.php?opcion=listaContratos" class="btn-volver-detalle">
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
            <span>Fecha de entrega</span>
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

    </section>

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


</section>