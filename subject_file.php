<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];

    // Loop through submitted marks and update or insert them
    foreach ($_POST['marks'] as $user_id => $marks) {
        $ca1 = intval($marks['ca1'] ?? 0);
        $ca2 = intval($marks['ca2'] ?? 0);
        $ut1 = intval($marks['ut1'] ?? 0);
        $term_test = intval($marks['term_test'] ?? 0);
        $project = intval($marks['project'] ?? 0);
        $total = $ca1 + $ca2 + $ut1 + $term_test + $project;

        // Check if marks already exist for the user in this subject
        $stmt = $connect->prepare("SELECT id FROM subject_marks WHERE user_id = ? AND subject_id = ?");
        $stmt->bind_param('ii', $user_id, $subject_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing marks
            $stmt = $connect->prepare(
                "UPDATE subject_marks 
                 SET ca1_marks = ?, ca2_marks = ?, ut1_marks = ?, term_test_marks = ?, project_marks = ?, total_marks = ? 
                 WHERE user_id = ? AND subject_id = ?"
            );
            $stmt->bind_param('iiiiiiii', $ca1, $ca2, $ut1, $term_test, $project, $total, $user_id, $subject_id);
        } else {
            // Insert new marks
            $stmt = $connect->prepare(
                "INSERT INTO subject_marks (user_id, subject_id, ca1_marks, ca2_marks, ut1_marks, term_test_marks, project_marks, total_marks) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('iiiiiiii', $user_id, $subject_id, $ca1, $ca2, $ut1, $term_test, $project, $total);
        }

        $stmt->execute();
        $stmt->close();
    }

    set_message('Marks have been saved successfully!');
    header("Location: subject_file.php?subject_id=" . $subject_id);
    die();
}

// Fetch subjects
$result_subjects = $connect->query("SELECT id, subject_name, ca1, ca2, ut1, term_test, project FROM subjects");

// Fetch users (excluding admins)
$result_users = $connect->query("SELECT id, username FROM users WHERE role != 'admin'");

// Fetch existing marks if a subject is selected
$existing_marks = [];
if (isset($_GET['subject_id'])) {
    $selected_subject_id = $_GET['subject_id'];
    $marks_query = $connect->prepare("SELECT * FROM subject_marks WHERE subject_id = ?");
    $marks_query->bind_param('i', $selected_subject_id);
    $marks_query->execute();
    $marks_result = $marks_query->get_result();
    while ($row = $marks_result->fetch_assoc()) {
        $existing_marks[$row['user_id']] = $row;
    }
    $marks_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Marks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-4">Enter Marks</h1>
            </div>
            <div class="col text-end">
                <a href="upload_pdf.php" class="btn btn-info">Upload PDF</a>
                <a href="fetch_marks.php" class="btn btn-info">View Marks</a>
            </div>
        </div>

        <form method="get" action="subject_file.php">
            <label for="subject_id" class="form-label">Select Subject:</label>
            <select name="subject_id" id="subject_id" class="form-select" required onchange="this.form.submit()">
                <option value="" disabled selected>Select a subject</option>
                <?php
                $subjects = [];
                while ($subject = $result_subjects->fetch_assoc()) {
                    $subjects[$subject['id']] = $subject;
                    $selected = (isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id']) ? "selected" : "";
                    echo '<option value="' . $subject['id'] . '" ' . $selected . '>' . htmlspecialchars($subject['subject_name']) . '</option>';
                }
                ?>
            </select>
        </form>

        <?php if (isset($_GET['subject_id'])) { ?>
            <form method="post" action="subject_file.php">
                <input type="hidden" name="subject_id" value="<?php echo $_GET['subject_id']; ?>">

                <table class="table table-bordered mt-3">
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
                        <?php while ($user = $result_users->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><input type="number" name="marks[<?php echo $user['id']; ?>][ca1]"
                                        class="form-control mark-input" min="0" max="100"
                                        value="<?php echo $existing_marks[$user['id']]['ca1_marks'] ?? ''; ?>"></td>
                                <td><input type="number" name="marks[<?php echo $user['id']; ?>][ca2]"
                                        class="form-control mark-input" min="0" max="100"
                                        value="<?php echo $existing_marks[$user['id']]['ca2_marks'] ?? ''; ?>"></td>
                                <td><input type="number" name="marks[<?php echo $user['id']; ?>][ut1]"
                                        class="form-control mark-input" min="0" max="100"
                                        value="<?php echo $existing_marks[$user['id']]['ut1_marks'] ?? ''; ?>"></td>
                                <td><input type="number" name="marks[<?php echo $user['id']; ?>][term_test]"
                                        class="form-control mark-input" min="0" max="100"
                                        value="<?php echo $existing_marks[$user['id']]['term_test_marks'] ?? ''; ?>"></td>
                                <td><input type="number" name="marks[<?php echo $user['id']; ?>][project]"
                                        class="form-control mark-input" min="0" max="100"
                                        value="<?php echo $existing_marks[$user['id']]['project_marks'] ?? ''; ?>"></td>
                                <td><input type="number" class="form-control total-mark" readonly
                                        value="<?php echo $existing_marks[$user['id']]['total_marks'] ?? ''; ?>"></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary mt-3">Submit Marks</button>
            </form>
        <?php } ?>
    </div>
</body>

</html>
