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

if ($resultSections === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sections = array();
while ($row = sqlsrv_fetch_array($resultSections, SQLSRV_FETCH_ASSOC)) {
    $sections[] = $row['Section'];
}

// Fetch unique dates
$sqlDates = "SELECT DISTINCT DateEnterLevel FROM UserRecord";
$resultDates = $conn->query($sqlDates);

$dates = array();
if ($resultDates->num_rows > 0) {
    while($row = $resultDates->fetch_assoc()) {
        $dates[] = $row['DateEnterLevel'];
    }
}


// SQL query to get leaderboard data
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
if ($section) {
    $params[] = $section;
}
if ($date) {
    $params[] = $date;
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
    "dates" => $dates,
    "userRecords" => $userRecords
);

echo json_encode($response);

// Close the connection
sqlsrv_close($conn);
?>
