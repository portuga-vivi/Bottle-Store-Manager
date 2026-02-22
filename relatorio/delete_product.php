<?php
include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erro DB"]);
    exit;
}

$id = (int)($_POST["id"] ?? 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao eliminar produto"]);
}

$stmt->close();
$conn->close();
