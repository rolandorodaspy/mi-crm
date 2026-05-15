<?php
// api.php - Maneja todas las peticiones AJAX
header('Content-Type: application/json');
require_once 'config.php';

$accion = $_GET['accion'] ?? '';

try {
    switch ($accion) {
        case 'obtener_leads':
            $stmt = $pdo->query("SELECT * FROM leads ORDER BY fecha_creacion DESC");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        case 'obtener_clientes':
            $stmt = $pdo->query("SELECT * FROM clientes ORDER BY fecha_conversion DESC");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            break;

        case 'agregar_lead':
            $datos = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO leads (nombre, email, telefono, empresa, estado) VALUES (?, ?, ?, ?, 'nuevo')");
            $stmt->execute([$datos['nombre'], $datos['email'], $datos['telefono'] ?? '', $datos['empresa'] ?? '']);
            echo json_encode(['success' => true, 'mensaje' => 'Lead agregado correctamente']);
            break;

        case 'editar_lead':
            $datos = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE leads SET nombre=?, email=?, telefono=?, empresa=?, estado=? WHERE id=?");
            $stmt->execute([$datos['nombre'], $datos['email'], $datos['telefono'], $datos['empresa'], $datos['estado'], $datos['id']]);
            echo json_encode(['success' => true, 'mensaje' => 'Lead actualizado correctamente']);
            break;

        case 'eliminar_lead':
            $datos = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("DELETE FROM leads WHERE id=?");
            $stmt->execute([$datos['id']]);
            echo json_encode(['success' => true, 'mensaje' => 'Lead eliminado correctamente']);
            break;

        case 'convertir_cliente':
            $datos = json_decode(file_get_contents('php://input'), true);
            $pdo->beginTransaction();
            
            // Obtener datos del lead
            $stmt = $pdo->prepare("SELECT * FROM leads WHERE id=?");
            $stmt->execute([$datos['id']]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Crear cliente
            $stmt = $pdo->prepare("INSERT INTO clientes (lead_id, nombre, email, telefono, empresa, valor_potencial, notas) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$datos['id'], $lead['nombre'], $lead['email'], $lead['telefono'], $lead['empresa'], $datos['valor_potencial'] ?? 0, $datos['notas'] ?? '']);
            
            // Eliminar lead
            $stmt = $pdo->prepare("DELETE FROM leads WHERE id=?");
            $stmt->execute([$datos['id']]);
            
            $pdo->commit();
            echo json_encode(['success' => true, 'mensaje' => 'Convertido a cliente correctamente']);
            break;

        case 'estadisticas':
            $leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
            $clientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
            $nuevos = $pdo->query("SELECT COUNT(*) FROM leads WHERE estado='nuevo'")->fetchColumn();
            echo json_encode(['success' => true, 'leads' => $leads, 'clientes' => $clientes, 'nuevos' => $nuevos]);
            break;

        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
}
?>
