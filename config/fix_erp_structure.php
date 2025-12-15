<?php
require_once 'database.php';

echo "<h3>🔧 Corrigiendo Estructura ERP...</h3>";

$database = new Database();
$conn = $database->getConnection();

if(!$conn) {
    die("❌ No se pudo conectar a la base de datos");
}

try {
    // Desactivar claves foráneas temporalmente
    $conn->exec("SET FOREIGN_KEY_CHECKS=0");
    
    // 1. ELIMINAR tablas problemáticas (en orden correcto por dependencias)
    $tables = ['contactos', 'clientes', 'proyectos'];
    foreach($tables as $table) {
        $conn->exec("DROP TABLE IF EXISTS `$table`");
        echo "✅ Tabla $table eliminada<br>";
    }
    
    // 2. CREAR nueva estructura corregida
    $sql = "
    -- -----------------------------------------------------
    -- Tabla CLIENTES (primero, sin dependencias)
    -- -----------------------------------------------------
    CREATE TABLE `clientes` (
      `idclientes` INT AUTO_INCREMENT PRIMARY KEY,
      `rucdni` VARCHAR(11) NOT NULL UNIQUE,
      `nombrerrazonsocial` VARCHAR(45) NOT NULL,
      `direccion` VARCHAR(45) NOT NULL,
      `email` VARCHAR(45) NULL,
      `cuentaprincipaldebancosoles` VARCHAR(45) NULL,
      `comentariosnotas` MEDIUMTEXT NULL,
      `activo` TINYINT(1) DEFAULT 1,
      `fechacreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

    -- -----------------------------------------------------
    -- Tabla CONTACTOS (depende de clientes)
    -- -----------------------------------------------------
    CREATE TABLE `contactos` (
      `idcontactos` INT AUTO_INCREMENT PRIMARY KEY,
      `idcliente` INT NOT NULL,
      `nombre` VARCHAR(45) NOT NULL,
      `telefono` VARCHAR(45) NOT NULL,
      `email` VARCHAR(45) NULL,
      `cargo` VARCHAR(45) NOT NULL,
      `area` VARCHAR(45) NULL,
      `activo` TINYINT(1) DEFAULT 1,
      `fechacreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`idcliente`) REFERENCES `clientes`(`idclientes`) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    -- -----------------------------------------------------
    -- Tabla PROYECTOS (depende de clientes)
    -- -----------------------------------------------------
    CREATE TABLE `proyectos` (
      `idproyectos` INT AUTO_INCREMENT PRIMARY KEY,
      `idcliente` INT NOT NULL,
      `idcontacto` INT NULL,
      `nombreoportunidad` VARCHAR(45) NOT NULL,
      `preciosolessinigv` DECIMAL(10,2) NOT NULL,
      `fechacierre` DATETIME NOT NULL,
      `estado` ENUM('cotizacion', 'aprobado', 'en_progreso', 'completado', 'cancelado') DEFAULT 'cotizacion',
      `fechacreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`idcliente`) REFERENCES `clientes`(`idclientes`) ON DELETE CASCADE,
      FOREIGN KEY (`idcontacto`) REFERENCES `contactos`(`idcontactos`) ON DELETE SET NULL
    ) ENGINE=InnoDB;

    -- -----------------------------------------------------
    -- Tabla ODTs (Sistema Kanban)
    -- -----------------------------------------------------
    CREATE TABLE `odts` (
      `idodt` INT AUTO_INCREMENT PRIMARY KEY,
      `idproyecto` INT NOT NULL,
      `numero_odt` VARCHAR(20) UNIQUE NOT NULL,
      `descripcion` TEXT NOT NULL,
      `estado` ENUM('odt', 'desarrollo', 'carpinteria', 'pintura', 'electricidad', 
                  'acabados', 'transporte', 'instalacion', 'facturacion', 'cobranza') DEFAULT 'odt',
      `prioridad` ENUM('baja', 'media', 'alta') DEFAULT 'media',
      `fechainicio` DATETIME NULL,
      `fechafin` DATETIME NULL,
      `notas` TEXT NULL,
      `fechacreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`idproyecto`) REFERENCES `proyectos`(`idproyectos`) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    
    $conn->exec($sql);
    echo "✅ Estructura corregida creada exitosamente<br>";
    
    // Reactivar claves foráneas
    $conn->exec("SET FOREIGN_KEY_CHECKS=1");
    
    // Insertar datos de ejemplo
    echo "<h4>📝 Insertando datos de ejemplo...</h4>";
    
    // Cliente
    $conn->exec("INSERT INTO clientes (rucdni, nombrerrazonsocial, direccion, email) VALUES ('20123456789', 'Empresa Demo SAC', 'Av. Demo 123', 'info@empresa.com')");
    echo "✅ Cliente demo creado<br>";
    
    // Contacto
    $conn->exec("INSERT INTO contactos (idcliente, nombre, telefono, email, cargo) VALUES (1, 'Ana García', '987654321', 'ana@empresa.com', 'Gerente General')");
    echo "✅ Contacto demo creado<br>";
    
    // Proyecto
    $conn->exec("INSERT INTO proyectos (idcliente, idcontacto, nombreoportunidad, preciosolessinigv, fechacierre) VALUES (1, 1, 'Sistema ERP ODT', 7500.00, '2024-12-31')");
    echo "✅ Proyecto demo creado<br>";
    
    // ODTs de ejemplo
    $conn->exec("INSERT INTO odts (idproyecto, numero_odt, descripcion, estado, prioridad) VALUES 
        (1, '#001', 'Desarrollo módulo clientes', 'desarrollo', 'alta'),
        (1, '#002', 'Diseño interfaz Kanban', 'odt', 'media'),
        (1, '#003', 'Implementación base de datos', 'electricidad', 'alta')");
    echo "✅ ODTs demo creadas<br>";
    
    echo "<h3>🎉 ¡Estructura ERP corregida y lista!</h3>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Mostrar tablas finales
$tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "<h4>📊 Tablas en el sistema:</h4>";
foreach($tables as $table) {
    $count = $conn->query("SELECT COUNT(*) as total FROM `$table`")->fetch();
    echo "• $table ({$count['total']} registros)<br>";
}
?>