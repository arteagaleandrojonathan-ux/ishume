<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva venta - ISHUME</title>
    <link rel="stylesheet" href="css/nuevaVenta.css">
</head>
<body>


<main class="contenedor-venta">

    <header class="encabezado-venta">
        <div>
            <h1>Registrar venta</h1>
            <a href="index.php?opcion=contratos">Volver</a>
            <p>
                Contrato:
                <strong><?= htmlspecialchars($idContrato) ?></strong>
            </p>
        </div>
    </header>

    <div class="contenido-venta">

        <!-- SECCIÓN DE PRODUCTOS -->
        <section class="seccion-productos">

            <h2>Seleccionar productos</h2>

            <div class="busqueda-productos">
                <input
                    type="text"
                    id="buscarProducto"
                    placeholder="Buscar por nombre o código..."
                >
            </div>

            <div class="filtros-productos">
                <button type="button" class="filtro activo" data-categoria="todos">
                    Todos
                </button>

                <button type="button" class="filtro" data-categoria="Cuadros">
                    Cuadros
                </button>

                <button type="button" class="filtro" data-categoria="Anuarios">
                    Anuarios
                </button>
            </div>

            <div class="encabezado-resultados">
                <h3>Resultados</h3>
                <span><?= count($productos) ?> productos</span>
            </div>

            <div id="listaProductos">

                <?php foreach ($productos as $producto): ?>

                    <article
                        class="producto"
                        data-nombre="<?= htmlspecialchars($producto['NombreMaterial']) ?>"
                        data-codigo="<?= htmlspecialchars($producto['idModelo']) ?>"
                        data-categoria="<?= htmlspecialchars($producto['NombreCategoria']) ?>"
                    >

                        <div class="imagen-producto">

                            <?php if (!empty($producto['Imagen'])): ?>

                                <img
                                    src="<?= htmlspecialchars($producto['Imagen']) ?>"
                                    alt="<?= htmlspecialchars($producto['NombreMaterial']) ?>"
                                >

                            <?php else: ?>

                                <span>Sin imagen</span>

                            <?php endif; ?>

                        </div>

                        <div class="informacion-producto">
                            <h4>
                                <?= htmlspecialchars($producto['NombreMaterial']) ?>
                            </h4>

                            <p>
                                Código:
                                <?= htmlspecialchars($producto['idModelo']) ?>
                            </p>

                            <p>
                                <?= htmlspecialchars($producto['NombreCategoria']) ?>
                            </p>
                        </div>

                        <div class="precio-producto">
                            S/ <?= number_format($producto['Precio'], 2) ?>
                        </div>

                        <div class="cantidad-producto">
                            <label for="cantidad-<?= htmlspecialchars($producto['idModelo']) ?>">
                            Cantidad:
                            </label>

                            <input
                                type="number"
                                class="cantidadProducto"
                                id="cantidad-<?= htmlspecialchars($producto['idModelo']) ?>"
                                min="1"
                                step="1"
                                value="1"
                            >
                        </div>

                        <button
                            type="button"
                            class="agregarProducto"
                            data-id="<?= htmlspecialchars($producto['idModelo']) ?>"
                            data-nombre="<?= htmlspecialchars($producto['NombreMaterial']) ?>"
                            data-precio="<?= htmlspecialchars($producto['Precio']) ?>"
                        >
                            <span>+</span> Agregar producto
                        </button>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>

        <!-- RESUMEN DE VENTA -->
        <aside class="seccion-resumen">

            <h2>Resumen de venta</h2>

            <div id="resumenVenta">
                <p>Aún no has agregado productos.</p>
            </div>

            <div class="total-venta">
                <span>Total:</span>
                <strong>
                    S/ <span id="totalVenta">0.00</span>
                </strong>
            </div>
            <button
                type="button"
                id="registrarVenta"
                class="boton-registrar-venta"
                data-id-contrato="<?= htmlspecialchars($idContrato) ?>"
                disabled
            >
                Registrar venta
            </button>

        </aside>

    </div>

</main>

<script src="js/ventas.js"></script>
</body>
</html>
