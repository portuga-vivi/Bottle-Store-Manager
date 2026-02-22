<?php

include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// RECEIVE FORM DATA
$dataa    = $_POST['dataa'];
$tipo    = $_POST['tipo'];
$marca   = $_POST['marca'];
$preco = $_POST['preco'];
$linkFile = isset($_FILES['link']) ? $_FILES['link'] : null;
$stock = 0;
$linkpath = '';
$linkpath2 = '';

// Folder where files will be saved
$targetDir = "uploads/";

// Create folder if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}
$targetFile = $targetDir . basename($linkFile["name"]);

if (move_uploaded_file($linkFile["tmp_name"], $targetFile)) {
    $linkPath = "Cadastro/".$targetFile; // é isto que vai para a BD
}
// INSERT INTO DATABASE
$sql = "INSERT INTO products (dataa, tipo, marca, preco, link, stock)
        VALUES ('$dataa', '$tipo', '$marca', '$preco', '$linkPath', '$stock')";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Processing...</title>

    <!-- LOAD SWEETALERT2 HERE -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-->
   <link rel="stylesheet" href="../sweetalert/sweetalert2.min.css">
<script src="../sweetalert/sweetalert2.all.min.js"></script>
</head>
<body>

<?php
if ($conn->query($sql) === TRUE) {
     echo "
    <script>
        Swal.fire({
            title: 'Success!',
            text: 'Record inserted successfully',
            icon: 'success',
            showConfirmButton: true,
            confirmButtonText: 'OK',
        }).then(function() {
            window.location = 'index.html';  // redirect back to form
        });
    </script>";




} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

</body>
</html>