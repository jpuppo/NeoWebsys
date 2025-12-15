<?php
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['idcliente'])) {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        $stmt = $conn->prepare("SELECT idcontactos, nombre, cargo FROM contactos WHERE idcliente = ? AND activo = 1 ORDER BY nombre");
        $stmt->execute([$_GET['idcliente']]);
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($contactos);
    }
}
?>