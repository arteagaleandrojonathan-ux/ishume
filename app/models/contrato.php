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

    public function registrarContrato($cliente, $contrato, $adelanto) {

        try{

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

            //veridicaar que la forma de pago exita 
            $sqlMetodo = 'SELECT idMetodoPago
            FROM MetodoPago
            where idMetodoPago = :idMetodoPago
            AND Estado = true';

            $consultaMetodo = $this->db->prepare($sqlMetodo);
            $consultaMetodo->execute([':idMetodoPago' => $adelanto['idMetodoPago']]);

            if (!$consultaMetodo -> fetch(PDO::FETCH_ASSOC)){
                throw new Exception('El metodo se encuentra inactivo');
            }   

            //validar el adelanto
            $montoAdelanto = round((float) $adelanto['Monto'], 2);
            if($montoAdelanto <= 0 ){
                throw new Exception('Adelanto debe ser mayor a cero');
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
            $sqlCliente = 'INSERT INTO Cliente VALUES(:idCliente, :Nombre, :Apellidos, 
            :Testigo, :Direccion, :TipoDOI, :NumDOI, :Telefono, :Correo)';

            $consultaCliente = $this->db->prepare($sqlCliente);

            $consultaCliente->execute([
                ':idCliente' => $idCliente,
                ':Nombre' => $Cliente ['Nombre'],
                ':Apellidos' => $Cliente ['Apellidos'],
                ':Testigo' => $Cliente ['Testigo'] !== '' ? $Cliente['Testigo'] : null,
                ':Direccion' => $Cliente ['Direccion'],
                ':TipoDOI' => $Cliente ['TipoDOI'] !== '' ? $Cliente['TipoDOI'] : null,
                ':NumDOI' => $Cliente ['NumDOI'] !== '' ? $Cliente['NumDOI'] : null,
                ':Telefono' => $Cliente ['Telefono'],
                ':Correo' => $Cliente ['Correo'] !== '' ? $Cliente['Correo'] : null,
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
        
            // GENERAR ID DE PAGO 
            $sqlIdPago = "SELECT MAX(CAST(SUBSTRING(idPago, 4) AS UNSIGNED)) AS ultimo
            FROM Pago";

            $consultaPago = $this->db->prepare($sqlIdPago);
            $consultaPago->execute();

            $ultimoPago = $consultaPago->fetchColumn();

            $numeroPago = ($ultimoPago !== false && $ultimoPago !== null)
            ? ((int) $ultimoPago + 1) : 1;

            $idPago = 'PA-' . str_pad($numeroPago, 5, '0', STR_PAD_LEFT);
        
            $sqlPago = "INSERT INTO Pago 
            (idPago, idContrato, idMetodoPago, Monto, FechaPago, NumOperacion,
             Observacion, Estado)
            VALUES 
            (:idPago, :idContrato, :idMetodoPago, :Monto, NOW(), :NumOperacion, 
            'Adelanto al firmar el contrato', true)";

            $consultaPago = $this->db->prepare($sqlPago);
            
            $consultaPago->execute([
                ':idPago' => $idPago,
                ':idContrato' => $idContrato,
                ':idMetodoPago' => $adelanto['idMetodoPago'],
                ':Monto' => $montoAdelanto,
                ':NumOperacion' => $adelanto['NumOperacion'] !== '' ? $adelanto['NumOperacion'] : null
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

}

