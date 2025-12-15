<?php
// debug_columns.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if($conn) {
    // Verificar estructura exacta de odts
    $stmt = $conn->query("DESCRIBE odts");
    $columns = $stmt->fetchAll();
    
    echo "<h4>Estructura EXACTA de odts:</h4>";
    foreach($columns as $col) {
        echo "{$col['Field']} - {$col['Type']}<br>";
    }
    
    // Verificar si hay problemas con tildes
    echo "<h4>Valores únicos en columna 'estado':</h4>";
    $stmt = $conn->query("SELECT DISTINCT estado FROM odts");
    $estados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach($estados as $estado) {
        echo "'$estado'<br>";
    }
}
?>