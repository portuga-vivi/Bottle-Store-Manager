<?php
/**
 * get_vendas.php
 * Reads sales data from DB and returns JSON (for AJAX / fetch)
 */

header("Content-Type: application/json; charset=utf-8");

// ============================
// DATABASE CONNECTION
// ============================
include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "Database connection failed",
        "details" => $conn->connect_error
    ]);
    exit;
}

$conn->set_charset("utf8mb4");

// ============================
// READ FILTERS (GET)
// ============================
// Examples:
// get_vendas.php
// get_vendas.php?marca=DK
// get_vendas.php?inicio=2025-12-01&fim=2025-12-31

$inicio = $_GET["inicio"] ?? "";   // YYYY-MM-DD
$fim    = $_GET["fim"] ?? "";      // YYYY-MM-DD
$marca  = trim($_GET["marca"] ?? "");

// ============================
// BUILD SQL QUERY (SAFE)
// ============================
$sql = "SELECT id, dataa, marca, quantidade, preco_total FROM vendas WHERE 1=1";
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

if ($marca !== "") {
    $sql .= " AND marca LIKE ?";
    $types .= "s";
    $params[] = "%" . $marca . "%";
}

$sql .= " ORDER BY dataa DESC, id DESC";

// ============================
// PREPARE & EXECUTE
// ============================
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "SQL prepare failed",
        "details" => $conn->error
    ]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// ============================
// PROCESS RESULTS
// ============================
$rows = [];
$total_quantidade = 0;
$total_preco = 0.0;

while ($row = $result->fetch_assoc()) {
    $row["id"] = (int)$row["id"];
    $row["quantidade"] = (int)$row["quantidade"];
    $row["preco_total"] = (float)$row["preco_total"];

    $total_quantidade += $row["quantidade"];
    $total_preco += $row["preco_total"];

    $rows[] = $row;
}

$stmt->close();
$conn->close();

// ============================
// JSON RESPONSE
// ============================
echo json_encode([
    "ok" => true,
    "count" => count($rows),
    "filters" => [
        "inicio" => $inicio,
        "fim"    => $fim,
        "marca"  => $marca
    ],
    "totals" => [
        "quantidade" => $total_quantidade,
        "preco" => $total_preco
    ],
    "rows" => $rows
]);
