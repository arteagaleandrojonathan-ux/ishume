
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
    <!-- BUSCADOR POR COLEGIO -->
<div class="buscador-colegio-contratos">

    <label for="buscarColegioContrato">
        Buscar por colegio
    </label>

    <div class="buscador-colegio-input">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="search"
            id="buscarColegioContrato"
            placeholder="Escribe el nombre del colegio..."
            autocomplete="off"
        >

        <button type="button" id="limpiarBusquedaColegio">
            Limpiar
        </button>

    </div>

</div>

    <!-- SELECTOR DE REGISTROS -->
    <nav class="selector-registros">
       

        <button type="button" class="btn-selector activo" data-vista="sin-ventas">
            Sin ventas
            <span><?= count($contratosSinVenta) ?></span>
        </button>
        
         <button type="button" class="btn-selector " data-vista="con-ventas">
            Con ventas
            <span><?= count($contratosConVenta) ?></span>
        </button>
    </nav>

    <!-- CONTRATOS SIN VENTA -->
    <section class="grupo-contratos vista-registros activa" id="sin-ventas">

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

                            <tr data-colegio="<?= htmlspecialchars($contrato['NombreColegio'], ENT_QUOTES, 'UTF-8') ?>">
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
                <p class="mensaje-busqueda-vacia" hidden>
                    No se encontraron contratos de ese colegio.
                </p>              
            <?php endif; ?>

        </div>
    </section>

    <!-- CONTRATOS CON VENTA -->
    <section class="grupo-contratos vista-registros " id="con-ventas">

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
            <div class="contenedor-scroll-con-ventas">    
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

                            <tr data-colegio="<?= htmlspecialchars($contrato['NombreColegio'], ENT_QUOTES, 'UTF-8') ?>">
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
                                        <div class="acciones-contrato">

                                            <a href="index.php?opcion=verContrato&idContrato=<?= urlencode($contrato['idContrato']) ?>"
                                                class="btn-ver-contrato"
                                                title="Ver contrato">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <a href="index.php?opcion=descargarContratoPDF&idContrato=<?= urlencode($contrato['idContrato']) ?>"
                                                class="btn-pdf-contrato"
                                                title="Descargar PDF">
                                                    <i class="fa-solid fa-file-pdf"></i>
                                            </a>

                                            <a href="index.php?opcion=ventas&idVenta=<?= urlencode($contrato['idVenta']) ?>"
                                                 class="btn-ir-venta">
                                                Ir a ventas →
                                            </a>

                                        </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>  
                <p class="mensaje-busqueda-vacia" hidden>
                    No se encontraron contratos de ese colegio.
                </p>             
            <?php endif; ?>

        </div>
    </section>

</section>

<script>
    
document.addEventListener('DOMContentLoaded', function () {

    // CAMBIAR ENTRE SIN VENTA Y CON VENTA
    const botones = document.querySelectorAll('.btn-selector');
    const vistas = document.querySelectorAll('.vista-registros');

    botones.forEach(function (boton) {

        boton.addEventListener('click', function () {

            const vistaSeleccionada = boton.dataset.vista;

            botones.forEach(function (item) {
                item.classList.remove('activo');
            });

            vistas.forEach(function (vista) {
                vista.classList.remove('activa');
            });

            boton.classList.add('activo');

            document.getElementById(vistaSeleccionada)
                .classList.add('activa');

        });

    });


    // BUSCADOR ÚNICO POR NOMBRE DEL COLEGIO
    const buscador = document.getElementById('buscarColegioContrato');
    const botonLimpiar = document.getElementById('limpiarBusquedaColegio');

    const filas = document.querySelectorAll(
        '.tabla-contratos tbody tr[data-colegio]'
    );

    function normalizarTexto(texto) {
        return texto
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    
    function filtrarContratos() {

        const busqueda = normalizarTexto(buscador.value);

        filas.forEach(function (fila) {

            const nombreColegio = normalizarTexto(
                fila.dataset.colegio
            );

            // Comprobar si el colegio coincide con lo escrito
            const coincide = nombreColegio.includes(busqueda);

            // Mostrar u ocultar la fila
            fila.style.display = coincide ? '' : 'none';

        });

        // Revisar cada apartado por separado
        document.querySelectorAll('.grupo-contratos').forEach(function (grupo) {

            const filasGrupo = grupo.querySelectorAll(
                'tbody tr[data-colegio]'
            );

            let coincidencias = 0;

            filasGrupo.forEach(function (fila) {
                if (fila.style.display !== 'none') {
                    coincidencias++;
                }
            });

            // Mostrar mensaje si no hay coincidencias
            const mensaje = grupo.querySelector('.mensaje-busqueda-vacia');

            if (mensaje) {
                mensaje.hidden = coincidencias > 0;
            }

        });

    }


    // Buscar mientras se escribe
    buscador.addEventListener('input', filtrarContratos);

    // Limpiar búsqueda
    botonLimpiar.addEventListener('click', function () {

        buscador.value = '';

        filtrarContratos();

        buscador.focus();

    });

});

</script>
