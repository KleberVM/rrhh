<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        $codeuser = $_POST['codeuser'];
        $username = $conn->quote($_POST['username']);
        $userci = $conn->quote($_POST['userci']);
        $userphone = isset($_POST['userphone']) && $_POST['userphone'] !== '' ? $conn->quote($_POST['userphone']) : 'NULL';
        $useraddress = isset($_POST['useraddress']) && $_POST['useraddress'] !== '' ? $conn->quote($_POST['useraddress']) : 'NULL';
        $usertype = $conn->quote($_POST['usertype']);
        $userlogin = $conn->quote($_POST['userlogin']);
        $userpassword = $conn->quote($_POST['userpassword']);
        $userstate = $conn->quote($_POST['userstate']);
        $useraccess = $conn->quote($_POST['useraccess']);

        $sql = "UPDATE user SET 
                username = $username, 
                userci = $userci, 
                userphone = $userphone, 
                useraddress = $useraddress, 
                usertype = $usertype, 
                userlogin = $userlogin, 
                userpassword = $userpassword, 
                userstate = $userstate, 
                useraccess = $useraccess 
                WHERE codeuser = $codeuser";

        if ($conn->exec($sql)) {
            $_SESSION['message'] = 'Usuario actualizado correctamente';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al actualizar el usuario';
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

