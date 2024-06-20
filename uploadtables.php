<?php
$servername = "localhost";  // Change this if your MySQL server is hosted elsewhere
$username = "root";         // Change this to your MySQL username
$password = "";             // Change this to your MySQL password
$dbname = "mydatabase";     // Change this to your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create table Files
$sql = "CREATE TABLE Files (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filecontent LONGBLOB NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Files created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    // Check if file was uploaded without errors
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $filename = $_FILES["fileToUpload"]["name"];
        $filetype = $_FILES["fileToUpload"]["type"];
        $filesize = $_FILES["fileToUpload"]["size"];
        $filecontent = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO Files (filename, filecontent) VALUES (?, ?)");
        $stmt->bind_param("sb", $filename, $filecontent);
        $stmt->send_long_data(1, $filecontent);

        // Execute the statement
        if ($stmt->execute()) {
            echo "File uploaded successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $_FILES["fileToUpload"]["error"];
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<body>

<h2>Upload File</h2>
<form action="uploadtables.php" method="post" enctype="multipart/form-data">
  Select file to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload File" name="submit">
</form>

</body>
</html>

