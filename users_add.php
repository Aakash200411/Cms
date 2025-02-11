<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_POST['username'])) {
    if ($stm = $connect->prepare('INSERT INTO users (username, email, password, active, role) VALUES (?, ?, ?, ?, ?)')) {
        $hashed = SHA1($_POST['password']);
        $stm->bind_param('sssss', $_POST['username'], $_POST['email'], $hashed, $_POST['active'], $_POST['role']);
        $stm->execute();

        // If the user is an admin, insert the subject they teach into the teacher_subjects table
        if ($_POST['role'] == 'admin' && isset($_POST['subject'])) {
            $teacher_id = $connect->insert_id; // Get the user ID of the newly created admin
            foreach ($_POST['subject'] as $subject_id) {
                $stmt = $connect->prepare("INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES (?, ?)");
                $stmt->bind_param('ii', $teacher_id, $subject_id);
                $stmt->execute();
            }
        }

        set_message("A new user " . $_SESSION['username'] . " has been added");
        header('Location: users.php');
        $stm->close();
        die();
    } else {
        echo 'Could not prepare statement!';
    }
}

// Fetch all subjects for admin (teachers)
$subjects_result = $connect->query("SELECT id, subject_name FROM subjects");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="display-1">Add user</h1>

            <form method="post">
                <!-- Username input -->
                <div class="form-outline mb-4">
                    <input type="text" id="username" name="username" class="form-control" required />
                    <label class="form-label" for="username">Username</label>
                </div>

                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input type="email" id="email" name="email" class="form-control" required />
                    <label class="form-label" for="email">Email address</label>
                </div>

                <!-- Password input -->
                <div class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control" required />
                    <label class="form-label" for="password">Password</label>
                </div>

                <!-- Active select -->
                <div class="form-outline mb-4">
                    <select name="active" class="form-select" id="active" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Role select -->
                <div class="form-outline mb-4">
                    <select name="role" class="form-select" id="role" required onchange="toggleSubjectField()">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Subject select (Visible only if admin role is selected) -->
                <div id="subject-select" class="form-outline mb-4" style="display:none;">
                    <label for="subject" class="form-label">Subjects</label>
                    <select name="subject[]" id="subject" class="form-select" multiple required>
                        <?php while ($subject = $subjects_result->fetch_assoc()) { ?>
                            <option value="<?php echo $subject['id']; ?>">
                                <?php echo htmlspecialchars($subject['subject_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block">Add user</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Show or hide the subject selection based on the role selected
    function toggleSubjectField() {
        var role = document.getElementById('role').value;
        var subjectSelect = document.getElementById('subject-select');
        if (role === 'admin') {
            subjectSelect.style.display = 'block';
        } else {
            subjectSelect.style.display = 'none';
        }
    }
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>

<?php
include('includes/footer.php');
?>
