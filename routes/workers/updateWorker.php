<?php
session_start();

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php?p=trabajadores');
    exit;
}

$codeworkerId = isset($_POST['codeworker_id']) ? (int)$_POST['codeworker_id'] : 0;
if ($codeworkerId <= 0 && isset($_POST['codeworker'])) {
    $codeworkerId = (int)$_POST['codeworker'];
}

if ($codeworkerId <= 0) {
    $_SESSION['message'] = 'ID de trabajador inválido';
    $_SESSION['message_type'] = 'error';
    header('Location: ../../index.php?p=trabajadores');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    $_SESSION['message'] = 'Error de conexión con la base de datos';
    $_SESSION['message_type'] = 'error';
    header('Location: ../../index.php?p=trabajadores');
    exit;
}

try {
    $conn->beginTransaction();

    // Manejo opcional de imagen
    $workerimg = null;
    if (isset($_FILES['workerimg']) && $_FILES['workerimg']['error'] === UPLOAD_ERR_OK) {
        $dir = '../../resource/workers/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $mime_type = mime_content_type($_FILES['workerimg']['tmp_name']);
        $permitidos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($permitidos[$mime_type])) {
            throw new Exception('Formato de imagen no permitido. Use JPG, JPEG, PNG o WEBP.');
        }

        $workercodeTmp = $_POST['workercode'];
        $nombre_archivo = $workercodeTmp . '.' . $permitidos[$mime_type];
        $ruta_archivo = $dir . $nombre_archivo;

        if (!move_uploaded_file($_FILES['workerimg']['tmp_name'], $ruta_archivo)) {
            throw new Exception('Error al guardar la imagen.');
        }

        if ($mime_type !== 'image/webp') {
            $ruta_webp = $dir . $workercodeTmp . '.webp';
            if ($mime_type === 'image/jpeg') {
                $imageResource = imagecreatefromjpeg($ruta_archivo);
            } elseif ($mime_type === 'image/png') {
                $imageResource = imagecreatefrompng($ruta_archivo);
                if (imageistruecolor($imageResource) === false) {
                    $truecolorImage = imagecreatetruecolor(imagesx($imageResource), imagesy($imageResource));
                    imagecopy($truecolorImage, $imageResource, 0, 0, 0, 0, imagesx($imageResource), imagesy($imageResource));
                    imagedestroy($imageResource);
                    $imageResource = $truecolorImage;
                }
            }
            if ($imageResource && imagewebp($imageResource, $ruta_webp, 80)) {
                imagedestroy($imageResource);
                unlink($ruta_archivo);
                $ruta_archivo = $ruta_webp;
            } else {
                throw new Exception('Error al convertir la imagen a WebP.');
            }
        }

        $extensionFinal = pathinfo($ruta_archivo, PATHINFO_EXTENSION);
        $workerimg = 'resource/workers/' . $workercodeTmp . '.' . $extensionFinal;
    }

    // Preparar valores
    $workercode = $conn->quote($_POST['workercode']);
    $workername1 = $conn->quote($_POST['workername1']);
    $workername2 = $conn->quote($_POST['workername2']);
    $workerlastname1 = $conn->quote($_POST['workerlastname1']);
    $workerlastname2 = $conn->quote($_POST['workerlastname2']);
    $workerhousbandname = $conn->quote($_POST['workerhousbandname']);
    $workerbirthdate = $conn->quote($_POST['workerbirthdate']);
    $workertypedoc = $conn->quote($_POST['workertypedoc']);
    $workerdoccity = $conn->quote($_POST['workerdoccity']);
    $workerdocnumber = $conn->quote($_POST['workerdocnumber']);
    $workersecurenum = $conn->quote($_POST['workersecurenum']);
    $workercuanum = $conn->quote($_POST['workercuanum']);
    $workercity = $conn->quote($_POST['workercity']);
    $workeremail = $conn->quote($_POST['workeremail']);
    $workerphone1 = $conn->quote($_POST['workerphone1']);
    $workerphone2 = $conn->quote($_POST['workerphone2']);
    $img = $workerimg ? $conn->quote($workerimg) : 'workerimg';
    $codearea = $conn->quote($_POST['codearea']);
    $codeoccupation = $conn->quote($_POST['codeoccupation']);
    $codesection = $conn->quote($_POST['codesection']);
    $workeraddress = $conn->quote($_POST['workeraddress']);
    $workernationality = $conn->quote($_POST['workernationality']);
    $workersex = $conn->quote($_POST['workersex']);
    $workernit = $conn->quote($_POST['workernit']);
    $workercivilstatus = $conn->quote($_POST['workercivilstatus']);
    $accountMain = $conn->quote($_POST['accountMain']);
    $workerdateinit = $conn->quote($_POST['workerdateinit']);
    $workerdateout = $conn->quote($_POST['workerdateout']);

    // Actualizar datos del trabajador
    $sqlUpdate = "UPDATE worker SET 
        workercode = $workercode,
        workername1 = $workername1,
        workername2 = $workername2,
        workerlastname1 = $workerlastname1,
        workerlastname2 = $workerlastname2,
        workerhousbandname = $workerhousbandname,
        workerbirthdate = $workerbirthdate,
        workertypedoc = $workertypedoc,
        workerdoccity = $workerdoccity,
        workerdocnumber = $workerdocnumber,
        workersecurenum = $workersecurenum,
        workercuanum = $workercuanum,
        workercity = $workercity,
        workeremail = $workeremail,
        workerphone1 = $workerphone1,
        workerphone2 = $workerphone2,
        workerimg = " . ($workerimg ? $conn->quote($workerimg) : "workerimg") . ",
        workerarea = $codearea,
        workerrol = $codeoccupation,
        workersection = $codesection,
        workeraddress = $workeraddress,
        workernationality = $workernationality,
        workersex = $workersex,
        workernit = $workernit,
        workercivilstatus = $workercivilstatus,
        workerbanknum = $accountMain,
        workerdateinit = $workerdateinit,
        workerdateout = $workerdateout
        WHERE codeworker = $codeworkerId";

    if ($conn->exec($sqlUpdate) === false) {
        throw new Exception('Error al actualizar el trabajador');
    }

    $conn->commit();
    $_SESSION['message'] = 'Trabajador actualizado correctamente';
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['message'] = 'Error al actualizar el trabajador: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header('Location: ../../index.php?p=trabajadores');
exit;