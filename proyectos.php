<div id="proyectos" style="background-color: #fde8e8; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 900px; margin: 20px auto;">
    <h2>Proyectos</h2>
    <p>Listado de proyectos en desarrollo:</p>
    <ul>
        <li>Proyecto 1 - Sitio Web Corporativo</li>
        <li>Proyecto 2 - Aplicación de Gestión</li>
        <li>Proyecto 3 - Plataforma E-learning</li>
    </ul>
    
    <!-- Puedes añadir más contenido dinámico aquí -->
    <?php
    // Ejemplo de contenido dinámico PHP
    $proyectos = [
        ['nombre' => 'Sitio Web Corporativo', 'estado' => 'En desarrollo'],
        ['nombre' => 'Aplicación de Gestión', 'estado' => 'Planificación'],
        ['nombre' => 'Plataforma E-learning', 'estado' => 'Producción']
    ];
    
    echo '<h3>Estado de proyectos:</h3>';
    echo '<table border="1" cellpadding="5" style="width:100%; border-collapse: collapse;">';
    echo '<tr><th>Proyecto</th><th>Estado</th></tr>';
    foreach($proyectos as $proyecto) {
        echo '<tr>';
        echo '<td>'.$proyecto['nombre'].'</td>';
        echo '<td>'.$proyecto['estado'].'</td>';
        echo '</tr>';
    }
    echo '</table>';
    ?>
</div>