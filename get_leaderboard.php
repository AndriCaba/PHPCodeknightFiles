<?php

// Securely store database credentials (e.g., environment variables)
$serverName = "tcp:codeknight-server.database.windows.net,1433";
$connectionInfo = array(
  "UID" => "codeknight-server-admin",
  "pwd" => getenv('DATABASE_PASSWORD'), // Access password from environment variable
  "Database" => "codeknight-database",
  "LoginTimeout" => 30,
  "Encrypt" => 1,
  "TrustServerCertificate" => 0
);

// Create connection using sqlsrv
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
  handle_error(sqlsrv_errors()); // Custom function for error handling
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
  handle_error(sqlsrv_errors());
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
  handle_error(sqlsrv_errors());
}

$datesString = !empty($dates) ? implode(", ", array_map('strval', $dates)) : '';

// Prepare SQL query with parameters
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

// Prepare and execute the query with parameters
$stmt = sqlsrv_prepare($conn, $sql1);
if ($stmt === false) {
  handle_error(sqlsrv_errors());
}

if (sqlsrv_execute($stmt, $params) === false) {
  handle_error(sqlsrv_errors($stmt)); // Get errors specific to the statement
}

$userRecords = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $userRecords[] = $row;
}

$response = array(
  "sections" => $sections,
  "dates" => $datesString,
  "userRecords" => $userRecords
);

echo json_encode($response);

// Close the connection
sqlsrv_close($conn);
?>