<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is uploaded
    if (isset($_FILES['pdf_upload']) && $_FILES['pdf_upload']['error'] == 0) {
        $pdf_file = $_FILES['pdf_upload'];
        $allowed_extensions = ['pdf'];
        $extension = pathinfo($pdf_file['name'], PATHINFO_EXTENSION);

        // Check if the file is a PDF
        if (in_array(strtolower($extension), $allowed_extensions)) {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
            }

            // Generate a unique file name to avoid conflicts
            $upload_file = $upload_dir . uniqid('pdf_') . '.' . $extension;

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($pdf_file['tmp_name'], $upload_file)) {
                // Get the description
                $description = $_POST['pdf_description'];

                // Store the file path and description in the database
                $stmt = $connect->prepare("INSERT INTO pdf_files (file_path, description) VALUES (?, ?)");
                $stmt->bind_param('ss', $upload_file, $description);
                $stmt->execute();

                set_message('PDF uploaded successfully with description!');
            } else {
                set_message('Failed to upload the PDF.');
            }
        } else {
            set_message('Only PDF files are allowed.');
        }
    } else {
        set_message('No PDF file uploaded.');
    }

    header('Location: upload_pdf.php');
    die();
}

// Fetch all PDFs and their descriptions from the database
$result = $connect->query("SELECT * FROM pdf_files ORDER BY upload_date DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload PDF</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-4">Upload PDF</h1>
            </div>
            <div class="col text-end">
                <a href="subject_file.php" class="btn btn-info">Back to Marks</a>
            </div>
        </div>

        <form method="post" action="upload_pdf.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="pdf_upload" class="form-label">Select PDF to Upload:</label>
                <input type="file" name="pdf_upload" id="pdf_upload" class="form-control" accept="application/pdf"
                    required>
            </div>

            <div class="mb-3">
                <label for="pdf_description" class="form-label">Description:</label>
                <textarea name="pdf_description" id="pdf_description" class="form-control" rows="3"
                    placeholder="Enter a description for the PDF" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Upload PDF</button>
        </form>

        <hr class="mt-5">

        <h2 class="mt-4">Uploaded PDFs</h2>

        <?php if ($result->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($pdf = $result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($pdf['description']); ?></h5>
                        <a href="<?php echo htmlspecialchars($pdf['file_path']); ?>" target="_blank" class="btn btn-info">View
                            PDF</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No PDFs uploaded yet.</p>
        <?php endif; ?>

    </div>
</body>

</html>
