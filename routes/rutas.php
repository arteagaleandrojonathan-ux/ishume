<?php
    
    $pagina = $_GET['opcion'] ?? 'dashboard';

    switch ($pagina) {
        case 'configuracion':
            require_once __DIR__ . '/../app/controllers/configuracion.php';
            $controller = new ConfiguracionController();
            $controller->Configuracion();
            break;
        case 'inventario':
            require_once __DIR__ . '/../app/controllers/inventario.php';
            $controller = new InventarioController();
            $controller->Inventario();
            break;
        case 'editar':
             require_once __DIR__ . '/../app/controllers/inventario.php';
            $controller = new InventarioController();
            $controller->Editar();
            break;
        case 'baja':
            require_once __DIR__ . '/../app/controllers/inventario.php';
            $controller = new InventarioController();
            $controller->DarDeBaja();
            break;
        case 'papelera':
            require_once __DIR__ . '/../app/controllers/inventario.php';
            $controller = new InventarioController();
            $controller->VerPapelera();
            break;
        case 'contratos':
            require_once __DIR__ . '/../app/Controllers/contratos.php';
            $controller = new ContratosController();
            $controller->contratos();
            break;
            
        case 'compra':
            require_once __DIR__ . '/../app/controllers/compra.php';
            $controller = new CompraController();
            $controller->Compra();
            break;
        
        case 'registrosCompra':
            require_once __DIR__ .'/../app/controllers/compra.php';
            $controller = new CompraController();
            $controller->RegistrosCompra();
            break;
            
        case 'registrarCompra':
            require_once __DIR__ . '/../app/controllers/compra.php';
            $controller = new CompraController();
            $controller->RegistrarCompra();
            break;
            
        default:
            require_once __DIR__ . '/../app/controllers/dashboard.php';
            $controller = new DashboardController();
            $controller->Dashboard();
            break;
    }

?>
