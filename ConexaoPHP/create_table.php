<?php
// DATABASE CONNECTION
include ('db_credentials.php');

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
    marca varchar(9),
    preco INT(5),
    link varchar(50), 
    stock INT (5)

)";

if ($conn->query($sql) === TRUE) {
    //echo "<script>alert('Table created!'); </script>";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
