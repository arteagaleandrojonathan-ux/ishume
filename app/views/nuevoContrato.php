<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Contrato</title>

    <link rel="stylesheet" href="css/NuevoContrato.css">
</head>

<body>

    <main class="contenedor-contrato">
        <!-- ESPACIO RESERVADO PARA EL BANNER OFICIAL DE ISHUME -->
        <div class="banner-contrato">
                <img src="img/banner_ishume.png" alt="Banner de ISHUME" width="1923" height="456">>
        </div>

        <!--  DATOS DEL CLIENTE-->
        <section class="seccion-contrato">
            <h2>Datos del cliente</h2>

            <div class="fila">

                <div class="campo">
                    <label for="Nombre">Nombre</label>
                    <div class="input-con-estado">                        
                        <input type="text" class="estadoDatos" id="Nombre" name="Nombre" maxlength="100" required>
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

                <div class="campo">
                    <label for="Apellidos">Apellidos</label>
                    <div class="input-con-estado">        
                        <input type="text" class="estadoDatos" id="Apellidos" name="Apellidos" maxlength="100" required>
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

            </div>

            <div class="fila">

                <div class="campo">
                    <label for="Testigo">Testigo</label>
                    <div class="input-con-estado">                        
                        <input type="text" class="estadoDatos" id="Testigo" name="Testigo" maxlength="100">
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

                <div class="campo">
                    <label for="TipoDOI">Tipo de DOI</label>

                    <select id="TipoDOI" name="TipoDOI">
                        <option value="seleccione un tipo"> Seleccione un tipo </option>
                        <option value="DNI"> DNI </option>
                        <option value="RUC"> RUC </option>
                        <option value="CE"> Carné de Extranjería </option>
                    </select>
                </div>

            </div>

            <div class="fila">
                <div class="campo">
                    <label for="NumDOI">Número de DOI</label>
                    <div class="input-con-estado">                        
                        <input type="text" class="estadoDatos" id="NumDOI" name="NumDOI">
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

                <div class="campo">
                    <label for="Telefono">Teléfono</label>
                    <div class="input-con-estado">                        
                        <input type="text" class="estadoDatos" id="Telefono" name="Telefono">
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

                <div class="campo">
                    <label for="Correo">Email</label>
                    <div class="input-con-estado">                        
                        <input type="email" class="estadoDatos" id="Correo" name="Correo" maxlength="100">
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

                <div class="campo">
                    <label for="Direccion-cliente">Direccion</label>
                    <div class="input-con-estado">                        
                        <input type="text" class="estadoDatos" id="Direccion-cliente" name ="Direccion-cliente" maxLength="200">
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>

            </div>
        </section>


        <!-- ==============================
             DATOS DEL COLEGIO
        =============================== -->
        <section class="seccion-contrato">
            <h2>Datos del colegio</h2>

            <div class="campo">

                <label for="buscarColegio"> Buscar colegio </label>
                <input type="text" id="buscarColegio" name="buscarColegio" placeholder="Escriba el nombre del colegio..." autocomplete="off">

            </div>

            <div id="resultadosColegios" class="resultados-colegios">
                <!-- Los resultados aparecerán aquí -->
            </div>

            <input type="hidden" id="idColegio" name="idColegio">
            <p id="estadoColegio" class="estado-colegio"></p>

        </section>


        <!-- ==============================
             DATOS DEL CONTRATO
        =============================== -->
        <section class="seccion-contrato-detalle">
            <h2>Detalles del contrato </h2>

            <div class="campo">
                <label for="Evento">Evento</label>
                <div class="input-con-estado">
                    <input type="text" class="estadoDatos" id="Evento" name="Evento" maxlength="150" required>
                    <p id="EstadoGeneral" class="checks" ></p>
                </div>
            </div>

            <div class="campo">
                <label for="Servicio"> Servicio </label>
                <div class="input-con-estado">
                    <input type="text" class="estadoDatos" id="Servicio" name="Servicio" maxlength="150" required>
                    <p id="EstadoGeneral" class="checks" ></p>
                </div>
            </div>

            <div class="fila">

                <div class="campo">
                    <label for="FechaEvento"> Fecha del evento </label>
                    <div class="input-con-estado">
                        <input type="date" class="estadoDatos" id="FechaEvento" name="FechaEvento" required>
                        <p id="EstadoGeneral" class="checks" ></p>
                    </div>
                </div>
            </div>

            <div class="campo">
                <label for="Direccion"> Dirección </label>
                <div class="input-con-estado">
                    <input type="text" class="estadoDatos" id="Direccion" name="Direccion" maxlength="200" required>
                    <p id="EstadoGeneral" class="checks" ></p>
                </div>
            </div>

            <div class="fila">
                <div class="campo">
                    <label for="idProvincia"> Provincia</label>

                    <div class="select-con-estado">
                        <select id="idProvincia" name="idProvincia" class="provincia-select">

                            <option value=""> Seleccione una provincia </option>

                            <?php foreach($Provincias as $provincia): ?>
                                <option value="<?= htmlspecialchars($provincia['idProvincia']) ?>">
                                    <?= htmlspecialchars($provincia['NomProvincia']) ?>
                                </option>
                            <?php endforeach; ?>
                             
                        </select>
                        <p id="estadoprovincia" class="estado"></p>
                     </div>

                </div>


                <div class="campo">

                    <label for="idDistrito"> Distrito</label>

                    <div class="select-con-estado">
                        <select id="idDistrito" name="idDistrito">
                            <option> Seleccione un distrito </option>
                        </select>
                        <p id="estadodistrito" class="estado"></p>
                    </div>

                </div>
                       
            </div>
                            
        </section>


        <!-- ==============================
             ACCIONES
        =============================== -->
        <div class="acciones-contrato">
            <button type="button" id="btnRegistrarContrato" class="boton-principal">
                Registrar contrato
            </button>
        </div>

    </main>


    <!-- ==============================
         MODAL NUEVO COLEGIO
    =============================== -->

    <div id="modalColegio" class="modal-colegio">

        <div class="contenido-colegio">

            <h2>Agregar nuevo colegio</h2>
            <div class="campo">
                <label for="NombreColegio">Nombre del colegio</label>
                <input type="text" id="NombreColegio" name="NombreColegio" maxlength="150">
            </div>

            <p id="errorColegioNuevo" class="error-modal"></p>                   
            
            <div class="acciones-colegio">
                <button type="button" id="btnCancelarColegio" class="boton-cancelar">
                    Cancelar
                </button>
                <button type="button" id="btnRegistrarColegio" class="boton-principal">
                    Registrar colegio
                </button>
            </div>

        </div>

    </div>


    <script src="js/contratos.js"></script>

</body>
</html>