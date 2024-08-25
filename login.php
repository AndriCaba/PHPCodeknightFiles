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

// Retrieve POST data
$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare and execute query
$sql = "SELECT teacher, StudentID, Lastname, Section FROM UserData WHERE Username = ? AND PASSWORD = ?";
$params = array($user, $pass);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Initialize response
$response = array();

if (sqlsrv_has_rows($stmt)) {
    // If login successful, retrieve the data
    $userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $response['status'] = 'Login successful';
    $response['teacher'] = $userData['teacher'];
    $response['student_id'] = $userData['StudentID'];
    $response['lastname'] = $userData['Lastname'];
    $response['section'] = $userData['Section'];
} else {
    $response['status'] = 'Invalid username or password';
    $response['teacher'] = null;
    $response['student_id'] = null;
    $response['lastname'] = null;
    $response['section'] = null;
}

// Close the statement and connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
