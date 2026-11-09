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


}

