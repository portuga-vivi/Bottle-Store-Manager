<?php

// DATABASE CONNECTION
include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// RECEIVE FORM DATA
$tipo = $_POST['tipo'];
$marca = $_POST['marca'];
$stock = $_POST['stock'];;
$stock_antigo = 0;   
$stock_novo = 0;

$sql_get = "SELECT tipo, marca, stock FROM products";
$result = $conn->query($sql_get);

if($result){

      while($row = $result->fetch_assoc()){

        if($row['tipo'] == $tipo & $row['marca'] == $marca){
$stock_antigo = $row['stock'];
$stock_novo = $stock_antigo + $stock;
        }
      }

      
}

// INSERT INTO DATABASE
$sql = "UPDATE products SET stock = '$stock_novo' WHERE (tipo = '$tipo' AND marca = '$marca')";
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
            text: 'Stock atualizado com sucesso',
            icon: 'success',
            showConfirmButton: true,
            confirmButtonText: 'OK',
        }).then(function() {
            window.location = 'stock.php';  // redirect back to form
        });
    </script>";

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

</body>
</html>