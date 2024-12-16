<?php

include('includes/config.php');
include('includes/database.php');
include('includes/functions.php');
secure();

include('includes/header.php');

if (isset($_POST['title'])) {

    if ($stm = $connect->prepare('UPDATE posts set  title = ?, content = ? , date = ?  WHERE id = ?')) {
        $stm->bind_param('sssi', $_POST['title'], $_POST['content'], $_POST['date'], $_GET['id']);
        $stm->execute();

        $stm->close();

        set_message("A announcement  " . $_GET['id'] . " has been updated");
        header('Location: posts.php');
        die();

    } else {
        echo 'Could not prepare announcement update statement!';
    }
}

if (isset($_GET['id'])) {

    if ($stm = $connect->prepare('SELECT * from posts WHERE id = ?')) {
        $stm->bind_param('i', $_GET['id']);
        $stm->execute();

        $result = $stm->get_result();
        $post = $result->fetch_assoc();

        if ($post) {
            ?>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h1 class="display-1">Edit Announcement</h1>

                        <form method="post">
                            <!-- Title input -->
                            <div class="form-outline mb-4">
                                <input type="text" id="title" name="title" class="form-control"
                                    value="<?php echo $post['title'] ?>" required />
                                <label class="form-label" for="title">Title</label>
                            </div>

                            <!-- Content input -->
                            <div class="form-outline mb-4">
                                <textarea name="content" id="content" class="form-control" required><?php echo $post['content'] ?></textarea>
                                <label class="form-label" for="content">Content</label>
                            </div>

                            <!-- Date select -->
                            <div class="form-outline mb-4">
                                <input type="date" id="date" name="date" class="form-control" value="<?php echo $post['date'] ?>" required />
                                <label class="form-label" for="date">Date</label>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary btn-block">Update Announcement</button>
                        </form>
                    </div>
                </div>
            </div>

            <script src="js/tinymce/tinymce.min.js"></script>
            <script>
                tinymce.init({
                    selector: '#content'
                });
            </script>

            <?php
        }
        $stm->close();
    } else {
        echo 'Could not prepare statement!';
    }

} else {
    echo "No announcement selected";
    die();
}

include('includes/footer.php');
?>

<!-- Internal CSS -->
<style>
    /* General page styling */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f9f9f9;
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

    /* Form Styling */
    .form-outline {
        margin-bottom: 20px;
    }

    .form-outline input, 
    .form-outline textarea {
        padding: 15px;
        font-size: 14px;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 100%;
        background-color: #fff;
        box-sizing: border-box;
    }

    .form-outline input:focus, 
    .form-outline textarea:focus {
        border-color: #007BFF;
        outline: none;
    }

    .form-label {
        font-size: 14px;
        color: #666;
    }

    /* Submit Button */
    .btn {
        width: 100%;
        padding: 12px;
        background-color: #007BFF;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        h1.display-1 {
            font-size: 2em;
        }

        .container {
            padding: 0 15px;
        }
    }

</style>
