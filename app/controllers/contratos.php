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

    public function registrarContrato(){

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: index.php?opcion=nuevoContrato');
            exit;
        }

        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        $tokenGuardado = $_SESSION['token_contrato'] ?? '';
        $tokenEnviado = $_POST['token'] ?? '';

        if($tokenGuardado  === '' || !hash_equals($tokenGuardado, $tokenEnviado)){
            $this->responderJson([
                'ok'=> false,
                'mensaje' => 'Este formulario ya se envio. Recargue la pagina '
            ]);
            return;
        }

        $cliente =[
            'Nombre' => trim($_POST['Nombre'] ?? ''),
            'Apellidos' => trim ($_POST['Apellidos'] ?? ''),
            'Testigo' => trim($_POST['Testigo'] ?? ''),
            'Direccion' => trim ($_POST['DireccionCliente'] ?? ''),
            'TipoDOI' => trim($_POST['TipoDOI'] ?? ''),
            'NumDOI' => trim($_POST['NumDOI'] ?? ''),
            'Telefono' => trim($_POST['Telefono'] ?? ''),
            'Correo' => trim($_POST['Correo'] ?? '')
        ];

        $contrato =[
            'idColegio' => trim($_POST['idColegio'] ?? ''),
            'idDistrito' => trim($_POST['idDistrito'] ?? ''),
            'Evento' => trim($_POST['Evento'] ?? ''),
            'Servicio' => trim($_POST['Servicio'] ?? ''),
            'Direccion' => trim($_POST['Direccion'] ?? ''),
            'FechaEvento' => trim($_POST['FechaEvento'] ?? ''),
            'Condiciones' => trim($_POST['Condiciones'] ?? '')
        ];

        $adelanto = [
            'Monto' => trim($_POST['Adelanto'] ?? ''),
            'idMetodoPago' => trim($_POST['idMetodoPago'] ?? ''),
            'NumOperacion' => trim($_POST['NumOperacion'] ?? '')
        ];

        //LLAMAR AL MODELO PARA PODER RELLENAR LAS CAJAS QUE SE HIZO ARRIBA 
        try{    
            $idContrato = $this->AccionModelo->registrarContrato($cliente ,$contrato ,$adelanto);

            unset($_SESSION['token_contrato']);

            $this->responderJson([
                'ok' => true,
                'idContrato' => $idContrato
                

            ]);
        } catch (PDOException $e) {
            error_log('registrarContrato: ' . $e->getMessage());

            $this->responderJson([
                'ok' => false,
                'mensaje' => 'No se encontro el contrato'
            ]);

        } catch(Exception $e){
            $this->responderJson([
                'ok' => false,
                'mensaje' => $e->getMessage()

            ]);
        }
    }

    private function responderJson($datos){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos);
    }

}

