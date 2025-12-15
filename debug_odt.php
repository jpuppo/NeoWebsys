<?php
// Activar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>🔧 Depurando odt.php</h3>";

// Probar cada parte del código por separado

// 1. Probar conexión a BD
echo "<h4>1. Probando conexión a BD...</h4>";
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if($conn) {
    echo "✅ Conexión BD exitosa<br>";
} else {
    die("❌ Error en conexión BD");
}

// 2. Probar consulta de ODTs
echo "<h4>2. Probando consulta ODTs...</h4>";
try {
    $stmt = $conn->query("
        SELECT o.*, p.nombreoportunidad as proyecto, c.nombrerrazonsocial as cliente 
        FROM odts o 
        LEFT JOIN proyectos p ON o.idproyecto = p.idproyectos 
        LEFT JOIN clientes c ON p.idcliente = c.idclientes 
        ORDER BY o.prioridad DESC, o.numero_odt
    ");
    $odts = $stmt->fetchAll();
    echo "✅ Consulta ODTs exitosa - " . count($odts) . " registros<br>";
} catch(PDOException $e) {
    die("❌ Error en consulta ODTs: " . $e->getMessage());
}

// 3. Probar consulta de proyectos
echo "<h4>3. Probando consulta proyectos...</h4>";
try {
    $stmt = $conn->query("SELECT idproyectos, nombreoportunidad FROM proyectos");
    $proyectos = $stmt->fetchAll();
    echo "✅ Consulta proyectos exitosa - " . count($proyectos) . " registros<br>";
} catch(PDOException $e) {
    die("❌ Error en consulta proyectos: " . $e->getMessage());
}

echo "<h4 style='color: green;'>✅ Todas las consultas funcionan correctamente</h4>";

// Mostrar datos crudos
echo "<h4>📊 Datos ODTs:</h4>";
echo "<pre>";
print_r($odts);
echo "</pre>";

echo "<h4>📊 Datos Proyectos:</h4>";
echo "<pre>";
print_r($proyectos);
echo "</pre>";
?>