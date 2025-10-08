<?php
define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaDatabaseConnect();

/*
On impossible only the admin is allowed to retrieve the data.
*/
if (dvwaSecurityLevelGet() == "impossible" && dvwaCurrentUser() != "admin") {
    print json_encode(array("result" => "fail", "error" => "Access denied"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    $result = array(
        "result" => "fail",
        "error"  => "Only POST requests are accepted"
    );
    echo json_encode($result);
    exit;
}

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if (is_null($data)) {
        $result = array(
            "result" => "fail",
            "error"  => 'Invalid format, expecting "{id: {user ID}, first_name: {first name}, surname: {surname}}"'
        );
        echo json_encode($result);
        exit;
    }
} catch (Exception $e) {
    $result = array(
        "result" => "fail",
        "error"  => 'Invalid format, expecting "{id: {user ID}, first_name: {first name}, surname: {surname}}"'
    );
    echo json_encode($result);
    exit;
}

// ✅ USAR CONSULTA PREPARADA PARA EVITAR SQL INJECTION
$conn = $GLOBALS["___mysqli_ston"]; // conexión de DVWA

$stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?");
if ($stmt === false) {
    echo json_encode(array("result" => "fail", "error" => $conn->error));
    exit;
}

$stmt->bind_param("ssi", $data->first_name, $data->surname, $data->id);

if ($stmt->execute()) {
    echo json_encode(array("result" => "ok"));
} else {
    echo json_encode(array("result" => "fail", "error" => $stmt->error));
}

$stmt->close();
exit;
?>
