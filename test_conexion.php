<?php
require_once 'config/database.php';

echo "<h3>🧪 Probando Conexiones...</h3>";

// Probar múltiples métodos de conexión
$methods = [
    ['neoprojects.ddns.net', '4001', 'Conexión Directa Puerto 4001'],
    ['neoprojects.ddns.net', '3306', 'Conexión Directa Puerto 3306'],
    ['127.0.0.1', '3306', 'Conexión Local (si está en mismo servidor)']
];

foreach($methods as $method) {
    list($host, $port, $desc) = $method;
    
    echo "<h4>🔍 Probando: $desc</h4>";
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=BDnuevo_sistema;charset=utf8mb4";
        $conn = new PDO($dsn, 'master', '12077752Aa@');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->query("SELECT NOW() as hora");
        $result = $stmt->fetch();
        
        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px;'>";
        echo "✅ <strong>CONEXIÓN EXITOSA</strong><br>";
        echo "Hora servidor: " . $result['hora'] . "<br>";
        echo "Host: $host:$port";
        echo "</div>";
        
        break; // Si una conexión funciona, salir
        
    } catch(PDOException $e) {
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px;'>";
        echo "❌ Error: " . $e->getMessage();
        echo "</div>";
    }
}

// Probar con la clase Database
echo "<h4>🔍 Probando Clase Database</h4>";
$database = new Database();
$conn = $database->getConnection();

if($conn) {
    echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px;'>";
    echo "🎉 <strong>CLASE DATABASE FUNCIONANDO</strong>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px;'>";
    echo "❌ Clase Database no pudo conectar";
    echo "</div>";
}
?>