<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidor Local - NeoProjects</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #2a3a7c, #3a4a8c);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 900px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(to right, #2c3e50, #4a5f7a);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .date-time {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .date-time h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .options {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .option-card {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #3498db;
            transition: transform 0.3s;
            cursor: pointer;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
        }
        
        .option-card.odt {
            border-top-color: #e74c3c;
        }
        
        .option-card.herramientas {
            border-top-color: #2ecc71;
        }
        
        .option-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3rem;
        }
        
        .server-info {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .server-info h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 12px;
            padding-left: 10px;
            border-left: 3px solid #3498db;
        }
        
        .info-label {
            font-weight: 600;
            width: 200px;
            color: #2c3e50;
        }
        
        .info-value {
            color: #555;
        }
        
        .description {
            margin-top: 25px;
            line-height: 1.6;
            color: #555;
            font-style: italic;
            text-align: center;
        }
        
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
        }
        
        /* Estilos para el contenido dinámico */
        .dynamic-content {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .back-button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .back-button:hover {
            background: #2980b9;
        }
        
        @media (max-width: 768px) {
            .options {
                flex-direction: column;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Bienvenido a mi Servidor Local</h1>
            <p class="subtitle">Entorno de desarrollo para proyectos web</p>
        </header>
        
        <div class="content">
            <div class="date-time">
                <h2>Fecha y Hora Actual</h2>
                <p>Hoy es <strong><?php echo date('d/m/Y'); ?></strong> y la hora actual es <strong><?php echo date('H:i:s'); ?></strong></p>
            </div>
            
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Opciones Disponibles</h2>
            <div class="options">
                <div class="option-card odt" onclick="loadODT()">
                    <h3>📊 Sistema ODT</h3>
                    <p>Gestión de Órdenes de Trabajo - Tablero Kanban</p>
                </div>
                <div class="option-card herramientas" onclick="loadHerramientas()">
                    <h3>⚙️ Herramientas</h3>
                    <p>Utilidades y aplicaciones para desarrollo web</p>
                </div>
            </div>
            
            <!-- Contenido dinámico para ODT -->
            <div id="odt-content" class="dynamic-content">
                <button class="back-button" onclick="showMainMenu()">← Volver al Menú Principal</button>
                <div id="odt-data"></div>
            </div>
            
            <!-- Contenido dinámico para Herramientas -->
            <div id="herramientas-content" class="dynamic-content">
                <button class="back-button" onclick="showMainMenu()">← Volver al Menú Principal</button>
                <div id="herramientas-data"></div>
            </div>
            
            <div class="server-info">
                <h2>Información del Servidor</h2>
                <div class="info-item">
                    <div class="info-label">Software del servidor:</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Versión de PHP:</div>
                    <div class="info-value"><?php echo phpversion(); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Dirección IP:</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SERVER['SERVER_ADDR']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nombre del servidor:</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SERVER['SERVER_NAME']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Sistema operativo:</div>
                    <div class="info-value"><?php echo htmlspecialchars(php_uname('s') . ' ' . php_uname('r')); ?></div>
                </div>
            </div>
            
            <div class="description">
                <p>Este es el entorno de desarrollo local donde trabajo en mis proyectos web.</p>
            </div>
        </div>
        
        <footer>
            <p>Servidor Local NeoProjects &copy; 2025</p>
        </footer>
    </div>

    <script>
        function showMainMenu() {
            // Ocultar todo el contenido dinámico
            document.getElementById('odt-content').style.display = 'none';
            document.getElementById('herramientas-content').style.display = 'none';
            
            // Mostrar opciones principales
            document.querySelector('.options').style.display = 'flex';
            document.querySelector('.server-info').style.display = 'block';
            document.querySelector('.description').style.display = 'block';
        }
        
        function loadODT() {
            // Ocultar menú principal
            document.querySelector('.options').style.display = 'none';
            document.querySelector('.server-info').style.display = 'none';
            document.querySelector('.description').style.display = 'none';
            
            // Mostrar contenido ODT
            document.getElementById('odt-content').style.display = 'block';
            
            // Cargar contenido ODT
            fetch('odt.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar el sistema ODT');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('odt-data').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('odt-data').innerHTML = `
                        <div style="color: red; text-align: center; padding: 20px;">
                            <h3>✗ Error al cargar el sistema ODT</h3>
                            <p>${error.message}</p>
                        </div>
                    `;
                });
        }
        
        function loadHerramientas() {
            // Ocultar menú principal
            document.querySelector('.options').style.display = 'none';
            document.querySelector('.server-info').style.display = 'none';
            document.querySelector('.description').style.display = 'none';
            
            // Mostrar contenido Herramientas
            document.getElementById('herramientas-content').style.display = 'block';
            
            // Cargar contenido Herramientas
            fetch('herramientas.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar las herramientas');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('herramientas-data').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('herramientas-data').innerHTML = `
                        <div style="color: red; text-align: center; padding: 20px;">
                            <h3>✗ Error al cargar las herramientas</h3>
                            <p>${error.message}</p>
                        </div>
                    `;
                });
        }
        
        // Cargar automáticamente el sistema ODT si hay un parámetro en la URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('section') === 'odt') {
                loadODT();
            } else if (urlParams.get('section') === 'herramientas') {
                loadHerramientas();
            }
        });
    </script>
</body>
</html>