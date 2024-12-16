<?php
include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

// Handle deletion
if (isset($_GET['delete'])) {
    if ($stm = $connect->prepare('DELETE FROM posts where id = ?')) {
        $stm->bind_param('i',  $_GET['delete']);
        $stm->execute();

        set_message("A post  " . $_GET['delete'] . " has been deleted");
        header('Location: posts.php');
        $stm->close();
        die();
    } else {
        echo 'Could not prepare statement!';
    }
}

// Fetch all posts
if ($stm = $connect->prepare('SELECT * FROM posts')) {
    $stm->execute();
    $result = $stm->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Management</title>
    <style>
        /* General page styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            margin-top: 50px;
        }

        /* Header styling */
        h1.display-1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 15px;
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

        /* Action buttons */
        a {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: background-color 0.3s;
        }

        a:hover {
            opacity: 0.8;
        }

        .btn-edit {
            background-color: #FFC107;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-add {
            background-color: #007BFF;
            color: white;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background-color: #0056b3;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }

            .btn-edit,
            .btn-delete,
            .btn-add {
                font-size: 12px;
                padding: 6px 12px;
            }

            h1.display-1 {
                font-size: 2em;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="display-1">Announcement Management</h1>
        <a href="posts_add.php" class="btn-add">Add New Announcement</a>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Author's ID</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($record = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $record['id']; ?></td>
                        <td><?php echo $record['title']; ?></td>
                        <td><?php echo $record['author']; ?></td>
                        <td><?php echo $record['content']; ?></td>
                        <td>
                            <a href="posts_edit.php?id=<?php echo $record['id']; ?>" class="btn-edit">Edit</a>
                            <a href="posts.php?delete=<?php echo $record['id']; ?>" class="btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
    $stm->close();
} else {
    echo 'Could not prepare statement!';
}
include('includes/footer.php');
?>
