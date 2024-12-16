<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $video = $_FILES['video'];
    $videoName = basename($video['name']);
    $videoTmpName = $video['tmp_name'];
    $description = isset($_POST['description']) ? $_POST['description'] : null;

    $uploadDir = "C:/xampp/htdocs/uploads/videos/"; // Absolute path where videos are stored
    $uploadPath = $uploadDir . $videoName;

    // Ensure the uploads directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory with appropriate permissions
    }

    // Move the file to the uploads directory
    if (move_uploaded_file($videoTmpName, $uploadPath)) {
        $relativePath = "uploads/videos/" . $videoName; // Relative path to save in database

        // Save video information to the database
        $conn = new mysqli("localhost", "cms", "secret@cms", "cms");
        if ($conn->connect_error)
            die("Connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("INSERT INTO video_paths (name, video_paths, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $videoName, $relativePath, $description);

        if ($stmt->execute()) {
            set_message("Video uploaded successfully.");
            header("Location: videos.php");
            $stmt->close();
            die();
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Failed to upload the video. Check directory permissions.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Video</title>
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
        <h1 class="display-1">Add New Video</h1>
        <form action="videos_add.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="video" class="form-label">Choose a Video:</label>
                <input type="file" name="video" id="video" class="form-control" required>
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
