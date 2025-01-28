<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "attendance_db");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Check if file is uploaded
  if (isset($_FILES['file']['tmp_name'])) {
    $file = $_FILES['file']['tmp_name'];
    $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    // Check if the file is a CSV file
    if ($fileType !== 'csv') {
      echo "Please upload a CSV file.";
      exit;
    }

    // Open the CSV file
    if (($handle = fopen($file, "r")) !== false) {
      // Skip the header row
      $header = fgetcsv($handle);

      // Read and process the rows
      while (($row = fgetcsv($handle)) !== false) {
        $roll_no = $row[0];
        $name = $row[1];
        $email_id = $row[2];
        $password = password_hash($row[3], PASSWORD_BCRYPT); // Hash the password
        $parent_email = isset($row[4]) ? $row[4] : null;
        $role = isset($row[5]) ? $row[5] : 'user'; // Default to 'user' if not provided

        // Extract session, teacher name, and course name
        $session_year = $row[6];
        $term = $row[7];
        $teacher_name = $row[8];
        $courses = array_slice($row, 9, 5); // Assuming the next 5 columns are for course names (subjects)

        // Insert student details into the database and get the student ID
        $query = "INSERT INTO student_details (roll_no, name, email_id, password, parent_email, role) 
                          VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $roll_no, $name, $email_id, $password, $parent_email, $role);

        if (!$stmt->execute()) {
          echo "Error inserting data for Roll No: $roll_no - " . $stmt->error . "<br>";
          continue;
        }

        // Get the student ID from the last inserted row
        $student_id = $conn->insert_id;

        // 1. Check if the session exists
        $session_query = "SELECT id FROM session_details WHERE year = ? AND term = ?";
        $session_stmt = $conn->prepare($session_query);
        $session_stmt->bind_param("is", $session_year, $term);
        $session_stmt->execute();
        $session_result = $session_stmt->get_result();

        if ($session_result->num_rows == 0) {
          echo "Session not found for year $session_year, term $term. Skipping student $roll_no.<br>";
          continue;
        }

        $session_id = $session_result->fetch_assoc()['id'];

        // 2. Check if the teacher exists
        $teacher_query = "SELECT id FROM faculty_details WHERE name = ?";
        $teacher_stmt = $conn->prepare($teacher_query);
        $teacher_stmt->bind_param("s", $teacher_name);
        $teacher_stmt->execute();
        $teacher_result = $teacher_stmt->get_result();

        if ($teacher_result->num_rows == 0) {
          echo "Teacher $teacher_name not found. Skipping student $roll_no.<br>";
          continue;
        }

        $faculty_id = $teacher_result->fetch_assoc()['id'];

        // 3. Insert the courses into the `course_registration` table for each of the 5 subjects
        foreach ($courses as $course_name) {
          if (empty($course_name))
            continue;

          // Check if the course exists
          $course_query = "SELECT id FROM course_details WHERE title = ?";
          $course_stmt = $conn->prepare($course_query);
          $course_stmt->bind_param("s", $course_name);
          $course_stmt->execute();
          $course_result = $course_stmt->get_result();

          if ($course_result->num_rows == 0) {
            echo "Course $course_name not found. Skipping student $roll_no.<br>";
            continue;
          }

          $course_id = $course_result->fetch_assoc()['id'];

          // Insert the course registration for the student
          $course_registration_query = "INSERT INTO course_registration (student_id, course_id, session_id) 
                                                  VALUES (?, ?, ?)";
          $course_registration_stmt = $conn->prepare($course_registration_query);
          $course_registration_stmt->bind_param("iii", $student_id, $course_id, $session_id);

          if (!$course_registration_stmt->execute()) {
            echo "Error inserting course registration for Roll No: $roll_no, Course: $course_name - " . $course_registration_stmt->error . "<br>";
          }
        }
      }

      fclose($handle); // Close the file after reading

      echo "Student details and course registrations uploaded successfully!";
    } else {
      echo "Error opening the CSV file.";
    }
  } else {
    echo "No file uploaded.";
  }
}

// Close the database connection
$conn->close();
?>