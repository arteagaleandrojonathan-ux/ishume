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

        // Indicar al panel principal qué vista debe cargar
        $vista = 'detalleVenta';

        require_once __DIR__ . "/../views/detalleVenta.php";
    }
}
?>