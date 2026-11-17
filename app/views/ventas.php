
<?php
    function escapar($valor) {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

?>
<?php
    $estadoFiltro = $_GET['estado'] ?? 'TODAS';

    $estadosPermitidos = ['TODAS', 'PENDIENTE', 'FINALIZADA', 'ANULADA'];

    if (!in_array($estadoFiltro, $estadosPermitidos, true)) {
        $estadoFiltro = 'TODAS';
    }

    $ventasFiltradas = array_filter($ventas, function ($venta) use ($estadoFiltro) {

        if ($estadoFiltro === 'TODAS') {
            return true;
        }

        return strtoupper(trim($venta['Estado'])) === $estadoFiltro;
    });
?>

    <main class="contenedor-ventas">

        <header class="encabezado-ventas">
            <div>
                <h1>Ventas</h1>
                <p>Ventas registradas en el sistema.</p>
            </div>

            <a href="index.php?opcion=contratos" class="btn-volver-contratos">
                ← Volver 
            </a>
        </header>

        <section class="resumen-listado">
            <div class="resumen-ventas">     
                <strong><?= count($ventas) ?></strong>
                <span>Total de ventas registradas</span>
            </div>
        </section>

        <section class="tabla-ventas">
            <?php if (isset($_GET['anulada'])): ?>
                <div class="mensaje-exito">
                    La venta se anuló correctamente.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="mensaje-error">
                    <?= escapar($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <div class="tabla-ventas-titulo">
                <h2>Registro de ventas</h2>
                <form method="GET" action="index.php" class="filtro-ventas">
                    <input type="hidden" name="opcion" value="ventas">
                    <label for="estado">Filtrar por estado:</label>

                    <select name="estado" id="estado" onchange="this.form.submit()">
                        <option value="TODAS" <?= $estadoFiltro === 'TODAS' ? 'selected' : '' ?>>
                            Todas las ventas
                        </option>
                        <option value="PENDIENTE" <?= $estadoFiltro === 'PENDIENTE' ? 'selected' : '' ?>>
                            Pendientes
                        </option>
                        <option value="FINALIZADA" <?= $estadoFiltro === 'FINALIZADA' ? 'selected' : '' ?>>
                            Finalizadas
                        </option>
                        <option value="ANULADA" <?= $estadoFiltro === 'ANULADA' ? 'selected' : '' ?>>
                            Anuladas
                        </option>
                    </select>
                </form>
            </div>

            <?php if (empty($ventasFiltradas)): ?>

                <div class="ventas-vacias">
                    <h3>No hay ventas registradas</h3>
                    <p>Cuando registres una venta, aparecerá en esta sección.</p>
                </div>

            <?php else: ?>

                <div class="tabla-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Contrato</th>
                                <th>Colegio</th>
                                <th>Cliente</th>
                                <th>Fecha contrato</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($ventasFiltradas as $venta): ?>
                                <tr>
                                    <td>
                                        <strong><?= escapar($venta['idVenta']) ?></strong>
                                    </td>

                                    <td>
                                        <?= escapar($venta['idContrato']) ?>
                                    </td>

                                    <td>
                                        <?= escapar($venta['NombreColegio']) ?>
                                    </td>

                                    <td>
                                        <?= escapar(
                                            $venta['Nombre'] . ' ' . $venta['Apellidos']
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= escapar(
                                            date('d/m/Y', strtotime($venta['FechaContrato']))
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= escapar($venta['CantidadPedidos']) ?>
                                    </td>

                                    <td>
                                        S/ <?= number_format(
                                            (float) $venta['PrecioTotal'],
                                            2
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="estado-venta <?= strtolower(escapar($venta['Estado'])) ?>">
                                            <?= escapar($venta['Estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="acciones-venta">

                                            <a href="index.php?opcion=detalleVenta&idVenta=<?= urlencode($venta['idVenta']) ?>"
                                                class="btn-ver-detalle">
                                                Ver
                                            </a>

                                            <?php if (strtoupper(trim($venta['Estado'])) == 'PENDIENTE'): ?>

                                                <button type="button" class="btn-anular-venta"
                                                    data-anular-registro="<?= escapar($venta['idVenta']) ?>">
                                                    Anular
                                                </button>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>    
                    </table>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <?php require __DIR__ . '/panelAnulacion.php'; ?>

