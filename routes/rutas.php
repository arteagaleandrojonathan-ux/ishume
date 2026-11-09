<?php
    
    $pagina = $_GET['opcion'] ?? 'dashboard';

    switch ($pagina) {


        //TODAS RUTAS A CONFIGURACION
        case 'configuracion':
            require_once __DIR__ . '/../app/controllers/configuracion.php';
            $controller = new ConfiguracionController();
            $controller->Configuracion();
            break;

        

        //TODAS LAS RUTAS A INVENTARIO
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


        //TODAS LAS RUTAS DE CONTRATO
        case 'contratos':
            require_once __DIR__ . '/../app/controllers/contratos.php';
            $controller = new ContratosController();
            $controller->contratos();
            break;
            
        case 'nuevoContrato':
            require_once __DIR__ . '/../app/controllers/contratos.php';
            $controller = new ContratosController();
            $controller->nuevoContrato();
            break;  
             
        case 'obtenerDistritos':
            require_once __DIR__ . '/../app/controllers/contratos.php';

            $controller = new ContratosController();
            $controller->obtenerDistritos();
            break;

        case 'registrarContrato':
            require_once __DIR__ . '/../app/controllers/contratos.php';
            $controller = new ContratosController();
            $controller->registrarContrato();
            break;
         case 'buscarColegios':
            require_once __DIR__ . '/../app/controllers/contratos.php';
            $controller = new ContratosController();
            $controller->buscarColegios();
            break;
        case 'registrarColegio':
            require_once __DIR__ . '/../app/controllers/contratos.php';
            $controller = new ContratosController();
            $controller->registrarColegio();
            break;
        
        case 'listaContratos':
            require_once __DIR__ . '/../app/controllers/contratos.php';
            $controller = new ContratosController();
            $controller->listaContratos();
            break;
    

        //TODAS LAS RUTAS DE VENTAS
        case 'nuevaVenta':
            require_once __DIR__ . '/../app/controllers/ventas.php';
            $controller = new VentasController();
            $controller->nuevaVenta();
            break;
        case 'registrarVenta':
            require_once __DIR__ . '/../app/controllers/ventas.php';
            $controller = new VentasController();
            $controller->registrarVenta();
            break;
        case 'ventas':
            require_once __DIR__ . '/../app/controllers/ventas.php';
            $controller = new VentasController();
            $controller->ventas();
            break; 
        case 'detalleVenta':
            require_once __DIR__ . '/../app/controllers/ventas.php';
            $controller = new VentasController();
            $controller->detalleVenta();
            break;


        //TODAS LAS RUTAS A COMPRA
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

        case 'anularCompra':
            require_once __DIR__ . '/../app/controllers/compra.php';
            $controller = new CompraController();
            $controller->AnularCompra();
            break;    
                    
        case 'registrarCompra':
            require_once __DIR__ . '/../app/controllers/compra.php';
            $controller = new CompraController();
            $controller->RegistrarCompra();
            break;
            

        //EL DASHBOARD POR DEFECTO
        default:
            require_once __DIR__ . '/../app/controllers/dashboard.php';
            $controller = new DashboardController();
            $controller->Dashboard();
            break;
    }

?>
