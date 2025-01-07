<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['attendance_date'];

    // Clear existing attendance for the selected date
    $stmt = $connect->prepare("DELETE FROM attendance WHERE date = ?");
    $stmt->bind_param('s', $date);
    $stmt->execute();

    // Insert new attendance data with Present/Absent selection
    foreach ($_POST['attendance'] as $user_id => $subjects) {
        foreach ($subjects as $subject_id => $status) {
            $status_value = ($status == 'present') ? 'Present' : 'Absent'; // Set status based on the radio button
            $stmt = $connect->prepare("INSERT INTO attendance (user_id, subject_id, date, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiss', $user_id, $subject_id, $date, $status_value);
            $stmt->execute();
        }
    }

    set_message('Attendance has been marked successfully!');
    header('Location: attendance.php');
    die();
}

// Fetch all users (excluding admins)
$result_users = $connect->query("SELECT id, username FROM users WHERE role != 'admin'");

// Fetch all subjects
$result_subjects = $connect->query("SELECT id, subject_name FROM subjects");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
    <script>
        // Optional: Mark all present if needed
        function markAllPresent() {
            const presentRadios = document.querySelectorAll('.present-radio');
            presentRadios.forEach(radio => radio.checked = true);
        }

        // Optional: Mark all absent if needed
        function markAllAbsent() {
            const absentRadios = document.querySelectorAll('.absent-radio');
            absentRadios.forEach(radio => radio.checked = true);
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-4">Mark Attendance</h1>
            </div>
            <div class="col text-end">
                <a href="view_attendance.php" class="h1 text-decoration-none text-info">View Attendance</a>
            </div>
        </div>

        <form method="post" action="attendance.php">
            <!-- Date Selection -->
            <div class="mb-4">
                <label for="attendance_date" class="form-label">Select Date:</label>
                <input type="date" id="attendance_date" name="attendance_date" class="form-control" required>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <?php while ($subject = $result_subjects->fetch_assoc()) { ?>
                            <th><?php echo htmlspecialchars($subject['subject_name']); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_subjects->data_seek(0); // Reset subjects result pointer
                    while ($user = $result_users->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <?php
                            $result_subjects->data_seek(0); // Reset subject pointer for each user
                            while ($subject = $result_subjects->fetch_assoc()) { ?>
                                <td>
                                    <!-- Present Radio Button -->
                                    <input type="radio"
                                        name="attendance[<?php echo $user['id']; ?>][<?php echo $subject['id']; ?>]"
                                        value="present" class="present-radio"> P
                                    <!-- Absent Radio Button -->
                                    <input type="radio"
                                        name="attendance[<?php echo $user['id']; ?>][<?php echo $subject['id']; ?>]"
                                        value="absent" class="absent-radio"> A
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button type="button" class="btn btn-secondary" onclick="markAllPresent()">Mark All Present</button>
                <button type="button" class="btn btn-secondary" onclick="markAllAbsent()">Mark All Absent</button>
                <button type="submit" class="btn btn-primary">Submit Attendance</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>

</html>