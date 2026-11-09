document.addEventListener('DOMContentLoaded', () => {

    //BUSCAR PRONCINCIAS Y DISTRITOS
    const provincia = document.getElementById('idProvincia');
    const distrito = document.getElementById('idDistrito');
    

    //BUSCAR COLEGIOS
    const buscarColegio = document.getElementById('buscarColegio');
    const resultadosColegio = document.getElementById('resultadosColegios');
    const idColegio = document.getElementById('idColegio');
    const estadoColegio = document.getElementById('estadoColegio');

    //CHEKS DE ESTADOS DE LOS DATOS DE CONTRATO
    const estadoProvincia = document.getElementById('estadoprovincia');
    const estadoDistrito = document.getElementById('estadodistrito');
    const estados = document.querySelectorAll('.estadoDatos');

    //agregar un nuevo colegio por si no esta en la base de datos
    const modalColegio = document.getElementById('modalColegio');
    const nombreColegioNuevo = document.getElementById('NombreColegio');
    const btnCancelarColegio = document.getElementById('btnCancelarColegio');
    const btnRegistrarColegio = document.getElementById('btnRegistrarColegio');
    const errorColegioNuevo = document.getElementById('errorColegioNuevo');
    

    
    estados.forEach(estado => {
        const check = estado.parentElement.querySelector(".checks");


        function validarInput() {

        if (estado.value.trim() !== "") {
                check.textContent = "✓";
            } else {
                check.textContent = "";
            }
        }

        // Cuando sales del input
        estado.addEventListener("blur", validarInput);

        // Cuando presionas Enter
        estado.addEventListener("keydown", function(event) {

            if (event.key === "Enter") {
                validarInput();
            }

        });
    });

    provincia.addEventListener("change", function() {

        if (provincia.value !== "") {
           estadoProvincia.textContent = "✓";

        } else {
            estadoProvincia.textContent = "";
        }

        // Limpiar distrito cuando cambia la provincia
        distrito.value = "";
        estadoDistrito.textContent = "";
        
    });

    distrito.addEventListener("change", function() {
        if (distrito.value !== "") {
           estadoDistrito.textContent = "✓";
        } 
    });




    // Deja el cuadro como "sin colegio elegido"
    function limpiarColegio(){
        idColegio.value = '';
        estadoColegio.innerHTML = '';
        buscarColegio.classList.remove('valido');
    }

    // Muestra "✓ Colegio seleccionado · Cambiar" y todos los demas elementos de estado
    function mostrarColegioElegido(mensaje = '✓ Colegio seleccionado · ') {
        buscarColegio.classList.add('valido');
        estadoColegio.textContent = mensaje;
        
    }



    buscarColegio.addEventListener('input', () => {
        const textoBusqueda = buscarColegio.value.trim();

        limpiarColegio();

        if(textoBusqueda.length < 2){
            resultadosColegio.innerHTML = '';
            return;
        }

        fetch('index.php?opcion=buscarColegios', {
            method: 'POST',
            body: new URLSearchParams({ nombreColegio: textoBusqueda })

        })
        .then(respuesta => respuesta.json())
        .then(colegios => {

            if(buscarColegio.value.trim() !== textoBusqueda ){
                return;
            }
            resultadosColegio.innerHTML = '';

            if(colegios.length === 0){
                const aviso = document.createElement('div')
                aviso.className = 'colegio-no-encontrado';

                const mensaje = document.createElement('p');
                mensaje.textContent = 'No se encontro : ' + textoBusqueda ;


                const agregar = document.createElement('button');
                agregar.type = 'button';
                agregar.className = 'boton-secundario';
                agregar.textContent = '+ Agregarlo como colegio nuevo';

                agregar.addEventListener('click', () => {
                    nombreColegioNuevo.value = textoBusqueda;
                    resultadosColegio.innerHTML = '';
                    modalColegio.classList.add('mostrar');
                    nombreColegioNuevo.focus();
                });

                aviso.appendChild(mensaje);
                aviso.appendChild(agregar);
                resultadosColegio.appendChild(aviso);
                return;
            }

            colegios.forEach(colegio => {
                const boton= document.createElement('button');
                boton.type = 'button';
                boton.className = ('resultado-colegio');
                boton.textContent = colegio.NombreColegio;

                boton.addEventListener('click', () => {
                    buscarColegio.value = colegio.NombreColegio;
                    idColegio.value = colegio.idColegio;
                    resultadosColegio.innerHTML = '';
                    mostrarColegioElegido();
                });

                resultadosColegio.appendChild(boton);

            });

            
        })
        .catch(error => {
            console.error('Error al buscar colegios:', error);
        });
    });

    // Cerrar el modal con "Cancelar"
    btnCancelarColegio.addEventListener('click', () => {
        modalColegio.classList.remove('mostrar');
        errorColegioNuevo.textContent = '';
    });

    // REGISTRAR EL COLEGIO NUEVO
    btnRegistrarColegio.addEventListener('click', async() => {
        const nombreNuevo = nombreColegioNuevo.value.trim();
        errorColegioNuevo.textContent = '';

        if(nombreNuevo === ''){
            errorColegioNuevo.textContent = 'Ingrese el nombre del colegio.';
            return;
        }

        btnRegistrarColegio.disabled = true;   // evita el doble clic

        try {
            const respuesta = await fetch('index.php?opcion=registrarColegio', {
                method: 'POST',
                body: new URLSearchParams({ NombreColegio: nombreNuevo })
            });
            const datos = await respuesta.json();

            if(!datos.ok){
                errorColegioNuevo.textContent = datos.mensaje;
            } else {
                buscarColegio.value = datos.NombreColegio;
                idColegio.value = datos.idColegio;
                modalColegio.classList.remove('mostrar');
                mostrarColegioElegido(datos.existente
                    ? '✓ Ese colegio ya estaba registrado y quedó seleccionado'
                    : '✓ Colegio nuevo registrado');
            }

        } catch (error) {
            console.error('Error al registrar el colegio:', error);
            errorColegioNuevo.textContent = 'No se pudo registrar el colegio. Intente nuevamente.';
        }

        btnRegistrarColegio.disabled = false;
    });

    // Enter dentro del modal = Registrar colegio (opcional, puedes borrarlo)
    nombreColegioNuevo.addEventListener('keydown', (e) => {
        if(e.key === 'Enter') btnRegistrarColegio.click();
    });

    // rESTRINCCIONES
    const nombre = document.getElementById('Nombre');
    const apellidos = document.getElementById('Apellidos');
    const testigo = document.getElementById('Testigo');
    const tipoDOI = document.getElementById('TipoDOI');
    const numDOI = document.getElementById('NumDOI');
    const telefono = document.getElementById('Telefono');


    // NOMBRE, APELLIDOS Y TESTIGO: solo letras (sin números ni símbolos)
    nombre.addEventListener('input', () => {
        nombre.value = nombre.value.replace(/[^\p{L} .'-]/gu, '');
    });

    apellidos.addEventListener('input', () => {
        apellidos.value = apellidos.value.replace(/[^\p{L} .'-]/gu, '');
    });

    testigo.addEventListener('input', () => {
        testigo.value = testigo.value.replace(/[^\p{L} .'-]/gu, '');
    });


    // TELÉFONO: solo números, máximo 9
    telefono.maxLength = 9;

    telefono.addEventListener('input', () => {
        telefono.value = telefono.value.replace(/\D/g, '');
    });


    // NÚMERO DE DOCUMENTO: solo números, y el largo depende del tipo
    numDOI.maxLength = 0;
    numDOI.addEventListener('input', () => {
        numDOI.value = numDOI.value.replace(/\D/g, '');
    });

    tipoDOI.addEventListener('change', () => {
        if(tipoDOI.value === ''){
            numDOI.value = '';
            numDOI.maxLength = 0;
            return;
        }
        if (tipoDOI.value === 'DNI') {
            numDOI.maxLength = 8;
        } else if (tipoDOI.value === 'RUC') {
            numDOI.maxLength = 11;
        } else {
            numDOI.maxLength = 12;
        }

        // Si ya había más dígitos de los permitidos, se recorta
        numDOI.value = numDOI.value.slice(0, numDOI.maxLength);
    });
    

    //condicional de provicinia con distritos
    if (!provincia || !distrito) {
        return;
    }
    // Cuando seleccione una provincia
    provincia.addEventListener('change', async (e) => {
        const idProvincia = e.target.value;

        distrito.innerHTML = '<option value="">Seleccione un distrito</option>';

        if (idProvincia === ''){
            return;
        }   
            fetch('index.php?opcion=obtenerDistritos', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'idProvincia=' + encodeURIComponent(idProvincia)
            })
            .then(respuesta  => respuesta.json())
            .then(distritos => {
                distritos.forEach(distritoDato => {
                    const option = document.createElement('option');
                    option.value = distritoDato.idDistrito;
                    option.textContent = distritoDato.NombreDistrito;

                    distrito.appendChild(option);
                });
            })
        
            .catch(error => {
                console.error('Error al obtener los distritos:', error);
            });
    });

    //Registro de contrato
    const formulario = document.getElementById('formularioContrato');
    if (formulario) {

        formulario.addEventListener('submit', async (e) => {
            e.preventDefault();

            const datosFormulario = new FormData(formulario);

            try{
                const respuesta = await fetch('index.php?opcion=registrarContrato', {
                    method: 'POST',
                    body: datosFormulario
                });
                
                const datos = await respuesta.json();

                if( !datos.ok){

                    if(datos.errores){
                        alert(Object.values(datos.errores).join('\n'));
                    }else{
                        alert('Error al registrar el contrato. Contrato ya registrado.' );
                    }
                    return;
                }

                // Guardamos el ID del contrato registrado
                const idContratoRegistrado = datos.idContrato;

                // Mostramos el modal
                const modalVenta = document.getElementById('modalContinuarVenta');
                modalVenta.classList.add('mostrar');

                document.getElementById('btnMasTarde').addEventListener('click', () => {
                    modalVenta.classList.remove('mostrar');
                    alert('El contrato quedó registrado. Puedes continuar con la venta más tarde.');
                });

                document.getElementById('btnContinuarVenta').href =
                    `index.php?opcion=nuevaVenta&idContrato=${encodeURIComponent(idContratoRegistrado)}`;


            } catch (error) {
                console.error('Error al registrar el contrato:', error);
                alert('Ocurrió un error al intentar registrar el contrato.');
            }

        });
    }
});