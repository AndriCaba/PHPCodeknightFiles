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

// Check connection
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch unique sections
$sqlSections = "SELECT DISTINCT Section FROM UserRecord";
$resultSections = sqlsrv_query($conn, $sqlSections);

$sections = array();
if ($resultSections) {
    while($row = sqlsrv_fetch_array($resultSections, SQLSRV_FETCH_ASSOC)) {
        $sections[] = (string)$row['Section'];  // Casting as string
    }
}

// Fetch unique dates
$sqlDates = "SELECT DISTINCT DateEnterLevel FROM UserRecord";
$resultDates = sqlsrv_query($conn, $sqlDates);

$dates = array();
if ($resultDates) {
    while($row = sqlsrv_fetch_array($resultDates, SQLSRV_FETCH_ASSOC)) {
        $dates[] = (string)$row['DateEnterLevel'];  // Casting as string
    }
}

// SQL query to get leaderboard data
$section = isset($_GET['section']) ? (string)$_GET['section'] : '';  // Casting as string
$date = isset($_GET['date']) ? (string)$_GET['date'] : '';  // Casting as string

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

$result1 = sqlsrv_query($conn, $sql1, $params);

$userRecords = array();
if ($result1) {
    while($row = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)) {
        $userRecords[] = array(
            "StudentID" => (int)$row['StudentID'],  // Casting as integer
            "LastName" => (string)$row['LastName'],  // Casting as string
            "Teacher" => (string)$row['Teacher'],  // Casting as string
            "TopicLevel" => (string)$row['TopicLevel'],  // Casting as string
            "DateEnterLevel" => (string)$row['DateEnterLevel'],  // Casting as string
            "TimeEnterLevel" => (string)$row['TimeEnterLevel'],  // Casting as string
            "TimeRecord" => (string)$row['TimeRecord'],  // Casting as string
            "Section" => (string)$row['Section']  // Casting as string
        );
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
