<?php

require_once __DIR__ . '/../config/conection.php';

class Inventario {
    private PDO $db;

    public function __construct() {
        $conexion = new Database();
        $this->db = $conexion->getConnection();
    }

    /*
      PARA SELECCIONAR LOS MODELOS DE LA BASE CONECTANDO TABLAS CON INNER JPIN PARA 
      PODER FILTRAR  OTROS ATRIBUTOS COMO CATEGORIA, MARCO, COLOR Y NIVEL EDUCATIVO 
     */
    /*TAMBIEN ESTA PARA SABER EN QUE PAGINA ESTOY Y CUANTOS ELEMENTOS MOSTRAR POR PAGINA */
    public function HablaConModelos ($buscar, $marco, $nivel, $color, $categoria
    , $inicio, $porPagina){
        $sql = "SELECT  modelos.idModelo, modelos.NombreMaterial,
        categorias.NombreCategoria, 
        TipoMarco.NombreMarco,
        modelos.Precio, 
        modelos.Cantidad, 
        modelos.TamañoHorizontal, 
        modelos.TamañoVertical, 
        modelos.Color, 
        modelos.NivelEducativo, 
        modelos.Estado
        FROM modelos
        INNER JOIN categorias
        ON modelos.idCategoria = categorias.idCategoria
        INNER JOIN TipoMarco
        ON modelos.idTipoMarco = TipoMarco.idTipoMarco
        WHERE modelos.Estado = true
        AND modelos.NombreMaterial LIKE :buscar";
        
        $parametros = [':buscar' => '%' . $buscar . '%'];
        if ($marco !== '') {

            $sql .= " AND TipoMarco.NombreMarco = :marco";

            $parametros[':marco'] = $marco;
        }
        if ($nivel !== '') {

            $sql .= " AND modelos.NivelEducativo = :nivel";

            $parametros[':nivel'] = $nivel;
        }
        if ($color !== '') {

            $sql .= " AND modelos.Color = :color";

            $parametros[':color'] = $color;
        }
        if ($categoria !== '') {

            $sql .= " AND categorias.NombreCategoria = :categoria";

            $parametros[':categoria'] = $categoria;
            
        }

        $inicio = (int) $inicio;
        $porPagina = (int) $porPagina;

        $sql .= " ORDER BY modelos.idModelo ASC LIMIT $inicio, $porPagina";
    
        $ConsultaPreparada = $this->db->prepare($sql);
        $ConsultaPreparada->execute($parametros);
        $RecogerDatos = $ConsultaPreparada->fetchAll();
        return $RecogerDatos;
    }

    public function contarModelos($buscar,$marco,$nivel,$color,$categoria) {
        $sql = "SELECT COUNT(*)
            FROM modelos
            INNER JOIN categorias
            ON modelos.idCategoria = categorias.idCategoria
            INNER JOIN TipoMarco
            ON modelos.idTipoMarco = TipoMarco.idTipoMarco
            WHERE modelos.Estado = true
            AND modelos.NombreMaterial LIKE :buscar";

        $parametros = [
            ':buscar' => '%' . $buscar . '%'
        ];

        if ($marco !== '') {
            $sql .= " AND TipoMarco.NombreMarco = :marco";
            $parametros[':marco'] = $marco;
        }

        if ($nivel !== '') {
            $sql .= " AND modelos.NivelEducativo = :nivel";
            $parametros[':nivel'] = $nivel;
        }

        if ($color !== '') {
         $sql .= " AND modelos.Color = :color";
            $parametros[':color'] = $color;
            
        }

        if ($categoria !== '') {
            $sql .= " AND categorias.NombreCategoria = :categoria";
         $parametros[':categoria'] = $categoria;
        }

        $consulta = $this->db->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchColumn();
    }

    /* FUNCIONES PARA QUE LA AGREGACION AUTOMATICA DE LOS OPTION EN MI VIEW 
    INVENTARIO ENTONCES ASI LAS OPCIONES NO DEPENDE DE MI SINO DE LO QUE 
    HAYA EN MI BD */
    public function obtenerMarcos(){
        $sql = "SELECT idTipoMarco, NombreMarco
                FROM TipoMarco
                ORDER BY NombreMarco ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function obtenerCategorias(){
        $sql = "SELECT idCategoria, NombreCategoria
                FROM categorias
                ORDER BY NombreCategoria ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function obtenerNiveles(){
        $sql = "SELECT DISTINCT NivelEducativo
                FROM modelos
                WHERE NivelEducativo IS NOT NULL
                AND NivelEducativo <> ''
                ORDER BY NivelEducativo ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function obtenerColores(){
        $sql = "SELECT DISTINCT Color
                 FROM modelos
                WHERE Color IS NOT NULL
                AND Color <> ''
                ORDER BY Color ASC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    public function obtenerModeloPorId($idModelo) {
        $sql = "SELECT *
        FROM modelos
        WHERE idModelo = :idModelo";

        $consulta = $this->db->prepare($sql);

        $consulta->execute([
            ':idModelo' => $idModelo
        ]);

        return $consulta->fetch();
    }

    public function actualizarModelo($idModelo,$NombreMaterial,$Precio,$TamañoHorizontal,$TamañoVertical,
        $Color,$NivelEducativo) {

        $sql = "UPDATE modelos
            SET NombreMaterial = ?,
                Precio = ?,
                TamañoHorizontal = ?,
                TamañoVertical = ?,
                Color = ?,
                NivelEducativo = ?
            WHERE idModelo = ?";

        $consulta = $this->db->prepare($sql);

        return $consulta->execute([$NombreMaterial,$Precio,$TamañoHorizontal,$TamañoVertical,$Color,
            $NivelEducativo,$idModelo]);
    }

    public function DarDeBaja($idModelo, $cantidad, $motivo){
        try{
            //OBTENGO LA CANTIDAD DE REGISTROS EN MODELOS QUE TODOS ESTAN EN ESTADO ACTIVO
            $this->db->BeginTransaction();
            $sqlModelo = "SELECT Cantidad
                        FROM modelos
                        WHERE idModelo =:idModelo
                        AND Estado = true
                        FOR UPDATE";
            $consultaModelo = $this->db->prepare($sqlModelo);
            $consultaModelo->execute([":idModelo" => $idModelo]);

            $modelo = $consultaModelo->fetch(PDO::FETCH_ASSOC);

        //PARA VERIFICAR SI EL METODO EXISTa Y ESTE ACTIVO 
        if (!$modelo) {
            throw new Exception('El modelo no existe o ya se encuentra inactivo.');
        }

        $cantidadActual = (int) $modelo['Cantidad'];
        $cantidadBaja = (int) $cantidad;

        // Validar cantidad
        if ($cantidadBaja <= 0) {
            throw new Exception('La cantidad debe ser mayor a cero.');
        }

        // Verificar que haya suficiente stock
        if ($cantidadBaja > $cantidadActual) {
            throw new Exception('La cantidad a retirar no puede ser mayor al stock actual.');
        }

        // Generar ID de la baja
        $sqlId = "SELECT MAX(CAST(SUBSTRING(idBaja, 4) AS UNSIGNED)) AS ultimo
                  FROM BajasInventario";

        $consultaId = $this->db->prepare($sqlId);
        $consultaId->execute();

        $ultimo = $consultaId->fetchColumn();

        $numero = ($ultimo !== false && $ultimo !== null)
            ? ((int) $ultimo + 1)
            : 1;

        $idBaja = 'BI-' . str_pad($numero, 5, '0', STR_PAD_LEFT);

        // Registrar la baja
        $sqlBaja = "INSERT INTO BajasInventario
                    (idBaja, idModelo, Cantidad, Motivo)
                    VALUES
                    (:idBaja, :idModelo, :Cantidad, :Motivo)";

        $consultaBaja = $this->db->prepare($sqlBaja);

        $consultaBaja->execute([
            ':idBaja' => $idBaja,
            ':idModelo' => $idModelo,
            ':Cantidad' => $cantidadBaja,
            ':Motivo' => $motivo
        ]);

        // Actualizar el stock del modelo
        $sqlActualizar = "UPDATE modelos
                          SET Cantidad = Cantidad - :Cantidad
                          WHERE idModelo = :idModelo";

        $consultaActualizar = $this->db->prepare($sqlActualizar);

        $consultaActualizar->execute([
            ':Cantidad' => $cantidadBaja,
            ':idModelo' => $idModelo
        ]);

            $this->db->commit();

            return true;

        }catch (Exception $e) {

            if ($this->db->inTransaction()) {
            $this->db->rollBack();
            }

            throw $e;
        }

    }

    public function obtenerBajas(){
        $sql = "SELECT
                BajasInventario.idBaja,
                BajasInventario.idModelo,
                modelos.NombreMaterial,
                categorias.NombreCategoria,
                TipoMarco.NombreMarco,
                BajasInventario.Cantidad,
                BajasInventario.Motivo,
                BajasInventario.FechaBaja
            FROM BajasInventario
            INNER JOIN modelos
            ON BajasInventario.idModelo = modelos.idModelo
            INNER JOIN categorias
            ON modelos.idCategoria = categorias.idCategoria
            INNER JOIN TipoMarco
            ON modelos.idTipoMarco = TipoMarco.idTipoMarco
            ORDER BY BajasInventario.FechaBaja DESC";

        $consulta = $this->db->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}