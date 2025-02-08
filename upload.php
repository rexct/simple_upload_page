<?php
$destination_path = "uploads/";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!file_exists($destination_path)) {
        mkdir($destination_path, 0777, true);
    }

    $target_path = $destination_path . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        echo "The file " . basename($_FILES['file']['name']) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    exit; // Terminate script execution after file upload is processed.
}

$delete_info = '';

if (isset($_GET['id']) && isset($_GET['size'])) {
    $file_index = (int) $_GET['id'];
    $expected_size = (int) $_GET['size'];

    // Get list of files, excluding '.' and '..'
    $files = array_values(array_diff(scandir($destination_path), array('.', '..')));

    // Check if the index is within the range of available files
    if ($file_index >= 0 && $file_index < count($files)) {
        $target_file = $destination_path . $files[$file_index];

        // Check if the file exists and matches the expected size
        if (file_exists($target_file) && filesize($target_file) === $expected_size) {
            if (unlink($target_file)) {
                $delete_info = "File '{$files[$file_index]}' successfully deleted.";
            } else {
                $delete_info = "Error: Unable to delete the file.";
            }
        } else {
            $delete_info = "Error: File size does not match the expected size or file does not exist.";
        }
    } else {
        $delete_info = "Error: File index is out of range.";
    }
}

$files = array_diff(scandir($destination_path), array('.', '..'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
    <link rel="stylesheet" href="upload.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <i><?= $delete_info ?></i>
    <div id="drop_zone" onclick="document.getElementById('fileInput').click();">Drop files here or click to select</div>
    <input type="file" id="fileInput" style="display: none;" multiple>

    <div id="file_list">
        <h3>Uploaded files:</h3>
        <ul>
            <?php $id = 0; foreach($files as $file): ?>
                <li>
                    <a href="<?php echo $destination_path . $file; ?>" download><?php echo $file; ?></a> &nbsp;
                    <a href="./upload.php?id=<?= $id ?>&size=<?=filesize($destination_path . $file)?>"  >&#x2613</a>
                </li>
            <?php $id++; endforeach; ?>
        </ul>
    </div>

    <script>
        $(document).ready(function() {
            var dropzone = $('#drop_zone');
            var fileInput = $('#fileInput');

            // Prevent default actions when file is dragged over.
            dropzone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            // Define drop event and handle file upload.
            dropzone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var files = e.originalEvent.dataTransfer.files; // Array of all files

                for (var i=0, file; file=files[i]; i++) {
                    var formData = new FormData();
                    formData.append('file', file);
                    upload(formData);
                }
            });

            fileInput.on('change', function(e) {
                var files = e.target.files; // Array of all files

                for (var i=0, file; file=files[i]; i++) {
                    var formData = new FormData();
                    formData.append('file', file);
                    upload(formData);
                }
            });

            // Function to upload file to server.
            function upload(formData) {
                $.ajax({
                    url : window.location.href, // Current PHP file
                    type : 'POST',
                    data : formData,
                    processData: false,
                    contentType: false,
                    success : function(data) {
                        console.log(data);
                        alert(data);
                        location.reload(); // Reload the page to update the file list
                    }
                });
            }
        });
    </script>
</body>
</html>
