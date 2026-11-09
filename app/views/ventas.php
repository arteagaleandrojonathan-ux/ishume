
<?php
function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de ventas | ISHUME</title>

    <link rel="stylesheet" href="css/ventas.css">
    <link rel="stylesheet" href="css/nuevaVenta.css">
</head>
<body>

    <main class="contenedor-ventas">

        <header class="encabezado-ventas">
            <div>
                <h1>Ventas</h1>
                <p>Consulta las ventas registradas en el sistema.</p>
            </div>

            <a href="index.php?opcion=contratos" class="btn-volver-contratos">
                ← Volver 
            </a>
        </header>

        <section class="resumen-listado">
            <div>
                <span>Total de ventas registradas</span>
                <strong><?= count($ventas) ?></strong>
            </div>
        </section>

        <section class="tabla-ventas">

            <div class="tabla-ventas-titulo">
                <h2>Registro de ventas</h2>
            </div>

            <?php if (empty($ventas)): ?>

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
                            <?php foreach ($ventas as $venta): ?>
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
                                        <a
                                            href="index.php?opcion=detalleVenta&idVenta=<?= urlencode($venta['idVenta']) ?>"
                                            class="btn-ver-detalle"
                                        >
                                             Ver detalle →
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
