document.addEventListener('DOMContentLoaded', function () {

    const panel = document.getElementById('panelDetalle');
    if (!panel) return;

    const cerrarBtn = document.getElementById('cerrarPanelDetalle');
    const imagen = document.getElementById('detalleImagen');
    const placeholder = imagen.dataset.placeholder;
    const filas = document.querySelectorAll('.fila-modelo');

    const campos = {
        nombre: document.getElementById('detalleNombre'),
        categoria: document.getElementById('detalleCategoria'),
        marco: document.getElementById('detalleMarco'),
        precio: document.getElementById('detallePrecio'),
        stock: document.getElementById('detalleStock'),
        medidas: document.getElementById('detalleMedidas'),
        color: document.getElementById('detalleColor'),
        nivel: document.getElementById('detalleNivel')
    };

    let filaActiva = null;

    // Muestra "—" si el dato no existe, y oculta esa fila del panel
    function poner(elemento, valor) {
        const tiene = valor !== undefined && valor !== null && String(valor).trim() !== '';
        elemento.textContent = tiene ? valor : '—';
        elemento.parentElement.style.display = tiene ? '' : 'none';
    }

    function abrir(fila) {
        const d = fila.dataset;

        campos.nombre.textContent = d.nombre;
        poner(campos.categoria, d.categoria);
        poner(campos.marco, d.marco);
        poner(campos.precio, d.precio ? 'S/ ' + Number(d.precio).toFixed(2) : '');
        poner(campos.stock, d.stock);
        poner(campos.medidas, (d.ancho && d.largo) ? d.ancho + ' × ' + d.largo : '');
        poner(campos.color, d.color);
        poner(campos.nivel, d.nivel);

        // Imagen del modelo; si no tiene o no carga, se usa el placeholder
        imagen.onerror = function () {
            imagen.onerror = null;
            imagen.src = placeholder;
        };
        imagen.alt = 'Imagen de ' + d.nombre;
        imagen.src = d.imagen ? d.imagen : placeholder;

        if (filaActiva) filaActiva.classList.remove('fila-seleccionada');
        fila.classList.add('fila-seleccionada');
        filaActiva = fila;

        panel.classList.add('abierto');
        panel.setAttribute('aria-hidden', 'false');
    }

    function cerrar() {
        panel.classList.remove('abierto');
        panel.setAttribute('aria-hidden', 'true');
        if (filaActiva) filaActiva.classList.remove('fila-seleccionada');
        filaActiva = null;
    }

    filas.forEach(function (fila) {
        fila.addEventListener('click', function (evento) {
            // Los botones y enlaces de acciones siguen funcionando normal
            if (evento.target.closest('a, button, input, select, textarea')) return;
            abrir(fila);
        });
    });

    cerrarBtn.addEventListener('click', cerrar);

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') cerrar();
    });
});
