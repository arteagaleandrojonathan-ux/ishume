<div class="cabecera-bajas">

    <div class ="texto-bajas">
        <h2>Papelera de inventario</h2>
    </div>

    <a href="index.php?opcion=inventario"
       class="boton-volver">
        Volver
    </a>

</div>


<div class="contenedor-papelera-inventario">
    <div class="panel-papelera-inventario">
        <div class="tabla-papelera-inventario">
            <section class="tabla-bajas">
                <table>
                    <thead>
                        <tr>
                            <th>ID BAJA</th>
                            <th>MODELO</th>
                            <th>CATEGORÍA</th>
                            <th>MATERIAL</th>
                            <th>CANTIDAD</th>
                            <th>MOTIVO</th>
                            <th>FECHA DE BAJA</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($Papelera)): ?>
                            <?php foreach ($Papelera as $baja): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($baja['idBaja']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($baja['NombreMaterial']) ?>
                                    </td>

                                    <td>
                                         <?= htmlspecialchars($baja['NombreCategoria']) ?>
                                     </td>

                                    <td>
                                        <?= htmlspecialchars($baja['NombreMarco']) ?>
                                    </td>

                                    <td>    
                                        <?= htmlspecialchars($baja['Cantidad']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($baja['Motivo']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($baja['FechaBaja']) ?>
                                    </td>
                                </tr>   
                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    No existen productos dados de baja.
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</div>