<?php

require_once __DIR__ . '/../models/compra.php';

class CompraController {

    private Compra $AccionModelo;

    public function __construct(){
        $this->AccionModelo = new Compra();
    }

    public function Compra() {
        $Proveedores = $this->AccionModelo->obtenerProveedores();
        $Modelos = $this->AccionModelo->obtenerModelos();
        $vista = 'compra';
        require_once __DIR__ . '/../views/PanelPrincipal.php';

        
    }

    public function RegistrarCompra(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?opcion=compra');
            exit;
        }

        try {

            $idProveedor = $_POST['idProveedor'] ?? '';
            $observacion = trim($_POST['Observacion'] ?? '');
            $productosJson = $_POST['productos'] ?? '';

            if ($idProveedor === '') {
                throw new Exception('Debe seleccionar un proveedor.');
            }

            if ($productosJson === '') {
                throw new Exception('No se recibieron productos.');
            }

            $productos = json_decode($productosJson, true);

            if (!is_array($productos) || count($productos) === 0) {
                throw new Exception('La compra debe tener al menos un producto.');
            }

            $idCompra = $this->AccionModelo->registrarCompra(
                $idProveedor,
                $observacion,
                $productos
            );

            header('Location: index.php?opcion=compra&registro=exito&id=' . urlencode($idCompra));
        exit;
        } catch (Exception $e) {
            header(
                'Location: index.php?opcion=compra&error=' .
                urlencode($e->getMessage())
            );
            exit;
        }
    }

    public function RegistrosCompra(){
        $fechaDesde = $_GET['fechaDesde'] ?? '';
        $fechaHasta = $_GET['fechaHasta'] ?? '';
        $proveedor = $_GET['proveedor'] ?? '';

        $errorFecha = '';

        if ($fechaDesde !== '' && $fechaHasta !== '') {
            if ($fechaDesde > $fechaHasta) {
                $errorFecha = 'La fecha "Desde" no puede ser posterior a la fecha "Hasta".';
            }
        }


        //aca es el detalle de la compra//
        $detalle = $_GET['detalle'] ?? '';
        $DetalleCompra = [];

        if($detalle !== ''){
            $DetalleCompra = $this->AccionModelo->obtenerDetalleCompra($detalle);
        }

        //aca es el panel apra cancelar la compra
        $cancelar = $_GET['cancelar'] ?? '';
        $CancelarCompra = [];
        $errorAnular = $_GET['error'] ?? '';

        if($cancelar !== ''){
            $CancelarCompra = $this->AccionModelo->obtenerDetalleCompra($cancelar);
            
            if(empty($CancelarCompra)){
                $errorAnular = 'La compra no existe';
            }elseif (!(int) $CancelarCompra[0]['Estado']){
                $errorAnular = 'La compra ya se encuentra anulada';
                $CancelarCompra = [];
            }
        }

        $pagina = $_GET['pagina'] ?? 1;
        $porPagina = 5;

        $inicio = ($pagina - 1) * $porPagina;

        $Compras=   $this->AccionModelo->obtenerCompras( $fechaDesde,
        $fechaHasta, $proveedor, $inicio, $porPagina);

        $totalCompras = $this->AccionModelo->contarCompras( $fechaDesde, $fechaHasta, $proveedor);

        $totalPaginas = ceil($totalCompras / $porPagina);
        $Proveedores = $this->AccionModelo->obtenerProveedores();

        $vista = 'registrosCompra';

        require_once __DIR__ .'/../views/PanelPrincipal.php';

    }

    public function AnularCompra(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?opcion=registrosCompra');
            exit;
        }

        $idCompra = $_POST['idCompra'] ?? '';
        $detalles = $_POST['detalles'] ?? [];
        $motivo = trim($_POST['Motivo'] ?? '');

        try {
            if ($idCompra === '') {
                throw new Exception('No se recibió la compra a cancelar.');
            }

            if (!is_array($detalles)) {
                $detalles = [];
            }

            $resultado = $this->AccionModelo->anularCompra($idCompra, $detalles, $motivo);

            header(
                'Location: index.php?opcion=registrosCompra&anulacion=ok' .
                '&id=' . urlencode($idCompra) .
                '&cant=' . (int) $resultado['cantidad'] .
                '&completa=' . ($resultado['completa'] ? 1 : 0)
            );
            exit;

        } catch (Exception $e) {
            header(
                'Location: index.php?opcion=registrosCompra&cancelar=' . urlencode($idCompra) .
                '&error=' . urlencode($e->getMessage())
            );
            exit;
        }
    }
    
}