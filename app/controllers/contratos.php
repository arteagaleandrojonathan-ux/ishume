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

    public function buscarColegios(){
       $nombreColegio = trim($_POST['nombreColegio'] ?? '');

        if (mb_strlen($nombreColegio) < 2){
            $this->responderJson([]);
            return;
        }

        $Colegios = $this->AccionModelo->buscarColegiosPorNombre($nombreColegio);
        $this->responderJson($Colegios);
    }

    public function registrarColegio(){

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: index.php?opcion=nuevoContrato');
            exit;

        }

        // Se quitan los espacios sobrantes (también los dobles del medio)
        $nombreColegio = trim($_POST['NombreColegio'] ?? '');
        $nombreColegio = preg_replace('/\s+/u', ' ', $nombreColegio);

        if ($nombreColegio === '') {
            $this->responderJson([
                'ok' => false,
                'mensaje' => 'Ingrese el nombre del colegio.'
            ]);
            return;
        }

        if (mb_strlen($nombreColegio) > 150) {
            $this->responderJson([
                'ok' => false,
                'mensaje' => 'El nombre del colegio no puede superar los 150 caracteres.'
            ]);
            return;
        }
        
        try{
            $colegio = $this->AccionModelo->registrarColegio($nombreColegio);
            
            $this->responderJson([
                'ok' => false,
                'idColegio' => $colegio['idColegio'],
                'NombreColegio' => $colegio['NombreColegio'],
                'existente' => $colegio['existente']
            ]);     
        } catch (PDOException $e) {
            error_log('registrarColegio: ' . $e->getMessage());

            $this->responderJson([
                'ok' => false,
                'mensaje' => 'No se pudo registrar el colegio por un error de base de datos.'
            ]);

        }  catch (Exception $e) {
            $this->responderJson([
                'ok' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
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
                'ok'=> false ,
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

    private function validarDatos($cliente){
        $errorres = [];

        $soloLetras = '/^[\p{L} .\'-]+$/u';


        //VALIDACIONES PARA NOMBRE
        if($cliente['Nombre'] === ''){
            $errores['Nombre']= 'Ingrese el nombre del cliente';
        }elseif(!preg_match($soloLetras, $cliente['Nombre'])){
            $errores['Nombre'] = 'El nombre del cliente solo puede contener letras y espacios';
        }elseif(strlen($cliente['Nombre']) > 50){
            $errores['Nombre'] = 'El nombre es demasiado largo';
        }
        
        //VALIDACIONES PARA APELLIDOS
        if($cliente['Apellidos'] === ''){
            $errores['Apellidos'] = 'Ingrese los apellidos del cliente';
        
        }elseif (!preg_match($soloLetras, $cliente['Apellidos'])){
            $errores['Apellidos'] = 'Los apellidos del cliente solo pueden contener letras y espacios';

        }elseif(strlen($cliente['Apellidos']) > 50){
            $errores['Apellidos'] = 'Los apellidos son demasiado largos';
        }

        //VALIDACIONES PARA TESTIGO OPCIONAL
        if($cliente['Testigo'] !== ''){
            if(!preg_match($soloLetras, $cliente['Testigo'])){
                $errores['Testigo'] = 'El nombre del testigo solo puede contener letras y espacios';
            }elseif(strlen($cliente['Testigo']) > 50){
                $errores['Testigo'] = 'El nombre del testigo es demasiado largo';
            }
        }

        //VALIDACIONES PARA tipo de documento
        $tipoDOI = $cliente['TipoDOI'];
        $numeroDOI = $cliente['NumeroDOI'];

        // TIPO Y NÚMERO DE DOCUMENTO (opcionales, pero van juntos)
        $tipo = $cliente['TipoDOI'];
        $numero = $cliente['NumDOI'];

        if ($tipo !== '' && $tipo !== 'DNI' && $tipo !== 'RUC' && $tipo !== 'CE') {
            $errores['TipoDOI'] = 'Seleccione un tipo de documento válido.';
        } elseif ($tipo === '' && $numero !== '') {
            $errores['TipoDOI'] = 'Seleccione el tipo de documento.';
        } elseif ($tipo !== '' && $numero === '') {
            $errores['NumDOI'] = 'Ingrese el número de documento.';
        } elseif ($numero !== '' && !ctype_digit($numero)) {
            $errores['NumDOI'] = 'El número de documento solo puede contener números.';
        } elseif ($tipo === 'DNI' && strlen($numero) !== 8) {
            $errores['NumDOI'] = 'El DNI debe tener 8 dígitos.';
        } elseif ($tipo === 'RUC' && strlen($numero) !== 11) {
            $errores['NumDOI'] = 'El RUC debe tener 11 dígitos.';
        } elseif ($tipo === 'CE' && (strlen($numero) < 9 || strlen($numero) > 12)) {
            $errores['NumDOI'] = 'El carné de extranjería debe tener entre 9 y 12 dígitos.';
        }

        // TELÉFONO (obligatorio)
        if ($cliente['Telefono'] === '') {
            $errores['Telefono'] = 'Ingrese el teléfono.';
        } elseif (!ctype_digit($cliente['Telefono'])) {
            $errores['Telefono'] = 'El teléfono solo puede contener números.';
        } elseif (strlen($cliente['Telefono']) < 7 || strlen($cliente['Telefono']) > 9) {
            $errores['Telefono'] = 'El teléfono debe tener entre 7 y 9 dígitos.';
        }

        // CORREO (opcional)
        if ($cliente['Correo'] !== '') {
            if (!filter_var($cliente['Correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($cliente['Correo']) > 100) {
                $errores['Correo'] = 'Ingrese un correo válido (máximo 100 caracteres).';
            }
        }

        // DIRECCIÓN DEL CLIENTE (obligatoria)
        if ($cliente['Direccion'] === '') {
            $errores['DireccionCliente'] = 'Ingrese la dirección del cliente.';
        } elseif (mb_strlen($cliente['Direccion']) > 200) {
            $errores['DireccionCliente'] = 'La dirección es demasiado larga.';
        }

        return $errores;
    }

   

    
}
