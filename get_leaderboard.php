<?php
// Database connection parameters
$servername = "codeknight-server.database.windows.net";
$username = "codeknight-server-admin";
$password = "PizzaMan22";
$dbname = "codeknight-database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 1433);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unique sections
$sqlSections = "SELECT DISTINCT Section FROM UserRecord";
$resultSections = $conn->query($sqlSections);

$sections = array();
if ($resultSections->num_rows > 0) {
    while($row = $resultSections->fetch_assoc()) {
        $sections[] = $row['Section'];
    }
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
    $conditions[] = "Section = '" . $conn->real_escape_string($section) . "'";
}

if ($date) {
    $conditions[] = "DateEnterLevel = '" . $conn->real_escape_string($date) . "'";
}

if (count($conditions) > 0) {
    $sql1 .= " WHERE " . implode(' AND ', $conditions);
}

$result1 = $conn->query($sql1);

$userRecords = array();
if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
        $userRecords[] = $row;
    }
}

$response = array(
    "sections" => $sections,
    "dates" => $dates,
    "userRecords" => $userRecords
);

echo json_encode($response);

// Close the connection
$conn->close();
?>
