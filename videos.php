<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Handle file deletion (only for admins)
if (isset($_GET['delete']) && $_SESSION['role'] == 'admin') {
    $id = intval($_GET['delete']);
    $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM video_paths WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        set_message("Video with ID $id has been deleted.");
        header("Location: videos.php");
        $stmt->close();
        die();
    } else {
        echo "Could not prepare statement!";
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $id = intval($_GET['download']);
    $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT video_paths FROM video_paths WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($video_path);
    $stmt->fetch();
    $stmt->close();

    $file_path = "C:\\CMS\\cms\\uploads\\videos";

    // Build the full path based on the document root and relative video path
    $full_path = $file_path; // Use the relative file path

    if ($video_path && file_exists($full_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: video/mp4');
        header('Content-Disposition: attachment; filename="' . basename($full_path) . '"');
        header('Content-Length: ' . filesize($full_path));
        header('Pragma: public');
        flush();
        readfile($full_path);
        exit;
    } else {
        echo "File not found at: " . $full_path . "<br>";
        exit;
    }
}

// Fetch all videos
$conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$stmt = $conn->prepare("SELECT * FROM video_paths");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Management</title>
    <style>
        /* General page styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 50px;
        }

        /* Header styling */
        h1.display-1 {
            color: black;
            text-align: center;
            margin-bottom: 20px;
            font-size: 3em;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td {
            font-size: 14px;
        }

        /* Button styling */
        .btn {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-right: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Add margin to the Add New File button */
        .btn-primary.mb-3 {
            margin-bottom: 20px;
        }

        /* Alert message styling */
        .alert {
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
        }

        /* Info alert styling */
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Danger alert styling */
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive table */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }

            .btn {
                font-size: 12px;
                padding: 6px 12px;
            }

            h1.display-1 {
                font-size: 2.5em;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="display-1">Video Management</h1>
        <a href="videos_add.php" class="btn btn-primary mb-3">Add New Video</a>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Video Name</th>
                    <th>Description</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['uploaded_at']; ?></td>
                        <td>
                            <a href="videos.php?download=<?php echo $row['id']; ?>" class="btn btn-success">Download</a>

                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                <!-- Only admins can see the Delete button -->
                                <a href="videos.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                            <?php } else { ?>
                                <!-- For regular users, you can hide the Delete button or disable it -->
                                <span class="text-muted">Delete (Admin Only)</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
include('includes/footer.php');
?>
