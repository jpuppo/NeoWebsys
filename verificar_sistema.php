<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

echo "<h3>✅ Verificación Final del Sistema ODT</h3>";

$database = new Database();
$conn = $database->getConnection();

if($conn) {
    // Verificar estructura de tabla odts
    try {
        $stmt = $conn->query("DESCRIBE odts");
        $columns = $stmt->fetchAll();
        
        echo "<h4>📋 Estructura de tabla 'odts':</h4>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th></tr>";
        foreach($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar ODTs existentes
        $stmt = $conn->query("SELECT * FROM odts");
        $odts = $stmt->fetchAll();
        
        echo "<h4>📊 ODTs en sistema (" . count($odts) . "):</h4>";
        foreach($odts as $odt) {
            echo "<div style='background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 5px;'>";
            echo "<strong>{$odt['numero_odt']}</strong> - {$odt['descripcion']}";
            echo " <span style='background: #007bff; color: white; padding: 2px 6px; border-radius: 3px;'>{$odt['estado']}</span>";
            echo " <span style='background: #28a745; color: white; padding: 2px 6px; border-radius: 3px;'>{$odt['prioridad']}</span>";
            echo "</div>";
        }
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
        echo "🎉 <strong>¡Sistema ODT listo y funcionando!</strong>";
        echo "</div>";
        
    } catch(PDOException $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "❌ Error: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "❌ Error de conexión a la base de datos";
}
?>