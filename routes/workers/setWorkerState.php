<?php
header('Content-Type: application/json');

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$codeworker = isset($_POST['codeworker']) ? (int)$_POST['codeworker'] : 0;
$newState = isset($_POST['new_state']) ? (int)$_POST['new_state'] : 0;

if ($codeworker <= 0) {
    echo json_encode(['error' => 'ID de trabajador inválido']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    if (!$conn) throw new Exception('Error de conexión');

    $stmt = $conn->prepare('UPDATE worker SET workerstate = :state WHERE codeworker = :id');
    $stmt->bindValue(':state', $newState, PDO::PARAM_INT);
    $stmt->bindValue(':id', $codeworker, PDO::PARAM_INT);
    $ok = $stmt->execute();
    if (!$ok) throw new Exception('No se pudo actualizar el estado');

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}