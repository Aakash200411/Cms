<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .course-list {
            text-align: left;
            margin-top: 10px;
            max-height: 100px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            background-color: #fafafa;
        }

        .button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .add-user-btn {
            background-color: #28a745;
            margin-bottom: 15px;
        }

        .add-user-btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Student Registration Form</h2>
        <button class="button add-user-btn" onclick="window.location.href='upload_excel.html';">ADD USER FROM
            EXCEL</button>
        <form action="submit_registration.php" method="POST">
            <label for="roll_no">Roll Number:</label>
            <input type="text" id="roll_no" name="roll_no" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">

            <label for="parent_email">Parent's Email:</label>
            <input type="email" id="parent_email" name="parent_email">

            <label for="session">Select Session:</label>
            <select id="session" name="session_id" required>
                <option value="">-- Select Session --</option>
                <?php
                $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
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
            </select>

            <label for="course">Select Courses:</label>
            <div class="course-list">
                <?php
                $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $query = "SELECT id, title FROM course_details";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<input type='checkbox' name='course_ids[]' value='" . $row['id'] . "'> " . $row['title'] . "<br>";
                }

                $conn->close();
                ?>
            </div><br>

            <button type="submit" class="button">Register</button>
        </form>
    </div>
</body>

</html>
