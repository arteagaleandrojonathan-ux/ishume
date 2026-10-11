<!-- PANEL LATERAL: DETALLE VISUAL DEL MODELO (se llena con JS al hacer clic en una fila) -->
<aside class="panel-detalle" id="panelDetalle" aria-hidden="true">

    <div class="cabecera-detalle">
        <h2 id="detalleNombre">Detalle del modelo</h2>

        <button type="button" id="cerrarPanelDetalle" class="cerrar-detalle" aria-label="Cerrar panel">
            ×
        </button>
    </div>

    <div class="imagen-detalle">
        <img id="detalleImagen"
             src="img/sin-imagen.svg"
             alt="Imagen del modelo"
             data-placeholder="img/sin-imagen.svg">
    </div>

    <dl class="datos-detalle">
        <div><dt>Categoría</dt><dd id="detalleCategoria"></dd></div>
        <div><dt>Material</dt><dd id="detalleMarco"></dd></div>
        <div><dt>Precio</dt><dd id="detallePrecio"></dd></div>
        <div><dt>Stock</dt><dd id="detalleStock"></dd></div>
        <div><dt>Medidas</dt><dd id="detalleMedidas"></dd></div>
        <div><dt>Color</dt><dd id="detalleColor"></dd></div>
        <div><dt>Nivel</dt><dd id="detalleNivel"></dd></div>
    </dl>

</aside>
