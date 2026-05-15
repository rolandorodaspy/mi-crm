<?php
// install.php - Ejecutar este archivo una vez para crear las tablas
require_once 'config.php';

$sql = "
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    empresa VARCHAR(100),
    estado ENUM('nuevo', 'contactado', 'calificado', 'perdido') DEFAULT 'nuevo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    empresa VARCHAR(100),
    valor_potencial DECIMAL(10,2),
    notas TEXT,
    fecha_conversion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL
);

INSERT INTO leads (nombre, email, telefono, empresa, estado) 
VALUES ('Juan Pérez', 'juan@ejemplo.com', '+1234567890', 'Empresa Demo', 'nuevo')
ON DUPLICATE KEY UPDATE nombre=nombre;
";

try {
    $pdo->exec($sql);
    echo "✅ Tablas creadas exitosamente. Ya puedes usar el CRM.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
