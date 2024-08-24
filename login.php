<?php
$servername = "codeknight-server.database.windows.net";
$username = "codeknight-server-admin";
$password = "$jUOat$ya7$XOK58";
$dbname = "codeknight-database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare and bind
$stmt = $conn->prepare("SELECT teacher, StudentID, Lastname, Section FROM UserData WHERE Username = ? AND PASSWORD = ?");
$stmt->bind_param("ss", $user, $pass);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Initialize response
$response = array();

if ($result->num_rows > 0) {
    // If login successful, retrieve the teacher's name, student ID, last name, and section
    $userData = $result->fetch_assoc();
    $response['status'] = 'Login successful';
    $response['teacher'] = $userData['teacher'];
    $response['student_id'] = $userData['StudentID'];
    $response['lastname'] = $userData['Lastname'];
    $response['section'] = $userData['Section'];
} else {
    $response['status'] = 'Invalid username or password';
    $response['teacher'] = 'Invalid teacher';
    $response['student_id'] = 'Invalid ID';
    $response['lastname'] = 'Invalid Lastname';
    $response['Section'] = 'Invalid section';
}

// Close the statements and connection
$stmt->close();
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
