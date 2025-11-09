<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        $namecategory = $conn->quote($_POST['namecategory']);

        $sql = "INSERT INTO category (namecategory) VALUES ($namecategory)";

        if ($conn->exec($sql)) {
            $_SESSION['message'] = 'Tipo de usuario agregado correctamente';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al agregar el tipo de usuario';
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

