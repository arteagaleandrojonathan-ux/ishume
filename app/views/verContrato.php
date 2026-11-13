
<?php
function mostrarDato($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function mostrarFecha($fecha) {
    if (empty($fecha)) {
        return 'No registrada';
    }

    return date('d/m/Y', strtotime($fecha));
}
?>
<div class="contenedor-scroll-contrato">
    <section class="detalle-contrato">

        <header class="encabezado-detalle-contrato">
        <div>
            <h1>Detalle del contrato</h1>
            <span>Información registrada del contrato</span>
        </div>

        <a href="index.php?opcion=listaContratos" class="btn-volver-contratos">
            ← Volver
        </a>
        </header>

        <div class="documento-contrato">

            <header class="documento-encabezado">
                <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
                <div class="banner-contrato">
                    <img src="img/banner_ishume.png" alt="Banner de ISHUME" width="1923" height="456">
                    <div class="contrato-barra">
   
                        <p>
                            <strong>CONTRATO DE SERVICIOS AUDIOVISUALES:</strong>
                            <span><?= mostrarDato($contrato['idContrato']) ?></span>
                        </p>

                        <p>
                            <strong>Fecha de emision:</strong>
                            <span><?= mostrarFecha($contrato['FechaContrato']) ?></span>
                        </p>
                    </div>
                </div>
            </header>

            <div class="seccion-contrato">
                <h3>DATOS DEL CLIENTE</h3>

                <div class="datos-contrato">
                    <p><strong>Nombre:</strong>
                        <?= mostrarDato($contrato['Nombre'] . ' ' . $contrato['Apellidos']) ?>
                    </p>

                    <p><strong>Testigo:</strong>
                        <?= mostrarDato($contrato['Testigo'] ?: 'No registrado') ?>
                    </p>

                    <p><strong>Tipo de documento:</strong>
                        <?= mostrarDato($contrato['TipoDOI'] ?: 'No registrado') ?>
                    </p>

                    <p><strong>Número de documento:</strong>
                        <?= mostrarDato($contrato['NumDOI'] ?: 'No registrado') ?>
                    </p>

                    <p><strong>Teléfono:</strong>
                        <?= mostrarDato($contrato['Telefono']) ?>
                    </p>

                    <p><strong>Correo:</strong>
                        <?= mostrarDato($contrato['Correo'] ?: 'No registrado') ?>
                    </p>

                    <p><strong>Dirección del cliente:</strong>
                        <?= mostrarDato($contrato['DireccionCliente']) ?>
                    </p>
                </div>

            </div>

            <div class="seccion-contrato">
                <h3>DATOS DEL CLIENTE</h3>

                <div class="datos-contrato">
                    <p>
                        <strong>Colegio:</strong>
                        <?= mostrarDato($contrato['NombreColegio']) ?>
                    </p>
                </div>
            </div>

            <div class="seccion-contrato">
                <h3>INFORMACION DEL CONTRATO</h3>

                <div class="datos-contrato">
                    <p><strong>Evento:</strong>
                        <?= mostrarDato($contrato['Evento']) ?>
                    </p>

                    <p><strong>Servicio:</strong>
                        <?= mostrarDato($contrato['Servicio']) ?>
                    </p>

                    <p><strong>Fecha del evento:</strong>
                        <?= mostrarFecha($contrato['FechaEvento']) ?>
                    </p>

                    <p><strong>Dirección del evento:</strong>
                        <?= mostrarDato($contrato['Direccion']) ?>
                    </p>

                    <p><strong>Provincia:</strong>
                        <?= mostrarDato($contrato['NomProvincia']) ?>
                    </p>

                    <p><strong>Distrito:</strong>
                        <?= mostrarDato($contrato['NombreDistrito']) ?>
                    </p>
                </div>
            </div>

            <div class="seccion-contrato">
                <h3>DATOS DEL PAGO</h3>

                <?php if ($contrato['PrecioTotal'] !== null): ?>

                <div class="detalle-pagos">

                    <h4>Estado </h4>

                    <div class="datos-complementarios-pago">
                        <p>
                            <strong>TOTAL A PAGAR</strong>
                            S/ <?= number_format((float)$contrato['PrecioTotal'], 2) ?>
                        </p>

                        <p>
                            <strong>ADELANTO</strong>
                             S/ <?= number_format((float)($contrato['PrimerPago'] ?? 0), 2) ?>
                        </p>
                    
                        <p>
                            <strong>SALDO PENDIENTE</strong>
                            <span>
                            S/ <?= number_format((float)$contrato['SaldoPendiente'], 2) ?>
                            </span>
                        </p>   

                        <div class="dato-resumen-pago solo-pantalla">
                            <p>
                                 <strong>TOTAL RECIBIDO HASTA LA FECHA</strong>
                                S/ <?= number_format((float)$contrato['TotalPagado'], 2) ?>
                            </p>       
                        </div>
                    </div>
                </div>

                <div class="detalle-pagos">
                    <h4>Detalles</h4>

                    <div class="datos-complementarios-pago">

                        <p>
                            <strong>MEDIO DE PAGO</strong>
                            <?= mostrarDato($contrato['MetodoPrimerPago'] ?: 'No registrado') ?>
                        </p>

                        <p>
                            <strong>FECHA DEL PAGO</strong>
                            <?= mostrarFecha($contrato['FechaPrimerPago']) ?>
                        </p>

                        <p>
                            <strong>FECHA LIMITE DEL PAGO</strong>
                            <?= mostrarFecha($contrato['FechaLimitePago']) ?>
                        </p>

                    </div>
                </div>

                <?php else: ?>

                <p class="condiciones-contrato">
                    No hay una venta registrada para este contrato.
                </p>

                <?php endif; ?>
            </div>

            <!-- 5. CONDICIONES -->
            <div class="seccion-contrato">
                <h3>CONDICIONES DEL CONTRATO</h3>

                <p class="condiciones-contrato">
                    <?= nl2br(mostrarDato($contrato['Condiciones'] ?: 'No se registraron condiciones.')) ?>
                </p>
            </div>
            <footer class="pie-documento-contrato">
                <p class="infomacion">
                    <strong>Correo:</strong>
                    <span>ISHUMEAGENCIA@HOTMAIL.COM</span>
                </p>
                
                <p class="informacion">
                    <strong>Número:</strong>
                    <span>991157028</span>
                </p>
                

            </footer>

        </div>
    </section>
</div>