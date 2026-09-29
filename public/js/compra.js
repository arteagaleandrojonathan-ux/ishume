document.addEventListener('DOMContentLoaded', function () {

    //BUSCADOR
    const buscarProducto = document.getElementById('buscarProducto');
    const sugerenciasProductos = document.getElementById('sugerenciasProductos');
    const modeloSeleccionado = document.getElementById('modeloSeleccionado');

    //PROVEEDORES
    const proveedorInput = document.getElementById('proveedor');
    const rucProveedor = document.getElementById('rucProveedor');
    const telefonoProveedor = document.getElementById('telefonoProveedor');

    //PRECIOS DE LA COMPRA
    const cantidadInput = document.getElementById('cantidad');
    const precioInput = document.getElementById('precioCompra');
    const totalCompra = document.getElementById('totalCompra');

    //TODOS LOS BOTONES
    const botonAgregar = document.querySelector('.boton-agregar-producto');
    const botonCancelar = document.querySelector('.boton-cancelar-compra');

    // estras pe
    const listaProductos = document.getElementById('listaProductosCompra');

    const productosInput = document.getElementById('productosCompra');
    const formCompra = document.getElementById('formCompra');
    
    let productosCompra = [];


    botonCancelar.style.display = 'none';   


    formCompra.addEventListener('submit', function () {
        prepararProductos();

    })


    proveedorInput.addEventListener('change', function () {
        const opcionSeleccionada = this.options[this.selectedIndex];
        const ruc = opcionSeleccionada.dataset.ruc;
        const telefono = opcionSeleccionada.dataset.telefono;

         rucProveedor.value = ruc || '';

        telefonoProveedor.value = telefono || '';
    });

    botonAgregar.addEventListener('click', function () {
        const idModelo = modeloSeleccionado.value;
        const idProveedor = proveedorInput.value;
        const cantidad = parseInt(cantidadInput.value);
        const precioCompra = parseFloat(precioInput.value);

        if (idModelo === '') {
            alert('Seleccione un modelo.');
            return;
        }
        if (idProveedor === '') {
            alert('Seleccione un proveedor.');
            return;
        }

        if (isNaN(cantidad) || cantidad <= 0) {
            alert('Ingrese una cantidad válida.');
            return;
        }

        if (isNaN(precioCompra) || precioCompra < 0) {
            alert('Ingrese un precio de compra válido.');
            return;
        }

        const subtotal = cantidad * precioCompra;
            productosCompra.push({
                idModelo: idModelo,
                cantidad: cantidad,
                precioCompra: precioCompra,
                subtotal: subtotal
            });
            mostrarProductos();

            buscarProducto.value = '';
            modeloSeleccionado.value = '';
            cantidadInput.value = '';
            precioInput.value = '';
        });


    botonCancelar.addEventListener('click', function () {

        productosCompra = [];

        listaProductos.innerHTML = '';

        totalCompra.textContent =
            'S/ 0.00';


        buscarProducto.value = '';
        modeloSeleccionado.value = '';
        cantidadInput.value = '';
        precioInput.value = '';
        proveedorInput.value = '';
        rucProveedor.value = '';
        telefonoProveedor.value = '';

        document.getElementById('observacion').value = '';
        actualizarBotonCancelar();
        });

    let resultadosBusqueda = [];
    let indiceSeleccionado = -1;

    buscarProducto.addEventListener('input', function () {
        const texto = this.value.toLowerCase().trim();

        sugerenciasProductos.innerHTML = '';

        resultadosBusqueda = [];
        indiceSeleccionado = -1;

        modeloSeleccionado.value = '';
        if (texto === '') {

            return;
        }

        resultadosBusqueda = modelosDisponibles.filter(function (modelo) {

            return modelo.NombreMaterial
                .toLowerCase()
                .includes(texto);

        });

        if (resultadosBusqueda.length === 0) {

            const mensaje = document.createElement('div');

            mensaje.classList.add('sugerencia-producto');

            mensaje.textContent = 'No se encontraron productos';

            sugerenciasProductos.appendChild(mensaje);

            return;
        }

        resultadosBusqueda.forEach(function (modelo, indice) {

            const sugerencia = document.createElement('div');

            sugerencia.classList.add('sugerencia-producto');
            sugerencia.textContent = modelo.NombreMaterial;
            sugerencia.dataset.indice = indice;
            sugerencia.addEventListener('click', function () {
            seleccionarProducto(indice);

        });

        sugerenciasProductos.appendChild(sugerencia);
        });
        });

    buscarProducto.addEventListener('keydown', function (event) {
        const sugerencias =
            sugerenciasProductos.querySelectorAll(
                '.sugerencia-producto'
            );


    // ================================================
    // FLECHA ABAJO
    // ================================================

    if (event.key === 'ArrowDown') {
        event.preventDefault();

        if (sugerencias.length === 0) {
            return;
        }

        indiceSeleccionado++;

        if (indiceSeleccionado >= sugerencias.length) {
            indiceSeleccionado = 0;

        }

        actualizarSugerenciaActiva(sugerencias);
    }


    else if (event.key === 'ArrowUp') {
        event.preventDefault();

        if (sugerencias.length === 0) {
            return;
        }

        indiceSeleccionado--;

        if (indiceSeleccionado < 0) {
            indiceSeleccionado =
                sugerencias.length - 1;

        }

        actualizarSugerenciaActiva(sugerencias);
    }

    else if (event.key === 'Enter') {

        event.preventDefault();
        if(resultadosBusqueda.length === 1){
            seleccionarProducto(0);
            return;
        }
        if (
            indiceSeleccionado >= 0 &&
            indiceSeleccionado < resultadosBusqueda.length
        ) {
            seleccionarProducto(indiceSeleccionado);
        }
    }

    else if (event.key === 'Escape') {
            sugerenciasProductos.innerHTML = '';
            indiceSeleccionado = -1;
        }
        });



    function actualizarSugerenciaActiva(sugerencias) {

        sugerencias.forEach(function (sugerencia, indice) {
            if (indice === indiceSeleccionado) {
                sugerencia.classList.add(
                    'sugerencia-activa'
            );

            } else {
                sugerencia.classList.remove(
                    'sugerencia-activa'
                );

            }

        });

    }

    function seleccionarProducto(indice) {
        const modelo = resultadosBusqueda[indice];

        if (!modelo) {
            return;
        }


        buscarProducto.value =
            modelo.NombreMaterial;


        modeloSeleccionado.value =
            modelo.idModelo;


        sugerenciasProductos.innerHTML = '';

        indiceSeleccionado = -1;

    }

    function prepararProductos(){
        productosInput.value = JSON.stringify(productosCompra);
    }

    function actualizarBotonCancelar() {

        if (productosCompra.length > 0) {
            botonCancelar.style.display = 'block';
        } else {
            botonCancelar.style.display = 'none';
        }

    }
    
    function mostrarProductos() {

        listaProductos.innerHTML = '';

         productosCompra.forEach(function (producto, indice) {

            const fila = document.createElement('tr');

            fila.innerHTML = `
                <td>${producto.idModelo}</td>
                <td>${producto.cantidad}</td>
                <td>S/ ${producto.precioCompra.toFixed(2)}</td>
                 <td>S/ ${producto.subtotal.toFixed(2)}</td>
                 <td>
                    <button type="button" class="boton-eliminar-producto"
                    data-indice="${indice}">
                         Eliminar
                    </button>
                </td>
            `;

            listaProductos.appendChild(fila);

        });

        calcularTotal();
        actualizarBotonCancelar();

        const botonesEliminar =document.querySelectorAll('.boton-eliminar-producto');

        botonesEliminar.forEach(function (boton) {

            boton.addEventListener('click', function () {

                const indice = parseInt(this.dataset.indice);
                productosCompra.splice(indice, 1);
                mostrarProductos();
            });
        });

        
    }

    function calcularTotal() {
        let total = 0;

        productosCompra.forEach(function (producto) {

            total += producto.subtotal;

        });

        totalCompra.textContent = 'S/ ' + total.toFixed(2);
    }
    
    
});