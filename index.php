<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');

include('includes/header.php');

// If user is already logged in, redirect to the dashboard
if (isset($_SESSION['id'])) {
    header('Location: dashboard.php');
    die();
}

// Check if the form is submitted
if (isset($_POST['email'])) {
    // Prepare the login query with email and password check
    if ($stm = $connect->prepare('SELECT id, username, role, email FROM users WHERE email = ? AND password = ? AND active = 1')) {
        // Hash the password to compare with the stored hashed password
        $hashed = SHA1($_POST['password']);
        $stm->bind_param('ss', $_POST['email'], $hashed);
        $stm->execute();

        $result = $stm->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Store user data in session
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store the role in session

            // Provide feedback or welcome message
            set_message("You have successfully logged in " . $_SESSION['username']);

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Invalid credentials
            echo 'Invalid username or password!';
        }

        $stm->close();
    } else {
        echo 'Could not prepare statement!';
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post">
                <!-- Email input -->
                <div data-mdb-input-init class="form-outline mb-4">
                    <input type="email" id="email" name="email" class="form-control" required />
                    <label class="form-label" for="email">Email address</label>
                </div>

                <!-- Password input -->
                <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control" required />
                    <label class="form-label" for="password">Password</label>
                </div>

                <!-- Submit button -->
                <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block">Sign in</button>
            </form>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>

<?php
include('includes/footer.php');
?>
