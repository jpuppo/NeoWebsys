<?php
header('Content-Type: text/html; charset=utf-8');

// Determinar qué sección mostrar
$seccion = isset($_GET['section']) ? $_GET['section'] : 'principal';
?>

<div style="max-width: 1200px; margin: 0 auto;">
    <h2>⚙️ Herramientas de Desarrollo</h2>
    
    <!-- Navegación entre secciones -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
        <a href="?section=principal" 
           style="padding: 10px 15px; background: <?php echo $seccion == 'principal' ? '#007bff' : '#f8f9fa'; ?>; 
                  color: <?php echo $seccion == 'principal' ? 'white' : '#333'; ?>; 
                  text-decoration: none; border-radius: 5px; border: 1px solid #ddd;">
            🛠️ Herramientas Principales
        </a>
        <a href="?section=clientes" 
           style="padding: 10px 15px; background: <?php echo $seccion == 'clientes' ? '#28a745' : '#f8f9fa'; ?>; 
                  color: <?php echo $seccion == 'clientes' ? 'white' : '#333'; ?>; 
                  text-decoration: none; border-radius: 5px; border: 1px solid #ddd;">
            👥 Gestión de Clientes
        </a>
    </div>

    <?php if ($seccion == 'principal'): ?>
        <!-- Sección de Herramientas Principales -->
        <div style="background-color: #e8f8f0; padding: 30px; border-radius: 8px;">
            <h3>🔧 Recursos Disponibles</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h4>📊 PHPMyAdmin</h4>
                    <p>Administración completa de bases de datos MySQL</p>
                    <a href="/phpmyadmin" target="_blank" style="color: #007bff; text-decoration: none;">🔗 Acceder</a>
                </div>
                
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h4>📁 File Manager</h4>
                    <p>Gestión de archivos del servidor</p>
                    <a href="/filemanager" target="_blank" style="color: #007bff; text-decoration: none;">🔗 Acceder</a>
                </div>
                
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h4>📝 Editor de Código</h4>
                    <p>Desarrollo y edición de archivos</p>
                    <a href="/editor" target="_blank" style="color: #007bff; text-decoration: none;">🔗 Acceder</a>
                </div>
                
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h4>🌐 Servidor Web</h4>
                    <p>Apache - Servidor web local</p>
                    <span style="color: #6c757d;">✅ Ejecutándose</span>
                </div>
                
            </div>
        </div>

    <?php elseif ($seccion == 'clientes'): ?>
        <!-- Sección de Gestión de Clientes -->
        <?php include 'modules/gestion_clientes.php'; ?>
        
    <?php endif; ?>
</div>