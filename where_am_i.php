<?php
echo "<h3>📍 Información del Directorio Actual</h3>";
echo "<strong>Ruta absoluta:</strong> " . __DIR__ . "<br>";
echo "<strong>Archivo actual:</strong> " . __FILE__ . "<br>";
echo "<strong>URL acceso:</strong> http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "<br>";

echo "<h3>📁 Contenido del directorio:</h3>";
$files = scandir(__DIR__);
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        $type = is_dir($file) ? "📁" : "📄";
        echo "$type $file<br>";
    }
}

echo "<h3>🧪 Probando inclusión de config:</h3>";
if(file_exists('config/database.php')) {
    require_once 'config/database.php';
    echo "✅ config/database.php cargado correctamente<br>";
    
    $database = new Database();
    $conn = $database->getConnection();
    
    if($conn) {
        echo "✅ Conexión a BD exitosa<br>";
    } else {
        echo "❌ Error en conexión BD<br>";
    }
} else {
    echo "❌ No se encuentra config/database.php<br>";
}
?>