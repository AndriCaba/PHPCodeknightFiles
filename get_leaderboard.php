<?php
// SQL Server connection parameters
$connectionInfo = array(
    "UID" => "codeknight-server-admin",
    "pwd" => "PizzaMan22", // Ensure the password is securely stored  
    "Database" => "codeknight-database",
    "LoginTimeout" => 30,
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
);
$serverName = "tcp:codeknight-server.database.windows.net,1433";

// Create connection using sqlsrv
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch unique sections
$sqlSections = "SELECT DISTINCT Section FROM UserRecord";
$resultSections = sqlsrv_query($conn, $sqlSections);

$sections = array();
if ($resultSections !== false) {
    while ($row = sqlsrv_fetch_array($resultSections, SQLSRV_FETCH_ASSOC)) {
        $sections[] = $row['Section'];
    }
} else {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch unique dates
$sqlDates = "SELECT DISTINCT DateEnterLevel FROM UserRecord";
$resultDates = sqlsrv_query($conn, $sqlDates);

$dates = array();
if ($resultDates !== false) {
    while ($row = sqlsrv_fetch_array($resultDates, SQLSRV_FETCH_ASSOC)) {
        $dates[] = $row['DateEnterLevel'];
    }
} else {
    die(print_r(sqlsrv_errors(), true));
}

// Convert dates array to a comma-separated string
$datesString = implode(", ", $dates);

// SQL query to get leaderboard data
$section = isset($_GET['section']) ? $_GET['section'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql1 = "SELECT StudentID, LastName, Teacher, TopicLevel, DateEnterLevel, TimeEnterLevel, TimeRecord, Section FROM UserRecord";
$conditions = array();
$params = array();

if ($section) {
    $conditions[] = "Section = ?";
    $params[] = $section;
}

if ($date) {
    $conditions[] = "DateEnterLevel = ?";
    $params[] = $date;
}

if (count($conditions) > 0) {
    $sql1 .= " WHERE " . implode(' AND ', $conditions);
}

$stmt = sqlsrv_query($conn, $sql1, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$userRecords = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $userRecords[] = $row;
}

$response = array(
    "sections" => $sections,
    "dates" => $datesString, // Use the comma-separated dates string
    "userRecords" => $userRecords
);

echo json_encode($response);

// Close the connection
sqlsrv_close($conn);
?>
