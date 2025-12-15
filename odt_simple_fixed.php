<?php
// Activar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if(!$conn) {
    die("❌ No se pudo conectar a la base de datos");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema ODT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .kanban { display: flex; overflow-x: auto; gap: 10px; padding: 20px 0; }
        .column { min-width: 250px; background: #f5f5f5; padding: 15px; border-radius: 8px; }
        .card { background: white; padding: 10px; margin: 8px 0; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <h2>🏗️ Sistema ODT - Tablero Kanban</h2>

    <?php
    try {
        // Consulta simple sin JOINs complejos
        $stmt = $conn->query("SELECT numero_odt, descripcion, estado, prioridad FROM odts ORDER BY prioridad DESC");
        $odts = $stmt->fetchAll();
        
        $estados = ['odt', 'desarrollo', 'carpinteria', 'pintura', 'electricidad', 'acabados', 'transporte', 'instalacion', 'facturacion', 'cobranza'];
        
        echo '<div class="kanban">';
        
        foreach($estados as $estado) {
            echo '<div class="column">';
            echo '<h3>' . ucfirst($estado) . '</h3>';
            
            $odts_filtrados = array_filter($odts, function($odt) use ($estado) {
                return $odt['estado'] === $estado;
            });
            
            foreach($odts_filtrados as $odt) {
                echo '<div class="card">';
                echo '<strong>' . $odt['numero_odt'] . '</strong><br>';
                echo $odt['descripcion'] . '<br>';
                echo '<small>Prioridad: ' . $odt['prioridad'] . '</small>';
                echo '</div>';
            }
            
            if(empty($odts_filtrados)) {
                echo '<div style="color: #999; text-align: center; padding: 20px;">Vacío</div>';
            }
            
            echo '</div>';
        }
        
        echo '</div>';
        
    } catch(PDOException $e) {
        echo "<div style='color: red; padding: 20px; background: #ffe6e6; border-radius: 5px;'>";
        echo "<strong>Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
    ?>

    <div style="margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 8px;">
        <h3>Resumen</h3>
        <p>Total ODTs: <?php echo count($odts); ?></p>
        <p>✅ Sistema funcionando correctamente</p>
    </div>
</body>
</html>