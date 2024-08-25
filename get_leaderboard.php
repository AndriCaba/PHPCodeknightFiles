<?php
$connectionInfo = array(
    "UID" => "codeknight-server-admin",
    "pwd" => "PizzaMan22", // Ensure the password is securely stored  
    "Database" => "codeknight-database",
    "LoginTimeout" => 30,
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
);
$serverName = "tcp:codeknight-server.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sqlSections = "SELECT DISTINCT Section FROM UserRecord";
$resultSections = sqlsrv_query($conn, $sqlSections);

$sections = array();
if ($resultSections === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    while ($row = sqlsrv_fetch_array($resultSections, SQLSRV_FETCH_ASSOC)) {
        $sections[] = $row['Section'];
    }
}

$sqlDates = "SELECT DISTINCT DateEnterLevel FROM UserRecord";
$resultDates = sqlsrv_query($conn, $sqlDates);

$dates = array();
if ($resultDates === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    while ($row = sqlsrv_fetch_array($resultDates, SQLSRV_FETCH_ASSOC)) {
        $dates[] = $row['DateEnterLevel'];
    }
}

$section = isset($_GET['section']) ? $_GET['section'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql1 = "SELECT StudentID, LastName, Teacher, TopicLevel, DateEnterLevel, TimeEnterLevel, TimeRecord, Section FROM UserRecord";
$conditions = array();

if ($section) {
    $conditions[] = "Section = ?";
}

if ($date) {
    $conditions[] = "DateEnterLevel = ?";
}

if (count($conditions) > 0) {
    $sql1 .= " WHERE " . implode(' AND ', $conditions);
}

$params = array();
if ($section) $params[] = $section;
if ($date) $params[] = $date;

$result1 = sqlsrv_query($conn, $sql1, $params);

$userRecords = array();
if ($result1 === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    while ($row = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
        $userRecords[] = $row;
    }
}

$response = array(
    "sections" => $sections,
    "dates" => $dates,
    "userRecords" => $userRecords
);

echo json_encode($response);

sqlsrv_close($conn);
?>
