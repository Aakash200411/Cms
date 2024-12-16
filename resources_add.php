<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $description = isset($_POST['description']) ? $_POST['description'] : null;

    $uploadDir = "C:/xampp/htdocs/uploads/"; // Absolute path where files are stored
    $uploadPath = $uploadDir . $fileName;

    // Ensure the uploads directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory with appropriate permissions
    }

    // Move the file to the uploads directory
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $relativePath = "uploads/" . $fileName; // Relative path to save in database

        // Save file information to the database
        $conn = new mysqli("localhost", "cms", "secret@cms", "cms");
        if ($conn->connect_error)
            die("Connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("INSERT INTO file_paths (name, file_paths, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fileName, $relativePath, $description);

        if ($stmt->execute()) {
            set_message("File uploaded successfully.");
            header("Location: resources.php");
            $stmt->close();
            die();
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Failed to upload the file. Check directory permissions.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New File</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* General page styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            margin-top: 50px;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1.display-1 {
            color: black;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: none;
            transition: border 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            h1.display-1 {
                font-size: 2em;
            }

            .container {
                padding: 20px;
            }

            .btn-primary {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="display-1">Add New File</h1>
        <form action="resources_add.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="file" class="form-label">Choose a File:</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description (optional):</label>
                <input type="text" name="description" id="description" class="form-control"
                    placeholder="Enter a description">
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>

</html>

<?php
include('includes/footer.php');
?>