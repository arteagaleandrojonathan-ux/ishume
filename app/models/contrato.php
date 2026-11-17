<?php
require_once __DIR__ . "/../config/conection.php";

class Contrato {

    private PDO $db;

    public function __construct(){
        $conexion = new Database();
        $this->db= $conexion->getConnection();

    }

    public function obtenerProvincias(){
        $sql = "SELECT idProvincia, NomProvincia
        FROM Provincias 
        ORDER BY NomProvincia ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }
    public function obtenerMetodosPago(){
        $sql = "SELECT idMetodoPago, NombreMetodo
                FROM MetodoPago
                WHERE Estado = true
                ORDER BY NombreMetodo ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerDistritosPorProvincia($idProvincia){
        $sql = "SELECT idDistrito, NombreDistrito
            FROM Distritos
            WHERE idProvincia = :idProvincia
            ORDER BY NombreDistrito ASC";

        $consulta = $this->db->prepare($sql);

        $consulta->execute([
            ':idProvincia' => $idProvincia
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClientes(){
        $sql = "SELECT idCliente, Nombre, Apellidos, Testigo, Direccion
        FROM Cliente
        ORDER BY Nombre ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerColegios(){

        $sql = "SELECT idColegio, NombreColegio
        FROM Colegios
        ORDER BY NombreColegio ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarColegiosPorNombre($nombreColegio){
        $sql = "SELECT idColegio, NombreColegio
        FROM Colegios
        WHERE NombreColegio LIKE :nombreColegio
        ORDER BY NombreColegio ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':nombreColegio' => '%' . $nombreColegio . '%'
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public function registrarColegio($nombreColegio){

        $sqlExiste = "SELECT idColegio, NombreColegio 
        FROM Colegios 
        WHERE NombreColegio = :nombreColegio
        LIMIT 1";

        $consultaExiste = $this->db->prepare($sqlExiste);
        $consultaExiste->execute([
            ':nombreColegio' => $nombreColegio
        ]);

        $existente = $consultaExiste->fetch(PDO::FETCH_ASSOC);

        if($existente){
           $existente['existente'] = true;
            return $existente;
        }

        //Generar el ID del colegio
        $sqlIdColegio = "SELECT MAX(CAST(SUBSTRING(idColegio, 4) AS UNSIGNED)) AS ultimo
                         FROM Colegios";

        $consultaIdColegio = $this->db->prepare($sqlIdColegio);
        $consultaIdColegio->execute();

        $ultimoId = $consultaIdColegio->fetchColumn();

        $numeroId = ($ultimoId !== false && $ultimoId !== null)
            ? ((int) $ultimoId + 1) : 1;

        $idColegio = 'CO-' . str_pad($numeroId, 5, '0', STR_PAD_LEFT);

        //INSERTAR COLEGIO
        $sqlInsertar = "INSERT INTO Colegios (idColegio, NombreColegio) 
        VALUES (:idColegio, :nombreColegio)";

        $consultaInsertar = $this->db->prepare($sqlInsertar);
        $consultaInsertar->execute([
            ':idColegio' => $idColegio,
            ':nombreColegio' => $nombreColegio
        ]);

        return ['idColegio' => $idColegio, 
        'NombreColegio' => $nombreColegio,
        'existente' => false
        ];
    }



    public function registrarContrato($cliente, $contrato) {

        try{

            $this->db->beginTransaction();

            //Verificar que el colegio exita 
            $sqlColegio = 'SELECT idColegio
            FROM Colegios
            Where idColegio = :idColegio';

            $consultaColegio = $this->db->prepare($sqlColegio);
            $consultaColegio->execute([':idColegio' => $contrato['idColegio'] ]);

            if (!$consultaColegio-> fetch(PDO::FETCH_ASSOC)){
                throw new Exception ('El colegio no se encontro');
            }

            //verificar que el distrito exista 
            $sqlDistrito = 'SELECT idDistrito
            FROM Distritos
            Where idDistrito = :idDistrito';

            $consultaDistrito = $this->db->prepare($sqlDistrito);
            $consultaDistrito->execute([':idDistrito' => $contrato['idDistrito']]);

            if (!$consultaDistrito-> fetch(PDO::FETCH_ASSOC)){
                throw new Exception ('El distrito no se encuentra');
            }

            //generar el ID del cliente 
            $sqlIdCliente = "SELECT MAX(CAST(SUBSTRING(idCliente, 4) AS UNSIGNED)) AS ultimo
            FROM Cliente";

            $consultaCliente = $this->db->prepare($sqlIdCliente);
            $consultaCliente->execute();

            $ultimoCliente = $consultaCliente->fetchColumn();

            $numeroCliente = ($ultimoCliente !== false && $ultimoCliente !== null)
            ? ((int) $ultimoCliente + 1) : 1;

            $idCliente = 'CL-' . str_pad($numeroCliente, 5, '0', STR_PAD_LEFT);

            //INSERTAR CLIENTE 
            $sqlCliente = 'INSERT INTO Cliente 
            (idCliente, Nombre, Apellidos, Testigo, Direccion, TipoDOI, NumDOI, Telefono, Correo)
            VALUES(:idCliente, :Nombre, :Apellidos, 
            :Testigo, :Direccion, :TipoDOI, :NumDOI, :Telefono, :Correo)';

            $consultaCliente = $this->db->prepare($sqlCliente);

            $consultaCliente->execute([
                ':idCliente' => $idCliente,
                ':Nombre' => $cliente ['Nombre'],
                ':Apellidos' => $cliente ['Apellidos'],
                ':Testigo' => $cliente ['Testigo'] !== '' ? $cliente['Testigo'] : null,
                ':Direccion' => $cliente ['Direccion'],
                ':TipoDOI' => $cliente ['TipoDOI'] !== '' ? $cliente['TipoDOI'] : null,
                ':NumDOI' => $cliente ['NumDOI'] !== '' ? $cliente['NumDOI'] : null,
                ':Telefono' => $cliente ['Telefono'],
                ':Correo' => $cliente ['Correo'] !== '' ? $cliente['Correo'] : null,
            ]);

        
       

            //Generar id del contrato
            $sqlIdContrato = "SELECT MAX(CAST(SUBSTRING(idContrato, 4) AS UNSIGNED)) AS ultimo
            FROM Contratos";

            $consultaContrato = $this->db->prepare($sqlIdContrato);
            $consultaContrato->execute();

            $ultimoContrato = $consultaContrato->fetchColumn();

            $numeroContrato = ($ultimoContrato !== false && $ultimoContrato !== null)
            ? ((int) $ultimoContrato + 1) : 1;

            $idContrato = 'CT-' . str_pad($numeroContrato, 5, '0', STR_PAD_LEFT);

            $sqlContrato = "INSERT INTO Contratos
                (idContrato, idCliente, idColegio, idDistrito, Evento, Servicio, Direccion,
                FechaContrato, FechaEvento, Condiciones, Estado)
                VALUES (:idContrato, :idCliente, :idColegio,:idDistrito, :Evento, :Servicio, :Direccion,
                NOW(), :FechaEvento, :Condiciones, 'PROCESO')";
            
            $consultaContrato = $this->db->prepare($sqlContrato);
            $consultaContrato->execute([
                ':idContrato' => $idContrato,
                ':idCliente' => $idCliente,
                ':idColegio' => $contrato['idColegio'],
                ':idDistrito' => $contrato['idDistrito'],
                ':Evento' => $contrato['Evento'],
                ':Servicio' => $contrato['Servicio'],
                ':Direccion' => $contrato['Direccion'],
                ':FechaEvento' => $contrato['FechaEvento'],
                ':Condiciones' => $contrato['Condiciones'] !== '' ? $contrato['Condiciones'] : null

            ]);
        
            
            $this->db->commit();
            return $idContrato;

         } catch (Exception $e) {
            if($this->db->inTransaction()){
                $this->db->rollBack();
            }
            throw $e;

        }

    }

    public function obtenerContratosConVentas() {

        $sql = "SELECT
                c.idContrato,
                c.Evento,
                c.Servicio,
                c.Direccion,
                c.FechaContrato,
                c.FechaEvento,
                c.Estado AS EstadoContrato,

                col.NombreColegio,

                cli.Nombre,
                cli.Apellidos,

                v.idVenta,
                v.PrecioTotal,
                v.CantidadPedidos,
                v.Estado AS EstadoVenta

            FROM Contratos c

            INNER JOIN Colegios col
                ON c.idColegio = col.idColegio

            INNER JOIN Cliente cli
                ON c.idCliente = cli.idCliente

            LEFT JOIN Ventas v
                ON c.idContrato = v.idContrato

            ORDER BY c.FechaContrato DESC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    public function obtenerContratoPorId($idContrato) {

        $sql = "SELECT
                c.idContrato,c.Evento,c.Servicio,c.Direccion,c.FechaContrato,c.FechaEvento,c.Condiciones,c.Estado,

                cli.Nombre,cli.Apellidos,cli.Testigo,cli.TipoDOI,cli.NumDOI,cli.Telefono,cli.Correo,cli.Direccion AS DireccionCliente,

                col.NombreColegio,

                d.NombreDistrito,p.NomProvincia,

            -- DATOS DE LA VENTA
                v.PrecioTotal,
                v.FechaLimitePago,

            -- TOTAL PAGADO (todos los pagos activos)
                COALESCE((SELECT SUM(pg.Monto)
                FROM Pago pg
                WHERE pg.idContrato = c.idContrato
                  AND pg.Estado = TRUE), 0) AS TotalPagado,

            -- PRIMER PAGO REGISTRADO
                ( SELECT pg.Monto
                    FROM Pago pg
                    WHERE pg.idContrato = c.idContrato
                        AND pg.Estado = TRUE
                    ORDER BY pg.FechaPago ASC, pg.idPago ASC
                    LIMIT 1) AS PrimerPago,

                -- MÉTODO DEL PRIMER PAGO
                (SELECT mp.NombreMetodo
                    FROM Pago pg
                    INNER JOIN MetodoPago mp
                        ON pg.idMetodoPago = mp.idMetodoPago
                    WHERE pg.idContrato = c.idContrato
                        AND pg.Estado = TRUE
                    ORDER BY pg.FechaPago ASC, pg.idPago ASC
                    LIMIT 1) AS MetodoPrimerPago,

            -- FECHA DEL PRIMER PAGO
                (SELECT pg.FechaPago
                    FROM Pago pg
                    WHERE pg.idContrato = c.idContrato
                        AND pg.Estado = TRUE
                    ORDER BY pg.FechaPago ASC, pg.idPago ASC
                    LIMIT 1) AS FechaPrimerPago,

            -- SALDO PENDIENTE
                 GREATEST(v.PrecioTotal - COALESCE((
                    SELECT SUM(pg.Monto)
                    FROM Pago pg
                    WHERE pg.idContrato = c.idContrato
                      AND pg.Estado = TRUE ), 0), 0) AS SaldoPendiente

        FROM Contratos c
        INNER JOIN Cliente cli
        ON c.idCliente = cli.idCliente
        INNER JOIN Colegios col
        ON c.idColegio = col.idColegio
        INNER JOIN Distritos d
        ON c.idDistrito = d.idDistrito
        INNER JOIN Provincias p
        ON d.idProvincia = p.idProvincia
        LEFT JOIN Ventas v
        ON c.idContrato = v.idContrato
        WHERE c.idContrato = :idContrato
        LIMIT 1";

        $consulta = $this->db->prepare($sql);

        $consulta->execute([
            ':idContrato' => $idContrato
        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }


    public function obtenerResumenPanel($anio){

        $fechaInicio = sprintf('%04d-01-01', $anio);
        $fechaFin = sprintf('%04d-01-01', $anio + 1);

        $sql = "SELECT
                /* TOTAL DE REGISTROS DEL AÑO */

                (SELECT COUNT(*) FROM Contratos c
                WHERE c.FechaContrato >= :inicioContratos 
                AND c.FechaContrato < :finContratos) AS TotalContratos,

                (SELECT COUNT(*) FROM Ventas v
                INNER JOIN Contratos c ON c.idContrato = v.idContrato
                WHERE c.FechaContrato >= :inicioVentas
                AND c.FechaContrato < :finVentas) AS TotalVentas,

                /* CONTRATOS */
                ( SELECT COUNT(*) FROM Contratos c
                WHERE c.Estado = 'PROCESO'AND
                NOT EXISTS (SELECT 1 FROM Ventas v WHERE v.idContrato = c.idContrato)
                AND c.FechaContrato >= :inicioProceso
                AND c.FechaContrato < :finProceso) AS ContratosProceso,


                (SELECT COUNT(*) FROM Contratos c WHERE c.Estado = 'FINALIZADO'
                AND EXISTS (SELECT 1 FROM Ventas v WHERE v.idContrato = c.idContrato)
                AND c.FechaContrato >= :inicioFinalizados
                AND c.FechaContrato < :finFinalizados) AS ContratosFinalizados,

                (SELECT COUNT(*) FROM Contratos c 
                WHERE c.Estado = 'CANCELADO'
                AND c.FechaContrato >= :inicioCancelados
                AND c.FechaContrato < :finCancelados) AS ContratosCancelados,

                /* VENTAS */
                (SELECT COUNT(*) FROM Ventas v 
                INNER JOIN Contratos c ON c.idContrato = v.idContrato
                WHERE v.Estado = 'PENDIENTE'
                AND c.FechaContrato >= :inicioPendientes
                AND c.FechaContrato < :finPendientes) AS VentasPendientes,

                (SELECT COUNT(*) FROM Ventas v 
                INNER JOIN Contratos c ON c.idContrato = v.idContrato
                WHERE v.Estado = 'FINALIZADA'
                AND c.FechaContrato >= :inicioVentasFinalizadas
                AND c.FechaContrato < :finVentasFinalizadas) AS VentasFinalizadas,

                (SELECT COUNT(*) FROM Ventas v 
                INNER JOIN Contratos c ON c.idContrato = v.idContrato
                WHERE v.Estado = 'ANULADA'
                AND c.FechaContrato >= :inicioAnuladas
                AND c.FechaContrato < :finAnuladas) AS VentasAnuladas,

                /* TOTAL POR COBRAR */
                (SELECT COALESCE(SUM(GREATEST(v.PrecioTotal - COALESCE(p.TotalPagado, 0),0)), 0)

            FROM Ventas v
            INNER JOIN Contratos c ON c.idContrato = v.idContrato
            LEFT JOIN (SELECT idContrato, SUM(Monto) AS TotalPagado
                FROM Pago WHERE Estado = 1
                GROUP BY idContrato) 
                p ON p.idContrato = v.idContrato WHERE v.Estado = 'PENDIENTE'
                AND c.FechaContrato >= :inicioPorCobrar
                AND c.FechaContrato < :finPorCobrar) AS TotalPorCobrar";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':inicioContratos' => $fechaInicio,
            ':finContratos' => $fechaFin,
            ':inicioVentas' => $fechaInicio,
            ':finVentas' => $fechaFin,
            ':inicioProceso' => $fechaInicio,
            ':finProceso' => $fechaFin,
            ':inicioFinalizados' => $fechaInicio,
            ':finFinalizados' => $fechaFin,
            ':inicioCancelados' => $fechaInicio,
            ':finCancelados' => $fechaFin,
            ':inicioPendientes' => $fechaInicio,
            ':finPendientes' => $fechaFin,
            ':inicioVentasFinalizadas' => $fechaInicio,
            ':finVentasFinalizadas' => $fechaFin,
            ':inicioAnuladas' => $fechaInicio,
            ':finAnuladas' => $fechaFin,
            ':inicioPorCobrar' => $fechaInicio,
            ':finPorCobrar' => $fechaFin
        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerProximosEventos($mes, $anio) {
            $fechaInicio = sprintf('%04d-%02d-01', $anio, $mes);
        $fechaFin = date('Y-m-d', strtotime($fechaInicio . ' +1 month'));

        $sql = "SELECT c.idContrato,col.NombreColegio,c.Evento,pr.NomProvincia,c.FechaEvento,
                    DATEDIFF(c.FechaEvento, CURDATE()) AS DiasRestantes

                FROM Contratos c

                INNER JOIN Ventas v
                ON c.idContrato = v.idContrato

                INNER JOIN Colegios col
                ON c.idColegio = col.idColegio

                INNER JOIN Distritos d
                ON c.idDistrito = d.idDistrito

                INNER JOIN Provincias pr
                ON d.idProvincia = pr.idProvincia

                WHERE c.Estado = 'FINALIZADO'
                AND v.Estado IN ('PENDIENTE', 'FINALIZADA')
                AND c.FechaEvento >= :fechaInicio
                AND c.FechaEvento < :fechaFin

                ORDER BY c.FechaEvento ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPagosPorVencer($mes, $anio) {
        $fechaInicio = sprintf('%04d-%02d-01', $anio, $mes);
        $fechaFin = date('Y-m-d', strtotime($fechaInicio . ' +1 month'));

        $sql = "SELECT c.idContrato,v.PrecioTotal,v.FechaLimitePago,
                    COALESCE(p.TotalPagado, 0) AS TotalPagado,

                    GREATEST(v.PrecioTotal - COALESCE(p.TotalPagado, 0),0) AS Saldo,

                    DATEDIFF(v.FechaLimitePago, CURDATE()) AS DiasRestantes

                FROM Ventas v
                INNER JOIN Contratos c
                ON v.idContrato = c.idContrato
                LEFT JOIN (
                    SELECT idContrato, SUM(Monto) AS TotalPagado
                    FROM Pago
                    WHERE Estado = 1
                    GROUP BY idContrato
                ) p ON p.idContrato = v.idContrato

                WHERE v.Estado = 'PENDIENTE'
                AND v.FechaLimitePago IS NOT NULL
                AND v.FechaLimitePago >= :fechaInicio
                AND v.FechaLimitePago < :fechaFin
                AND v.PrecioTotal > COALESCE(p.TotalPagado, 0)

                ORDER BY v.FechaLimitePago ASC
                LIMIT 10";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUltimosPagos($mes, $anio) {
        $fechaInicio = sprintf('%04d-%02d-01', $anio, $mes);
        $fechaFin = date('Y-m-d', strtotime($fechaInicio . ' +1 month'));

        $sql = "SELECT p.idPago,p.FechaPago,p.idContrato,mp.NombreMetodo,p.Monto

                FROM Pago p
                INNER JOIN MetodoPago mp
                ON p.idMetodoPago = mp.idMetodoPago

                WHERE p.Estado = 1
                AND p.FechaPago >= :fechaInicio
                AND p.FechaPago < :fechaFin

                ORDER BY p.FechaPago DESC, p.idPago DESC
                LIMIT 100";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin

        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenMensual($mes, $anio) {
        // Definir el inicio y el final del período seleccionado
        $fechaInicio = sprintf('%04d-%02d-01', $anio, $mes);

        $fechaFin = date('Y-m-d',strtotime($fechaInicio . ' +1 month'));

        $sql = "SELECT
                    COUNT(v.idVenta) AS TotalRegistros,

                    -- Total contratado durante el mes
                    COALESCE(SUM(v.PrecioTotal), 0) AS TotalContratado,

                    -- Total pagado por esas ventas
                    COALESCE(SUM(COALESCE(p.TotalPagado, 0)), 0) AS TotalCobrado,

                    -- Saldo pendiente de esas ventas
                    COALESCE(SUM(GREATEST(v.PrecioTotal - COALESCE(p.TotalPagado, 0),0)), 0) AS TotalPorCobrar

                FROM Ventas v

                INNER JOIN Contratos c
                ON v.idContrato = c.idContrato

                LEFT JOIN (
                    SELECT idContrato, SUM(Monto) AS TotalPagado
                    FROM Pago
                    WHERE Estado = 1
                    GROUP BY idContrato
                ) p ON p.idContrato = v.idContrato

                WHERE c.FechaContrato >= :fechaInicio
                AND c.FechaContrato < :fechaFin
                AND v.Estado <> 'ANULADA'";

        $consulta = $this->db->prepare($sql);
        $consulta->execute([
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin
        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

}

