<?php

require_once __DIR__ . '/../models/inventario.php';

class InventarioController {

    private Inventario $AccionModelo;

    public function   __construct() {
        $this->AccionModelo=new Inventario();
    }

    public function Inventario() {
        //Mi buscador xd
        $buscar = $_GET['buscar'] ?? '';

        // Mis filtros oño 
        $marco = $_GET['marco'] ?? '';
        $nivel = $_GET['nivel'] ?? '';
        $color = $_GET['color'] ?? '';
        $categoria = $_GET['categoria'] ?? '';

        $pagina = $_GET['pagina'] ?? 1;
        $porPagina = 10;
        $inicio = ($pagina - 1) * $porPagina;

        $RecibeModelo = $this->AccionModelo->HablaConModelos($buscar, $marco, $nivel, $color, $categoria, $inicio, $porPagina);
        
        $totalModelos = $this->AccionModelo->contarModelos($buscar,$marco,$nivel,$color,$categoria);
        $totalPaginas = ceil($totalModelos / $porPagina);
        

        $Marcos = $this->AccionModelo->obtenerMarcos();
        $Categorias = $this->AccionModelo->obtenerCategorias();
        $Niveles = $this->AccionModelo->obtenerNiveles();
        $Colores = $this->AccionModelo->obtenerColores();

        $vista = 'inventario';
        require_once __DIR__ . '/../views/PanelPrincipal.php';

    }

    public function Editar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $idModelo = $_POST['idModelo'] ?? '';

        $NombreMaterial = $_POST['NombreMaterial'] ?? '';
        $Precio = $_POST['Precio'] ?? 0;
        $TamañoHorizontal = $_POST['TamañoHorizontal'] ?? 0;
        $TamañoVertical = $_POST['TamañoVertical'] ?? 0;
        $Color = $_POST['Color'] ?? '';
        $NivelEducativo = $_POST['NivelEducativo'] ?? '';

            $this->AccionModelo->actualizarModelo( $idModelo, $NombreMaterial,$Precio, $TamañoHorizontal, $TamañoVertical,
            $Color, $NivelEducativo );

             header('Location: index.php?opcion=inventario');
            exit;
        }

    // MOSTRAR EL FORMULARIO DE EDICIÓN
    $idModelo = $_GET['idModelo'] ?? '';

    // Obtener el modelo seleccionado
    $modelo = $this->AccionModelo->obtenerModeloPorId($idModelo);

    // DATOS DEL INVENTARIO
    $buscar = $_GET['buscar'] ?? '';
    $marco = $_GET['marco'] ?? '';
    $nivel = $_GET['nivel'] ?? '';
    $color = $_GET['color'] ?? '';
    $categoria = $_GET['categoria'] ?? '';

    $pagina = $_GET['pagina'] ?? 1;

    $porPagina = 10;

    $inicio = ($pagina - 1) * $porPagina;

    $RecibeModelo = $this->AccionModelo->HablaConModelos($buscar, $marco, $nivel, $color, $categoria, $inicio,
        $porPagina);

    $totalModelos = $this->AccionModelo->contarModelos($buscar,$marco,$nivel,$color,$categoria);

    $totalPaginas = ceil($totalModelos / $porPagina);

    $Marcos = $this->AccionModelo->obtenerMarcos();
    $Categorias = $this->AccionModelo->obtenerCategorias();
    $Niveles = $this->AccionModelo->obtenerNiveles();
    $Colores = $this->AccionModelo->obtenerColores();

    $vista = 'inventario';

    $mostrarEditar = true;

    require_once __DIR__ . '/../views/PanelPrincipal.php';
    }


    public function DarDeBaja(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?opcion=inventario');
            exit;
        }

        $idModelo = $_POST['idModelo'] ?? '';
        $cantidad = $_POST['Cantidad'] ?? '';
        $motivo = trim($_POST['Motivo'] ?? '');

        if ($idModelo === '' || $cantidad === '' || $motivo === '') {
            header('Location: index.php?opcion=inventario&error=datos');
            exit;
        }

        try {

            $this->AccionModelo->darDeBaja( $idModelo, $cantidad, $motivo );

            header('Location: index.php?opcion=inventario&baja=ok');
            exit;

        } catch (Exception $e) {
            header(
                'Location: index.php?opcion=inventario&error=' .
                urlencode($e->getMessage())
            );

        exit;
        }
    }   
    public function VerPapelera(){
        $Papelera = $this->AccionModelo->obtenerBajas();

        $vista = 'papelera';

        require_once __DIR__ . '/../views/PanelPrincipal.php';
    }
}
?>