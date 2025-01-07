<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];

    // Clear existing marks for the selected subject
    $stmt = $connect->prepare("DELETE FROM subject_marks WHERE subject_id = ?");
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();

    // Insert new marks data
    foreach ($_POST['marks'] as $user_id => $marks) {
        $total = $marks['ca1'] + $marks['ca2'] + $marks['ut1'] + $marks['term_test'] + $marks['project'];
        $stmt = $connect->prepare(
            "INSERT INTO subject_marks (user_id, subject_id, ca1_marks, ca2_marks, ut1_marks, term_test_marks, project_marks, total_marks) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'iiiiiiii',
            $user_id,
            $subject_id,
            $marks['ca1'],
            $marks['ca2'],
            $marks['ut1'],
            $marks['term_test'],
            $marks['project'],
            $total
        );
        $stmt->execute();
    }

    set_message('Marks have been saved successfully!');
    header('Location: subject_file.php');
    die();
}

// Fetch all subjects
$result_subjects = $connect->query("SELECT id, subject_name FROM subjects");

// Fetch all users (excluding admins)
$result_users = $connect->query("SELECT id, username FROM users WHERE role != 'admin'");
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
                <a href="fetch_marks.php" class="btn btn-info">View Marks</a>
            </div>
        </div>

        <!-- Subject Selection -->
        <form method="post" action="subject_file.php">
            <div class="mb-4">
                <label for="subject_id" class="form-label">Select Subject:</label>
                <select name="subject_id" id="subject_id" class="form-select" required>
                    <option value="" disabled selected>Select a subject</option>
                    <?php while ($subject = $result_subjects->fetch_assoc()) { ?>
                        <option value="<?php echo $subject['id']; ?>">
                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Marks Entry Table -->
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
                    <?php while ($user = $result_users->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><input type="number" name="marks[<?php echo $user['id']; ?>][ca1]"
                                    class="form-control mark-input" min="0" max="100" required></td>
                            <td><input type="number" name="marks[<?php echo $user['id']; ?>][ca2]"
                                    class="form-control mark-input" min="0" max="100" required></td>
                            <td><input type="number" name="marks[<?php echo $user['id']; ?>][ut1]"
                                    class="form-control mark-input" min="0" max="100" required></td>
                            <td><input type="number" name="marks[<?php echo $user['id']; ?>][term_test]"
                                    class="form-control mark-input" min="0" max="100" required></td>
                            <td><input type="number" name="marks[<?php echo $user['id']; ?>][project]"
                                    class="form-control mark-input" min="0" max="100" required></td>
                            <td><input type="number" name="marks[<?php echo $user['id']; ?>][total]"
                                    class="form-control total-mark" readonly></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Submit Marks</button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('.mark-input').forEach(input => {
            input.addEventListener('input', function () {
                const row = this.closest('tr');
                const inputs = row.querySelectorAll('.mark-input');
                let total = 0;

                inputs.forEach(field => {
                    total += parseInt(field.value) || 0;
                });

                row.querySelector('.total-mark').value = total;
            });
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>

</html>