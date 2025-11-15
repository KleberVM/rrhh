<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../../config/database.php';
    $database = new Database();
    $conn = $database->getConnection();

    if (!$conn) {
        echo json_encode(['error' => 'Conexión inválida']);
        exit;
    }

    $id = 0;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    } else {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    }

    if (!$id) {
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }

    // Datos del trabajador con nombres de catálogo
    $stmt = $conn->prepare('SELECT w.*, 
                                   a.areaname AS namearea,
                                   o.nameoccupation AS nameoccupation,
                                   s.namesection AS namesection
                            FROM worker w
                            LEFT JOIN area a ON a.codearea = w.workerarea
                            LEFT JOIN occupation o ON o.codeoccupation = w.workerrol
                            LEFT JOIN section s ON s.codesection = w.workersection
                            WHERE w.codeworker = :id LIMIT 1');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$worker) {
        echo json_encode(['error' => 'Trabajador no encontrado']);
        exit;
    }

    // Cuentas bancarias secundarias
    $stmt = $conn->prepare('SELECT id, accountbank, accountnro FROM account WHERE codeworker = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Familiares
    $stmt = $conn->prepare('SELECT id, familyname, familylastname, familysex, familyage, familykin FROM family WHERE codeworker = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $family = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Turnos asignados (turnw)
    $stmt = $conn->prepare('SELECT t.id, t.codeturn, tu.turnname AS turn_name, t.turn_start, t.turn_end 
                            FROM turnw t 
                            LEFT JOIN turn tu ON t.codeturn = tu.codeturn 
                            WHERE t.codeworker = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $turnw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Documentos
    $stmt = $conn->prepare('SELECT id, gradoFormacion, titulo, urlCertificado, descripcionCurso, fechaCursada FROM documentWorker WHERE codeWorker = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [
        'codeworker' => $worker['codeworker'],
        'workercode' => $worker['workercode'],
        'workername1' => $worker['workername1'],
        'workername2' => $worker['workername2'],
        'workerlastname1' => $worker['workerlastname1'],
        'workerlastname2' => $worker['workerlastname2'],
        'workerhousbandname' => $worker['workerhousbandname'],
        'workerbirthdate' => $worker['workerbirthdate'],
        'workertypedoc' => $worker['workertypedoc'],
        'workerdoccity' => $worker['workerdoccity'],
        'workerdocnumber' => $worker['workerdocnumber'],
        'workersecurenum' => $worker['workersecurenum'],
        'workercuanum' => $worker['workercuanum'],
        'workercity' => $worker['workercity'],
        'workeremail' => $worker['workeremail'],
        'workerphone1' => $worker['workerphone1'],
        'workerphone2' => $worker['workerphone2'],
        'codearea' => $worker['workerarea'],
        'namearea' => isset($worker['namearea']) ? $worker['namearea'] : null,
        'codeoccupation' => $worker['workerrol'],
        'nameoccupation' => isset($worker['nameoccupation']) ? $worker['nameoccupation'] : null,
        'codesection' => $worker['workersection'],
        'namesection' => isset($worker['namesection']) ? $worker['namesection'] : null,
        'workeraddress' => $worker['workeraddress'],
        'workernationality' => $worker['workernationality'],
        'workersex' => $worker['workersex'],
        'workernit' => $worker['workernit'],
        'workercivilstatus' => $worker['workercivilstatus'],
        'accountMain' => $worker['workerbanknum'],
        'workerdateinit' => $worker['workerdateinit'],
        'workerdateout' => $worker['workerdateout'],
        'workerimg' => $worker['workerimg'],
        'accounts' => $accounts,
        'family' => $family,
        'turnw' => $turnw,
        'documents' => $documents
    ];

    echo json_encode($result);
    exit;
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
