<?php
// Start session to manage login state or for additional use like tracking current user
session_start();

// Check if all form data is available
if (isset($_POST['roll_no']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['session_id']) && isset($_POST['course_ids'])) {

    // Get form data
    $roll_no = $_POST['roll_no'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $session_id = $_POST['session_id'];
    $course_ids = $_POST['course_ids']; // This is an array of selected course IDs
    $password = isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : 'secret'; // Default to 'secret' if not provided
    $parent_email = isset($_POST['parent_email']) ? $_POST['parent_email'] : NULL; // Optional, can be NULL if not provided

    // Ensure course_ids is an array
    if (!is_array($course_ids)) {
        echo "Error: No courses selected.";
        exit;
    }

    // Create a connection to the attendance_db database
    $conn_attendance = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
    if ($conn_attendance->connect_error) {
        die("Attendance DB connection failed: " . $conn_attendance->connect_error);
    }

    // Insert student details into the student_details table
    $stmt = $conn_attendance->prepare("INSERT INTO student_details (roll_no, name, email_id, password, parent_email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $roll_no, $name, $email, $password, $parent_email);

    if ($stmt->execute()) {
        // Get the student ID after insertion
        $student_id = $conn_attendance->insert_id;

        // Now insert selected courses into the course_registration table
        foreach ($course_ids as $course_id) {
            $stmt = $conn_attendance->prepare("INSERT INTO course_registration (student_id, course_id, session_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $student_id, $course_id, $session_id);

            if (!$stmt->execute()) {
                echo "Error registering course ID $course_id: " . $stmt->error;
                exit;
            }
        }

        // Now connect to the CMS database
        $conn_cms = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
        if ($conn_cms->connect_error) {
            die("CMS DB connection failed: " . $conn_cms->connect_error);
        }

        // Insert username, email, and password into the users table of CMS
        $stmt_cms = $conn_cms->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt_cms->bind_param("sss", $name, $email, $password); // Use 'name' as the username

        if (!$stmt_cms->execute()) {
            echo "Error inserting into users table: " . $stmt_cms->error;
            exit;
        }

        // Close the CMS connection
        $stmt_cms->close();
        $conn_cms->close();

        // Close the statement and the attendance DB connection
        $stmt->close();
        $conn_attendance->close();

        // Redirect to success page (after 3 seconds)
        echo "Registration successful! Redirecting...";
        header("Refresh: 3; url=attendance.php");  // Change 'attendance.php' to your success page URL
        exit;
    } else {
        echo "Error inserting student details: " . $stmt->error;
    }
} else {
    echo "Error: Some required fields are missing!";
}

// Close the database connection if it's still open
if (isset($conn_attendance) && $conn_attendance->ping()) {
    $conn_attendance->close();
}
?>
