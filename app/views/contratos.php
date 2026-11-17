<section class="panel-contratos">

    <!-- ENCABEZADO Y BUSCADOR -->
    <header class="panel-encabezado">


       
    </header>


    <!-- TARJETAS: CONTRATOS Y VENTAS -->
    <div class="panel-tarjetas">

        <section class="tarjeta">
            <h2>Registro Anual de Contratos</h2>

            <div class="contadores">
                <div class="contador">
                    <strong><?= $resumen['ContratosProceso'] ?></strong>
                    <span>En proceso</span>
                </div>

                <div class="contador">
                    <strong><?= $resumen['ContratosFinalizados'] ?></strong>
                    <span>Finalizados</span>
                </div>

                <div class="contador">
                    <strong><?= $resumen['ContratosCancelados'] ?></strong>
                    <span>Cancelados</span>
                </div>
            </div>

            <div class="acciones-tarjeta">
                <a href="index.php?opcion=nuevoContrato" class="boton-principal">Registrar contrato</a>
                <a href="index.php?opcion=listaContratos" class="boton-secundario">Ver contratos</a>
            </div>
        </section>

        <section class="tarjeta">
            <h2>Registro Anual de Ventas</h2>

            <div class="contadores">
                <div class="contador">
                    <strong><?= $resumen['VentasPendientes'] ?></strong>
                    <span>Pendientes</span>
                </div>

                <div class="contador">
                    <strong><?= $resumen['VentasFinalizadas'] ?></strong>
                    <span>Finalizadas</span>
                </div>

                <div class="contador">
                    <strong><?= $resumen['VentasAnuladas'] ?></strong>
                    <span>Anuladas</span>
                </div>

                <div class="contador">
                    <strong>S/ <?= number_format((float)$resumen['TotalPorCobrar'], 2) ?></strong>
                    <span>Por cobrar en total</span>
                </div>
            </div>

            <div class="acciones-tarjeta">
                <a href="index.php?opcion=ventas" class="boton-principal">Ver ventas</a>
            </div>
        </section>

    </div>


    <!-- RESUMEN DEL MES -->
    <section class="panel-resumen">

        <div class="filtro-resumen">
            <form method="GET" action="index.php">
                <input type="hidden" name="opcion" value="contratos">

                <label for="periodoResumen">Consultar resumen:</label>

                <input
                    type="month"
                    id="periodoResumen"
                    value="<?= sprintf('%04d-%02d', $anio, $mes) ?>"
                    onchange="
                        const [anio, mes] = this.value.split('-');
                        window.location.href =
                            'index.php?opcion=contratos&mes=' + mes + '&anio=' + anio;
                    "
                >
            </form>
        </div>

        <div class="dato-resumen">
            <span>Contratado en el período</span>
            <strong>
                <?php if ((int)$resumenMensual['TotalRegistros'] > 0): ?>
                    S/ <?= number_format((float)$resumenMensual['TotalContratado'], 2) ?>
                <?php else: ?>
                    <span class="sin-registros">Sin registros</span>
                <?php endif; ?>
            </strong>
        </div>

        <div class="dato-resumen">
            <span>Cobrado</span>
            <strong>
                <?php if ((int)$resumenMensual['TotalRegistros'] > 0): ?>
                    S/ <?= number_format((float)$resumenMensual['TotalCobrado'], 2) ?>
                <?php else: ?>
                    <span class="sin-registros">Sin registros</span>
                <?php endif; ?>
            </strong>
        </div>

        <div class="dato-resumen">
            <span>Por cobrar</span>
            <strong>
                <?php if ((int)$resumenMensual['TotalRegistros'] > 0): ?>
                    S/ <?= number_format((float)$resumenMensual['TotalPorCobrar'], 2) ?>
                <?php else: ?>
                    <span class="sin-registros">Sin registros</span>
                <?php endif; ?>
            </strong>
        </div>

    </section>


    <!-- PRÓXIMOS EVENTOS -->
    <section class="tarjeta tarjeta-proximos-eventos">
        <h2>Próximos eventos <small>· próximos 30 días</small></h2>
        <div class="contenedor-scroll-eventos">
            <table class="tabla-panel">
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Colegio</th>
                        <th>Evento</th>
                        <th>Provincia</th>
                        <th>Fecha</th>
                        <th>Faltan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($proximosEventos)): ?>

                        <?php foreach ($proximosEventos as $evento): ?>

                            <tr>
                                <td><?= htmlspecialchars($evento['idContrato']) ?></td>
                                <td><?= htmlspecialchars($evento['NombreColegio']) ?></td>
                                <td><?= htmlspecialchars($evento['Evento']) ?></td>
                                <td><?= htmlspecialchars($evento['NomProvincia']) ?></td>
                                <td><?= date('d/m/Y', strtotime($evento['FechaEvento'])) ?></td>
                                <td><?= (int)$evento['DiasRestantes'] ?> días</td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6">No hay próximos eventos en los siguientes 30 días.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>


    <!-- PAGOS POR VENCER Y ÚLTIMOS PAGOS -->
    <div class="panel-doble">

        <section class="tarjeta tarjeta-pagos-vencer">
            <h2>Pagos por vencer</h2>
            <div class="contenedor-scroll-vencer">
                <table class="tabla-panel">
                    <thead>
                        <tr><th>Contrato</th><th>Saldo</th><th>Fecha límite</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagosPorVencer)): ?>

                            <?php foreach ($pagosPorVencer as $pago): ?>

                                <tr>
                                    <td><?= htmlspecialchars($pago['idContrato']) ?></td>
                                    <td>S/ <?= number_format((float)$pago['Saldo'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($pago['FechaLimitePago'])) ?></td>
                                    <td>

                                        <?php if ($pago['DiasRestantes'] < 0): ?>
                                            <span class="estado estado-vencido">
                                                Vencido hace <?= abs((int)$pago['DiasRestantes']) ?> días
                                            </span>

                                        <?php elseif ($pago['DiasRestantes'] == 0): ?>
                                            <span class="estado estado-alerta">
                                                Vence hoy
                                            </span>

                                        <?php elseif ($pago['DiasRestantes'] <= 5): ?>
                                            <span class="estado estado-alerta">
                                                Vence en <?= (int)$pago['DiasRestantes'] ?> días
                                            </span>

                                        <?php else: ?>
                                            <span class="estado estado-ok">
                                                Vence en <?= (int)$pago['DiasRestantes'] ?> días
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="4">No hay pagos pendientes por vencer.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </div>
            </table>
        </section>

        <section class="tarjeta tarjeta-ultimos-pagos">
            <h2>Últimos pagos registrados</h2>
            <div class="contenedor-scroll-pagos">            
                <table class="tabla-panel">
                    <thead>
                        <tr><th>Fecha</th><th>Contrato</th><th>Forma</th><th>Monto</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ultimosPagos)): ?>

                            <?php foreach ($ultimosPagos as $pago): ?>

                                <tr>
                                    <td><?= date('d/m/Y', strtotime($pago['FechaPago'])) ?></td>
                                    <td><?= htmlspecialchars($pago['idContrato']) ?></td>
                                    <td><?= htmlspecialchars($pago['NombreMetodo']) ?></td>
                                    <td>S/ <?= number_format((float)$pago['Monto'], 2) ?></td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="4">Todavía no hay pagos registrados.</td>
                            </tr>
                        <?php endif; ?>                
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</section>