<?php
header("Content-Type: application/json; charset=utf-8");

// DATABASE CONNECTION
include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $conn->connect_error]);
    exit;
}
$conn->set_charset("utf8mb4");

// FILTERS (optional)
// Examples:
// get_products.php
// get_products.php?marca=DK
// get_products.php?tipo=semente
// get_products.php?inicio=2025-12-01&fim=2025-12-31&marca=DK
$inicio = $_GET["inicio"] ?? ""; // YYYY-MM-DD
$fim    = $_GET["fim"] ?? "";    // YYYY-MM-DD
$tipo   = trim($_GET["tipo"] ?? "");
$marca  = trim($_GET["marca"] ?? "");

// BUILD QUERY (SAFE)
$sql = "SELECT id, dataa, tipo, marca, preco, link, stock FROM products WHERE 1=1";
$types = "";
$params = [];

if ($inicio !== "") {
    $sql .= " AND dataa >= ?";
    $types .= "s";
    $params[] = $inicio;
}
if ($fim !== "") {
    $sql .= " AND dataa <= ?";
    $types .= "s";
    $params[] = $fim;
}
if ($tipo !== "") {
    $sql .= " AND tipo LIKE ?";
    $types .= "s";
    $params[] = "%".$tipo."%";
}
if ($marca !== "") {
    $sql .= " AND marca LIKE ?";
    $types .= "s";
    $params[] = "%".$marca."%";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $conn->error]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$count = 0;

while ($row = $result->fetch_assoc()) {
    // Cast types
    $row["id"] = (int)$row["id"];
    $row["preco"] = (float)$row["preco"];
    $row["stock"] = (int)$row["stock"];

    $rows[] = $row;
    $count++;
}

$stmt->close();
$conn->close();

echo json_encode([
    "ok" => true,
    "count" => $count,
    "filters" => [
        "inicio" => $inicio,
        "fim"    => $fim,
        "tipo"   => $tipo,
        "marca"  => $marca
    ],
    "rows" => $rows
]);
