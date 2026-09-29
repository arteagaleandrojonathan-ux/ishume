

<div class="modal-editar">

    <div class="contenido-editar">
        
        <!-- CABECERA -->
        <div class="cabecera-editar">

            <h2>Editar modelo</h2>

          
        </div>


        <!-- FORMULARIO -->
        <form method="POST" action="index.php?opcion=editar" class="form-editar">

            <input type="hidden"
                   name="idModelo"
                   value="<?= $modelo['idModelo'] ?>">


            <!-- NOMBRE -->
            <div class="campo-editar">
                <label>Nombre del material</label>

                <input type="text"
                       name="NombreMaterial"
                       id="NombreMaterial"
                       value="<?= $modelo['NombreMaterial'] ?>">
            </div>


            <!-- PRECIO -->
            <div class="campo-editar">
                <label>Precio</label>

                <input type="number"
                       name="Precio"
                       id="Precio"
                       step="0.01"
                       value="<?= $modelo['Precio'] ?>"
                        required>
                        <span class="mensaje-error" id="errorPrecio"></span>
            </div>


            <!-- ANCHO -->
            <div class="campo-editar">
                <label>Ancho</label>

                <input type="number"
                       name="TamañoHorizontal"
                       id="TamañoHorizontal"
                       step="0.01"
                       value="<?= $modelo['TamañoHorizontal'] ?>"
                        required>
                <span class="mensaje-error" id="errorHorizontal"></span>
            </div>


            <!-- LARGO -->
            <div class="campo-editar">
                <label>Largo</label>

                <input type="number"
                       name="TamañoVertical"
                       id="TamañoVertical"
                        step="0.01"
                       value="<?= $modelo['TamañoVertical'] ?>"
                        required>
                <span class="mensaje-error" id="errorVertical"></span>        
            </div>


            <!-- COLOR -->
            <div class="campo-editar">
                <label>Color</label>

                <input type="text"
                       name="Color"
                       id="Color"
                       value="<?= $modelo['Color'] ?>">
            </div>


            <!-- NIVEL EDUCATIVO -->
             <div class ="campo-editar" >
                <label>Nivel educativo</label>
            <select name="NivelEducativo" class="select-editar"required>
               
                <option value="Inicial"
                    <?= $modelo['NivelEducativo'] === 'Inicial' ? 'selected' : '' ?>>
                        Inicial
                </option>

                <option value="Primaria y Secundaria"
                    <?= $modelo['NivelEducativo'] === 'Primaria y Secundaria' ? 'selected' : '' ?>>
                        Primaria y Secundaria
                </option>

            </select>
            </div>

            <!-- BOTONES -->
            <div class="acciones-editar">

                <a href="index.php?opcion=inventario" class="boton-cancelar">
                    Cancelar
                </a>

                <button type="submit" class="boton-guardar">
                    Guardar 
                </button>

            </div>

        </form>

    </div>

</div>