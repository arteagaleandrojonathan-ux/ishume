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

    public function registrarContrato($idCliente, $idColegio, $idDistrito, $Evento,
        $Servicio, $Direccion, $Proforma, $FechaEvento) {
    
        // Generar el siguiente ID del contrato
        $sqlId = "SELECT MAX(CAST(SUBSTRING(idContrato, 4) AS UNSIGNED)) AS ultimo
              FROM Contratos";

        $consultaId = $this->db->prepare($sqlId);
        $consultaId->execute();

        $ultimo = $consultaId->fetchColumn();

        $numero = ($ultimo !== false && $ultimo !== null)
            ? ((int) $ultimo + 1)
            : 1;

        $idContrato = 'CT-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
        
        //REGISTRAR CONTRATOS
        $sqlContrato = "INSERT INTO Contratos VALUES
         (:idContrato, :idCliente, :idColegio, :idDistrito, 
         :Evento, :Servicio, :Direccion, :Proforma, NOW(), :FechaEvento)";

          $consulta = $this->db->prepare($sqlContrato);

        $consulta->execute([
            ':idContrato' => $idContrato,
            ':idCliente' => $idCliente,
            ':idColegio' => $idColegio,
            ':idDistrito' => $idDistrito,
            ':Evento' => $Evento,
            ':Servicio' => $Servicio,
            ':Direccion' => $Direccion,
            ':Proforma' => $Proforma,
            ':FechaEvento' => $FechaEvento
        ]); 

        return $idContrato; 
    }





}

