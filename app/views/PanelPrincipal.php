<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >

    <link rel="stylesheet" href="../public/css/PanelPrincipal.css">

     <?php if ($vista != 'dashboard'): ?>
            <link rel="stylesheet" href="css/<?= ucfirst($vista) ?>.css">
        <?php endif; ?>

    <title>Document</title>
</head>
<body>
    <div>
        
        <div class="PanelPrincipal">
        
        
    <!-- Menu parte izqueirda -->
            <aside class="menu">
                
                <div class="logo">

                    ISHUME

                </div>

                <nav>
                    <a href="index.php?opcion=dashboard" > Inicio </a>
                    <div class="menu-inventario">
                        <a href="index.php?opcion=inventario">Inventario</a>

                        <a href="index.php?opcion=papelera" class="subopcion-inventario">
                            Papelera
                        </a>
                    </div>
                    <a href="index.php?opcion=compra"> Compra </a>
                    <a href="index.php?opcion=ventas"> Ventas</a>
                    <a href="index.php?opcion=configuracion"> Configuración </a>
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
<?php endif; ?>

<?php if ($vista == 'compra'): ?>
    <script src="js/compra.js"></script>
<?php endif; ?>
</html>