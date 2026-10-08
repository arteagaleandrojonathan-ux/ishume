<?php   

require_once __DIR__ . '/../models/contrato.php';

class ContratosController {

    private Contrato $AccionModelo;

    public function __construct(){
        $this->AccionModelo = new Contrato();
    }


    public function contratos(){
        $Provincias = $this->AccionModelo->obtenerProvincias();
        $Clientes = $this->AccionModelo->obtenerClientes();
        $Colegios = $this->AccionModelo->obtenerColegios();
        

        $vista = 'contratos';
        require_once __DIR__ . '/../views/PanelPrincipal.php';

    }

    public function obtenerDistritos(){
        $idProvincia = $_POST['idProvincia'];

        if ($idProvincia === ''){
            echo json_encode([]);
            return;
        }

        $Distritos = $this->AccionModelo->obtenerDistritosPorProvincia($idProvincia);
        header('content-type: application/json; charset=utf-8');
        echo  json_encode($Distritos);

    }
}

