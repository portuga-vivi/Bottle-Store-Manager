<?php
include ('../../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB error"]);
    exit;
}
$conn->set_charset("utf8mb4");

// Filters
$data_inicio = $_GET["inicio"] ?? "";
$data_fim    = $_GET["fim"] ?? "";
$tipo        = trim($_GET["tipo"] ?? "");
$marca       = trim($_GET["marca"] ?? "");

// Query
$sql = "
    SELECT DATE(dataa) as dia,
           SUM(preco_total) as total_valor
    FROM recibo_itens
    WHERE 1=1
";

$types = "";
$params = [];

if ($data_inicio !== "") {
    $sql .= " AND dataa >= ?";
    $types .= "s";
    $params[] = $data_inicio;
}
if ($data_fim !== "") {
    $sql .= " AND dataa <= ?";
    $types .= "s";
    $params[] = $data_fim;
}
if ($tipo !== "") {
    $sql .= " AND tipo LIKE ?";
    $types .= "s";
    $params[] = "%$tipo%";
}
if ($marca !== "") {
    $sql .= " AND marca LIKE ?";
    $types .= "s";
    $params[] = "%$marca%";
}

$sql .= " GROUP BY DATE(dataa) ORDER BY dia ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

$labels = [];
$values = [];

while ($r = $res->fetch_assoc()) {
    $labels[] = $r["dia"];
    $values[] = (float)$r["total_valor"];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
