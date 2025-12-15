<?php
require_once '../config/database.php';

if($_POST) {
    $database = new Database();
    $conn = $database->getConnection();
    
    if($conn) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO odts (numero_odt, idproyecto, descripcion, estado, prioridad) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $_POST['numero_odt'],
                $_POST['idproyecto'],
                $_POST['descripcion'],
                $_POST['estado'],
                $_POST['prioridad']
            ]);
            
            header("Location: ../odt.php?success=1");
            exit;
            
        } catch(PDOException $e) {
            header("Location: ../odt.php?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}
?>