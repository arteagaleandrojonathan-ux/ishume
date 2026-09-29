<?php
class DataBase{
    private $host = "localhost";
    private $db_name = "BD_ISHUME_1";
    private $username = "root";
    private $password = "";

    public ?PDO $conn =null;

    public function getConnection(): PDO {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
        
    }


}  
