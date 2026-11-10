
document.addEventListener('DOMContentLoaded', () => {

    const resumenVenta = document.getElementById('resumenVenta');
    const totalVenta = document.getElementById('totalVenta');
    const buscarProducto = document.getElementById('buscarProducto');
    const registrarVenta = document.getElementById('registrarVenta');

    let productosVenta = [];
    let categoriaSeleccionada = 'todos';



    //FILTROS DE ANUARIOS Y CUADROS 
    const botonesFiltro = document.querySelectorAll('.filtro');
    const tarjetasProductos = document.querySelectorAll('.producto');

    botonesFiltro.forEach(boton =>{

        boton.addEventListener('click', ()=>{

            categoriaSeleccionada = boton.dataset.categoria;

            // Activar únicamente el botón seleccionado
            botonesFiltro.forEach(btn => {
                btn.classList.remove('activo');
            });

            boton.classList.add('activo');

            filtrarProductos();
        });
    });

    // Buscador
    buscarProducto.addEventListener('input', () => {
        filtrarProductos();
    });

    // Función que combina categoría y búsqueda
    function filtrarProductos() {

        const texto = buscarProducto.value.trim().toLowerCase();

        tarjetasProductos.forEach(producto => {

            const nombre = producto.dataset.nombre.toLowerCase();
            const codigo = producto.dataset.codigo.toLowerCase();
            const categoria = producto.dataset.categoria;

            const coincideCategoria =
                categoriaSeleccionada === 'todos' ||
                categoria === categoriaSeleccionada;

            const coincideBusqueda =
                nombre.includes(texto) ||
                codigo.includes(texto);

            producto.style.display =
                coincideCategoria && coincideBusqueda ? '' : 'none';

        });

    }

    // Agregar productos
    document.querySelectorAll('.agregarProducto').forEach(boton => {

        boton.addEventListener('click', () => {

            const tarjeta = boton.closest('.producto');
            const cantidadInput = tarjeta.querySelector('.cantidadProducto');

            const cantidad = parseInt(cantidadInput.value);
            const idModelo = boton.dataset.id;
            const nombre = boton.dataset.nombre;
            const precio = parseFloat(boton.dataset.precio);

            if (!Number.isInteger(cantidad) || cantidad <= 0) {
                alert('Ingresa una cantidad válida.');
                return;
            }

            const existente = productosVenta.find(
                producto => producto.idModelo === idModelo
            );

            if (existente) {
                existente.cantidad += cantidad;
            } else {
                productosVenta.push({
                    idModelo,
                    nombre,
                    precio,
                    cantidad
                });
            }

            cantidadInput.value = 1;

            mostrarResumen();
        });
    });

    // Mostrar resumen y calcular total
    function mostrarResumen() {

        resumenVenta.innerHTML = '';

        if (productosVenta.length === 0) {
            resumenVenta.innerHTML = '<p>Aún no has agregado productos.</p>';
            totalVenta.textContent = '0.00';
            registrarVenta.disabled = true;
            return;
        }

        let total = 0;

        productosVenta.forEach((producto, indice) => {

            const subtotal = producto.precio * producto.cantidad;
            total += subtotal;

            const fila = document.createElement('div');

            const nombre = document.createElement('strong');
            nombre.textContent = producto.nombre;

            const detalle = document.createElement('p');
            detalle.textContent =
                `Cantidad: ${producto.cantidad} | ` +
                `Precio: S/ ${producto.precio.toFixed(2)} | ` +
                `Subtotal: S/ ${subtotal.toFixed(2)}`;

            const eliminar = document.createElement('button');
            eliminar.type = 'button';
            eliminar.textContent = 'Quitar';

            eliminar.addEventListener('click', () => {
                productosVenta.splice(indice, 1);
                mostrarResumen();
            });

            fila.append(nombre, detalle, eliminar);
            resumenVenta.appendChild(fila);
        });

        totalVenta.textContent = total.toFixed(2);
        registrarVenta.disabled = false;
    }

    // PANEL DE PAGOS
    const panelPagos = document.getElementById('panelPagos');
    const listaPagos = document.getElementById('listaPagos');

    // Mostrar u ocultar número de operación
    function actualizarNumeroOperacion(fila) {
        const metodo = fila.querySelector('.metodo-pago');
        const numeroOperacion = fila.querySelector('.numero-operacion');

        const requiereOperacion = ['MET-0001', 'MET-0003'].includes(metodo.value);

        numeroOperacion.hidden = !requiereOperacion;
        numeroOperacion.required = requiereOperacion;

        if (!requiereOperacion) {
            numeroOperacion.value = '';
        }
    }

    listaPagos.addEventListener('change', (e) => {
        if (e.target.classList.contains('metodo-pago')) {
            const fila = e.target.closest('.fila-pago');
            actualizarNumeroOperacion(fila);
        }
    });

    const agregarMetodoPago = document.getElementById('agregarMetodoPago');
    const cancelarPago = document.getElementById('cancelarPago');
    const confirmarVenta = document.getElementById('confirmarVenta');

    const totalPanelPago = document.getElementById('totalPanelPago');
    const totalPagos = document.getElementById('totalPagos');
    const saldoPendiente = document.getElementById('saldoPendiente');

    // Obtener el total real de los productos seleccionados
    function obtenerTotalVenta() {
        return productosVenta.reduce((total, producto) => {
            return total + producto.precio * producto.cantidad;
        }, 0);
    }

    function calcularPagos(){
        const totalVentaActual = obtenerTotalVenta();
        let sumaPagos = 0;

        document.querySelectorAll('.monto-pago').forEach(input => {
            const monto = parseFloat(input.value);
            if (Number.isFinite(monto) && monto > 0) {
                sumaPagos += monto;
                }
        });

        totalPanelPago.textContent = totalVentaActual.toFixed(2);
        totalPagos.textContent = sumaPagos.toFixed(2);
        saldoPendiente.textContent = Math.max(
            0,
            totalVentaActual - sumaPagos
        ).toFixed(2);
    }

    // Escuchar cambios en los importes
    listaPagos.addEventListener('input', evento => {
        if (evento.target.classList.contains('monto-pago')) {
            calcularPagos();
        }
    });

    // Agregar otro método de pago
    agregarMetodoPago.addEventListener('click', () => {
        const primeraFila = listaPagos.querySelector('.fila-pago');

        if (!primeraFila) return;

        const nuevaFila = primeraFila.cloneNode(true);

        nuevaFila.querySelector('.metodo-pago').value = '';
        nuevaFila.querySelector('.monto-pago').value = '';

        const eliminar = document.createElement('button');
        eliminar.type = 'button';
        eliminar.className = 'eliminar-pago';
        eliminar.textContent = 'Quitar';

        eliminar.addEventListener('click', () => {
            nuevaFila.remove();
            calcularPagos();
        });

        nuevaFila.appendChild(eliminar);
        listaPagos.appendChild(nuevaFila);

        calcularPagos();
    });

    // Abrir panel de pagos
    registrarVenta.addEventListener('click', () => {
        if (productosVenta.length === 0) {
            alert('Agrega al menos un producto.');
            return;
        }

        const idContrato = registrarVenta.dataset.idContrato;

        if (!idContrato) {
            alert('No se encontró el contrato.');
            return;
        }

        // Limpiar pagos anteriores
        const filas = listaPagos.querySelectorAll('.fila-pago');

        filas.forEach((fila, indice) => {
            if (indice > 0) {
                fila.remove();
            }
        });

        const primeraFila = listaPagos.querySelector('.fila-pago');

        if (primeraFila) {
            primeraFila.querySelector('.metodo-pago').value = '';
            primeraFila.querySelector('.monto-pago').value = '';
        }

        calcularPagos();
        panelPagos.hidden = false;
    });

    // Cancelar el registro
    cancelarPago.addEventListener('click', () => {
        panelPagos.hidden = true;
    });

    // Confirmar venta y pagos
    confirmarVenta.addEventListener('click', async () => {

        const total = obtenerTotalVenta();
        const filas = listaPagos.querySelectorAll('.fila-pago');

        const pagos = [];

        for (const fila of filas) {
            const idMetodoPago = fila.querySelector('.metodo-pago').value;
            const montoTexto = fila.querySelector('.monto-pago').value;

            const numeroOperacion = fila.querySelector('.numero-operacion').value.trim();
            const observacion = fila.querySelector('.observacion-pago').value.trim();

            const monto = Number(montoTexto);

            if (!idMetodoPago || montoTexto.trim() === '' ||
                !Number.isFinite(monto) || monto <= 0) {
                    alert('Completa correctamente el método y el monto de cada pago.');
                return;
            }
            if (['MET-0001', 'MET-0003'].includes(idMetodoPago) &&numeroOperacion === '') {
                alert('Debes ingresar el número de operación para Yape o Transferencia.');
                return;
            }

            pagos.push({
                idMetodoPago,
                monto,
                numeroOperacion,
                observacion
            });
        }

        if (pagos.length === 0) {
            alert('Agrega al menos un pago.');
            return;
        }

        const sumaPagos = pagos.reduce((suma, pago) => suma + pago.monto, 0);

        if (sumaPagos > total + 0.001) {
            alert('La suma de los pagos no puede superar el total de la venta.');
            return;
        }

        const idContrato = registrarVenta.dataset.idContrato;

        const datos = {
            idContrato,
            productos: productosVenta.map(producto => ({
                idModelo: producto.idModelo,
                cantidad: producto.cantidad
            })),
            pagos
        };

        confirmarVenta.disabled = true;
        confirmarVenta.textContent = 'Registrando...';

        try {
            const respuesta = await fetch('index.php?opcion=registrarVenta', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.ok) {
                throw new Error(
                    resultado.mensaje || 'No se pudo registrar la venta.'
                );
            }

            alert('Venta registrada correctamente.');
            window.location.href = `index.php?opcion=detalleVenta&idVenta=${encodeURIComponent(resultado.idVenta)}`;

        } catch (error) {
            alert(error.message);
            confirmarVenta.disabled = false;
            confirmarVenta.textContent = 'Confirmar venta';
        }
    });

});
