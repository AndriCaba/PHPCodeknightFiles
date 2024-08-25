<?php
// SQL Server connection using sqlsrv_connect
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

// Collect data from the request
$studentID = $_POST['StudentID'] ?? '';
$lastName = $_POST['LastName'] ?? '';
$teacher = $_POST['Teacher'] ?? '';
$topicLevel = $_POST['TopicLevel'] ?? '';
$timeRecord = $_POST['TimeRecord'] ?? '';
$section = $_POST['Section'] ?? '';

// Get the current date and time
$dateEnterLevel = date('Y-m-d'); // Current date
$timeEnterLevel = date('H:i:s'); // Current time

// Check if the user exists in the UserData table
$checkSql = "SELECT * FROM UserData WHERE StudentID = ? AND LastName = ? AND Teacher = ?";
$checkParams = array($studentID, $lastName, $teacher);
$checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

if ($checkStmt === false) {
    die("Error: " . print_r(sqlsrv_errors(), true));
}

if (sqlsrv_has_rows($checkStmt)) {
    // User exists, proceed to insert into UserRecord
    $insertSql = "INSERT INTO UserRecord (StudentID, LastName, Teacher, TopicLevel, DateEnterLevel, TimeEnterLevel, TimeRecord, Section) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertParams = array($studentID, $lastName, $teacher, $topicLevel, $dateEnterLevel, $timeEnterLevel, $timeRecord, $section);
    $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

    // Execute the statement
    if ($insertStmt === false) {
        die("Error: " . print_r(sqlsrv_errors(), true));
    } else {
        echo "New record created successfully";
    }

    // Free the statement
    sqlsrv_free_stmt($insertStmt);
} else {
    echo "Error: User not found in UserData table";
}

// Free the statement and close the connection
sqlsrv_free_stmt($checkStmt);
sqlsrv_close($conn);
?>
