<?php
include('includes/config.php'); // Configuration settings
include('includes/database.php'); // Database connection
include('includes/functions.php'); // Utility functions
secure(); // Ensure the user is authenticated

include('includes/header.php');

// Handle file deletion (only for admin users)
if (isset($_GET['delete']) && $_SESSION['role'] == 'admin') {
    $id = intval($_GET['delete']);
    $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT file_paths FROM file_paths WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    // The file path stored in the database is relative (e.g., 'uploads/aakashphoto.pdf')
    $full_path = $file_path; // Use the relative file path

    // Check if the file exists before deleting
    if (file_exists($full_path)) {
        unlink($full_path); // Delete the file from the server

        // Now delete the record from the database
        $stmt = $conn->prepare("DELETE FROM file_paths WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            set_message("File with ID $id has been deleted.");
            header("Location: resources.php");
            $stmt->close();
            die();
        } else {
            echo "Error deleting file from database.";
        }
    } else {
        echo "File not found on the server.";
    }
}

// Handle file download (accessible to all users)
if (isset($_GET['download'])) {
    $id = intval($_GET['download']);
    $conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT file_paths FROM file_paths WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    // The file path stored in the database is relative (e.g., 'uploads/aakashphoto.pdf')
    $full_path = $file_path; // Use the relative file path

    if ($file_path && file_exists($full_path)) {
        // Send headers to trigger file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($full_path) . '"');
        header('Content-Length: ' . filesize($full_path));
        header('Pragma: public');
        flush();
        readfile($full_path);  // This should work since file is in the 'uploads' folder
        exit;
    } else {
        echo "File not found at: " . $full_path . "<br>";
        exit;
    }
}

// Fetch all files
$conn = new mysqli('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$stmt = $conn->prepare("SELECT * FROM file_paths");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>
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
        <h1 class="display-1">File Management</h1>

        <!-- Only admins can see the "Add New File" button -->
        <?php if ($_SESSION['role'] == 'admin') { ?>
            <a href="resources_add.php" class="btn btn-primary mb-3">Add New File</a>
        <?php } ?>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>File Name</th>
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
                            <!-- Both admins and users can see the Download button -->
                            <a href="resources.php?download=<?php echo $row['id']; ?>" class="btn btn-success">Download</a>

                            <!-- Only admins can see the Delete button -->
                            <?php if ($_SESSION['role'] == 'admin') { ?>
                                <a href="resources.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                            <?php } else { ?>
                                <!-- Non-admin users will see a message instead of Delete button -->
                                <span class="text-muted">Actions available for admins only</span>
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
