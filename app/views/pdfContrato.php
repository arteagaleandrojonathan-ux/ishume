
<?php

// =====================================================
// FUNCIONES AUXILIARES
// =====================================================

function mostrarDatoPDF($valor) {
    $valor = trim((string)($valor ?? ''));

    return $valor !== ''
        ? htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')
        : 'No registrado';
}

function mostrarFechaPDF($fecha) {
    if (empty($fecha)) {
        return 'No registrada';
    }

    $timestamp = strtotime($fecha);

    return $timestamp
        ? date('d/m/Y', $timestamp)
        : 'No registrada';
}

function mostrarMontoPDF($valor) {
    return 'S/ ' . number_format((float)($valor ?? 0), 2);
}


// =====================================================
// CARGAR BANNER COMO BASE64
// =====================================================


$rutaBannerPDF = dirname(__DIR__, 2) . '/public/img/banner_ishume.png';

$bannerBase64 = '';

if (file_exists($rutaBannerPDF) && is_readable($rutaBannerPDF)) {

    $contenidoBanner = file_get_contents($rutaBannerPDF);

    if ($contenidoBanner !== false) {
        $bannerBase64 = base64_encode($contenidoBanner);
    }
}


// =====================================================
// DATOS DEL CONTRATO
// =====================================================

$precioTotal = (float)($contrato['PrecioTotal'] ?? 0);
$totalPagado = (float)($contrato['TotalPagado'] ?? 0);
$primerPago = (float)($contrato['PrimerPago'] ?? 0);
$saldoPendiente = (float)($contrato['SaldoPendiente'] ?? 0);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <title>Contrato <?= mostrarDatoPDF($contrato['idContrato'] ?? '') ?></title>

    <style>

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
    padding: 0;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #222222;
        }

        /* =========================================
           BANNER
        ========================================= */

        .banner {
            width: 100%;
            margin-bottom: 10px;
        }

        .banner img {
           display: block;
    width: 100%;
    height: auto;
        }

        /* =========================================
           BARRA NEGRA
        ========================================= */

        .barra {

        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 10px;

            font-size: 12px;
            background-color: #080808;
            color: #ffffff;
            padding: 7px 14px;
            font-family: 'Roboto', sans-serif;

        }


        .barra table {
            width: 100%;
            border-collapse: collapse;
        }

        .barra td {
            color: #ffffff;
            border: none;
            padding: 0;
        }

        .barra .derecha {
            text-align: right;
        }

        /* =========================================
           SECCIONES
        ========================================= */

        .seccion {
            width: 100%;
            margin-bottom: 13px;
        }

        .seccion h3 {
            background-color: #F7931E;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 5px 10px;
            margin: 0 0 9px 0;
        }

        /* =========================================
   TABLAS DE DATOS
========================================= */

.datos {
    width: 100%;
    border-collapse: collapse;
}

.datos td {
    width: 50%;
    padding: 5px 8px 7px 0;
    vertical-align: top;
    border: none;
}

.etiqueta {
    display: block;
    color: #777777;
    font-size: 10px;
    margin-bottom: 3px;
}

.valor {
    display: block;
    color: #222222;
    font-size: 11px;
    font-weight: bold;
    word-wrap: break-word;
}

        /* =========================================
           DETALLE DE PAGOS
        ========================================= */

        

        .detalle-pagos .etiqueta {
            margin-bottom: 3px;
        }

        /* =========================================
           RESUMEN DE PAGOS
        ========================================= */

        
        /* =========================================
        DETALLE DE PAGOS
        ========================================= */

        .detalle-pagos {
            width: 100%;
            margin-top: 14px;
        }

        .detalle-pagos h4 {
            margin: 0 0 12px;
            padding-bottom: 7px;
            color: #333333;
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #cccccc;
        }

        .datos-complementarios-pago {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.datos-complementarios-pago td {
    width: 33.33%;
    padding: 0 12px 0 5px;
    border: none;
    vertical-align: top;
    color: #333333;
    font-size: 11px;
    line-height: 1.6;
}

.datos-complementarios-pago strong {
    display: block;
    margin-bottom: 4px;
    color: #555555;
    font-size: 10px;
    font-weight: bold;
}

        .sin-pago {
            margin: 0;
            padding: 10px 0;
            color: #555555;
            font-size: 10px;
        }

          

        /* =========================================
           CONDICIONES
        ========================================= */

        .condiciones {
            border: 1px solid #dddddd;
            padding: 10px 12px;
            font-size: 10px;
            line-height: 1.5;
            min-height: 45px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* =========================================
           PIE DE PÁGINA
        ========================================= */

        .pie {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #cccccc;
            text-align: center;
            color: #777777;
            font-size: 9px;
        }

        /* =========================================
           IMPRESIÓN DE COLORES
        ========================================= */

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

    </style>
</head>

<body>

    <!-- =========================================
         BANNER DE ISHUME
    ========================================== -->

    <div class="banner">

    <?php if (!empty($bannerBase64)): ?>

        <img
            src="data:image/png;base64,<?= $bannerBase64 ?>"
            alt="Banner ISHUME"
            class="banner-img"
        >

    <?php else: ?>

        <p style="color:red;">
            ERROR: No se pudo cargar el banner PNG.
        </p>

    <?php endif; ?>

</div>


    <!-- =========================================
         IDENTIFICACIÓN DEL CONTRATO
    ========================================== -->

    <div class="barra">

        <table>
            <tr>
                    
                <td>
                    CONTRATO DE SERVICIO
                    <?= mostrarDatoPDF($contrato['idContrato'] ?? '') ?>
                </td>

                 <td class="derecha">
                    Fecha de emisión:
                    <?= mostrarFechaPDF($contrato['FechaContrato'] ?? '') ?>
                </td>
            </tr>
        </table>

    </div>


    <!-- =========================================
         DATOS DEL CLIENTE
    ========================================== -->

    <div class="seccion">

        <h3>DATOS DEL CLIENTE</h3>

        <table class="datos">

            <tr>
                <td>
                    <span class="etiqueta">Nombre del cliente</span>
                    <span class="valor">
                        <?= mostrarDatoPDF(
                            ($contrato['Nombre'] ?? '') . ' ' .
                            ($contrato['Apellidos'] ?? '')
                        ) ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Testigo / Representante</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['Testigo'] ?? '') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="etiqueta">Tipo de documento</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['TipoDOI'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Número de documento</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['NumDOI'] ?? '') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="etiqueta">Teléfono</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['Telefono'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Correo electrónico</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['Correo'] ?? '') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span class="etiqueta">Dirección del cliente</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['DireccionCliente'] ?? '') ?>
                    </span>
                </td>
            </tr>

        </table>

    </div>


    <!-- =========================================
         DATOS DEL COLEGIO
    ========================================== -->

    <div class="seccion">

        <h3>DATOS DEL COLEGIO</h3>

        <table class="datos">

            <tr>
                <td>
                    <span class="etiqueta">Nombre del colegio</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['NombreColegio'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Provincia</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['NomProvincia'] ?? '') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="etiqueta">Distrito</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['NombreDistrito'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Dirección del evento</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['Direccion'] ?? '') ?>
                    </span>
                </td>
            </tr>

        </table>

    </div>


    <!-- =========================================
         INFORMACIÓN DEL CONTRATO
    ========================================== -->

    <div class="seccion">

        <h3>DETALLES DEL CONTRATO</h3>

        <table class="datos">

            <tr>
                <td>
                    <span class="etiqueta">Evento</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['Evento'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Servicio</span>
                    <span class="valor">
                        <?= mostrarDatoPDF($contrato['Servicio'] ?? '') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="etiqueta">Fecha del contrato</span>
                    <span class="valor">
                        <?= mostrarFechaPDF($contrato['FechaContrato'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <span class="etiqueta">Fecha del evento</span>
                    <span class="valor">
                        <?= mostrarFechaPDF($contrato['FechaEvento'] ?? '') ?>
                    </span>
                </td>
            </tr>

        </table>

    </div>


    <!-- =========================================
         INFORMACIÓN DE PAGOS
    ========================================== -->

<!-- =========================================
     INFORMACIÓN DE PAGOS
========================================== -->

<div class="seccion">

    <h3>DATOS DEL PAGO</h3>

    <?php if (($contrato['PrecioTotal'] ?? null) !== null): ?>

        <div class="detalle-pagos">

            <h4>Estado</h4>

            <table class="datos-complementarios-pago">
                <tr>
                    <td>
                        <strong>TOTAL A PAGAR</strong>
                        S/ <?= number_format($precioTotal, 2) ?>
                    </td>

                    <td>
                        <strong>ADELANTO</strong>
                        S/ <?= number_format($primerPago, 2) ?>
                    </td>

                    <td>
                        <strong>SALDO PENDIENTE</strong>
                        S/ <?= number_format($saldoPendiente, 2) ?>
                    </td>
                </tr>
            </table>

        </div>

        <div class="detalle-pagos">

            <h4>Detalles</h4>

            <table class="datos-complementarios-pago">
                <tr>
                    <td>
                        <strong>MEDIO DE PAGO</strong>
                        <?= mostrarDatoPDF($contrato['MetodoPrimerPago'] ?? '') ?>
                    </td>

                    <td>
                        <strong>FECHA DEL PAGO</strong>
                        <?= mostrarFechaPDF($contrato['FechaPrimerPago'] ?? '') ?>
                    </td>

                    <td>
                        <strong>FECHA LÍMITE DEL PAGO</strong>
                        <?= mostrarFechaPDF($contrato['FechaLimitePago'] ?? '') ?>
                    </td>
                </tr>
            </table>

        </div>

    <?php else: ?>

        <p class="sin-pago">
            No hay una venta registrada para este contrato.
        </p>

    <?php endif; ?>

</div>



    <!-- =========================================
         CONDICIONES DEL CONTRATO
    ========================================== -->

    <div class="seccion">

        <h3>CONDICIONES DEL CONTRATO</h3>

        <div class="condiciones">
            <?= mostrarDatoPDF($contrato['Condiciones'] ?? '') ?>
        </div>

    </div>


    <!-- =========================================
         PIE DE PÁGINA
    ========================================== -->

    <div class="pie">
        <span>
            Correo: ISHUMEAGENCIA@HOTMAIL.COM
        </span>

        <span>
            Contactanos: 991157028
        </span>

    </div>

</body>
</html>
