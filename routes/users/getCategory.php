<?php
header('Content-Type: application/json');

require '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de categoría no proporcionado']);
    exit;
}

$codecategory = $_GET['id'];

try {
    $sql = "SELECT codecategory, namecategory FROM category WHERE codecategory = :codecategory";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':codecategory', $codecategory, PDO::PARAM_INT);
    $stmt->execute();
    
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($categoria) {
        echo json_encode(['success' => true, 'categoria' => $categoria]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Categoría no encontrada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

