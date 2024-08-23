<?php
// Database configuration
$servername = "codeknight.database.windows.net";
$username = "cabagbag.224136@globalcity.sti.edu.ph";
$password = "QHPjuOg9NtrinejL";
$dbname = "CodeknightData";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect data from the request
$studentID = $_POST['StudentID'];
$lastName = $_POST['LastName'];
$teacher = $_POST['Teacher'];
$topicLevel = $_POST['TopicLevel'];
$timeRecord = $_POST['TimeRecord'];
$section = $_POST['Section']; // Add this line

// Get the current date and time
$dateEnterLevel = date('Y-m-d'); // Current date
$timeEnterLevel = date('H:i:s'); // Current time

// Check if the user exists in the UserData table
$checkStmt = $conn->prepare("SELECT * FROM UserData WHERE StudentID = ? AND LastName = ? AND Teacher = ?");
$checkStmt->bind_param("iss", $studentID, $lastName, $teacher);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // User exists, proceed to insert into UserRecord
    $stmt = $conn->prepare("INSERT INTO UserRecord (StudentID, LastName, Teacher, TopicLevel, DateEnterLevel, TimeEnterLevel, TimeRecord, Section) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $studentID, $lastName, $teacher, $topicLevel, $dateEnterLevel, $timeEnterLevel, $timeRecord, $section);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Error: User not found in UserData table";
}

// Close the connection
$checkStmt->close();
$conn->close();
?>
