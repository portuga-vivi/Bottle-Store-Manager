<?php

include ('create_table.php');

// DATABASE CONNECTION
include ('db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// RECEIVE FORM DATA
$data    = $_POST['data'];
$money_in   = $_POST['money_in'];
$money_out = $_POST['money_out'];
$money_out_details  = $_POST['money_out_details'];
$balance  = $_POST['balance'];
if(empty($_POST['financial_habit1'])){
    
    $financial_habit1  = "off";}else{
    $financial_habit1  = $_POST['financial_habit1'];
}
if(empty($_POST['financial_habit2'])){
    
    $financial_habit2  = "off";}else{
    $financial_habit2  = $_POST['financial_habit2'];
}
if(empty($_POST['financial_habit3'])){
    
    $financial_habit3  = "off";}else{
    $financial_habit3  = $_POST['financial_habit3'];
}
if(empty($_POST['financial_habit4'])){
    
    $financial_habit4  = "off";}else{
    $financial_habit4  = $_POST['financial_habit4'];
}
if(empty($_POST['financial_habit5'])){
    
    $financial_habit5  = "off";}else{
    $financial_habit5  = $_POST['financial_habit5'];
}
$money_mistake  = $_POST['money_mistake'];
$money_win  = $_POST['money_win'];
$money_win_details  = $_POST['money_win_details'];
$plan_tomorrow  = $_POST['plan_tomorrow'];



// INSERT INTO DATABASE
$sql = "INSERT INTO journal (dataa, money_in, money_out, money_out_details, balance, financial_habit1, financial_habit2,
financial_habit3, financial_habit4, financial_habit5, money_mistake, money_win, money_win_details, plan_tomorrow)
        VALUES ('$data', '$money_in', '$money_out', '$money_out_details', '$balance', '$financial_habit1', '$financial_habit2', 
        '$financial_habit3', '$financial_habit4', '$financial_habit5' , '$money_mistake', '$money_win',
         '$money_win_details', '$plan_tomorrow')";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Processing...</title>

    <!-- LOAD SWEETALERT2 HERE -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-->
   <link rel="stylesheet" href="sweetalert/sweetalert2.min.css">
<script src="sweetalert/sweetalert2.all.min.js"></script>
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
            timer: 2000,
            showConfirmButton: false
        }).then(function() {
            window.location = 'index.php';  // redirect back to form
        });
    </script>";

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

</body>
</html>