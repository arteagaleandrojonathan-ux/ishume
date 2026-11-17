<?php

require_once __DIR__ . '/../models/venta.php';

class VentasController {

    private Venta $AccionModelo;

    public function __construct() {
        $this->AccionModelo = new Venta();
    }

    // Mostrar el listado de ventas
    public function ventas() {

        $ventas = $this->AccionModelo->obtenerVentas();

        //comprobar si llegamos desde un contrato en espeficico
        $idVenta = trim($_GET['idVenta']?? '');

        // si se recibe el ID de la venta filtramos la lista de esA VENTA gracias a su ID
        if($idVenta !==''){
            $ventas = array_filter($ventas, function($venta) use ($idVenta){
                return $venta['idVenta'] === $idVenta;
            });
            
        }

        $vista = 'ventas';

        require_once __DIR__ . "/../views/PanelPrincipal.php";
    }

    // Mostrar pantalla para registrar una venta
    public function nuevaVenta() {

        $idContrato = $_GET['idContrato'] ?? '';

        if (empty($idContrato)) {
            http_response_code(400);
            exit("No se recibió el ID del contrato.");
        }

        $productos = $this->AccionModelo->obtenerProductosActivos();

        $metodosPago = $this->AccionModelo->obtenerMetodosPago();

        require_once __DIR__ . "/../views/nuevaVenta.php";
    }

    // Registrar una venta
    public function registrarVenta() {

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);
            return;
        }

        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Los datos enviados no son válidos.'
            ]);
            return;
        }

        $idContrato = trim($datos['idContrato'] ?? '');
        $productos = $datos['productos'] ?? [];
        $pagos = $datos['pagos'] ?? [];

        if ($idContrato === '') {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No se recibió el contrato.'
            ]);
            return;
        }

        if (!is_array($productos) || empty($productos)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Agrega al menos un producto.'
            ]);
            return;
        }

        if (!is_array($pagos) || empty($pagos)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Agrega al menos un pago.'
            ]);
            return;
        }

        foreach ($pagos as $pago) {

            $idMetodoPago = trim($pago['idMetodoPago'] ?? '');
            $monto = $pago['monto'] ?? 0;
            $numeroOperacion = trim($pago['numeroOperacion'] ?? '');

            if ($idMetodoPago === '' || !is_numeric($monto) ||!is_finite((float)$monto) || (float)$monto <= 0) {
                http_response_code(400);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'Los datos de uno de los pagos no son válidos.'
                ]);
                return;
            }

            if (in_array($idMetodoPago, ['MET-0001', 'MET-0003'], true) && $numeroOperacion === '') {
                http_response_code(400);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'El número de operación es obligatorio para Yape o Transferencia.'
                ]);
                return;
            }
        }

        try {

            $idVenta = $this->AccionModelo->registrarVenta(
                $idContrato,
                $productos,
                $pagos
            );

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Venta registrada correctamente.',
                'idVenta' => $idVenta
            ]);

        } catch (Exception $e) {

            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // Mostrar el detalle de una venta
    public function detalleVenta() {

        $idVenta = $_GET['idVenta'] ?? '';

        if (empty($idVenta)) {
            http_response_code(400);
            exit("No se recibió el código de la venta.");
        }

        $venta = $this->AccionModelo->obtenerVentaPorId($idVenta);

        if (!$venta) {
            http_response_code(404);
            exit("No se encontró la venta solicitada.");
        }

        $detalles = $this->AccionModelo->obtenerDetallesVenta($idVenta);
        $pagos = $this->AccionModelo->obtenerPagosVenta($idVenta);
        $metodosPago = $this->AccionModelo->obtenerMetodosPago();

        //no puede ser menor a la fehca de hoy 
        $fechaMinima = (new DateTimeImmutable('today', new DateTimeZone('America/Lima')))->format('Y-m-d');

        // Indicar al panel principal qué vista debe cargar
        $vista = 'detalleVenta';

        require_once __DIR__ . "/../views/PanelPrincipal.php";
    }


    // Actualizar las fechas de una venta
    public function actualizarFechasVenta() {

        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);
            return;
        }

        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Los datos enviados no son válidos.'
            ]);
            return;
        }

        $idVenta = trim($datos['idVenta'] ?? '');
        $fechaEntrega = trim($datos['fechaEntrega'] ?? '');
        $fechaLimitePago = trim($datos['fechaLimitePago'] ?? '');

        if ($idVenta === '') {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No se recibió el código de la venta.'
            ]);
            return;
        }

        $hoy = (new DateTimeImmutable( 'today',new DateTimeZone('America/Lima')))->format('Y-m-d');

        // Validar fecha y hora de entrega
        if ($fechaEntrega !== '') {
            $fecha = DateTime::createFromFormat('!Y-m-d', $fechaEntrega);

            if (!$fecha || $fecha->format('Y-m-d') !== $fechaEntrega) {
                throw new Exception('La fecha de entrega no es válida.');
            }


            $fechaEntrega = $fecha->format('Y-m-d');

            if ($fechaEntrega < $hoy) {
                http_response_code(400); echo json_encode([
                    'ok' => false,
                    'mensaje' => 'La fecha de entrega no puede ser anterior a hoy.'
                ]);
                return;
            }
        } else {
            $fechaEntrega = null;
        }

        // Validar fecha límite de pago
        if ($fechaLimitePago !== '') {
            $fecha = DateTime::createFromFormat('!Y-m-d', $fechaLimitePago);

            if (!$fecha || $fecha->format('Y-m-d') !== $fechaLimitePago) {
                http_response_code(400);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'La fecha límite de pago no es válida.'
                ]);
                return;
            }

            $fechaLimitePago = $fecha->format('Y-m-d');
            if ($fechaLimitePago < $hoy) {
                http_response_code(400);
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'La fecha límite de pago no puede ser anterior a hoy.'
                ]);

                return;
            }
        } else {
            $fechaLimitePago = null;
        }

        try {
            $resultado = $this->AccionModelo->actualizarFechasVenta(
                $idVenta,
                $fechaEntrega,
                $fechaLimitePago
            );

            if (!$resultado) {
                throw new Exception('No se pudieron actualizar las fechas.');
            }

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Las fechas se actualizaron correctamente.'
            ]);

        } catch (Throwable $e) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }


    // Registrar un pago adicional en una venta existente
    public function registrarPagoAdicional() {

        header('Content-Type: application/json; charset=utf-8');

        // Comprobar que la solicitud sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Método no permitido.'
            ]);

            return;
        }

        // Recibir los datos enviados desde JavaScript
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!is_array($datos)) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Los datos enviados no son válidos.'
            ]);

            return;
        }

        // Obtener los datos del pago
        $idVenta = trim($datos['idVenta'] ?? '');
        $idMetodoPago = trim($datos['idMetodoPago'] ?? '');
        $monto = $datos['monto'] ?? null;
        $numeroOperacion = trim($datos['numeroOperacion'] ?? '');
        $observacion = trim($datos['observacion'] ?? '');

        // Validar los datos obligatorios
        if ($idVenta === '' || $idMetodoPago === '') {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Faltan datos obligatorios para registrar el pago.'
            ]);

            return;
        }

        if (
            !is_numeric($monto) ||
            !is_finite((float) $monto) ||
            (float) $monto <= 0
        ) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Ingresa un monto válido mayor que cero.'
            ]);

            return;
        }

        // Registrar el pago mediante el modelo
        try {

            $resultado = $this->AccionModelo->registrarPagoAdicional(
                $idVenta,
                $idMetodoPago,
                $monto,
                $numeroOperacion,
                $observacion
            );

            echo json_encode([
                'ok' => true,
                'mensaje' => 'El pago se registró correctamente.',
                'pago' => $resultado
            ]);

        } catch (Throwable $e) {

            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }


    public function anularVenta() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido.');
        }

        $idVenta = trim($_POST['idVenta'] ?? '');
        $motivo = $_POST['motivo'] ?? '';
        $motivo = is_string($motivo) ? trim($motivo) : '';

        if ($idVenta === '') {
            header('Location: index.php?opcion=ventas&error=' . urlencode('No se recibió el código de la venta.'));
            exit;
        }

        try {
            if ($motivo === '' || mb_strlen($motivo) > 255) {
                throw new Exception('Escribe un motivo de 1 a 255 caracteres para anular la venta.');
            }

            $this->AccionModelo->anularVenta($idVenta, $motivo);

            header('Location: index.php?opcion=ventas&anulada=1');
            exit;

        } catch (Throwable $e) {
            header('Location: index.php?opcion=ventas&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
?>
