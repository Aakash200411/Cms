<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
require_once('tcpdf/tcpdf.php'); // Include TCPDF library

secure();

// Fetch subjects
$result_subjects = $connect->query("SELECT id, subject_name FROM subjects");

// Check if "Overview" button is clicked to generate PDF
if (isset($_GET['export_pdf'])) {
    // Fetch all marks data
    $stmt = $connect->prepare("
        SELECT u.username, s.subject_name, m.ca1_marks, m.ca2_marks, m.ut1_marks, m.term_test_marks, m.project_marks
        FROM subject_marks m
        JOIN users u ON m.user_id = u.id
        JOIN subjects s ON m.subject_id = s.id
        ORDER BY s.subject_name
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    // Organize data into an associative array grouped by subject
    $marks_data = [];
    while ($row = $result->fetch_assoc()) {
        $subject = $row['subject_name'];
        if (!isset($marks_data[$subject])) {
            $marks_data[$subject] = [];
        }
        $marks_data[$subject][] = $row;
    }

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Marks Overview');
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // PDF Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Marks Overview', 0, 1, 'C');
    $pdf->Ln(5);

    // Generate tables for each subject and test type
    $pdf->SetFont('helvetica', '', 10);
    foreach ($marks_data as $subject => $records) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, "Subject: $subject", 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        $test_types = ['ca1_marks' => 'CA1', 'ca2_marks' => 'CA2', 'ut1_marks' => 'UT1', 'term_test_marks' => 'Term Test', 'project_marks' => 'Project'];

        foreach ($test_types as $column => $test_name) {
            // Create table header
            $table_html = '<table border="1" cellpadding="4">
                <thead>
                    <tr>
                        <th><b>Username</b></th>
                        <th><b>' . $test_name . '</b></th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($records as $record) {
                $table_html .= '<tr>
                    <td>' . htmlspecialchars($record['username']) . '</td>
                    <td>' . htmlspecialchars($record[$column]) . '</td>
                </tr>';
            }

            $table_html .= '</tbody></table><br>';
            $pdf->writeHTML($table_html, true, false, false, false, '');
        }
        $pdf->Ln(5);
    }

    // Output PDF
    $pdf->Output('Marks_Overview.pdf', 'D');
    exit;
}

include('includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="display-4">View Marks</h1>

        <!-- Overview Button -->
        <form method="get" action="fetch_marks.php">
            <button type="submit" name="export_pdf" class="btn btn-danger">Export PDF</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>

</html>
