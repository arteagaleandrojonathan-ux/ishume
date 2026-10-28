<section class="panel-contratos">

    <!-- ENCABEZADO Y BUSCADOR -->
    <header class="panel-encabezado">
        <div>
            <p class="nota-ejemplo">Datos de ejemplo</p>
            <h1>Contratos y ventas</h1>
        </div>

        <div class="panel-buscador">
            <label for="buscarContrato">Buscar contrato</label>
            <input type="text" id="buscarContrato" placeholder="ID, cliente o colegio..." autocomplete="off">
        </div>
    </header>


    <!-- TARJETAS: CONTRATOS Y VENTAS -->
    <div class="panel-tarjetas">

        <section class="tarjeta">
            <h2>Contratos</h2>

            <div class="contadores">
                <div class="contador"><strong>12</strong><span>En proceso</span></div>
                <a href="#" class="contador contador-destacado"><strong>4</strong><span>Sin productos aún · ver lista</span></a>
                <div class="contador"><strong>30</strong><span>Finalizados</span></div>
                <div class="contador"><strong>1</strong><span>Cancelados</span></div>
            </div>

            <div class="acciones-tarjeta">
                <a href="index.php?opcion=nuevoContrato" class="boton-principal">Registrar contrato</a>
                <a href="#" class="boton-secundario">Ver contratos</a>
            </div>
        </section>

        <section class="tarjeta">
            <h2>Ventas</h2>

            <div class="contadores">
                <div class="contador"><strong>5</strong><span>Pendientes</span></div>
                <div class="contador"><strong>20</strong><span>Finalizadas</span></div>
                <div class="contador"><strong>1</strong><span>Canceladas</span></div>
                <div class="contador"><strong>S/ 8,450</strong><span>Por cobrar en total</span></div>
            </div>

            <div class="acciones-tarjeta">
                <a href="#" class="boton-principal">Ver ventas</a>
            </div>
        </section>

    </div>


    <!-- RESUMEN DEL MES -->
    <section class="panel-resumen">
        <div><span>Contratado este mes</span><strong>S/ 10,680</strong></div>
        <div><span>Cobrado</span><strong>S/ 4,235</strong></div>
        <div><span>Por cobrar</span><strong>S/ 6,445</strong></div>
    </section>


    <!-- PRÓXIMOS EVENTOS -->
    <section class="tarjeta">
        <h2>Próximos eventos <small>· próximos 30 días</small></h2>

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
                <tr><td>CT-00007</td><td>Colegio de ejemplo A</td><td>Promoción</td><td>Pisco</td><td>02/11/2026</td><td>17 días</td></tr>
                <tr><td>CT-00006</td><td>Colegio de ejemplo B</td><td>Graduación</td><td>Ica</td><td>14/11/2026</td><td>29 días</td></tr>
                <tr><td>CT-00005</td><td>Luder Inicial</td><td>Promoción</td><td>Chincha</td><td>28/11/2026</td><td>43 días</td></tr>
            </tbody>
        </table>
    </section>


    <!-- PAGOS POR VENCER Y ÚLTIMOS PAGOS -->
    <div class="panel-doble">

        <section class="tarjeta">
            <h2>Pagos por vencer</h2>

            <table class="tabla-panel">
                <thead>
                    <tr><th>Contrato</th><th>Saldo</th><th>Fecha límite</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    <tr><td>CT-00002</td><td>S/ 800.00</td><td>12/10/2026</td><td><span class="estado estado-vencido">Vencido hace 4 días</span></td></tr>
                    <tr><td>CT-00003</td><td>S/ 1,335.00</td><td>20/10/2026</td><td><span class="estado estado-alerta">Vence en 4 días</span></td></tr>
                    <tr><td>CT-00004</td><td>S/ 650.00</td><td>28/10/2026</td><td><span class="estado estado-ok">Vence en 12 días</span></td></tr>
                </tbody>
            </table>
        </section>

        <section class="tarjeta">
            <h2>Últimos pagos registrados</h2>

            <table class="tabla-panel">
                <thead>
                    <tr><th>Fecha</th><th>Contrato</th><th>Forma</th><th>Monto</th></tr>
                </thead>
                <tbody>
                    <tr><td>15/10/2026</td><td>CT-00005</td><td>Yape</td><td>S/ 1,335.00</td></tr>
                    <tr><td>14/10/2026</td><td>CT-00003</td><td>Otros</td><td>S/ 500.00</td></tr>
                    <tr><td>12/10/2026</td><td>CT-00001</td><td>Yape</td><td>S/ 900.00</td></tr>
                </tbody>
            </table>
        </section>

    </div>

</section>