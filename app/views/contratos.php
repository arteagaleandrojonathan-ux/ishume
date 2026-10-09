<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Contrato</title>

    <link rel="stylesheet" href="css/contratos.css">
</head>

<body>

    <main class="contenedor-contrato">
        <!-- ESPACIO RESERVADO PARA EL BANNER OFICIAL DE ISHUME -->
        <div class="banner-contrato">
            <img src="img/banner.png" alt="">
        </div>

        <!-- ==============================
             DATOS DEL CLIENTE
        =============================== -->
        <section class="seccion-contrato">
            <h2>Datos del cliente</h2>

            <div class="fila">

                <div class="campo">
                    <label for="Nombre">Nombre</label>
                    <input type="text" id="Nombre" name="Nombre" maxlength="100" required>
                </div>

                <div class="campo">
                    <label for="Apellidos">Apellidos</label>
                    <input type="text" id="Apellidos" name="Apellidos" maxlength="100" required>
                </div>

            </div>

            <div class="fila">

                <div class="campo">
                    <label for="Representante">Testigo</label>
                    <input type="text" id="Representante" name="Representante" maxlength="100">
                </div>

                <div class="campo">
                    <label for="TipoDOI">Tipo de DOI</label>

                    <select id="TipoDOI" name="TipoDOI">
                        <option value=""> Seleccione un tipo </option>
                        <option value="DNI"> DNI </option>
                        <option value="RUC"> RUC </option>
                        <option value="CE"> Carné de Extranjería </option>
                    </select>
                </div>

            </div>

            <div class="fila">
                <div class="campo">
                    <label for="NumDOI">Número de DOI</label>
                    <input type="text" id="NumDOI" name="NumDOI" maxlength="20">
                </div>

                <div class="campo">
                    <label for="Telefono">Teléfono</label>
                    <input type="text" id="Telefono" name="Telefono" maxlength="20">
                </div>

                <div class="campo">
                    <label for="Correo">Email</label>
                    <input type="email" id="Correo" name="Correo" maxlength="100">
                </div>

                <div class="campo">
                    <label for="Direccion-cliente">Direccion</label>
                    <input type="text" id="Direccion-cliente" name ="Direccion-cliente", maxLength="100">
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

            <button type="button" id="btnAgregarColegio" class="boton-secundario">
                + Agregar nuevo colegio
            </button>

        </section>


        <!-- ==============================
             DATOS DEL CONTRATO
        =============================== -->
        <section class="seccion-contrato-detalle">
            <h2>Detalles del contrato </h2>

            <div class="campo">
                <label for="Evento">Evento</label>
                <input type="text" id="Evento" name="Evento" maxlength="150" required>
            </div>

            <div class="campo">
                <label for="Servicio"> Servicio </label>
                <input type="text" id="Servicio" name="Servicio" maxlength="150" required>
            </div>

            <div class="fila">
                <div class="campo">
                    <label for="Proforma"> Proforma </label>
                    <input type="number" id="Proforma" name="Proforma"min="0"step="0.01">
                </div>

                <div class="campo">
                    <label for="FechaEvento"> Fecha del evento </label>
                    <input type="date" id="FechaEvento" name="FechaEvento" required>
                </div>
            </div>

            <div class="campo">
                <label for="Direccion"> Dirección </label>
                <input type="text" id="Direccion" name="Direccion" maxlength="200" required>
            </div>

            <div class="fila">
                <div class="campo">
                    <label for="idProvincia"> Provincia</label>

                    <select id="idProvincia" name="idProvincia">

                        <option value=""> Seleccione una provincia </option>

                        <?php foreach($Provincias as $provincia): ?>
                            <option value="<?= htmlspecialchars($provincia['idProvincia']) ?>">
                                <?= htmlspecialchars($provincia['NomProvincia']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>


                <div class="campo">
                    <label for="idDistrito"> Distrito</label>
                        <select id="idDistrito" name="idDistrito">
                            <option> Seleccione un distrito </option>
                        </select>
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