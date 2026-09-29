<div class="cabecera-registros-compra">

        <div class="textos-registros-compra">
            <h1>Registros Compra</h1>

        </div>
        <a href="index.php?opcion=compra" class="nueva-compra">Volver</a>
    </div>

<form method="GET" action="index.php" class="formulario">

    <input type="hidden"
           name="opcion"
           value="registrosCompra">

    <!-- FILTRO FECHA DESDE -->
    <div class="campo-filtro">

        <label for="fechaDesde">
            Desde
        </label>

        <input type="date"
               id="fechaDesde"
               name="fechaDesde"
               value="<?= htmlspecialchars($_GET['fechaDesde'] ?? '') ?>">

    </div>


    <!-- FILTRO FECHA HASTA -->
    <div class="campo-filtro">

        <label for="fechaHasta">
            Hasta
        </label>

        <input type="date"
               id="fechaHasta"
               name="fechaHasta"
               value="<?= htmlspecialchars($_GET['fechaHasta'] ?? '') ?>">

    </div>


    <!-- FILTRO PROVEEDOR -->
    <div class="campo-filtro">

        <label for="filtroProveedor">
            Proveedor
        </label>

        <select id="filtroProveedor"
                name="proveedor"
                class="filtroboton">

            <option value="">
                Todos los proveedores
            </option>

            <?php foreach ($Proveedores as $proveedorBD): ?>

                <option
                    value="<?= $proveedorBD['idProveedor'] ?>"
                    <?= ($_GET['proveedor'] ?? '') === $proveedorBD['idProveedor'] ? 'selected' : '' ?>>

                    <?= htmlspecialchars($proveedorBD['RazonSocial']) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>


    <button type="submit" class="boton-buscar">
        Filtrar
    </button>

    <?php if (!empty($errorFecha)): ?>
        <div class="mensaje-error-fecha">
            <?= htmlspecialchars($errorFecha) ?>
        </div>
    <?php endif; ?>

</form> 



<div class="contenedor-registros-compra">

    <div class="panel-registros-compra">

        <div class="tabla-registros-compra">
            <table>
                <thead>
                    <tr>
                        
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if(!empty($Compras)): ?>

                        <?php foreach($Compras as $compra):?>
                            <tr>
                                

                                <td>
                                    <?= htmlspecialchars($compra['Fecha'])?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($compra['RazonSocial'])?>
                                </td>

                                <td>
                                    S/ <?= htmlspecialchars($compra['TotalCompra'],2)?>
                                </td>

                                <td>
                                    <?php if($compra['Estado']): ?>
                                        <span class="estado-compra activa">Activa</span>
                                    <?php else: ?>
                                        <span class="estado-compra anulado">Anulado</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="?opcion=registrosCompra&detalle=<?= urlencode($compra['idCompra']) ?>"
                                    class="boton-detalle-compra">
                                        Ver detalle
                                    </a>

                    
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>  
                        <tr>
                            <td colspan="5" class="sin-registros">
                                No existe compras registradas
                            </td>
                        </tr>  
                    <?php endif; ?>
                                    
                </tbody>

            </table>
        </div>

        <?php if (!empty($DetalleCompra)): ?>
            <div class="detalle-overlay">
                <div class="detalle-compra-panel">
                    <div class="detalle-comrpa-cabecera">

                        <div>
                            <h2>Detalle Compra</h2>
                        </div>

                    </div>
                            <div class="informacion-compra">

                                <div class="dato-compra">
                                    <span>ID Compra</span>
                                    <strong><?= htmlspecialchars($DetalleCompra[0]['idCompra']) ?></strong>
                                </div>
                                <div class="dato-compra">
                                    <span>Proveedor</span>
                                    <strong><?= htmlspecialchars($DetalleCompra[0]['RazonSocial']) ?></strong>
                                </div>
                                <div class="dato-compra">
                                    <span>Fecha</span>
                                    <strong><?= date('d/m/y', strtotime($DetalleCompra[0]['FechaCompra'])) ?></strong>
                                </div>
                                <div class="dato-compra">
                                    <span>Hora</span>
                                    <strong><?= date('H:i:s', strtotime($DetalleCompra[0]['FechaCompra'])) ?></strong>
                                </div>
                                <div class="dato-compra">
                                    <span>Observaciones</span>
                                    <p> <?= !empty($DetalleCompra[0]['Observacion'])
                                    ? htmlspecialchars($DetalleCompra[0]['Observacion'])
                                    : 'Sin observación' ?> </p>
                                </div>
                            </div>

                                <div class="productos-detalle-compra">
                                    <?php foreach($DetalleCompra as $detalle): ?>
                                        <div class="producto-detalle">

                                            <div class="producto-detalle-nombre">
                                                <span>Producto</span>
                                                <strong> <?=  htmlspecialchars($detalle['NombreMaterial']) ?> </strong> 
                                            </div>

                                            <div class="producto-detalle-datos">
                                                <div>
                                                    <span>Cantidad</span>
                                                    <strong> <?=  htmlspecialchars($detalle['Cantidad']) ?> </strong>
                                                </div>
                                                    
                                                <div>
                                                    <span>Precio de compra</span>
                                                    <strong> S/ <?=  number_format((float)$detalle['PrecioCompra'], 2) ?>  </strong>
                                                </div>

                                                <div>
                                                    <span>SubTotal</span>
                                                    <strong>S/ <?=  number_format((float)$detalle['SubTotal'], 2) ?>  </strong>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?> 
                                </div>    
                                    

                                <div class="total-detalle-compra">
                                    <span>Total de compra</span>
                                    <strong>
                                        S/ <?= number_format((float)$DetalleCompra[0]['TotalCompra'], 2) ?>
                                    </strong>
                                </div>

                                <!-- BOTÓN -->
                                <div class="acciones-detalle-compra">

                                    <a
                                        href="index.php?opcion=registrosCompra"
                                        class="boton-volver-detalle"
                                    >
                                        Volver
                                    </a>

                                </div>     
                    
                </div>
            </div>

        <?php endif; ?>

     </div> 

        <div class="paginacion">

            <span class="rango">
                <?= $pagina ?> / <?= $totalPaginas ?>
            </span>

            <?php if ($pagina > 1): ?>

                <a class="flecha"
                href="?opcion=registrosCompra&fechaDesde=<?= urlencode($_GET['fechaDesde'] ?? '') ?>&fechaHasta=<?= urlencode($_GET['fechaHasta'] ?? '') ?>&proveedor=<?= urlencode($_GET['proveedor'] ?? '') ?>&pagina=<?= $pagina - 1 ?>">
                    ‹
                </a>

            <?php else: ?>

                <span class="flecha desactivada">
                     ‹
                </span>

            <?php endif; ?>


            <?php if ($pagina < $totalPaginas): ?>

                <a class="flecha"
                    href="?opcion=registrosCompra&fechaDesde=<?= urlencode($_GET['fechaDesde'] ?? '') ?>&fechaHasta=<?= urlencode($_GET['fechaHasta'] ?? '') ?>&proveedor=<?= urlencode($_GET['proveedor'] ?? '') ?>&pagina=<?= $pagina + 1 ?>">
                    ›
                </a>

            <?php else: ?>

                <span class="flecha desactivada">
                    ›
                </span>
            <?php endif; ?>
        </div>

</div>