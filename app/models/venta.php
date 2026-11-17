<?php
require_once __DIR__ . "/../config/conection.php";
class Venta{
    private PDO $db;

    public function __construct() {
        $conexion = new Database();
        $this->db = $conexion->getConnection();
    }

    //Obtener todos los modelos de la base de datos que esten activos
    public function obtenerProductosActivos() {
    

        $sql = " SELECT modelos.idModelo, modelos.NombreMaterial, 
            categorias.NombreCategoria, TipoMarco.NombreMarco, modelos.Precio, 
            modelos.NivelEducativo, modelos.Imagen
            FROM modelos 
            INNER JOIN categorias 
                ON modelos.idCategoria = categorias.idCategoria 
            INNER JOIN TipoMarco 
                ON modelos.idTipoMarco = TipoMarco.idTipoMarco 
            WHERE modelos.Estado =  1 
            ORDER BY modelos.NombreMaterial ASC";

            $consulta = $this->db->prepare($sql);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Obtener todas las ventas registradas
    public function obtenerVentas() {

    $sql = "SELECT
                v.idVenta,
                v.idContrato,
                v.CantidadPedidos,
                v.PrecioTotal,
                v.Estado,
                c.FechaContrato,
                c.FechaEvento,
                c.Evento,
                col.NombreColegio,
                cli.Nombre,
                cli.Apellidos
            FROM Ventas v
            INNER JOIN Contratos c
                ON v.idContrato = c.idContrato
            INNER JOIN Cliente cli
                ON c.idCliente = cli.idCliente
            INNER JOIN Colegios col
                ON c.idColegio = col.idColegio
            ORDER BY c.FechaContrato DESC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar una venta y sus detalles
    public function registrarVenta($idContrato, $productos, $pagos) {

        try {

            $this->db->beginTransaction();

            // 1. Verificar que el contrato exista
            $sqlContrato = "SELECT idContrato, Estado
                            FROM Contratos
                            WHERE idContrato = :idContrato
                            LIMIT 1";

            $consultaContrato = $this->db->prepare($sqlContrato);
            $consultaContrato->execute([
                ':idContrato' => $idContrato
            ]);
            $contrato = $consultaContrato->fetch(PDO::FETCH_ASSOC);


            if (!$contrato) {
                throw new Exception("El contrato indicado no existe.");
            }
            if ($contrato['Estado'] === 'CANCELADO') {
                throw new Exception("No se puede registrar una venta para un contrato cancelado.");
            }

            // 2. Verificar que el contrato no tenga una venta registrada
            $sqlExiste = "SELECT idVenta
                          FROM Ventas
                          WHERE idContrato = :idContrato
                          LIMIT 1";

            $consultaExiste = $this->db->prepare($sqlExiste);
            $consultaExiste->execute([
                ':idContrato' => $idContrato
            ]);

            if ($consultaExiste->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("Este contrato ya tiene una venta registrada.");
            }

            // 3. Validar que existan productos
            if (!is_array($productos) || empty($productos)) {
                throw new Exception("Agrega al menos un producto.");
            }

            // Validar que existan pagos
            if (!is_array($pagos) || empty($pagos)) {
                throw new Exception("Agrega al menos un pago.");
            }

            $totalPagado = 0;

            foreach ($pagos as $pago) {

                $idMetodoPago = trim($pago['idMetodoPago'] ?? '');
                $monto = $pago['monto'] ?? null;
                $numeroOperacion = trim($pago['numeroOperacion'] ?? '');

                if ($idMetodoPago === '' || !is_numeric($monto) || !is_finite((float)$monto) ||(float)$monto <= 0) {
                    throw new Exception("Los datos de uno de los pagos no son válidos.");
                }

                if (in_array($idMetodoPago, ['MET-0001', 'MET-0003'], true) &&$numeroOperacion === '') {
                    throw new Exception("El número de operación es obligatorio para Yape o Transferencia.");
                }

                $totalPagado += (float)$monto;
            }

            // Inicializar los totales y detalles de la venta
                $totalVenta = 0;
            $cantidadPedidos = 0;
            $detalles = [];

            // 4. Consultar y validar cada producto en la base de datos
            $sqlProducto = "SELECT idModelo, NombreMaterial, Precio
                            FROM modelos
                            WHERE idModelo = :idModelo
                            AND Estado = 1
                            LIMIT 1";

            $consultaProducto = $this->db->prepare($sqlProducto);

            foreach ($productos as $producto) {

                $idModelo = trim($producto['idModelo'] ?? '');
                $cantidad = filter_var(
                    $producto['cantidad'] ?? null,
                    FILTER_VALIDATE_INT
                );

                if ($idModelo === '') {
                    throw new Exception("Se recibió un producto sin código.");
                }

                if ($cantidad === false || $cantidad <= 0) {
                    throw new Exception("La cantidad de un producto no es válida.");
                }

                $consultaProducto->execute([
                    ':idModelo' => $idModelo
                ]);

                $datosProducto = $consultaProducto->fetch(PDO::FETCH_ASSOC);

                if (!$datosProducto) {
                    throw new Exception(
                        "El producto con código " . $idModelo . " no existe o está inactivo."
                    );
                }

                // El precio se obtiene directamente de la base de datos
                $precioUnitario = (float) $datosProducto['Precio'];

                if ($precioUnitario < 0) {
                    throw new Exception("El precio del producto no es válido.");
                }

                $subtotal = $precioUnitario * $cantidad;

                $totalVenta += $subtotal;
                $cantidadPedidos += $cantidad;

                $detalles[] = [
                    'idModelo' => $idModelo,
                    'cantidad' => $cantidad,
                    'precioUnitario' => $precioUnitario,
                    'subtotal' => $subtotal
                ];
            }

            // Validar que los pagos no superen el total de la venta
            if (round($totalPagado, 2) > round($totalVenta, 2)) {
                throw new Exception("La suma de los pagos no puede superar el total de la venta.");
            }

            // Validar que el primer pago sea como mínimo el 50 % del total
            $montoMinimo = round($totalVenta / 2, 2);

            if (round($totalPagado, 2) < $montoMinimo) {
                throw new Exception(
                    "El pago inicial debe ser como mínimo el 50 % del total de la venta. Debes pagar S/ " .
                    number_format($montoMinimo, 2, '.', '')
                );
            }


            // 5. Generar el siguiente código de venta
            $sqlUltimaVenta = "SELECT MAX(CAST(SUBSTRING(idVenta, 4) AS UNSIGNED))
                               AS ultimo
                               FROM Ventas
                               WHERE idVenta LIKE 'VT-%'";

            $consultaUltimaVenta = $this->db->query($sqlUltimaVenta);
            $ultimoNumeroVenta = (int) $consultaUltimaVenta->fetchColumn();

            $idVenta = 'VT-' . str_pad(
                $ultimoNumeroVenta + 1,
                5,
                '0',
                STR_PAD_LEFT
            );

            // 6. Registrar la venta principal
            $sqlVenta = "INSERT INTO Ventas (idVenta,idContrato, CantidadPedidos,FechaEntrega,
                            FechaLimitePago,PrecioTotal,Estado) 
                        VALUES (:idVenta,:idContrato,:cantidadPedidos,NULL,NULL,:precioTotal,'PENDIENTE')";

            $consultaVenta = $this->db->prepare($sqlVenta);

            $consultaVenta->execute([
                ':idVenta' => $idVenta,
                ':idContrato' => $idContrato,
                ':cantidadPedidos' => $cantidadPedidos,
                ':precioTotal' => $totalVenta
            ]);

            // 7. Preparar el registro de los detalles
            $sqlUltimoDetalle = "SELECT MAX(CAST(SUBSTRING(idDetalleVenta, 4) AS UNSIGNED))
                                 AS ultimo
                                 FROM DetalleVenta
                                 WHERE idDetalleVenta LIKE 'DV-%'";

            $consultaUltimoDetalle = $this->db->query($sqlUltimoDetalle);
            $ultimoNumeroDetalle = (int) $consultaUltimoDetalle->fetchColumn();

            $sqlDetalle = "INSERT INTO DetalleVenta VALUES (
                                :idDetalleVenta,:idVenta,:idModelo,NULL,NULL, :cantidad,:precioUnitario,
                                0,:subtotal)";

            $consultaDetalle = $this->db->prepare($sqlDetalle);

            // 8. Registrar cada producto de la venta
            foreach ($detalles as $detalle) {

                $ultimoNumeroDetalle++;

                $idDetalleVenta = 'DV-' . str_pad(
                    $ultimoNumeroDetalle,
                    5,
                    '0',
                    STR_PAD_LEFT
                );

                $consultaDetalle->execute([
                    ':idDetalleVenta' => $idDetalleVenta,
                    ':idVenta' => $idVenta,
                    ':idModelo' => $detalle['idModelo'],
                    ':cantidad' => $detalle['cantidad'],
                    ':precioUnitario' => $detalle['precioUnitario'],
                    ':subtotal' => $detalle['subtotal']
                ]);
            }

            // 9. Registrar los pagos
            $sqlMetodo = "SELECT idMetodoPago
                    FROM MetodoPago
                    WHERE idMetodoPago = :idMetodoPago
                    AND Estado = 1
                    LIMIT 1";

            $consultaMetodo = $this->db->prepare($sqlMetodo);

            // Obtener el último código de pago
            $sqlUltimoPago = "SELECT MAX(CAST(SUBSTRING(idPago, 5) AS UNSIGNED))
                  FROM Pago
                  WHERE idPago LIKE 'PAG-%'";

            $ultimoNumeroPago = (int) $this->db->query($sqlUltimoPago)->fetchColumn();

            // Preparar inserción
            $sqlPago = "INSERT INTO Pago (idPago,idContrato,idMetodoPago,Monto,FechaPago,
                NumOperacion,  Observacion, Estado ) 
            VALUES 
                (:idPago,:idContrato,:idMetodoPago,:monto,NOW(),:numeroOperacion,:observacion,1)";

            $consultaPago = $this->db->prepare($sqlPago);

            foreach ($pagos as $pago) {

                $idMetodoPago = trim($pago['idMetodoPago']);
                $monto = round((float)$pago['monto'], 2);

                $numeroOperacion = trim($pago['numeroOperacion'] ?? '');
                $observacion = trim($pago['observacion'] ?? '');

                // Verificar que el método de pago esté activo
                $consultaMetodo->execute([
                    ':idMetodoPago' => $idMetodoPago
                ]);

                if (!$consultaMetodo->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception("Uno de los métodos de pago no existe o está inactivo.");
                }

                $ultimoNumeroPago++;

                $idPago = 'PAG-' . str_pad(
                    $ultimoNumeroPago,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                $consultaPago->execute([
                    ':idPago' => $idPago,
                    ':idContrato' => $idContrato,
                    ':idMetodoPago' => $idMetodoPago,
                    ':monto' => $monto,
                    ':numeroOperacion' => $numeroOperacion !== '' ? $numeroOperacion : null,
                    ':observacion' => $observacion !== '' ? $observacion : null
                ]);
            }

            // 10. Cambiar el estado del contrato a FINALIZADO
            $sqlContratoEstado = "UPDATE Contratos
                                SET Estado = 'FINALIZADO'
                                WHERE idContrato = :idContrato
                                AND Estado <> 'CANCELADO'";

            $consultaContratoEstado = $this->db->prepare($sqlContratoEstado);

            $consultaContratoEstado->execute([
                ':idContrato' => $idContrato
            ]);

            // 11. Confirmar todos los registros

            $this->db->commit();

            return $idVenta;

        } catch (Throwable $e) {

            // Si algo falla, deshacer todos los cambios
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new Exception($e->getMessage());
        }
    }

    // Obtener los datos principales de una venta
    public function obtenerVentaPorId($idVenta) {

        $sql = "SELECT
                v.idVenta,
                v.idContrato,
                v.CantidadPedidos,
                v.FechaEntrega,
                v.FechaLimitePago,
                v.PrecioTotal,
                v.Estado AS EstadoVenta,

                c.Evento,
                c.Servicio,
                c.Direccion,
                c.FechaContrato,
                c.FechaEvento,
                c.Condiciones,
                c.Estado AS EstadoContrato,

                col.NombreColegio,

                cli.Nombre,
                cli.Apellidos,
                cli.Telefono,
                cli.Correo,
                cli.TipoDOI,
                cli.NumDOI

            FROM Ventas v

            INNER JOIN Contratos c
                ON v.idContrato = c.idContrato

            INNER JOIN Cliente cli
                ON c.idCliente = cli.idCliente

            INNER JOIN Colegios col
                ON c.idColegio = col.idColegio

            WHERE v.idVenta = :idVenta
            LIMIT 1";

        $consulta = $this->db->prepare($sql);

        $consulta->execute([
            ':idVenta' => $idVenta
        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }


    // Obtener los productos registrados en una venta
    public function obtenerDetallesVenta($idVenta) {

        $sql = "SELECT
                dv.idDetalleVenta,
                dv.idModelo,
                m.NombreMaterial,
                m.Imagen,
                dv.Cantidad,
                dv.PrecioUnitario,
                dv.Descuento,
                dv.SubTotal,
                dv.idDescuento,
                dv.idPromocion

            FROM DetalleVenta dv

            INNER JOIN modelos m
                ON dv.idModelo = m.idModelo

            WHERE dv.idVenta = :idVenta

            ORDER BY m.NombreMaterial ASC";

        $consulta = $this->db->prepare($sql);

        $consulta->execute([
            ':idVenta' => $idVenta
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

     // Obtener los pagos realizados en una venta
    public function obtenerPagosVenta($idVenta) {

        $sql = "SELECT
                p.idPago,
                mp.NombreMetodo,
                p.Monto,
                p.FechaPago,
                p.NumOperacion,
                p.Observacion

            FROM Pago p

            INNER JOIN MetodoPago mp
                ON p.idMetodoPago = mp.idMetodoPago

            INNER JOIN Ventas v
                ON p.idContrato = v.idContrato

            WHERE v.idVenta = :idVenta
            AND p.Estado = 1

            ORDER BY p.FechaPago ASC";

        $consulta = $this->db->prepare($sql);

        $consulta->execute([
            ':idVenta' => $idVenta
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //OBtener los metodos de pago 
    public function obtenerMetodosPago(){
        $sql = "SELECT idMetodoPago, NombreMetodo
        FROM MetodoPago
        WHERE Estado = 1
        ORDER BY NombreMetodo ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    // Actualizar las fechas de una venta
    public function actualizarFechasVenta($idVenta, $fechaEntrega, $fechaLimitePago) {

        $sql = "UPDATE Ventas
                SET FechaEntrega = :fechaEntrega,
                    FechaLimitePago = :fechaLimitePago
                WHERE idVenta = :idVenta";

        $consulta = $this->db->prepare($sql);

        $consulta->bindValue(':fechaEntrega', $fechaEntrega, 
            $fechaEntrega === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $consulta->bindValue(':fechaLimitePago', $fechaLimitePago,
            $fechaLimitePago === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $consulta->bindValue(':idVenta', $idVenta, PDO::PARAM_STR);

        return $consulta->execute();
    }

    //para los registros de pago
    
    // Registrar un pago adicional en una venta existente
    public function registrarPagoAdicional($idVenta,$idMetodoPago,$monto,$numeroOperacion,$observacion) {

        try {

            // Iniciar una transacción
            $this->db->beginTransaction();

            // 1. Buscar la venta y bloquear su registro durante la operación
            $sqlVenta = "SELECT
                        idVenta,
                        idContrato,
                        PrecioTotal,
                        Estado
                     FROM Ventas
                     WHERE idVenta = :idVenta
                     LIMIT 1
                     FOR UPDATE";

            $consultaVenta = $this->db->prepare($sqlVenta);

            $consultaVenta->execute([
                ':idVenta' => $idVenta
            ]);

            $venta = $consultaVenta->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                throw new Exception("La venta no existe.");
            }

            // 2. Comprobar que la venta no esté anulada
            if (strtoupper(trim($venta['Estado'])) === 'ANULADA') {
                throw new Exception("No se pueden registrar pagos en una venta anulada.");
            }   

            // 3. Validar el monto recibido
            if (!is_numeric($monto) || !is_finite((float) $monto) || (float) $monto <= 0) {
                throw new Exception("El monto del pago no es válido.");
            }

            $monto = round((float) $monto, 2);

            // 4. Obtener el total pagado anteriormente
            $sqlPagado = "SELECT COALESCE(SUM(Monto), 0)
                      FROM Pago
                      WHERE idContrato = :idContrato
                      AND Estado = 1";

            $consultaPagado = $this->db->prepare($sqlPagado);

            $consultaPagado->execute([
                ':idContrato' => $venta['idContrato']
            ]);

            $totalPagado = round((float) $consultaPagado->fetchColumn(), 2);

            // 5. Calcular cuánto falta pagar
            $totalVenta = round((float) $venta['PrecioTotal'], 2);

            $saldoPendiente = round($totalVenta - $totalPagado, 2);

            if ($saldoPendiente <= 0) {
                throw new Exception("Esta venta ya está completamente pagada.");
            }

            // 6. Comprobar que el nuevo pago no supere el saldo pendiente
            if ($monto > $saldoPendiente) {
                throw new Exception( "El pago supera el saldo pendiente. Solo falta pagar S/ " .number_format($saldoPendiente, 2, '.', ''));
            }

            // 7. Verificar que el método de pago exista y esté activo
            $sqlMetodo = "SELECT idMetodoPago
                      FROM MetodoPago
                      WHERE idMetodoPago = :idMetodoPago
                      AND Estado = 1
                      LIMIT 1";

            $consultaMetodo = $this->db->prepare($sqlMetodo);

            $consultaMetodo->execute([
                ':idMetodoPago' => $idMetodoPago
            ]);

            if (!$consultaMetodo->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("El método de pago no existe o está inactivo.");
            }

            // 8. Validar el número de operación para Yape y Transferencia
            $numeroOperacion = trim($numeroOperacion ?? '');

            if (in_array($idMetodoPago, ['MET-0001', 'MET-0003'], true) &&$numeroOperacion === ''){

                throw new Exception("El número de operación es obligatorio para Yape o Transferencia.");
            }

            // 9. Generar el siguiente código de pago
            $sqlUltimoPago = "SELECT MAX(CAST(SUBSTRING(idPago, 5) AS UNSIGNED))
                          FROM Pago
                          WHERE idPago LIKE 'PAG-%'";

            $ultimoNumeroPago = (int) $this->db->query($sqlUltimoPago)->fetchColumn();

            $idPago = 'PAG-' . str_pad($ultimoNumeroPago + 1,4, '0', STR_PAD_LEFT);

            // 10. Registrar el nuevo pago
            $sqlPago = "INSERT INTO Pago (idPago, idContrato,idMetodoPago,Monto,FechaPago,
                                        NumOperacion,Observacion,Estado)
                    VALUES (:idPago,:idContrato,:idMetodoPago,:monto,NOW(),:numeroOperacion,:observacion,1)";

            $consultaPago = $this->db->prepare($sqlPago);

            $consultaPago->execute([
                ':idPago' => $idPago,
                ':idContrato' => $venta['idContrato'],
                ':idMetodoPago' => $idMetodoPago,
                ':monto' => $monto,
                ':numeroOperacion' => $numeroOperacion !== ''
                    ? $numeroOperacion
                    : null,
                ':observacion' => trim($observacion ?? '') !== ''
                    ? trim($observacion)
                    : null
            ]);

            // 11. Actualizar el estado de la venta
            $nuevoTotalPagado = round($totalPagado + $monto, 2);

            $nuevoEstado = $nuevoTotalPagado >= $totalVenta
                ? 'FINALIZADA'
                : 'PENDIENTE';

            $sqlEstado = "UPDATE Ventas
                      SET Estado = :estado
                      WHERE idVenta = :idVenta";

            $consultaEstado = $this->db->prepare($sqlEstado);

            $consultaEstado->execute([
                ':estado' => $nuevoEstado,
                ':idVenta' => $idVenta
            ]);

            // 12. Confirmar todos los cambios
            $this->db->commit();

            return [
                'idPago' => $idPago,
                'totalPagado' => $nuevoTotalPagado,
                'saldoPendiente' => round($totalVenta - $nuevoTotalPagado, 2),
                'estado' => $nuevoEstado
            ];

        } catch (Throwable $e) {

            // Si ocurre un error, deshacer los cambios
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new Exception($e->getMessage());
        }
    }


    // Anular una venta sin eliminar sus detalles ni sus pagos
    public function anularVenta($idVenta) {

        try {

            $this->db->beginTransaction();

            // 1. Buscar y bloquear la venta
            $sql = "SELECT idVenta, idContrato, Estado
                FROM Ventas
                WHERE idVenta = :idVenta
                LIMIT 1
                FOR UPDATE";

            $consulta = $this->db->prepare($sql);

            $consulta->execute([
                ':idVenta' => $idVenta
            ]);

            $venta = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                throw new Exception("La venta no existe.");
            }

            // 2. Comprobar si ya está anulada
            if (strtoupper(trim($venta['Estado'])) === 'ANULADA') {
                throw new Exception("Esta venta ya se encuentra anulada.");
            }
            // 2.1. No permitir anular ventas finalizadas
            if (strtoupper(trim($venta['Estado'])) === 'FINALIZADA') {
                throw new Exception("No se puede anular una venta que ya está finalizada.");
            }

            // 3. Cambiar únicamente el estado
            $sqlAnular = "UPDATE Ventas
                      SET Estado = 'ANULADA'
                      WHERE idVenta = :idVenta";

            $consultaAnular = $this->db->prepare($sqlAnular);

            $consultaAnular->execute([
                ':idVenta' => $idVenta
            ]);

            // 4. Devolver el contrato a PROCESO
            $sqlContrato = "UPDATE Contratos
                            SET Estado = 'PROCESO'
                            WHERE idContrato = :idContrato
                            AND Estado <> 'CANCELADO'";

            $consultaContrato = $this->db->prepare($sqlContrato);

            $consultaContrato->execute([
                ':idContrato' => $venta['idContrato']
            ]);

            // 5. Confirmar la operación
            $this->db->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new Exception($e->getMessage());
        }
    }

}