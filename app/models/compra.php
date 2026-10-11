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
                P.RazonSocial,
                (SELECT COUNT(*) FROM DetalleCompra D
                 WHERE D.idCompra = C.idCompra AND D.Estado = false) AS Cancelados
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
                DC.idModelo, DC.Cantidad, DC.PrecioCompra, DC.SubTotal, DC.Estado AS EstadoDetalle, A.Motivo AS MotivoAnulacion, A.FechaAnulacion,
                M.NombreMaterial, M.Color, M.NivelEducativo, C.Observacion
        FROM Compras C
        INNER JOIN Proveedores P
        ON C.idProveedor = P.idProveedor
        INNER JOIN DetalleCompra DC
        ON C.idCompra = DC.idCompra
        INNER JOIN modelos M    
        ON DC.idModelo = M.idModelo
        LEFT JOIN AnularCompras A
        ON A.idDetalleCompra = DC.idDetalleCompra
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

    /* =====================================================
       CANCELAR PRODUCTOS DE UNA COMPRA (uno, varios o todos)
       - Descuenta del stock lo que la compra había sumado
       - Marca cada línea como cancelada
       - Guarda un registro por línea en AnularCompras
       - Si ya no queda ninguna línea vigente, la compra queda anulada
       ===================================================== */
    public function anularCompra($idCompra, array $idsDetalle, $motivo){
        try {
            $idsDetalle = array_values(array_unique(array_filter(array_map('strval', $idsDetalle))));

            if ($motivo === '') {
                throw new Exception('Debe indicar el motivo de la cancelación.');
            }
            if (mb_strlen($motivo) > 500) {
                throw new Exception('El motivo no puede superar los 500 caracteres.');
            }
            if (count($idsDetalle) === 0) {
                throw new Exception('Seleccione al menos un producto para cancelar.');
            }

            $this->db->beginTransaction();

            // 1. La compra debe existir y estar vigente
            $consultaCompra = $this->db->prepare(
                "SELECT Estado FROM Compras WHERE idCompra = :idCompra FOR UPDATE"
            );
            $consultaCompra->execute([':idCompra' => $idCompra]);
            $compra = $consultaCompra->fetch(PDO::FETCH_ASSOC);

            if (!$compra) {
                throw new Exception('La compra no existe.');
            }
            if (!(int) $compra['Estado']) {
                throw new Exception('La compra ya se encuentra anulada.');
            }

            // 2. Las líneas elegidas deben ser de esta compra y seguir vigentes
            $marcadores = [];
            $parametros = [':idCompra' => $idCompra];
            foreach ($idsDetalle as $i => $idDetalle) {
                $marcadores[] = ':d' . $i;
                $parametros[':d' . $i] = $idDetalle;
            }

            $consultaLineas = $this->db->prepare(
                "SELECT idDetalleCompra, idModelo, Cantidad
                 FROM DetalleCompra
                 WHERE idCompra = :idCompra
                 AND Estado = true
                 AND idDetalleCompra IN (" . implode(',', $marcadores) . ")
                 FOR UPDATE"
            );
            $consultaLineas->execute($parametros);
            $lineas = $consultaLineas->fetchAll(PDO::FETCH_ASSOC);

            if (count($lineas) !== count($idsDetalle)) {
                throw new Exception('Alguno de los productos seleccionados no pertenece a la compra o ya fue cancelado.');
            }

            // 3. Procesar cada línea
            $consultaStock = $this->db->prepare(
                "SELECT NombreMaterial, Color, Cantidad FROM modelos WHERE idModelo = :idModelo FOR UPDATE"
            );
            $descontar = $this->db->prepare(
                "UPDATE modelos SET Cantidad = Cantidad - :Cantidad WHERE idModelo = :idModelo"
            );
            $marcar = $this->db->prepare(
                "UPDATE DetalleCompra SET Estado = false WHERE idDetalleCompra = :idDetalleCompra"
            );
            $insertar = $this->db->prepare(
                "INSERT INTO AnularCompras (idAnulacion, idCompra, idDetalleCompra, Motivo)
                 VALUES (:idAnulacion, :idCompra, :idDetalleCompra, :Motivo)"
            );

            foreach ($lineas as $linea) {

                $consultaStock->execute([':idModelo' => $linea['idModelo']]);
                $modelo = $consultaStock->fetch(PDO::FETCH_ASSOC);

                if (!$modelo) {
                    throw new Exception('El modelo ' . $linea['idModelo'] . ' ya no existe.');
                }

                if ((int) $modelo['Cantidad'] < (int) $linea['Cantidad']) {
                    throw new Exception(
                        'No se puede cancelar "' . $modelo['NombreMaterial'] . ' (' . $modelo['Color'] . ')": ' .
                        'el stock actual (' . $modelo['Cantidad'] . ') es menor que la cantidad comprada (' .
                        $linea['Cantidad'] . '). Parte de ese stock ya salió del inventario.'
                    );
                }

                $descontar->execute([
                    ':Cantidad' => (int) $linea['Cantidad'],
                    ':idModelo' => $linea['idModelo']
                ]);

                $marcar->execute([':idDetalleCompra' => $linea['idDetalleCompra']]);

                $ultimo = $this->db->query(
                    "SELECT MAX(CAST(SUBSTRING(idAnulacion, 4) AS UNSIGNED)) FROM AnularCompras"
                )->fetchColumn();
                $numero = ($ultimo !== false && $ultimo !== null) ? ((int) $ultimo + 1) : 1;

                $insertar->execute([
                    ':idAnulacion' => 'AN-' . str_pad($numero, 5, '0', STR_PAD_LEFT),
                    ':idCompra' => $idCompra,
                    ':idDetalleCompra' => $linea['idDetalleCompra'],
                    ':Motivo' => $motivo
                ]);
            }

            // 4. Actualizar la compra según lo que quede vigente
            $consultaResto = $this->db->prepare(
                "SELECT COUNT(*) AS restantes, COALESCE(SUM(SubTotal), 0) AS total
                 FROM DetalleCompra
                 WHERE idCompra = :idCompra AND Estado = true"
            );
            $consultaResto->execute([':idCompra' => $idCompra]);
            $resto = $consultaResto->fetch(PDO::FETCH_ASSOC);

            $completa = ((int) $resto['restantes'] === 0);

            if ($completa) {
                $actualizar = $this->db->prepare("UPDATE Compras SET Estado = false WHERE idCompra = :idCompra");
                $actualizar->execute([':idCompra' => $idCompra]);
            } else {
                $actualizar = $this->db->prepare("UPDATE Compras SET TotalCompra = :total WHERE idCompra = :idCompra");
                $actualizar->execute([':total' => $resto['total'], ':idCompra' => $idCompra]);
            }

            $this->db->commit();

            return ['cantidad' => count($lineas), 'completa' => $completa];

        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

}