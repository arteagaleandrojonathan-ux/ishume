<?php   

require_once __DIR__ . '/../models/contrato.php';

class ContratosController {

    private Contrato $AccionModelo;

    public function __construct(){
        $this->AccionModelo = new Contrato();
    }


    public function contratos(){
        $vista = 'contratos';
        require_once __DIR__ . '/../views/PanelPrincipal.php';
    }

    public function nuevoContrato(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['token_contrato'] = bin2hex(random_bytes(16));
        $tokenContrato = $_SESSION['token_contrato'];

        $Provincias = $this->AccionModelo->obtenerProvincias();
        $Colegios = $this->AccionModelo->obtenerColegios();
        $MetodosPago = $this->AccionModelo->obtenerMetodosPago();

        $vista = 'nuevoContrato';
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

