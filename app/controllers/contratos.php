<?php   

class ContratosController {

    public function contratos() {
        $vista = 'contratos';
        require_once __DIR__ . '/../views/PanelPrincipal.php';

    }


    public function contratos(){
        $Provincias = $this->obtenerModelo->obtenerProvincias();
        $Clientes = $this->obtenerModelo->obtenerClientes();
        $Colegios = $this->obtenerModelo->obtenerColegios();
        $vista = 'contrastos';
        require_once __DIR__ . '/../views/PanelPrincipal.php';

    }

    public function obtenerDistritos(){
        $idProvincia = $_POST['idProvincia'];

        if ($idProvincia){
            echo json_encode([]) 
            return;

        }
        $Distritos = $this->obtenerModelo->obtenerDistritosPorProvincia($idProvincia);
        header('content-type: application/json; chartset=utf-8');
        echo  json_encode($Distritos);

    }
}

