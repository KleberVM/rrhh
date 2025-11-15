<?php
session_start();
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        $codeuser = isset($_POST['codeuser']) ? (int)$_POST['codeuser'] : 0;
        if ($codeuser <= 0) {
            $_SESSION['message'] = 'ID de usuario inválido';
            $_SESSION['message_type'] = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE user SET userstate = 'Inactivo' WHERE codeuser = :id");
            $stmt->bindValue(':id', $codeuser, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Usuario marcado como inactivo';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Error al actualizar el estado del usuario';
                $_SESSION['message_type'] = 'error';
            }
        }
    } else {
        $_SESSION['message'] = 'Error de conexión a la base de datos';
        $_SESSION['message_type'] = 'error';
    }
}

header("Location: ../../index.php?p=usuarios");
exit;


