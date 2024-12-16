<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

?>

<!-- Add Font Awesome CDN for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Add Google Font (Roboto) -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

<style>
    body {
        background-color: #f8f9fa; /* Light background */
        font-family: 'Roboto', sans-serif;
    }
    .display-4 {
        font-size: 2.5rem;
        font-weight: bold;
    }
    .card {
        height: 100%; /* Ensure cards have the same height */
        display: flex;
        flex-direction: column; /* Allow content to expand vertically */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        text-decoration: none; /* Remove default anchor styles */
    }
    .card-body {
        flex-grow: 1; /* Allow body to take up available space */
        text-align: center;
    }
    .card:hover {
        transform: scale(1.05); /* Zoom effect */
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
            <div class="row">
                <div class="col-md-3 mb-4">
                    <a href="users.php" class="card text-decoration-none">
                        <div class="card-body bg-primary text-white">
                            <h5 class="card-title"><i class="fas fa-users"></i> Users Management</h5>
                            <p class="card-text">Manage user accounts and roles</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-4">
                    <a href="posts.php" class="card text-decoration-none">
                        <div class="card-body bg-success text-white">
                            <h5 class="card-title"><i class="fas fa-bullhorn"></i> Announcement Management</h5>
                            <p class="card-text">Create and manage announcements</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-4">
                    <a href="resources.php" class="card text-decoration-none">
                        <div class="card-body bg-warning text-dark">
                            <h5 class="card-title"><i class="fas fa-book"></i> Resources</h5>
                            <p class="card-text">Manage and share resources</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-3 mb-4">
                    <a href="videos.php" class="card text-decoration-none">
                        <div class="card-body bg-danger text-white">
                            <h5 class="card-title"><i class="fas fa-video"></i> Videos</h5>
                            <p class="card-text">Upload and manage videos</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

include('includes/footer.php');

?>