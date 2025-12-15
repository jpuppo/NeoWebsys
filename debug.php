<?php
// Activar mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Modo Depuración Activado</h3>";

// Probar la conexión a la base de datos
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if($conn) {
    echo "✅ Conexión a BD exitosa<br>";
    
    // Probar consulta de ODTs
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM odts");
        $result = $stmt->fetch();
        echo "✅ ODTs en sistema: " . $result['total'] . "<br>";
    } catch(PDOException $e) {
        echo "❌ Error en consulta ODTs: " . $e->getMessage() . "<br>";
    }
    
    // Probar consulta de proyectos
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM proyectos");
        $result = $stmt->fetch();
        echo "✅ Proyectos en sistema: " . $result['total'] . "<br>";
    } catch(PDOException $e) {
        echo "❌ Error en consulta proyectos: " . $e->getMessage() . "<br>";
    }
    
} else {
    echo "❌ Error en conexión BD<br>";
}

// Verificar si la tabla se llama 'odts' o 'odis'
echo "<h4>📊 Verificando nombres de tablas:</h4>";
try {
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $table) {
        echo "• $table<br>";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>