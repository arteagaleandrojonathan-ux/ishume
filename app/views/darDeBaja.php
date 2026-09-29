
<div class="modal-baja" id="modalBaja">

    <div class="contenido-baja">

        <div class="cabecera-baja">

            <h2>Dar de baja producto</h2>

            <button type="button"
                    id="cerrarModalBaja"
                    class="cerrar-baja">
                ×
            </button>

        </div>

        <form method="POST"
              action="index.php?opcion=baja"
              id="formBajaInventario">

            <input type="hidden"
                   name="idModelo"
                   id="idModeloBaja">

            <div class="informacion-baja">

                <p>
                    <strong>Modelo:</strong>
                    <span id="nombreModeloBaja"></span>
                </p>

                <p>
                    <strong>Stock actual:</strong>
                    <span id="stockActualBaja"></span>
                </p>

            </div>

            <div class="campo-baja">

                <label for="cantidadBaja">
                    Cantidad a retirar
                </label>

                <input type="number"
                       name="Cantidad"
                       id="cantidadBaja"
                       min="1"
                       step="1">

                <span class="mensaje-error"
                      id="errorCantidadBaja"></span>

            </div>

            <div class="campo-baja">

                <label for="motivoBaja">
                    Motivo de la baja
                </label>

                <textarea name="Motivo"
                          id="motivoBaja"
                          maxlength="500"
                          placeholder="Especifique el motivo de la baja..."></textarea>

                <span class="mensaje-error"
                      id="errorMotivoBaja"></span>

            </div>

            <div class="acciones-baja">

                <button type="submit"
                        class="boton-confirmar-baja">
                    Dar de baja
                </button>

            </div>

        </form>

    </div>

</div>