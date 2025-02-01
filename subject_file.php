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
        $total = ($marks['ca1'] ?? 0) + ($marks['ca2'] ?? 0) + ($marks['ut1'] ?? 0) + ($marks['term_test'] ?? 0) + ($marks['project'] ?? 0);
        $stmt = $connect->prepare(
            "INSERT INTO subject_marks (user_id, subject_id, ca1_marks, ca2_marks, ut1_marks, term_test_marks, project_marks, total_marks) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'iiiiiiii',
            $user_id,
            $subject_id,
            $marks['ca1'] ?? 0,
            $marks['ca2'] ?? 0,
            $marks['ut1'] ?? 0,
            $marks['term_test'] ?? 0,
            $marks['project'] ?? 0,
            $total
        );
        $stmt->execute();
    }

    set_message('Marks have been saved successfully!');
    header('Location: subject_file.php');
    die();
}

// Fetch subjects with test availability
$result_subjects = $connect->query("SELECT id, subject_name, ca1, ca2, ut1, term_test, project FROM subjects");

// Fetch users (excluding admins)
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
            <!-- Moved the Upload PDF button here -->
            <div class="col text-end">
                <a href="upload_pdf.php" class="btn btn-info">Upload PDF</a>
            </div>
            <div class="col text-end">
                <a href="fetch_marks.php" class="btn btn-info">View Marks</a>
            </div>
        </div>

        <form method="post" action="subject_file.php">
            <label for="subject_id" class="form-label">Select Subject:</label>
            <select name="subject_id" id="subject_id" class="form-select" required>
                <option value="" disabled selected>Select a subject</option>
                <?php
                $subjects = [];
                while ($subject = $result_subjects->fetch_assoc()) {
                    $subjects[$subject['id']] = $subject;
                    echo '<option value="' . $subject['id'] . '">' . htmlspecialchars($subject['subject_name']) . '</option>';
                }
                ?>
            </select>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th class="ca1-column">CA1</th>
                        <th class="ca2-column">CA2</th>
                        <th class="ut1-column">UT1</th>
                        <th class="term-test-column">Term Test</th>
                        <th class="project-column">Project</th>
                        <th>Total Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result_users->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="ca1-column"><input type="number" name="marks[<?php echo $user['id']; ?>][ca1]"
                                    class="form-control mark-input" min="0" max="100"></td>
                            <td class="ca2-column"><input type="number" name="marks[<?php echo $user['id']; ?>][ca2]"
                                    class="form-control mark-input" min="0" max="100"></td>
                            <td class="ut1-column"><input type="number" name="marks[<?php echo $user['id']; ?>][ut1]"
                                    class="form-control mark-input" min="0" max="100"></td>
                            <td class="term-test-column"><input type="number"
                                    name="marks[<?php echo $user['id']; ?>][term_test]" class="form-control mark-input"
                                    min="0" max="100"></td>
                            <td class="project-column"><input type="number"
                                    name="marks[<?php echo $user['id']; ?>][project]" class="form-control mark-input"
                                    min="0" max="100"></td>
                            <td><input type="number" class="form-control total-mark" readonly></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary mt-3">Submit Marks</button>
        </form>
    </div>

    <script>
        const subjectsData = <?php echo json_encode($subjects); ?>;

        document.getElementById('subject_id').addEventListener('change', function () {
            const subjectId = this.value;
            const selectedSubject = subjectsData[subjectId];

            document.querySelectorAll('.ca1-column, .ca2-column, .ut1-column, .term-test-column, .project-column')
                .forEach(el => el.style.display = 'none');

            if (selectedSubject.ca1 == 1) document.querySelectorAll('.ca1-column').forEach(el => el.style.display = 'table-cell');
            if (selectedSubject.ca2 == 1) document.querySelectorAll('.ca2-column').forEach(el => el.style.display = 'table-cell');
            if (selectedSubject.ut1 == 1) document.querySelectorAll('.ut1-column').forEach(el => el.style.display = 'table-cell');
            if (selectedSubject.term_test == 1) document.querySelectorAll('.term-test-column').forEach(el => el.style.display = 'table-cell');
            if (selectedSubject.project == 1) document.querySelectorAll('.project-column').forEach(el => el.style.display = 'table-cell');
        });

        document.getElementById('subject_id').dispatchEvent(new Event('change'));

        document.querySelectorAll('.mark-input').forEach(input => {
            input.addEventListener('input', function () {
                const row = this.closest('tr');
                const inputs = row.querySelectorAll('.mark-input');
                let total = 0;

                inputs.forEach(field => {
                    if (field.offsetParent !== null) {
                        total += parseInt(field.value) || 0;
                    }
                });

                row.querySelector('.total-mark').value = total;
            });
        });
    </script>
</body>

</html>
