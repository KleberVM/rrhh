<?php
session_start();
require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?p=trabajadores');
    exit;
}

$codeworker = isset($_POST['codeworker']) ? (int)$_POST['codeworker'] : 0;
if ($codeworker <= 0) {
    $_SESSION['message'] = 'ID de trabajador inválido';
    $_SESSION['message_type'] = 'error';
    header('Location: ../../index.php?p=trabajadores');
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    if (!$conn) throw new Exception('Error de conexión');

    $stmtSel = $conn->prepare('SELECT workerstate FROM worker WHERE codeworker = :id');
    $stmtSel->bindValue(':id', $codeworker, PDO::PARAM_INT);
    $stmtSel->execute();
    $row = $stmtSel->fetch(PDO::FETCH_ASSOC);

    if (!$row) throw new Exception('Trabajador no encontrado');

    if ((int)$row['workerstate'] === 0) {
        $_SESSION['message'] = 'El trabajador ya está inactivo';
        $_SESSION['message_type'] = 'success';
    } else {
        $stmtUpd = $conn->prepare('UPDATE worker SET workerstate = 0 WHERE codeworker = :id');
        $stmtUpd->bindValue(':id', $codeworker, PDO::PARAM_INT);
        if ($stmtUpd->execute()) {
            $_SESSION['message'] = 'Trabajador marcado como inactivo';
            $_SESSION['message_type'] = 'success';
        } else {
            throw new Exception('No se pudo actualizar el estado');
        }
    }
} catch (Exception $e) {
    $_SESSION['message'] = 'Error: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header('Location: ../../index.php?p=trabajadores');
exit;