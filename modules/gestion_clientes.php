<?php
header('Content-Type: text/html; charset=utf-8');
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar formularios
if ($_POST) {
    try {
        if (isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'crear_cliente':
                    $stmt = $conn->prepare("INSERT INTO clientes (rucdni, nombrerrazonsocial, direccion, email, cuentaprincipaldebancosoles, comentariosnotas) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['rucdni'],
                        $_POST['nombrerrazonsocial'],
                        $_POST['direccion'],
                        $_POST['email'],
                        $_POST['cuentaprincipaldebancosoles'],
                        $_POST['comentariosnotas']
                    ]);
                    $mensaje = "✓ Cliente creado exitosamente";
                    $tipo_mensaje = 'success';
                    break;

                case 'editar_cliente':
                    $stmt = $conn->prepare("UPDATE clientes SET rucdni=?, nombrerrazonsocial=?, direccion=?, email=?, cuentaprincipaldebancosoles=?, comentariosnotas=? WHERE idclientes=?");
                    $stmt->execute([
                        $_POST['rucdni'],
                        $_POST['nombrerrazonsocial'],
                        $_POST['direccion'],
                        $_POST['email'],
                        $_POST['cuentaprincipaldebancosoles'],
                        $_POST['comentariosnotas'],
                        $_POST['idclientes']
                    ]);
                    $mensaje = "✓ Cliente actualizado exitosamente";
                    $tipo_mensaje = 'success';
                    break;

                case 'eliminar_cliente':
                    $stmt = $conn->prepare("UPDATE clientes SET activo = 0 WHERE idclientes = ?");
                    $stmt->execute([$_POST['idclientes']]);
                    $mensaje = "✓ Cliente desactivado exitosamente";
                    $tipo_mensaje = 'success';
                    break;

                case 'crear_contacto':
                    $stmt = $conn->prepare("INSERT INTO contactos (idcliente, nombre, telefono, email, cargo, area) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['idcliente'],
                        $_POST['nombre'],
                        $_POST['telefono'],
                        $_POST['email'],
                        $_POST['cargo'],
                        $_POST['area']
                    ]);
                    $mensaje = "✓ Contacto creado exitosamente";
                    $tipo_mensaje = 'success';
                    break;
            }
        }
    } catch (PDOException $e) {
        $mensaje = "✗ Error: " . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}
?>

<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Mensajes -->
    <?php if ($mensaje): ?>
        <div style="background: <?php echo $tipo_mensaje == 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                    color: <?php echo $tipo_mensaje == 'success' ? '#155724' : '#721c24'; ?>; 
                    padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid <?php echo $tipo_mensaje == 'success' ? '#c3e6cb' : '#f5c6cb'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2>👥 Gestión de Clientes y Contactos</h2>
    <p>Administra los clientes y sus contactos para asociarlos a las ODTs</p>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
        
        <!-- Sección de Clientes -->
        <div>
            <h3>🏢 Clientes</h3>
            
            <!-- Formulario para crear/editar cliente -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h4><?php echo isset($_GET['editar_cliente']) ? '✏️ Editar Cliente' : '➕ Nuevo Cliente'; ?></h4>
                <form method="POST">
                    <input type="hidden" name="accion" value="<?php echo isset($_GET['editar_cliente']) ? 'editar_cliente' : 'crear_cliente'; ?>">
                    <?php if (isset($_GET['editar_cliente'])): ?>
                        <input type="hidden" name="idclientes" value="<?php echo htmlspecialchars($_GET['editar_cliente']); ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; gap: 10px;">
                        <div>
                            <label>RUC/DNI:</label>
                            <input type="text" name="rucdni" required 
                                   value="<?php echo isset($cliente_editar) ? htmlspecialchars($cliente_editar['rucdni']) : ''; ?>">
                        </div>
                        <div>
                            <label>Nombre/Razón Social:</label>
                            <input type="text" name="nombrerrazonsocial" required
                                   value="<?php echo isset($cliente_editar) ? htmlspecialchars($cliente_editar['nombrerrazonsocial']) : ''; ?>">
                        </div>
                        <div>
                            <label>Dirección:</label>
                            <input type="text" name="direccion" required
                                   value="<?php echo isset($cliente_editar) ? htmlspecialchars($cliente_editar['direccion']) : ''; ?>">
                        </div>
                        <div>
                            <label>Email:</label>
                            <input type="email" name="email"
                                   value="<?php echo isset($cliente_editar) ? htmlspecialchars($cliente_editar['email']) : ''; ?>">
                        </div>
                        <div>
                            <label>Cuenta Bancaria:</label>
                            <input type="text" name="cuentaprincipaldebancosoles"
                                   value="<?php echo isset($cliente_editar) ? htmlspecialchars($cliente_editar['cuentaprincipaldebancosoles']) : ''; ?>">
                        </div>
                        <div>
                            <label>Comentarios:</label>
                            <textarea name="comentariosnotas" rows="3"><?php echo isset($cliente_editar) ? htmlspecialchars($cliente_editar['comentariosnotas']) : ''; ?></textarea>
                        </div>
                        <div>
                            <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                                <?php echo isset($_GET['editar_cliente']) ? '💾 Actualizar Cliente' : '➕ Crear Cliente'; ?>
                            </button>
                            <?php if (isset($_GET['editar_cliente'])): ?>
                                <a href="?section=clientes" style="color: #6c757d; margin-left: 10px;">❌ Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Lista de Clientes -->
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h4>📋 Lista de Clientes</h4>
                <?php
                $clientes = $conn->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombrerrazonsocial")->fetchAll();
                if ($clientes): 
                ?>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">RUC/DNI</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Nombre</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($cliente['rucdni']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($cliente['nombrerrazonsocial']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">
                                        <a href="?section=clientes&editar_cliente=<?php echo $cliente['idclientes']; ?>" 
                                           style="color: #007bff; text-decoration: none; margin-right: 10px;">✏️</a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="accion" value="eliminar_cliente">
                                            <input type="hidden" name="idclientes" value="<?php echo $cliente['idclientes']; ?>">
                                            <button type="submit" onclick="return confirm('¿Está seguro de desactivar este cliente?')" 
                                                    style="background: none; border: none; color: #dc3545; cursor: pointer;">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: #6c757d; text-align: center; padding: 20px;">No hay clientes registrados</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección de Contactos -->
        <div>
            <h3>👤 Contactos</h3>
            
            <!-- Formulario para crear contacto -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h4>➕ Nuevo Contacto</h4>
                <form method="POST">
                    <input type="hidden" name="accion" value="crear_contacto">
                    
                    <div style="display: grid; gap: 10px;">
                        <div>
                            <label>Cliente:</label>
                            <select name="idcliente" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['idclientes']; ?>">
                                    <?php echo htmlspecialchars($cliente['nombrerrazonsocial']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Nombre:</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div>
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" required>
                        </div>
                        <div>
                            <label>Email:</label>
                            <input type="email" name="email">
                        </div>
                        <div>
                            <label>Cargo:</label>
                            <input type="text" name="cargo" required>
                        </div>
                        <div>
                            <label>Área:</label>
                            <input type="text" name="area">
                        </div>
                        <div>
                            <button type="submit" style="background: #17a2b8; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
                                ➕ Crear Contacto
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Lista de Contactos -->
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h4>📞 Lista de Contactos</h4>
                <?php
                $contactos = $conn->query("
                    SELECT c.*, cl.nombrerrazonsocial as cliente 
                    FROM contactos c 
                    LEFT JOIN clientes cl ON c.idcliente = cl.idclientes 
                    WHERE c.activo = 1 
                    ORDER BY cl.nombrerrazonsocial, c.nombre
                ")->fetchAll();
                if ($contactos): 
                ?>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Cliente</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Contacto</th>
                                    <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Cargo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contactos as $contacto): ?>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($contacto['cliente']); ?></td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">
                                        <?php echo htmlspecialchars($contacto['nombre']); ?><br>
                                        <small style="color: #666;">📞 <?php echo htmlspecialchars($contacto['telefono']); ?></small>
                                    </td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($contacto['cargo']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: #6c757d; text-align: center; padding: 20px;">No hay contactos registrados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>