<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Allotment</title>
</head>

<body>
    <h2>Course Allotment Form</h2>
    <?php
    // Database connection
    $conn = new mysqli('localhost', 'cms', 'secret@cms', 'cms');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch faculty details
    $facultyQuery = "SELECT id, name FROM faculty_details";
    $facultyResult = $conn->query($facultyQuery);

    // Fetch course details
    $courseQuery = "SELECT id, title FROM course_details";
    $courseResult = $conn->query($courseQuery);

    // Fetch session details
    $sessionQuery = "SELECT id, CONCAT(year, ' - ', term) AS session_name FROM session_details";
    $sessionResult = $conn->query($sessionQuery);
    ?>

    <form action="form1_handler.php" method="POST">
        <!-- Session Dropdown -->
        <label for="session">Select Session:</label>
        <select name="session_id" id="session" required>
            <option value="">-- Select Session --</option>
            <?php
            if ($sessionResult->num_rows > 0) {
                while ($row = $sessionResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['session_name'] . "</option>";
                }
            } else {
                echo "<option value=''>No sessions found</option>";
            }
            ?>
        </select>
        <br><br>

        <!-- Faculty Dropdown -->
        <label for="faculty">Select Faculty:</label>
        <select name="faculty_id" id="faculty" required>
            <option value="">-- Select Faculty --</option>
            <?php
            if ($facultyResult->num_rows > 0) {
                while ($row = $facultyResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                }
            } else {
                echo "<option value=''>No faculty found</option>";
            }
            ?>
        </select>
        <br><br>

        <!-- Course Dropdown -->
        <label for="course">Select Course:</label>
        <select name="course_id" id="course" required>
            <option value="">-- Select Course --</option>
            <?php
            if ($courseResult->num_rows > 0) {
                while ($row = $courseResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                }
            } else {
                echo "<option value=''>No courses found</option>";
            }
            ?>
        </select>
        <br><br>

        <button type="submit">Submit</button>
    </form>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>

</html>
