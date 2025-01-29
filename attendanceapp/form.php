<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
</head>

<body>
    <h2>Student Registration Form</h2>
    <button class="button" id="btnAddUser" onclick="window.location.href='upload_excel.html';">ADD USER FROM
        EXCEL</button>
    <form action="submit_registration.php" method="POST">
        <!-- Input for Roll Number -->
        <label for="roll_no">Roll Number:</label><br>
        <input type="text" id="roll_no" name="roll_no" required><br><br>

        <!-- Input for Name -->
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <!-- Input for Email -->
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <!-- Input for Password (optional) -->
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br><br>

        <!-- Input for Parent's Email -->
        <label for="parent_email">Parent's Email:</label><br>
        <input type="email" id="parent_email" name="parent_email"><br><br>

        <!-- Dropdown for Sessions -->
        <label for="session">Select Session:</label><br>
        <select id="session" name="session_id" required>
            <option value="">-- Select Session --</option>
            <?php
            // Fetch session details from the database
            $conn = new mysqli('mysql.railway.internal', 'root', 'llBhSnJhgoNktIPCqMYyNgPPAPJRdalt', 'railway');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $query = "SELECT id, year, term FROM session_details";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['year'] . " - " . $row['term'] . "</option>";
            }

            $conn->close();
            ?>
        </select><br><br>

        <!-- Checkboxes for Courses -->
        <label for="course">Select Courses:</label><br>
        <div id="course-list">
            <?php
            // Fetch course details from the database
            $conn = new mysqli('mysql.railway.internal', 'root', 'llBhSnJhgoNktIPCqMYyNgPPAPJRdalt', 'railway');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $query = "SELECT id, title FROM course_details";  // Change 'course_name' to 'title'
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                echo "<input type='checkbox' name='course_ids[]' value='" . $row['id'] . "'> " . $row['title'] . "<br>";
            }

            $conn->close();
            ?>

            ?>
        </div><br>

        <!-- Submit Button -->
        <button type="submit">Register</button>
    </form>
</body>

</html>
