<?php
include ('../../ConexaoPHP/db_credentials.php');

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

<style>
body{font-family:Arial;padding:18px;}
.chart-box{height:380px;}
.btn{
    background:#111827;
    color:#fff;
    padding:10px 14px;
    border-radius:8px;
    text-decoration:none;
    display:inline-block;
    margin-bottom:14px;
}
</style>

<link rel="stylesheet" href="../style2.css">
</head>
<body>

<a class="btn" href="../<?= http_build_query($_GET) ?>">
⬅ Voltar à Lista
</a>


<form id="filters" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <input type="date" name="inicio">
    <input type="date" name="fim">
    <input type="text" name="tipo" placeholder="Tipo">
    <input type="text" name="marca" placeholder="Marca">
    <button type="submit" class="btn">Aplicar</button>
</form>


<h2>📈 Evolução de Vendas</h2>

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
