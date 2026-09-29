<?php if (isset($_GET['registro']) && $_GET['registro'] === 'exito'): ?>

    <div class="mensaje-compra-exito">
        Compra registrada correctamente.
    </div>

<?php endif; ?>


<div class="formulario-compra">
    <form
        method="POST"
        action="index.php?opcion=registrarCompra"
        id="formCompra"
    >

        <input
            type="hidden"
            name="productos"
            id="productosCompra"
        >

        <!-- =====================================================
             DATOS DEL PROVEEDOR
        ====================================================== -->
        <div class="seccion-proveedor">
            <div class="datos-proveedor">

                <!-- PROVEEDOR -->
                <div class="campo-compra campo-proveedor">
                    <label for="proveedor">Proveedor</label>

                    <select
                        id="proveedor"
                        name="idProveedor"
                    >

                        <option
                            value=""
                            data-ruc=""
                            data-telefono=""
                        >
                            Seleccionar proveedor
                        </option>

                        <?php foreach ($Proveedores as $proveedor): ?>

                            <option
                                value="<?= $proveedor['idProveedor'] ?>"
                                data-ruc="<?= htmlspecialchars($proveedor['Ruc']) ?>"
                                data-telefono="<?= htmlspecialchars($proveedor['Telefono'] ?? '') ?>"
                            >

                                <?= htmlspecialchars($proveedor['RazonSocial']) ?>

                            </option>

                        <?php endforeach; ?>
                    </select>
                </div>


                <!-- RUC -->
                <div class="campo-compra campo-ruc">
                    <label for="rucProveedor">RUC</label>
                    <input
                        type="text"
                        id="rucProveedor"
                        value=""
                        placeholder="—"
                        readonly
                    >
                </div>


                <!-- TELÉFONO -->
                <div class="campo-compra campo-telefono">
                    <label for="telefonoProveedor">Teléfono</label>

                    <input
                        type="text"
                        id="telefonoProveedor"
                        value=""
                        placeholder="—"
                        readonly
                    >
                </div>
            </div>
        </div>



        <!-- =====================================================
             PRODUCTOS DE LA COMPRA
        ====================================================== -->
        <div class="productos-compra">
            <div class="titulo-seccion-compra">
                <h2>Productos de la compra</h2>
                <p>Agregue los modelos adquiridos al proveedor</p>
            </div>


            <!-- =================================================
                 AGREGAR PRODUCTO
            ================================================== -->
            <div class="agregar-producto">
                        
                <!-- MODELO -->
                <div class="campo-compra campo-buscador-producto">
                    <label for="buscarProducto">
                        Producto
                    </label>

                    <input
                        type="text"
                        id="buscarProducto"
                        placeholder="Buscar por nombre del material..."
                        autocomplete="off"
                    >

                    <div
                        id="sugerenciasProductos"
                        class="sugerencias-productos"
                    ></div>

                    <input
                        type="hidden"
                        id="modeloSeleccionado"
                    >
                </div>

                <script>
                     const modelosDisponibles = <?= json_encode($Modelos) ?>;
                </script>


                <!-- CANTIDAD -->
                <div class="campo-compra">
                    <label for="cantidad">
                        Cantidad
                    </label>

                    <input
                        type="number"
                        id="cantidad"
                        name="Cantidad"
                        min="1"
                        step="1"
                        placeholder="0"
                    >
                </div>


                <!-- PRECIO DE COMPRA -->
                <div class="campo-compra">
                    <label for="precioCompra">
                        Precio de compra
                    </label>

                    <input
                        type="number"
                        id="precioCompra"
                        name="PrecioCompra"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                    >
                </div>


                <!-- AGREGAR -->
                <button
                    type="button"
                    class="boton-agregar-producto"
                >
                    + Agregar
                </button>
            </div>



            <!-- =================================================
                 DETALLE DE PRODUCTOS
            ================================================= -->   
            <div class="detalle-compra">
                <table class="tabla-detalle-compra">
                    <thead>
                        <tr>
                            <th>Modelo</th>
                            <th>Cantidad</th>
                            <th>Precio compra</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody id="listaProductosCompra">
                        <!--
                            JavaScript agregará aquí
                            los productos seleccionados.
                        -->
                    </tbody>
                </table>
            </div>

            <!-- =================================================
                 TOTAL
            ================================================== -->
            <div class="total-compra">
                <span> Total de compra:</span>
                <strong id="totalCompra"> S/ 0.00</strong>
            </div>

        </div>



        <!-- =====================================================
             OBSERVACIÓN
        ====================================================== -->
        <div class="seccion-observacion">

            <div class="titulo-seccion-compra">
                <h2> Observación</h2>
                <p> Agregue información adicional sobre la compra</p>
            </div>

            <div class="campo-compra">
                <textarea
                    id="observacion"
                    name="Observacion"
                    maxlength="500"
                    placeholder="Escriba una observación sobre la compra..."
                ></textarea>
            </div>
        </div>

        <!-- =====================================================
             ACCIONES
        ====================================================== -->
        <div class="acciones-compra">
            <button
                type="button"
                class="boton-cancelar-compra"
            >
                Cancelar
            </button>


            <button
                type="submit"
                class="boton-registrar-compra"
            >
                Registrar compra
            </button>

            <a
                 href="index.php?opcion=registrosCompra"
                 class="boton-ver-registros"    
            >
                Ver Registros
            </a>
        </div>
    </form>
</div>