<?php
header('Content-Type: application/json');

require '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de usuario no proporcionado']);
    exit;
}

$codeuser = $_GET['id'];

try {
    $sql = "SELECT user.codeuser, user.username, user.userci, user.userphone, user.useraddress, 
                   user.usertype, user.userlogin, user.userpassword, user.userstate, user.useraccess
            FROM user
            WHERE user.codeuser = :codeuser";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':codeuser', $codeuser, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo json_encode(['success' => true, 'usuario' => $usuario]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

