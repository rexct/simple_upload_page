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
    <div id="drop_zone" onclick="document.getElementById('fileInput').click();">Drop files here or click to select</div>
    <input type="file" id="fileInput" style="display: none;" multiple>

    <div id="file_list">
        <h3>Uploaded files:</h3>
        <ul>
            <?php foreach($files as $file): ?>
                <li><a href="<?php echo $destination_path . $file; ?>" download><?php echo $file; ?></a></li>
            <?php endforeach; ?>
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
