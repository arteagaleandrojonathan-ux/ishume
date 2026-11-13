<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
      
    <link rel="stylesheet" href="../public/css/PanelPrincipal.css">

     <?php if ($vista != 'dashboard'): ?>
            <?php $archivoCss = __DIR__ . '/../../public/css/' . ucfirst($vista) . '.css'; ?>
            <link rel="stylesheet" href="css/<?= ucfirst($vista) ?>.css">
        <?php endif; ?>

    <title>Document</title>
</head>
<body>
    <div>
        
        <div class="PanelPrincipal">

            <!-- Botón del menú (solo se ve en celular y tablet vertical) -->
            <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Abrir o cerrar menú">

            <label for="menu-toggle" class="menu-boton">
                <span class="menu-icono"></span>
                ISHUME
            </label>

            <label for="menu-toggle" class="menu-fondo"></label>


    <!-- Menu parte izqueirda -->
            <aside class="menu">
                
                <div class="logo">

                    ISHUME

                </div>

                <nav>
    <a class="<?= $vista == 'dashboard' ? 'activo' : '' ?>"
       href="index.php?opcion=dashboard">
        Inicio
    </a>

    <div class="menu-inventario">

        <a class="<?= in_array($vista, ['inventario', 'papelera']) ? 'activo' : '' ?>"
           href="index.php?opcion=inventario">
            Inventario
        </a>

        <a href="index.php?opcion=papelera"
           class="subopcion-inventario <?= $vista == 'papelera' ? 'activo' : '' ?>">
            Papelera
        </a>

    </div>

    <a class="<?= in_array($vista, ['compra', 'registrosCompra']) ? 'activo' : '' ?>"
       href="index.php?opcion=compra">
        Compra
    </a>

    <a class="<?= in_array($vista, ['contratos', 'listaContratos', 'detalleContrato', 'detalleVenta']) ? 'activo' : '' ?>"
       href="index.php?opcion=contratos">
        Contratos
    </a>

    <a class="<?= $vista == 'configuracion' ? 'activo' : '' ?>"
       href="index.php?opcion=configuracion">
        Configuración
    </a>
</nav>
            </aside>
            
        <!-- CONTENIDO PRINCIPAL -->
            <main class="contenido">

            
                <?php
                
                require_once __DIR__ . '/' . $vista . '.php';

                ?>
            
            </main>
            
        </div>
    </div>

</body>
    <?php if ($vista == 'inventario'): ?>
        <script src="js/bajaInventario.js"></script>
        <script src="js/editarModelo.js"></script>
        <script src="js/detalleModelo.js"></script>
    <?php endif; ?>

<?php if ($vista == 'compra'): ?>
    <script src="js/compra.js"></script>
<?php endif; ?>

<?php if ($vista == 'registrosCompra'): ?>
    <script src="js/anularCompra.js"></script>
<?php endif; ?>

<?php if ($vista == 'detalleVenta'): ?>
    <script src="js/ventas.js"></script>
<?php endif; ?>

</html>