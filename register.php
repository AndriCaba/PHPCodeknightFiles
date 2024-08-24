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

// Get POST data and validate it
$user = $_POST['Username'] ?? '';
$pass = $_POST['PASSWORD'] ?? '';
$studentID = $_POST['StudentID'] ?? '';
$section = $_POST['Section'] ?? '';
$teacher = $_POST['Teacher'] ?? '';
$lastName = $_POST['LastName'] ?? '';
$firstName = $_POST['FirstName'] ?? '';
$mi = $_POST['MI'] ?? '';

// Validate required fields
if (empty($studentID) || empty($lastName) || empty($teacher)) {
    die("Error: StudentID, LastName, and Teacher are required fields.");
}

// Hash the password (if you are storing passwords)
$hashedPass = password_hash($pass, PASSWORD_DEFAULT);

// Prepare the SQL query
$sql = "INSERT INTO UserData (Username, PASSWORD, StudentID, Section, Teacher, LastName, FirstName, MI) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$params = array($user, $hashedPass, $studentID, $section, $teacher, $lastName, $firstName, $mi);

// Prepare and execute the statement
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("Error: " . print_r(sqlsrv_errors(), true));
} else {
    echo "New record created successfully";
}

// Close the statement and connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
