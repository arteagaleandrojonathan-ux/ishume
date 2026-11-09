
document.addEventListener('DOMContentLoaded', () => {

    const resumenVenta = document.getElementById('resumenVenta');
    const totalVenta = document.getElementById('totalVenta');
    const buscarProducto = document.getElementById('buscarProducto');
    const registrarVenta = document.getElementById('registrarVenta');

    let productosVenta = [];

    // Buscar productos por nombre
    buscarProducto.addEventListener('input', () => {
        const texto = buscarProducto.value.toLowerCase();

        document.querySelectorAll('.producto').forEach(producto => {
            const nombre = producto.dataset.nombre.toLowerCase();

            producto.style.display = nombre.includes(texto) ? '' : 'none';
        });
    });

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

    // Registrar venta
    registrarVenta.addEventListener('click', async () => {

        if (productosVenta.length === 0) {
            alert('Agrega al menos un producto.');
            return;
        }

        const idContrato = registrarVenta.dataset.idContrato;

        if (!idContrato) {
            alert('No se encontró el contrato.');
            return;
        }

        const datos = {
            idContrato: idContrato,
            productos: productosVenta.map(producto => ({
                idModelo: producto.idModelo,
                cantidad: producto.cantidad
            }))
        };

        registrarVenta.disabled = true;
        registrarVenta.textContent = 'Registrando...';

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
                throw new Error(resultado.mensaje || 'No se pudo registrar la venta.');
            }

            alert('Venta registrada correctamente.');

            window.location.href = 'index.php?opcion=ventas';

        } catch (error) {

            alert(error.message);

            registrarVenta.disabled = false;
            registrarVenta.textContent = 'Registrar venta';
        }

    });

});
