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

// Get POST data and validate it
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';
$studentID = $_POST['studentID'] ?? '';
$section = $_POST['section'] ?? '';
$teacher = $_POST['teacher'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$firstName = $_POST['firstName'] ?? '';
$mi = $_POST['mi'] ?? '';

// Validate required fields
if (empty($studentID) || empty($lastName) || empty($teacher)) {
    die("Error: StudentID, LastName, and Teacher are required fields.");
}

// Hash the password (if you are storing passwords)
$hashedPass = password_hash($plainPassword , PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO userdata (Username, PASSWORD, StudentID, Section, Teacher, LastName, FirstName, MI) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisssss", $user, $hashedPass, $studentID, $section, $teacher, $lastName, $firstName, $mi);

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
