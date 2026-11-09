
<?php

$contratosConVenta = array_filter($contratos, function ($contrato) {
    return !empty($contrato['idVenta']);
});

$contratosSinVenta = array_filter($contratos, function ($contrato) {
    return empty($contrato['idVenta']);
});

?>

<section class="lista-contratos">

    <!-- ENCABEZADO -->
    <header class="encabezado-lista-contratos">
        <div>
            <h1>Contratos</h1>
            <span>Administración de registros</span>
        </div>

        <a href="index.php?opcion=contratos" class="btn-volver-contratos">
            ← Volver
        </a>
    </header>

    <!-- RESUMEN -->
    <section class="resumen-contratos">

        <div class="resumen-item">
            <strong><?= count($contratos) ?></strong>
            <span>Total de contratos</span>
        </div>

        <div class="resumen-item con-venta">
            <strong><?= count($contratosConVenta) ?></strong>
            <span>Con venta</span>
        </div>

        <div class="resumen-item sin-venta">
            <strong><?= count($contratosSinVenta) ?></strong>
            <span>Sin venta</span>
        </div>

    </section>
    <!-- SELECTOR DE REGISTROS -->
    <nav class="selector-registros">
        <button type="button" class="btn-selector activo" data-vista="con-ventas">
            Con ventas
            <span><?= count($contratosConVenta) ?></span>
        </button>

        <button type="button" class="btn-selector" data-vista="sin-ventas">
            Sin ventas
            <span><?= count($contratosSinVenta) ?></span>
        </button>
    </nav>




    <!-- CONTRATOS CON VENTA -->
    <section class="grupo-contratos vista-registros activa" id="con-ventas">

        <header class="titulo-grupo">
            <div>
                <h2>Con venta</h2>
                <span>Contratos con productos registrados</span>
            </div>

            <span class="contador-contratos">
                <?= count($contratosConVenta) ?>
            </span>
        </header>

        <div class="tabla-contratos-contenedor">

            <?php if (empty($contratosConVenta)): ?>

                <p class="mensaje-sin-contratos">
                    Todavía no hay contratos con venta.
                </p>

            <?php else: ?>
               
                <table class="tabla-contratos">
                    <thead>
                        <tr>
                            <th>Contrato</th>
                            <th>Colegio / Cliente</th>
                            <th>Evento</th>
                            <th>Venta / Total</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($contratosConVenta as $contrato): ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($contrato['idContrato']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <div class="dato-principal">
                                        <?= htmlspecialchars($contrato['NombreColegio']) ?>
                                    </div>

                                    <div class="dato-secundario">
                                        <?= htmlspecialchars($contrato['Nombre'] . ' ' . $contrato['Apellidos']) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="dato-principal">
                                        <?= htmlspecialchars($contrato['Evento']) ?>
                                    </div>

                                    <div class="dato-secundario">
                                        <?= date('d/m/Y', strtotime($contrato['FechaEvento'])) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="dato-principal">
                                        <?= htmlspecialchars($contrato['idVenta']) ?>
                                    </div>

                                    <div class="dato-secundario">
                                        S/ <?= number_format((float)$contrato['PrecioTotal'], 2) ?>
                                    </div>
                                </td>
                                

                                <td>
                                    <span class="estado-contrato <?= strtolower(htmlspecialchars($contrato['EstadoContrato'])) ?>">
                                        <?= htmlspecialchars($contrato['EstadoContrato']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?opcion=detalleVenta&idVenta=<?= urlencode($contrato['idVenta']) ?>" 
                                    class="btn-registrar-venta">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
                           
            <?php endif; ?>

        </div>
    </section>

    <!-- CONTRATOS SIN VENTA -->
    <section class="grupo-contratos vista-registros" id="sin-ventas">

        <header class="titulo-grupo">
            <div>
                <h2>Sin venta</h2>
                <span>Contratos pendientes de registrar productos</span>
            </div>

            <span class="contador-contratos pendiente">
                <?= count($contratosSinVenta) ?>
            </span>
        </header>

        <div class="tabla-contratos-contenedor">

            <?php if (empty($contratosSinVenta)): ?>

                <p class="mensaje-sin-contratos">
                    Todos los contratos tienen una venta registrada.
                </p>

            <?php else: ?>
            <div class="contenedor-scroll-sin-ventas">     
                <table class="tabla-contratos">
                    <thead>
                        <tr>
                            <th>Contrato</th>
                            <th>Colegio / Cliente</th>
                            <th>Evento</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($contratosSinVenta as $contrato): ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($contrato['idContrato']) ?>
                                    </strong>
                                    
                                </td>

                                <td>
                                    <div class="dato-principal">
                                        <?= htmlspecialchars($contrato['NombreColegio']) ?>
                                    </div>

                                    <div class="dato-secundario">
                                        <?= htmlspecialchars($contrato['Nombre'] . ' ' . $contrato['Apellidos']) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="dato-principal">
                                        <?= htmlspecialchars($contrato['Evento']) ?>
                                    </div>

                                    <div class="dato-secundario">
                                        <?= date('d/m/Y', strtotime($contrato['FechaEvento'])) ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="estado-contrato <?= strtolower(htmlspecialchars($contrato['EstadoContrato'])) ?>">
                                        <?= htmlspecialchars($contrato['EstadoContrato']) ?>
                                    </span>
                                </td>
                                    

                                <td>
                                    <a
                                        href="index.php?opcion=nuevaVenta&idContrato=<?= urlencode($contrato['idContrato']) ?>"
                                        class="btn-registrar-venta"
                                    >
                                        Registrar
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>                
            <?php endif; ?>

        </div>
    </section>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const botones = document.querySelectorAll('.btn-selector');
        const vistas = document.querySelectorAll('.vista-registros');

        botones.forEach(function (boton) {

            boton.addEventListener('click', function () {

                const vistaSeleccionada = boton.dataset.vista;

                // Quitar selección de todos los botones
                botones.forEach(function (item) {
                    item.classList.remove('activo');
                });

                // Ocultar todos los grupos
                vistas.forEach(function (vista) {
                    vista.classList.remove('activa');
                });

                // Activar el botón seleccionado
                boton.classList.add('activo');

                // Mostrar el grupo correspondiente
                document.getElementById(vistaSeleccionada).classList.add('activa');

            });

        });

    });
</script>
