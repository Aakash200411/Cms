<?php
// Database connection
$conn = new mysqli('mysql.railway.internal', 'root', 'llBhSnJhgoNktIPCqMYyNgPPAPJRdalt', 'railway');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$faculty_id = $_POST['faculty_id'];
$course_id = $_POST['course_id'];
$session_id = $_POST['session_id'];

// Insert data into course_allotment table
$insertQuery = "INSERT INTO course_allotment (faculty_id, course_id, session_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iii", $faculty_id, $course_id, $session_id);

if ($stmt->execute()) {
    echo "Course allotment successfully added!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
