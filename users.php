<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
        h1.display-4 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
            font-weight: 600;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            font-size: 16px;
        }

        table tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        table td {
            font-size: 14px;
        }

        /* Button Styling */
        .btn {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-warning {
            background-color: #ffc107;
            color: black;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Additional layout adjustments */
        .row {
            display: flex;
            justify-content: center;
        }

        .col-md-8 {
            width: 100%;
            max-width: 900px;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
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

        /* Custom hover effect for table rows */
        table tr:hover {
            cursor: pointer;
        }

        /* Add spacing between buttons */
        a.btn {
            margin-top: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1.display-4 {
                font-size: 1.6em;
            }

            .btn {
                font-size: 12px;
                padding: 6px 12px;
            }

            table th, table td {
                padding: 8px;
                font-size: 13px;
            }

            .col-md-8 {
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>

<?php
if (isset($_GET['delete'])) {
    if ($stm = $connect->prepare('DELETE FROM users WHERE id = ?')) {
        $stm->bind_param('i', $_GET['delete']);
        $stm->execute();

        set_message("A user " . $_GET['delete'] . " has been deleted");
        header('Location: users.php');
        $stm->close();
        die();

    } else {
        echo 'Could not prepare statement!';
    }
}

if ($stm = $connect->prepare('SELECT * FROM users')) {
    $stm->execute();

    $result = $stm->get_result();

    if ($result->num_rows > 0) {
?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-4">User Management</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($record = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $record['id']; ?></td>
                                    <td><?php echo $record['username']; ?></td>
                                    <td><?php echo $record['email']; ?></td>
                                    <td><?php echo $record['active'] ? 'Active' : 'Inactive'; ?></td>
                                    <td>
                                        <a href="users_edit.php?id=<?php echo $record['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="users.php?delete=<?php echo $record['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <a href="users_add.php" class="btn btn-primary">Add New User</a>
                </div>
            </div>
        </div>
<?php
    } else {
        echo '<div class="alert alert-info text-center">No users found.</div>';
    }

    $stm->close();

} else {
    echo '<div class="alert alert-danger">Could not prepare statement!</div>';
}

include('includes/footer.php');
?>