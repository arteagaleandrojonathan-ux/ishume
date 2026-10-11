<?php
/* Bloque reutilizable: lista de productos cancelados (se usa en "Ver detalle" y en "Cancelar") */
$pintarCancelados = function (array $items) {
    if (empty($items)) { return; }

    $montoCancelado = 0;
    foreach ($items as $it) { $montoCancelado += (float) $it['SubTotal']; }
    ?>
    <section class="bloque-cancelados">
        <div class="bloque-cancelados-cabecera">
            <h3> Productos cancelados</h3>
            <span><?= count($items) ?> producto(s) · S/ <?= number_format($montoCancelado, 2) ?></span>
        </div>

        <?php foreach ($items as $it): ?>
            <div class="fila-cancelada">
                <span class="icono-cancelado">✕</span>

                <div class="fila-cancelada-info">
                    <strong class="texto-tachado"><?= htmlspecialchars($it['NombreMaterial']) ?></strong>
                    <span><?= htmlspecialchars($it['Color']) ?> · <?= htmlspecialchars($it['NivelEducativo']) ?></span>
                    <p class="motivo-cancelado">
                        <b>Motivo:</b>
                        <?= !empty($it['MotivoAnulacion']) ? htmlspecialchars($it['MotivoAnulacion']) : 'Sin motivo registrado' ?>
                        <?php if (!empty($it['FechaAnulacion'])): ?>
                            <small>· <?= date('d/m/y H:i', strtotime($it['FechaAnulacion'])) ?></small>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="fila-cancelada-cifras">
                    <span><?= (int) $it['Cantidad'] ?> uds × S/ <?= number_format((float) $it['PrecioCompra'], 2) ?></span>
                    <strong class="texto-tachado">S/ <?= number_format((float) $it['SubTotal'], 2) ?></strong>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <?php
};
?>
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

                                    <?php if ($compra['Estado'] && (int) $compra['Cancelados'] > 0): ?>
                                        <span class="estado-compra parcial"><?= (int) $compra['Cancelados'] ?> cancelado(s)</span>
                                    <?php endif; ?>                                    
                                </td>

                                <td>
                                    <div>
                                        <a href="?opcion=registrosCompra&detalle=<?= urlencode($compra['idCompra']) ?>"
                                        class="boton-detalle-compra">
                                        Ver detalle
                                        </a>

                                        <?php if($compra['Estado']): ?>
                                            <a href="?opcion=registrosCompra&cancelar=<?= urlencode($compra['idCompra']) ?>"
                                            class="boton-anular-compra">
                                                Cancelar Compra
                                            </a>
                                        <?php endif; ?>
                                    </div>
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

                                <?php
                                    $vigentesDetalle = array_filter($DetalleCompra, function ($l) { return (int) $l['EstadoDetalle'] === 1; });
                                    $canceladosDetalle = array_filter($DetalleCompra, function ($l) { return (int) $l['EstadoDetalle'] !== 1; });
                                ?>

                                <?php if (!(int) $DetalleCompra[0]['Estado']): ?>
                                    <div class="banner-anulada">
                                        ✕ Esta compra fue anulada por completo.
                                    </div>
                                <?php endif; ?>

                                <?php $pintarCancelados(array_values($canceladosDetalle)); ?>

                                <div class="productos-detalle-compra">
                                    
                                    <?php if (!empty($canceladosDetalle) && !empty($vigentesDetalle)): ?>
                                        <h3 class="titulo-bloque">Productos vigentes</h3>
                                    <?php endif; ?>

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

        <?php if (!empty($errorAnular) && empty($CancelarCompra)): ?>
            <div class="mensaje-error-fecha"><?= htmlspecialchars($errorAnular) ?></div>
        <?php endif; ?>

        <?php if (!empty($CancelarCompra)): ?>
            <div class="detalle-overlay">
                <div class="detalle-compra-panel panel-cancelar-compra">

                    <div class="detalle-compra-cabecera">
                        <h2>Cancelar compra</h2>
                    </div>

                    <div class="informacion-compra">
                        <div class="dato-compra">
                            <span>ID Compra</span>
                            <strong><?= htmlspecialchars($CancelarCompra[0]['idCompra']) ?></strong>
                        </div>
                        <div class="dato-compra">
                            <span>Proveedor</span>
                            <strong><?= htmlspecialchars($CancelarCompra[0]['RazonSocial']) ?></strong>
                        </div>
                        <div class="dato-compra">
                            <span>Fecha</span>
                            <strong><?= date('d/m/y', strtotime($CancelarCompra[0]['FechaCompra'])) ?></strong>
                        </div>
                        <div class="dato-compra">
                            <span>Total vigente</span>
                            <strong>S/ <?= number_format((float) $CancelarCompra[0]['TotalCompra'], 2) ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($errorAnular)): ?>
                        <div class="alerta-anulacion"><?= htmlspecialchars($errorAnular) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?opcion=anularCompra" id="formAnularCompra">

                        <input type="hidden" name="idCompra" value="<?= htmlspecialchars($CancelarCompra[0]['idCompra']) ?>">

                        <label class="seleccionar-todo">
                            <input type="checkbox" id="seleccionarTodo">
                            <span>Seleccionar todos los productos</span>
                        </label>

                        <div class="lista-cancelar">
                            <?php foreach ($CancelarCompra as $linea): ?>
                                <?php $vigente = (int) $linea['EstadoDetalle'] === 1; ?>

                                <label class="producto-cancelar <?= $vigente ? '' : 'cancelado' ?>">

                                    <input type="checkbox"
                                           class="check-producto"
                                           name="detalles[]"
                                           value="<?= htmlspecialchars($linea['idDetalleCompra']) ?>"
                                           data-subtotal="<?= htmlspecialchars($linea['SubTotal']) ?>"
                                           <?= $vigente ? '' : 'disabled' ?>>

                                    <div class="producto-cancelar-info">
                                        <strong><?= htmlspecialchars($linea['NombreMaterial']) ?></strong>
                                        <span><?= htmlspecialchars($linea['Color']) ?> · <?= htmlspecialchars($linea['NivelEducativo']) ?></span>
                                    </div>

                                    <div class="producto-cancelar-cifras">
                                        <span><?= (int) $linea['Cantidad'] ?> uds × S/ <?= number_format((float) $linea['PrecioCompra'], 2) ?></span>
                                        <strong>S/ <?= number_format((float) $linea['SubTotal'], 2) ?></strong>
                                    </div>

                                    <?php if (!$vigente): ?>
                                        <em class="etiqueta-cancelado">Cancelado</em>
                                    <?php endif; ?>

                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="resumen-cancelar">
                            <span id="resumenSeleccion">Ningún producto seleccionado</span>
                            <strong id="resumenMonto">S/ 0.00</strong>
                        </div>

                        <div class="campo-motivo">
                            <label for="motivoAnulacion">Motivo de la cancelación</label>
                            <textarea name="Motivo"
                                      id="motivoAnulacion"
                                      maxlength="500"
                                      placeholder="Especifique el motivo..."></textarea>
                            <span class="mensaje-error" id="errorMotivoAnulacion"></span>
                        </div>

                        <div class="acciones-detalle-compra acciones-cancelar">
                            <a href="index.php?opcion=registrosCompra" class="boton-volver-detalle">Volver</a>

                            <button type="submit" id="botonConfirmarAnulacion" class="boton-confirmar-anulacion" disabled>
                                Confirmar cancelación
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        <?php endif; ?>

        <?php if (($_GET['anulacion'] ?? '') === 'ok'): ?>
            <div class="detalle-overlay">
                <div class="detalle-compra-panel panel-confirmacion">
                    <div class="icono-exito">✓</div>
                    <h2><?= !empty($_GET['completa']) ? 'Compra cancelada' : 'Productos cancelados' ?></h2>
                    <p>
                        Se canceló <strong><?= (int) ($_GET['cant'] ?? 0) ?></strong>
                        producto(s) de la compra <strong><?= htmlspecialchars($_GET['id'] ?? '') ?></strong>.
                        <?= !empty($_GET['completa'])
                            ? 'La compra quedó anulada por completo.'
                            : 'La compra sigue vigente con los productos restantes.' ?>
                        El stock fue descontado y el motivo quedó registrado.
                    </p>
                    <a href="index.php?opcion=registrosCompra" class="boton-confirmar-anulacion">Aceptar</a>
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