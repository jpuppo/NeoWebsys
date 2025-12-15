<?php
require_once 'config/database.php';

echo "<h3>🧪 Probando Conexión Interna</h3>";

$database = new Database();
$conn = $database->getConnection();

if($conn) {
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 5px;'>";
    echo "🎉 <strong>¡CONEXIÓN INTERNA EXITOSA!</strong><br>";
    
    $stmt = $conn->query("SELECT @@hostname as servidor, USER() as usuario, NOW() as hora");
    $info = $stmt->fetch();
    
    echo "Servidor: " . $info['servidor'] . "<br>";
    echo "Usuario: " . $info['usuario'] . "<br>";
    echo "Hora: " . $info['hora'] . "<br>";
    echo "<strong>✅ MySQL seguro - solo conexiones locales</strong>";
    echo "</div>";
    
    // Mostrar tablas
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h4>📊 Tablas disponibles:</h4>";
    echo "<ul>";
    foreach($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px;'>";
    echo "❌ Error en conexión interna<br>";
    echo "Ejecuta en MySQL:<br>";
    echo "<code>CREATE USER 'master'@'localhost' IDENTIFIED BY '12077752Aa@';<br>";
    echo "GRANT ALL PRIVILEGES ON BDnuevo_sistema.* TO 'master'@'localhost';</code>";
    echo "</div>";
}
?>