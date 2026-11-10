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
        
                <p>
                    Contrato:
                    <strong><?= htmlspecialchars($idContrato) ?></strong>
                </p>
            </div>
            <a href="index.php?opcion=listaContratos" class="btn-volver-contratos">    
                ← Volver
            </a>
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

                <br>

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

    

    <!-- PANEL DE REGISTRO DE PAGOS -->
        <div id="panelPagos" class="panel-pagos" hidden>

            <div class="contenido-panel-pagos">

                <h2>Registrar pago</h2>

                <p>
                    Total de la venta:
                    <strong>S/ <span id="totalPanelPago">0.00</span></strong>
                </p>

                <h3>Formas de pago</h3>

                <div id="listaPagos">

                <!-- Aquí se agregarán los métodos de pago -->
                    <div class="fila-pago">

                        <select class="metodo-pago">
                            <option value="">Seleccionar método de pago</option>

                            <?php foreach ($metodosPago as $metodo): ?>

                                <option value="<?= htmlspecialchars($metodo['idMetodoPago']) ?>">
                                    <?= htmlspecialchars($metodo['NombreMetodo']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <input
                            type="number"
                            class="monto-pago"
                            min="0.01"
                            step="0.01"
                            placeholder="Monto S/"
                        >
                        <input
                            type="text"
                            class="numero-operacion"
                            placeholder="N° Operación"
                            maxlength="50"
                            hidden
                        >
                        <textarea
                            class="observacion-pago"
                            placeholder="Observación (opcional)"
                            maxlength="500"
                            rows="2"
                        ></textarea>

                    </div>

                </div>

                <button type="button" id="agregarMetodoPago">
                    + Agregar otro método de pago
                </button>

                <div class="resumen-pagos">

                    <p>
                        Total a pagar:
                        <strong>S/ <span id="totalPagos">0.00</span></strong>
                    </p>

                    <p>
                        Saldo pendiente:
                        <strong>S/ <span id="saldoPendiente">0.00</span></strong>
                    </p>

                </div>

                <div class="acciones-panel-pagos">

                    <button type="button" id="cancelarPago">
                        Cancelar
                    </button>

                    <button type="button" id="confirmarVenta">
                        Confirmar venta
                    </button>

                </div>

            </div>

        </div>

    </main>
<script src="js/ventas.js"></script>
</body>
</html>
