<main> 
        <form method="GET" action="index.php" class="formulario">

            <input type="hidden" name="opcion"  value="inventario" class="lupita">


            <!-- EL BUSCADOR -->
            <div class="buscador">
                <input type="text"
                         name="buscar"
                        placeholder="Buscar material..."
                        value="<?= $_GET['buscar'] ?? '' ?>">
            </div>


                <button type="submit" class="boton-buscar">
                    Buscar
                </button>


        <!-- EL DE NIVELES OSEA EL FILTRO DE campos-->
        <div class="campo-filtro">

            <label for="filtroMarco">Tipo de Marco </label>
                <select id="filtroMarco" name="marco" class="filtroboton">
                
                    <option value="">Todos</option>

                    <?php foreach ($Marcos as $marcoBD): ?>
                        <option class = "opciones" value="<?= $marcoBD['NombreMarco'] ?>" 
                            <?= ($_GET['marco'] ?? '') === $marcoBD['NombreMarco'] ? 'selected' : '' ?>>
                            <?= $marcoBD['NombreMarco'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
        </div>   


        <!-- EL FILTRO DE NIVELES-->
        <div class="campo-filtro">

            <label for="filtroNivel"> Nivel Educativo </label>                       
                <select id='filtroNivel' name="nivel"  class="filtroboton">

                    <option value="">Todos los niveles</option>

                    <?php foreach ($Niveles as $nivelBD): ?>
                        <option value="<?= $nivelBD["NivelEducativo"] ?>"
                            <?= ($_GET['nivel'] ?? '') === $nivelBD["NivelEducativo"] ? 'selected' : '' ?>>
                            <?= $nivelBD["NivelEducativo"] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

        </div>


        <!-- EL FILTRO DE COLORES-->
        <div class="campo-filtro">                

            <label for="filtroColor"> Color </label>
                <select id='filtroColor' name="color" class="filtroboton"> 

                    <option value="">Todos los colores</option> 

                     <?php foreach ($Colores as $coloresBD): ?>
                        <option value ="<?= $coloresBD["Color"] ?>"
                            <?= ($_GET['color'] ?? '') === $coloresBD["Color"] ? 'selected' : '' ?>>
                            <?= $coloresBD["Color"] ?>
                        </option>     
                    <?php endforeach ; ?>   
                </select>
        </div>       


        <!-- EL FILTRO DE CATEGORIAS-->
        <div class="campo-filtro">
            <label for="filtroCategoria"> Categoría </label>
                <select id='filtroCategoria' name="categoria" class="filtroboton">

                    <option value="">Todas las categorías </option>

                    <?php foreach($Categorias as $categoriaBD): ?>
                        <option value= '<?= $categoriaBD['NombreCategoria'] ?>'
                            <?= ($_GET['categoria'] ?? '') === $categoriaBD['NombreCategoria'] ? 'selected' : '' ?>>
                            <?= $categoriaBD['NombreCategoria'] ?>
                        </option>
                    <?php endforeach ; ?>

                </select>
        </div>
    </form> 
</main>




<section class="tabla-contenedor">
    <table > 
        <thead> 
            <tr>
                <th>NOMBRE </th>
                <th>CATEGORIA </th>
                <th>MATERIAL </th>
                <th>PRECIO </th>
                <th>STOCK </th>
                <th>ANCHO </th>
                <th>LARGO </th>
                <th>COLOR </th>
                <th>NIVEL </th>
                <th>ESTADO </th>
                <th>ACCIONES</th>
            </tr>
        </thead>

        <tbody> 

        <?php foreach ($RecibeModelo as $datos):  ?>
            <tr >
                <td ><?= $datos['NombreMaterial']  ?></td>
                <td ><?= $datos['NombreCategoria']  ?></td>
                <td ><?= $datos['NombreMarco']  ?></td>
                <td ><?= $datos['Precio']  ?></td>
                <td ><?= $datos['Cantidad']  ?></td>
                <td ><?= $datos['TamañoHorizontal']  ?></td>
                <td ><?= $datos['TamañoVertical']  ?></td>
                <td ><?= $datos['Color']  ?></td>
                <td ><?= $datos['NivelEducativo']  ?></td>
                <td ><?= $datos['Estado']  ?></td>
                <td>
                    <a class ="lin-editar "href="?opcion=editar&idModelo=<?=$datos['idModelo'] ?>">Editar</a>
                    <button type="button"
                        class="boton-baja"
                        data-id="<?= $datos['idModelo'] ?>"
                        data-nombre="<?= htmlspecialchars($datos['NombreMaterial']) ?>"
                        data-stock="<?= $datos['Cantidad'] ?>">
                            Dar de baja
                    </button>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>


<nav class="paginacion">
    
    <span class="rango">
            <?= $pagina ?> / <?= $totalPaginas ?>
    </span>

    <?php if ($pagina > 1): ?>
        <a class="flecha"
            href="?opcion=inventario&buscar=<?=urlencode($_GET['buscar'] ?? '')?>&marco=<?=urlencode($_GET['marco'] ?? '')?>&nivel=<?=urlencode($_GET['nivel'] ?? '')?>&color=<?=urlencode($_GET['color'] ?? '')?>&categoria=<?=urlencode($_GET['categoria'] ?? '')?>&pagina=<?= $pagina - 1 ?>">
            ‹
        </a>
    <?php else: ?>
        <span class="flecha desactivada"> 
            ‹
         </span>
    <?php endif; ?>


    <?php if ($pagina < $totalPaginas): ?>
        <a class="flecha"
            href="?opcion=inventario&buscar=<?=urlencode($_GET['buscar'] ?? '')?>&marco=<?=urlencode($_GET['marco'] ?? '') ?>&nivel=<?=urlencode($_GET['nivel'] ?? '')?>&color=<?=urlencode($_GET['color'] ?? '')?>&categoria=<?=urlencode($_GET['categoria'] ?? '')?>&pagina=<?= $pagina + 1 ?>">
            ›
        </a> 
    <?php else: ?>
    <span class="flecha desactivada"> 
        › 
    </span>   
    <?php endif; ?>
</nav>

<?php if (!empty($mostrarEditar)): ?>
    <?php require_once __DIR__ . '/editarModelo.php'; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/darDeBaja.php'; ?>

