<?php
// DATABASE CONNECTION
include ('../ConexaoPHP/db_credentials.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dataa varchar(100),
    tipo VARCHAR(20),
    marca varchar(50),
    preco decimal(5.2),
    link varchar(100),
    stock int(5)

)";

if ($conn->query($sql) === TRUE) {
    //echo "<script>alert('Table created!'); </script>";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
