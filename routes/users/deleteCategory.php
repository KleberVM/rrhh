<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        $codecategory = $_POST['codecategory'];

        $sql = "DELETE FROM category WHERE codecategory = $codecategory";

        if ($conn->exec($sql)) {
            $_SESSION['message'] = 'Tipo de usuario eliminado correctamente';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al eliminar el tipo de usuario';
            $_SESSION['message_type'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'Error de conexión a la base de datos';
        $_SESSION['message_type'] = 'error';
    }
}

header("Location: ../../index.php?p=usuarios");
exit;
?>

