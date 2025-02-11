<?php
ob_start(); // Start output buffering to prevent header issues
session_start(); // Start session at the very top

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');

// If user is already logged in, redirect to the dashboard
if (isset($_SESSION['id'])) {
    header('Location: dashboard.php');
    exit(); // Always use exit() after header()
}

include('includes/header.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'], $_POST['password'])) {
    // Database connection
    $cms_connect = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

    if ($cms_connect->connect_error) {
        die("Connection failed: " . $cms_connect->connect_error);
    }

    if ($stm = $cms_connect->prepare('SELECT id, username, role, email FROM users WHERE email = ? AND password = ? AND active = 1')) {
        $hashed = SHA1($_POST['password']);
        $stm->bind_param('ss', $_POST['email'], $hashed);
        $stm->execute();

        $result = $stm->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            set_message("You have successfully logged in " . $_SESSION['username']);
            header('Location: dashboard.php');
            exit();
        }

        $stm->close();
    }

    // Check in student_details if not found in users table
    $attendance_connect = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

    if ($attendance_connect->connect_error) {
        die("Connection failed: " . $attendance_connect->connect_error);
    }

    if ($stm = $attendance_connect->prepare('SELECT id, name, role, email_id, password FROM student_details WHERE email_id = ?')) {
        $stm->bind_param('s', $_POST['email']);
        $stm->execute();

        $result = $stm->get_result();
        $student = $result->fetch_assoc();

        if ($student) {
            if ($_POST['password'] == $student['password']) { // Plain-text password check (not secure)
                $_SESSION['id'] = $student['id'];
                $_SESSION['email'] = $student['email_id'];
                $_SESSION['username'] = $student['name'];
                $_SESSION['role'] = $student['role'];

                set_message("You have successfully logged in " . $_SESSION['username']);
                header('Location: dashboard.php');
                exit();
            } else {
                echo '<p class="text-danger text-center">Invalid email or password!</p>';
            }
        } else {
            echo '<p class="text-danger text-center">Invalid email or password!</p>';
        }

        $stm->close();
    }
}
?>

<!-- Login Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post">
                <div class="form-outline mb-4">
                    <input type="email" id="email" name="email" class="form-control" required />
                    <label class="form-label" for="email">Email address</label>
                </div>

                <div class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control" required />
                    <label class="form-label" for="password">Password</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
            </form>
        </div>
    </div>
</div>

<!-- MDB UI Kit -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>

<?php
include('includes/footer.php');
ob_end_flush(); // Flush output buffer at the end
?>
