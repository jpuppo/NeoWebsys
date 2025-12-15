<?php
// Configuración de caracteres
header('Content-Type: text/html; charset=utf-8');

// Activar errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Variable para controlar errores de BD
$db_error = null;
$odts = array();
$proyectos = array();
$clientes = array();

if($conn) {
    try {
        // Obtener ODTs de la base de datos
        $stmt = $conn->query("
            SELECT o.*, p.nombreoportunidad as proyecto, c.nombrerrazonsocial as cliente, 
                   con.nombre as contacto_nombre, con.cargo as contacto_cargo
            FROM odts o 
            LEFT JOIN proyectos p ON o.idproyecto = p.idproyectos 
            LEFT JOIN clientes c ON p.idcliente = c.idclientes 
            LEFT JOIN contactos con ON p.idcontacto = con.idcontactos 
            ORDER BY o.prioridad DESC, o.numero_odt
        ");
        $odts = $stmt->fetchAll();
        
        // Obtener proyectos para el formulario
        $stmt_proyectos = $conn->query("SELECT idproyectos, nombreoportunidad FROM proyectos");
        $proyectos = $stmt_proyectos->fetchAll();
        
        // Obtener clientes para el formulario
        $stmt_clientes = $conn->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombrerrazonsocial");
        $clientes = $stmt_clientes->fetchAll();
        
    } catch(PDOException $e) {
        $db_error = "Error cargando datos: " . $e->getMessage();
    }
} else {
    $db_error = "No se pudo conectar a la base de datos";
}

// Generar próximo número de ODT automáticamente
$proximo_numero = "#001";
if($conn && !$db_error) {
    try {
        $stmt = $conn->query("SELECT numero_odt FROM odts ORDER BY idodt DESC LIMIT 1");
        $ultima_odt = $stmt->fetch();
        
        if($ultima_odt) {
            // Extraer el número y incrementar
            $ultimo_numero = intval(str_replace('#', '', $ultima_odt['numero_odt']));
            $proximo_numero = '#' . str_pad($ultimo_numero + 1, 3, '0', STR_PAD_LEFT);
        }
    } catch(PDOException $e) {
        // Si hay error, usar #001 por defecto
        $proximo_numero = "#001";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema ODT - Kanban</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #f5f5f5; 
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .kanban-board {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 20px 0;
            min-height: 600px;
        }
        .kanban-column {
            min-width: 280px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        .column-header {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            text-align: center;
        }
        .odt-card {
            background: white;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .odt-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .priority-alta { border-left-color: #dc3545; }
        .priority-media { border-left-color: #ffc107; }
        .priority-baja { border-left-color: #28a745; }
        .card-desc {
            font-size: 0.9em;
            color: #666;
            margin: 5px 0;
        }
        .card-meta {
            font-size: 0.8em;
            margin-top: 5px;
            color: #6c757d;
        }
        .badge {
            background: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7em;
            display: inline-block;
            margin-right: 5px;
        }
        .badge-alta { background: #dc3545; }
        .badge-media { background: #ffc107; color: #000; }
        .badge-baja { background: #28a745; }
        .empty-state {
            text-align: center;
            color: #999;
            padding: 30px;
            font-style: italic;
        }
        .summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .new-odt-form {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-full {
            grid-column: span 2;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        input:read-only {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        .client-info {
            font-size: 0.8em;
            color: #007bff;
            margin-top: 3px;
        }
        .contact-info {
            font-size: 0.75em;
            color: #6c757d;
            margin-top: 2px;
        }
        label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            display: block;
        }
        small {
            color: #6c757d;
            font-size: 0.8em;
        }
        .required::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Sistema ODT - Tablero Kanban</h1>
            <p>Gestión visual de Órdenes de Trabajo</p>
        </div>

        <?php if($db_error): ?>
            <div class="error-message">
                <strong>✗ Error de Base de Datos:</strong> <?php echo $db_error; ?>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="success-message">
                ✓ <strong>¡ODT creada exitosamente!</strong> Número: <?php echo isset($_GET['odt_numero']) ? htmlspecialchars($_GET['odt_numero']) : ''; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                ✗ <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(!$db_error): ?>
        <div class="kanban-board">
            <?php
            $estados = array(
                'odt' => array('📝 ODT', '#e3f2fd', 'Trabajos Pendientes'),
                'desarrollo' => array('💻 Desarrollo', '#fff3e0', 'Diseño y Planificación'),
                'carpinteria' => array('🔨 Carpintería', '#f3e5f5', 'Trabajos en Madera'),
                'pintura' => array('🎨 Pintura', '#e8f5e8', 'Acabados Superficiales'),
                'electricidad' => array('⚡ Electricidad', '#fff8e1', 'Sistemas Eléctricos'),
                'acabados' => array('✨ Acabados', '#fce4ec', 'Detalles Finales'),
                'transporte' => array('🚚 Transporte', '#e0f2f1', 'Movilización'),
                'instalacion' => array('🔧 Instalación', '#f9fbe7', 'Montaje en Sitio'),
                'facturacion' => array('🧾 Facturación', '#fffde7', 'Emisión de Facturas'),
                'cobranza' => array('💰 Cobranza', '#e8f5e9', 'Pendiente de Pago')
            );
            
            foreach($estados as $key => $estado) {
                echo '<div class="kanban-column" style="background-color: '.$estado[1].';">';
                echo '<h3 class="column-header">'.$estado[0].'</h3>';
                echo '<small style="color: #666; display: block; text-align: center; margin-bottom: 10px;">'.$estado[2].'</small>';
                
                // Filtrar ODTs por estado
                $odts_estado = array();
                foreach($odts as $odt) {
                    if ($odt['estado'] === $key) {
                        $odts_estado[] = $odt;
                    }
                }
                
                if(count($odts_estado) > 0) {
                    foreach($odts_estado as $odt) {
                        $priority_class = 'priority-' . $odt['prioridad'];
                        $badge_class = 'badge badge-' . $odt['prioridad'];
                        echo '<div class="odt-card ' . $priority_class . '">';
                        echo '<strong>'.$odt['numero_odt'].'</strong>';
                        echo '<div class="card-desc">'.$odt['descripcion'].'</div>';
                        echo '<div class="card-meta">';
                        echo '<span class="' . $badge_class . '">'.$odt['prioridad'].'</span>';
                        if($odt['cliente']) {
                            echo '<div class="client-info">👥 ' . $odt['cliente'] . '</div>';
                        }
                        if($odt['contacto_nombre']) {
                            echo '<div class="contact-info">👤 ' . $odt['contacto_nombre'] . ' - ' . $odt['contacto_cargo'] . '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty-state">No hay ODTs en este estado</div>';
                }
                
                echo '</div>';
            }
            ?>
        </div>

        <div class="summary">
            <h3>📈 Resumen del Sistema</h3>
            <?php
                $total_odts = count($odts);
                $estados_count = array();
                foreach($odts as $odt) {
                    $estado = $odt['estado'];
                    if (isset($estados_count[$estado])) {
                        $estados_count[$estado] += 1;
                    } else {
                        $estados_count[$estado] = 1;
                    }
                }
                
                echo "<p><strong>Total ODTs:</strong> $total_odts</p>";
                echo "<p><strong>Distribución por estado:</strong></p>";
                echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
                foreach($estados_count as $estado => $count) {
                    $nombre_estado = isset($estados[$estado]) ? $estados[$estado][0] : $estado;
                    echo "<span style='background: #007bff; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.9em;'>$nombre_estado: $count</span>";
                }
                echo "</div>";
            ?>
        </div>

        <!-- Formulario para nueva ODT -->
        <div class="new-odt-form">
            <h3>➕ Nueva ODT</h3>
            <form method="POST" action="modules/agregar_odt.php">
                <div class="form-grid">
                    <div>
                        <label class="required">Número ODT:</label>
                        <input type="text" name="numero_odt" value="<?php echo $proximo_numero; ?>" readonly>
                        <small>Generado automáticamente</small>
                    </div>
                    <div>
                        <label class="required">Cliente:</label>
                        <select name="idcliente" required onchange="cargarContactos(this.value)">
                            <option value="">Seleccionar cliente...</option>
                            <?php
                            foreach($clientes as $cliente) {
                                echo "<option value='{$cliente['idclientes']}'>{$cliente['nombrerrazonsocial']}</option>";
                            }
                            ?>
                        </select>
                        <small>Cliente para el proyecto</small>
                    </div>
                    <div>
                        <label class="required">Contacto:</label>
                        <select name="idcontacto" id="select-contacto" required>
                            <option value="">Primero seleccione un cliente</option>
                        </select>
                        <small>Contacto del cliente</small>
                    </div>
                    <div>
                        <label class="required">Proyecto:</label>
                        <select name="idproyecto" required>
                            <option value="">Seleccionar proyecto...</option>
                            <?php
                            foreach($proyectos as $proyecto) {
                                echo "<option value='{$proyecto['idproyectos']}'>{$proyecto['nombreoportunidad']}</option>";
                            }
                            ?>
                        </select>
                        <small>Proyecto asociado</small>
                    </div>
                    <div class="form-full">
                        <label class="required">Descripción del Trabajo:</label>
                        <textarea name="descripcion" placeholder="Describa detalladamente el trabajo a realizar..." required rows="3"></textarea>
                        <small>Descripción completa de la ODT</small>
                    </div>
                    <div>
                        <label class="required">Prioridad:</label>
                        <select name="prioridad" required>
                            <option value="baja">🟢 Baja</option>
                            <option value="media" selected>🟡 Media</option>
                            <option value="alta">🔴 Alta</option>
                        </select>
                        <small>Nivel de prioridad</small>
                    </div>
                    <div>
                        <label class="required">Estado inicial:</label>
                        <select name="estado" required>
                            <option value="odt">📝 ODT</option>
                            <option value="desarrollo">💻 Desarrollo</option>
                            <option value="carpinteria">🔨 Carpintería</option>
                            <option value="pintura">🎨 Pintura</option>
                            <option value="electricidad">⚡ Electricidad</option>
                            <option value="acabados">✨ Acabados</option>
                        </select>
                        <small>Estado inicial de la ODT</small>
                    </div>
                    <div class="form-full" style="text-align: center; padding-top: 15px;">
                        <button type="submit" style="background: #28a745; padding: 12px 30px; font-size: 1.1em;">
                            ➕ Crear ODT
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function cargarContactos(idCliente) {
            if (!idCliente) {
                document.getElementById('select-contacto').innerHTML = '<option value="">Primero seleccione un cliente</option>';
                return;
            }
            
            fetch('modules/cargar_contactos.php?idcliente=' + idCliente)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar contactos');
                    }
                    return response.json();
                })
                .then(contactos => {
                    const select = document.getElementById('select-contacto');
                    select.innerHTML = '<option value="">Seleccionar contacto...</option>';
                    
                    if (contactos.length === 0) {
                        select.innerHTML = '<option value="">No hay contactos para este cliente</option>';
                        return;
                    }
                    
                    contactos.forEach(contacto => {
                        const option = document.createElement('option');
                        option.value = contacto.idcontactos;
                        option.textContent = contacto.nombre + ' - ' + contacto.cargo;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('select-contacto').innerHTML = '<option value="">Error al cargar contactos</option>';
                });
        }

        // Validación del formulario antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const cliente = document.querySelector('select[name="idcliente"]').value;
            const contacto = document.querySelector('select[name="idcontacto"]').value;
            const proyecto = document.querySelector('select[name="idproyecto"]').value;
            const descripcion = document.querySelector('textarea[name="descripcion"]').value;
            
            if (!cliente || !contacto || !proyecto || !descripcion.trim()) {
                e.preventDefault();
                alert('Por favor complete todos los campos obligatorios (*)');
                return false;
            }
        });

        // Mostrar tooltip en las tarjetas ODT
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.odt-card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    // Aquí puedes agregar funcionalidad para editar la ODT
                    console.log('ODT clickeada:', this.querySelector('strong').textContent);
                });
            });
        });

        // Auto-focus en el primer campo del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const firstSelect = document.querySelector('select[name="idcliente"]');
            if (firstSelect) {
                firstSelect.focus();
            }
        });
    </script>
</body>
</html>