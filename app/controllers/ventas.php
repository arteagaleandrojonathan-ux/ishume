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

        require_once __DIR__ . "/../views/ventas.php";
    }

    // Mostrar pantalla para registrar una venta
    public function nuevaVenta() {

        $idContrato = $_GET['idContrato'] ?? '';

        if (empty($idContrato)) {
            http_response_code(400);
            exit("No se recibió el ID del contrato.");
        }

        $productos = $this->AccionModelo->obtenerProductosActivos();

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

        try {

            $idVenta = $this->AccionModelo->registrarVenta(
                $idContrato,
                $productos
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

        require_once __DIR__ . "/../views/detalleVenta.php";
    }
}
?>