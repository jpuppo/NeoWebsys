<?php
class Database {
    private $host = "127.0.0.1";  // Solo conexión local
    private $port = "3306";       // Puerto interno
    private $db_name = "BDnuevo_sistema";
    private $username = "master";
    private $password = "12077752Aa@";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            error_log("Error de conexión BD: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}
?>