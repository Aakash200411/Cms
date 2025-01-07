<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Get the start and end dates from the query parameters (if set)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days')); // Default to 30 days ago
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Default to today

// Fetch all users (excluding admins)
$result_users = $connect->query("SELECT id, username FROM users WHERE role != 'admin'");

// Fetch all subjects
$result_subjects = $connect->query("SELECT id, subject_name FROM subjects");

// Initialize an array to hold the total attendance data
$user_attendance = [];

while ($user = $result_users->fetch_assoc()) {
    $user_id = $user['id'];

    // Fetch attendance data for each user per subject for the selected date range
    $attendance_query = "
        SELECT subject_id, status, date 
        FROM attendance 
        WHERE user_id = ? AND date BETWEEN ? AND ?
        GROUP BY subject_id, date
    ";
    $stmt = $connect->prepare($attendance_query);
    $stmt->bind_param('iss', $user_id, $start_date, $end_date); // bind user_id, start_date, and end_date
    $stmt->execute();
    $result_attendance = $stmt->get_result();

    // Initialize variables to count present and total days for each subject
    $attendance_data = [];
    $total_classes = $result_subjects->num_rows;  // Total number of subjects for the user
    $present_days = [];

    while ($attendance = $result_attendance->fetch_assoc()) {
        $subject_id = $attendance['subject_id'];
        $status = $attendance['status'];

        // Store attendance data as present or absent
        if (!isset($attendance_data[$subject_id])) {
            $attendance_data[$subject_id] = [
                'total_classes' => 0,
                'present_days' => 0
            ];
        }

        // If present, increment present_days for that subject
        if ($status === 'Present') {
            $attendance_data[$subject_id]['present_days']++;
        }

        $attendance_data[$subject_id]['total_classes']++;
    }

    // Store the attendance data for each user
    $user_attendance[$user_id] = [
        'username' => $user['username'],
        'attendance_data' => $attendance_data,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="display-4 text-center">View Attendance from <?php echo htmlspecialchars($start_date); ?> to
            <?php echo htmlspecialchars($end_date); ?>
        </h1>

        <!-- Date Range Form -->
        <form method="get" action="view_attendance.php" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                        value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                        value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <?php while ($subject = $result_subjects->fetch_assoc()) { ?>
                        <th><?php echo htmlspecialchars($subject['subject_name']); ?> (Attended / Total)</th>
                    <?php } ?>
                    <th>Overall Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_attendance as $user_id => $attendance) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($attendance['username']); ?></td>
                        <?php
                        $result_subjects->data_seek(0); // Reset subjects result pointer
                        $total_present_for_user = 0;
                        $total_classes_for_user = 0;
                        while ($subject = $result_subjects->fetch_assoc()) {
                            $subject_id = $subject['id'];
                            $subject_name = $subject['subject_name'];

                            // Get the number of classes attended and total classes for each subject
                            $attended = isset($attendance['attendance_data'][$subject_id]) ? $attendance['attendance_data'][$subject_id]['present_days'] : 0;
                            $total_classes = isset($attendance['attendance_data'][$subject_id]) ? $attendance['attendance_data'][$subject_id]['total_classes'] : 0;

                            // Update totals for overall attendance
                            $total_present_for_user += $attended;
                            $total_classes_for_user += $total_classes;
                            ?>
                            <td><?php echo $attended . '/' . $total_classes; ?></td>
                        <?php } ?>
                        <td>
                            <?php
                            // Calculate the overall attendance percentage for the user
                            $overall_percentage = ($total_classes_for_user > 0) ? round(($total_present_for_user / $total_classes_for_user) * 100, 2) : 0;
                            echo $overall_percentage . "%";
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- MDB Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>

</html>