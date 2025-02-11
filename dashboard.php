<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');
?>

<!-- Add Font Awesome CDN for icons (latest version 6.x.x) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<!-- Add Google Font (Roboto) -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Roboto', sans-serif;
    }

    .display-4 {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .card {
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        text-decoration: none;
    }

    .card-body {
        flex-grow: 1;
        text-align: center;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 500;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
        text-decoration: none;
    }

    .btn-custom:hover {
        background-color: #0056b3;
        color: white;
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <h1 class="display-4 mb-4">Dashboard</h1>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <!-- First row with 3 cards -->
                <div class="col">
                    <a href="users.php" class="card text-decoration-none" aria-label="Users Management">
                        <div class="card-body bg-primary text-white">
                            <h5 class="card-title"><i class="fas fa-users"></i> Users Management</h5>
                            <p class="card-text">Manage user accounts and roles</p>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="posts.php" class="card text-decoration-none" aria-label="Announcement Management">
                        <div class="card-body bg-success text-white">
                            <h5 class="card-title"><i class="fas fa-bullhorn"></i> Announcement Management</h5>
                            <p class="card-text">Create and manage announcements</p>
                        </div>
                    </a>
                </div>

                <div class="col">
                    <a href="resources.php" class="card text-decoration-none" aria-label="Resources Management">
                        <div class="card-body bg-warning text-dark">
                            <h5 class="card-title"><i class="fas fa-book"></i> Resources</h5>
                            <p class="card-text">Manage and share resources</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 g-4 mt-3">
                <!-- Second row with 2 cards -->
                <div class="col">
                    <a href="videos.php" class="card text-decoration-none" aria-label="Video Management">
                        <div class="card-body bg-danger text-white">
                            <h5 class="card-title"><i class="fas fa-video"></i> Videos</h5>
                            <p class="card-text">Upload and manage videos</p>
                        </div>
                    </a>
                </div>

                <!-- Attendance Card with Conditional Link -->
                <div class="col">
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a href="/attendanceapp/attendance.php" class="card text-decoration-none"
                            aria-label="Attendance Management">
                            <div class="card-body bg-info text-white">
                                <h5 class="card-title"><i class="fas fa-calendar-check"></i> Attendance Management</h5>
                                <p class="card-text">Track and manage attendance</p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="row row-cols-1 g-4 mt-3">
                <!-- Third row with 1 full-width card (only for admin) -->
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <div class="col-12">
                        <a href="subject_file.php" class="card text-decoration-none" aria-label="Subject Files Management">
                            <div class="card-body bg-dark text-white">
                                <h5 class="card-title"><i class="fas fa-file-alt"></i> Subject Files</h5>
                                <p class="card-text">Manage subject-specific files</p>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
?>
