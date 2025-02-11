<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Allotment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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
            margin-top: 10px;
            text-align: left;
        }

        select,
        button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Course Allotment Form</h2>
        <form action="form1_handler.php" method="POST">
            <label for="session">Select Session:</label>
            <select name="session_id" id="session" required>
                <option value="">-- Select Session --</option>
                <?php
                $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
                if ($conn->connect_error)
                    die("Connection failed: " . $conn->connect_error);
                $sessionQuery = "SELECT id, CONCAT(year, ' - ', term) AS session_name FROM session_details";
                $sessionResult = $conn->query($sessionQuery);
                while ($row = $sessionResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['session_name'] . "</option>";
                }
                ?>
            </select>

            <label for="faculty">Select Faculty:</label>
            <select name="faculty_id" id="faculty" required>
                <option value="">-- Select Faculty --</option>
                <?php
                $facultyQuery = "SELECT id, name FROM faculty_details";
                $facultyResult = $conn->query($facultyQuery);
                while ($row = $facultyResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                }
                ?>
            </select>

            <label for="course">Select Course:</label>
            <select name="course_id" id="course" required>
                <option value="">-- Select Course --</option>
                <?php
                $courseQuery = "SELECT id, title FROM course_details";
                $courseResult = $conn->query($courseQuery);
                while ($row = $courseResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                }
                $conn->close();
                ?>
            </select>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>

</html>
