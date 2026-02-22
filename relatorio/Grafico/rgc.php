<?php

require_once "../../ConexaoPHP/db.php";  // ✅ CONNECT FIRST
// 🔁 Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json; charset=utf-8");

    $value = $_POST['value'] ?? '';

    // Here you can save to DB / file / session
    echo json_encode([
        "ok" => true,
        "received" => $value
    ]);

    
    exit; // IMPORTANT: stop here for AJAX

    echo "<script>alert('$value')</script>";
}


$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Erro de ligação");
}
$conn->set_charset("utf8mb4");

// Filters
$data_inicio = $_GET["inicio"] ?? "";
$data_fim    = $_GET["fim"] ?? "";
$tipo        = trim($_GET["tipo"] ?? "");
$marca       = trim($_GET["marca"] ?? "");

// ==========================
// LINE CHART DATA
// ==========================
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
?>
<!doctype html>
<html lang="pt">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Relatório de Gráficos</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const allProducts = <?php
  $sql = "SELECT tipo, marca FROM products ORDER BY id DESC";
  $res = $conn->query($sql);
  $arr = [];
  while($row = $res->fetch_assoc()){
    $arr[] = $row;
  }
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
?>;
</script>

<style>
body{font-family:Arial; padding:18px;}
.chart-box{height:380px;}
.btn{background:#111827;color:#fff;padding:10px 14px;border-radius:8px;text-decoration:none;}
</style>
</head>

<body>

<a class="btn" href="../<?= http_build_query($_GET) ?>">
⬅ Voltar às Vendas
</a>


<form id="filters" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <input type="date" name="inicio">
    <input type="date" name="fim">

    

    <input type="text" name="marca" placeholder="Marca">
    <button type="submit" class="btn">Aplicar</button>
</form>



<h2>📊 Relatório de Vendas</h2>

<div class="chart-box">
    <canvas id="salesChart"></canvas>
</div>

<script>
let chart;

async function loadChart(params = {}) {
    const query = new URLSearchParams(params).toString();
    const res = await fetch("ajax_graficos_vendas.php?" + query);
    const data = await res.json();

    if (!chart) {
        chart = new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Total de Vendas',
                    data: data.values,
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    } else {
        chart.data.labels = data.labels;
        chart.data.datasets[0].data = data.values;
        chart.update();
    }
}

// Initial load
loadChart();

// Filter submit
document.getElementById('filters').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    loadChart(Object.fromEntries(formData.entries()));
});
</script>


</body>
</html>
