<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Fetch all subjects
$result_subjects = $connect->query("SELECT id, subject_name FROM subjects");

// Fetch selected subject's marks if subject is selected
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    $stmt = $connect->prepare("
        SELECT u.username, m.ca1_marks, m.ca2_marks, m.ut1_marks, m.term_test_marks, m.project_marks, m.total_marks
        FROM subject_marks m
        JOIN users u ON m.user_id = u.id
        WHERE m.subject_id = ?
    ");
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch all marks for all subjects if the 'overview' button is clicked
if (isset($_GET['overview'])) {
    $stmt_all_marks = $connect->prepare("
        SELECT u.username, s.subject_name, m.ca1_marks, m.ca2_marks, m.ut1_marks, m.term_test_marks, m.project_marks, m.total_marks
        FROM subject_marks m
        JOIN users u ON m.user_id = u.id
        JOIN subjects s ON m.subject_id = s.id
    ");
    $stmt_all_marks->execute();
    $result_all_marks = $stmt_all_marks->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="display-4">View Marks</h1>

        <!-- Subject Selection -->
        <form method="get" action="fetch_marks.php" class="mb-4">
            <label for="subject_id" class="form-label">Select Subject:</label>
            <select name="subject_id" id="subject_id" class="form-select" required>
                <option value="" disabled selected>Select a subject</option>
                <?php while ($subject = $result_subjects->fetch_assoc()) { ?>
                    <option value="<?php echo $subject['id']; ?>" <?php if (isset($subject_id) && $subject_id == $subject['id'])
                           echo 'selected'; ?>>
                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-primary mt-2">View Marks</button>
        </form>

        <!-- Overview Button -->
        <form method="get" action="fetch_marks.php" class="mb-4">
            <button type="submit" name="overview" class="btn btn-info">Overview</button>
        </form>

        <!-- Marks Table for Selected Subject -->
        <?php if (isset($result) && $result->num_rows > 0) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>CA1</th>
                        <th>CA2</th>
                        <th>UT1</th>
                        <th>Term Test</th>
                        <th>Project</th>
                        <th>Total Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['ca1_marks']); ?></td>
                            <td><?php echo htmlspecialchars($row['ca2_marks']); ?></td>
                            <td><?php echo htmlspecialchars($row['ut1_marks']); ?></td>
                            <td><?php echo htmlspecialchars($row['term_test_marks']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_marks']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_marks']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else if (isset($subject_id)) { ?>
                <p class="text-danger">No marks found for the selected subject.</p>
        <?php } ?>

        <!-- Overview Marks Tables -->
        <?php if (isset($result_all_marks) && $result_all_marks->num_rows > 0) { ?>
            <?php
            $previous_subject = "";
            while ($row = $result_all_marks->fetch_assoc()) {
                // Check if we need to start a new table for a new subject
                if ($previous_subject != $row['subject_name']) {
                    if ($previous_subject != "") {
                        echo "</tbody></table><br>";
                    }
                    echo "<h3 class='mt-4'>" . htmlspecialchars($row['subject_name']) . "</h3>";
                    echo "<table class='table table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>CA1</th>
                                        <th>CA2</th>
                                        <th>UT1</th>
                                        <th>Term Test</th>
                                        <th>Project</th>
                                        <th>Total Marks</th>
                                    </tr>
                                </thead>
                                <tbody>";
                    $previous_subject = $row['subject_name'];
                }

                // Table rows for each student in the subject
                echo "<tr>
                            <td>" . htmlspecialchars($row['username']) . "</td>
                            <td>" . htmlspecialchars($row['ca1_marks']) . "</td>
                            <td>" . htmlspecialchars($row['ca2_marks']) . "</td>
                            <td>" . htmlspecialchars($row['ut1_marks']) . "</td>
                            <td>" . htmlspecialchars($row['term_test_marks']) . "</td>
                            <td>" . htmlspecialchars($row['project_marks']) . "</td>
                            <td>" . htmlspecialchars($row['total_marks']) . "</td>
                          </tr>";
            }
            echo "</tbody></table>";
            ?>
        <?php } else if (isset($_GET['overview'])) { ?>
                <p class="text-danger">No marks found for the overview.</p>
        <?php } ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>

</html>