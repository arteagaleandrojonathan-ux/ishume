<?php

require_once __DIR__ . '/../config/conection.php';

class Compra{
    private PDO $db;

    public function __construct(){
        $conexion = new Database();
        $this->db = $conexion->getConnection();
    }

    public function obtenerProveedores(){
        $sql = "SELECT idProveedor, NombreProveedor, RazonSocial, Ruc, Telefono
        FROM proveedores
        WHERE Estado = True
        ORDER BY RazonSocial ASC";

        $consulta = $this->db->prepare($sql);

        $consulta ->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerModelos(){
        $sql = "SELECT  idModelo, NombreMaterial, Color, NivelEducativo
                FROM modelos
                WHERE Estado = True 
                AND Cantidad >= 0 
                ORDER BY idModelo ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function registrarCompra($idProveedor, $observacion, $productos){
        try {

        $this->db->beginTransaction();

        // ==========================================
        // 1. VERIFICAR QUE EL PROVEEDOR EXISTA Y ESTÉ ACTIVO
        // ==========================================

        $sqlProveedor = "SELECT idProveedor
                         FROM proveedores
                         WHERE idProveedor = :idProveedor
                         AND Estado = true
                         FOR UPDATE";

        $consultaProveedor = $this->db->prepare($sqlProveedor);

        $consultaProveedor->execute([
            ':idProveedor' => $idProveedor
        ]);

        $proveedor = $consultaProveedor->fetch(PDO::FETCH_ASSOC);

        if (!$proveedor) {
            throw new Exception('El proveedor no existe o se encuentra inactivo.');
        }


        // ==========================================
        // 2. GENERAR ID DE LA COMPRA
        // ==========================================

        $sqlIdCompra = "SELECT MAX(CAST(SUBSTRING(idCompra, 4) AS UNSIGNED)) AS ultimo
                        FROM Compras";

        $consultaIdCompra = $this->db->prepare($sqlIdCompra);
        $consultaIdCompra->execute();

        $ultimo = $consultaIdCompra->fetchColumn();

        $numeroCompra = ($ultimo !== false && $ultimo !== null)
            ? ((int) $ultimo + 1)
            : 1;

        $idCompra = 'CO-' . str_pad($numeroCompra, 5, '0', STR_PAD_LEFT);


        // ==========================================
        // 3. FECHA DE LA COMPRA
        // ==========================================

        $fechaCompra = date('Y-m-d H:i:s');


        // ==========================================
        // 4. INSERTAR CABECERA DE LA COMPRA
        // ==========================================

        $sqlCompra = "INSERT INTO Compras
                      (idCompra, idProveedor, FechaCompra, TotalCompra, Observacion, Estado)
                      VALUES
                      (:idCompra, :idProveedor, :FechaCompra, 0, :Observacion, true)";

        $consultaCompra = $this->db->prepare($sqlCompra);

        $consultaCompra->execute([
            ':idCompra' => $idCompra,
            ':idProveedor' => $idProveedor,
            ':FechaCompra' => $fechaCompra,
            ':Observacion' => $observacion !== '' ? $observacion : null
        ]);


        // ==========================================
        // 5. PREPARAR DETALLE
        // ==========================================

        $totalCompra = 0;


        foreach ($productos as $producto) {

            $idModelo = $producto['idModelo'];
            $cantidad = (int) $producto['cantidad'];
            $precioCompra = (float) $producto['precioCompra'];


            // Validaciones
            if ($cantidad <= 0) {
                throw new Exception('La cantidad debe ser mayor a cero.');
            }

            if ($precioCompra < 0) {
                throw new Exception('El precio de compra no puede ser negativo.');
            }


            // ==========================================
            // 6. OBTENER MODELO Y BLOQUEAR SU REGISTRO
            // ==========================================

            $sqlModelo = "SELECT idModelo
                          FROM modelos
                          WHERE idModelo = :idModelo
                          AND Estado = true
                          FOR UPDATE";

            $consultaModelo = $this->db->prepare($sqlModelo);

            $consultaModelo->execute([
                ':idModelo' => $idModelo
            ]);

            $modelo = $consultaModelo->fetch(PDO::FETCH_ASSOC);

            if (!$modelo) {
                throw new Exception(
                    'El modelo ' . $idModelo . ' no existe o se encuentra inactivo.'
                );
            }


            // ==========================================
            // 7. CALCULAR SUBTOTAL EN EL SERVIDOR
            // ==========================================

            $subtotal = $cantidad * $precioCompra;

            $totalCompra += $subtotal;


            // ==========================================
            // 8. GENERAR ID DEL DETALLE
            // ==========================================

            $sqlIdDetalle = "SELECT MAX(CAST(SUBSTRING(idDetalleCompra, 4) AS UNSIGNED)) AS ultimo
                             FROM DetalleCompra";

            $consultaIdDetalle = $this->db->prepare($sqlIdDetalle);
            $consultaIdDetalle->execute();

            $ultimoDetalle = $consultaIdDetalle->fetchColumn();

            $numeroDetalle = ($ultimoDetalle !== false && $ultimoDetalle !== null)
                ? ((int) $ultimoDetalle + 1)
                : 1;

            $idDetalleCompra = 'DC-' . str_pad(
                $numeroDetalle,
                5,
                '0',
                STR_PAD_LEFT
            );


            // ==========================================
            // 9. INSERTAR DETALLE
            // ==========================================

            $sqlDetalle = "INSERT INTO DetalleCompra
                           (idDetalleCompra, idCompra, idModelo, Cantidad, PrecioCompra, SubTotal)
                           VALUES
                           (:idDetalleCompra, :idCompra, :idModelo, :Cantidad, :PrecioCompra, :SubTotal)";

            $consultaDetalle = $this->db->prepare($sqlDetalle);

            $consultaDetalle->execute([
                ':idDetalleCompra' => $idDetalleCompra,
                ':idCompra' => $idCompra,
                ':idModelo' => $idModelo,
                ':Cantidad' => $cantidad,
                ':PrecioCompra' => $precioCompra,
                ':SubTotal' => $subtotal
            ]);


            // ==========================================
            // 10. AUMENTAR STOCK
            // ==========================================

            $sqlStock = "UPDATE modelos
                         SET Cantidad = Cantidad + :Cantidad
                         WHERE idModelo = :idModelo";

            $consultaStock = $this->db->prepare($sqlStock);

            $consultaStock->execute([
                ':Cantidad' => $cantidad,
                ':idModelo' => $idModelo
            ]);
        }


        // ==========================================
        // 11. ACTUALIZAR TOTAL DE LA COMPRA
        // ==========================================

        if ($totalCompra <= 0) {
            throw new Exception('La compra debe tener al menos un producto con un importe válido.');
        }

        $sqlTotal = "UPDATE Compras
                     SET TotalCompra = :TotalCompra
                     WHERE idCompra = :idCompra";

        $consultaTotal = $this->db->prepare($sqlTotal);

        $consultaTotal->execute([
            ':TotalCompra' => $totalCompra,
            ':idCompra' => $idCompra
        ]);


        // ==========================================
        // 12. CONFIRMAR TODA LA OPERACIÓN
        // ==========================================

        $this->db->commit();

        return $idCompra;
        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function obtenerCompras( $fechaDesde, $fechaHasta, $proveedor, $inicio, $porPagina){
        $sql = "SELECT 
                C.idCompra,
                DATE(C.FechaCompra) as Fecha,
                C.TotalCompra,
                C.Observacion,
                C.Estado,
                P.RazonSocial
            FROM Compras C
            INNER JOIN proveedores P
            ON C.idProveedor = P.idProveedor
            WHERE 1 = 1";

        $parametros = [];

        if ($fechaDesde !== '') {
            $sql .= " AND C.FechaCompra >= :fechaDesde";
            $parametros[':fechaDesde'] = $fechaDesde . ' 00:00:00';
        }

        if ($fechaHasta !== '') {
            $sql .= " AND C.FechaCompra < DATE_ADD(:fechaHasta, INTERVAL 1 DAY)";
             $parametros[':fechaHasta'] = $fechaHasta;
        }

        if ($proveedor !== '') {
            $sql .= " AND C.idProveedor = :proveedor";
            $parametros[':proveedor'] = $proveedor;
        }

        $inicio = (int) $inicio;
        $porPagina = (int) $porPagina;

        $sql .= " ORDER BY C.FechaCompra DESC
              LIMIT $inicio, $porPagina";

        $consulta = $this->db->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerDetalleCompra($idCompra){
        $sql = " SELECT C.idCompra, C.FechaCompra,C.TotalCompra, C.Estado, P.RazonSocial, DC.idDetalleCompra,
                DC.idModelo, DC.Cantidad, DC.PrecioCompra, DC.SubTotal, M.NombreMaterial, M.Color, M.NivelEducativo, C.Observacion
        FROM Compras C
        INNER JOIN Proveedores P
        ON C.idProveedor = P.idProveedor
        INNER JOIN DetalleCompra DC
        ON C.idCompra = DC.idCompra
        INNER JOIN modelos M    
        ON DC.idModelo = M.idModelo
        WHERE C.idCompra = :idCompra
        ORDER BY DC.idDetalleCompra ASC ";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':idCompra' => $idCompra
        ]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }




    public function contarCompras($fechaDesde, $fechaHasta, $proveedor){
        $sql = "SELECT COUNT(*)
            FROM Compras C
            INNER JOIN proveedores P
            ON C.idProveedor = P.idProveedor
            WHERE 1 = 1";
            $parametros = [];

        if ($fechaDesde !== '') {
            $sql .= " AND C.FechaCompra >= :fechaDesde";
            $parametros[':fechaDesde'] = $fechaDesde . ' 00:00:00';
        }

        if ($fechaHasta !== '') {
            $sql .= " AND C.FechaCompra < DATE_ADD(:fechaHasta, INTERVAL 1 DAY)";
            $parametros[':fechaHasta'] = $fechaHasta;
        }

        if ($proveedor !== '') {
            $sql .= " AND C.idProveedor = :proveedor";
            $parametros[':proveedor'] = $proveedor;
        }

        $consulta = $this->db->prepare($sql);

        $consulta->execute($parametros);

        return $consulta->fetchColumn();

    }

}