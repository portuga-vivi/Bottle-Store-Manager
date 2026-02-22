<?php
// listar_vendas.php

// DATABASE CONNECTION
include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Erro de ligação: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset("utf8mb4");

// GET FILTERS (optional)
$data_inicio = $_GET["inicio"] ?? ""; // format: YYYY-MM-DD
$data_fim    = $_GET["fim"] ?? "";    // format: YYYY-MM-DD
$tipo       = trim($_GET["tipo"] ?? "");
$marca       = trim($_GET["marca"] ?? "");

// BUILD QUERY
$sql = "SELECT id, dataa, tipo, marca, quantidade, preco_total FROM recibo_itens WHERE 1=1";
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
    $params[] = "%" . $tipo . "%";
}

if ($marca !== "") {
    $sql .= " AND marca LIKE ?";
    $types .= "s";
    $params[] = "%" . $marca . "%";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro no prepare: " . htmlspecialchars($conn->error));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Totals
$total_qtd = 0;
$total_valor = 0.0;

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $total_qtd += (int)$row["quantidade"];
    $total_valor += (float)$row["preco_total"];
}

$stmt->close();




$conn->close();

// ==========================
// EXPORT TO EXCEL (CSV)
// ==========================
// ==========================
// EXPORT NORMAL CSV
// ==========================
if (isset($_GET["export_csv"]) && $_GET["export_csv"] == "1") {

    $filename = "vendas_" . date("Ymd_His") . ".csv";

    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    $out = fopen("php://output", "w");
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
    // Header row
    fputcsv($out, ["ID", "Data", "Tipo", "Marca", "Quantidade", "Preco_Total"]);

    // Data rows
    foreach ($rows as $r) {
        fputcsv($out, [
            $r["id"],
            $r["dataa"],
            $r["tipo"],
            $r["marca"],
            $r["quantidade"],
            $r["preco_total"]
        ]);
    }

    fclose($out);
    exit;
}


?>


<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <title>Vendas</title>
    <style>
        body{font-family: Arial, sans-serif; padding:18px;}
        .wrap{overflow-x:auto;}
        table{border-collapse:collapse; width:100%; min-width:720px;}
        th,td{border:1px solid #ddd; padding:10px; text-align:left;}
        th{background:#f3f4f6;}
        tr:nth-child(even){background:#fafafa;}
        .top{display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin:12px 0 18px;}
        label{font-size:14px; display:block; margin-bottom:6px;}
        input{padding:10px; border:1px solid #ddd; border-radius:8px;}
        button{padding:10px 14px; border:0; border-radius:8px; cursor:pointer;}
        .btn{background:#111827; color:#fff;}
        .btn2{background:#e5e7eb;}
        .totals{margin:12px 0; padding:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px;}
    </style>


    <link rel="stylesheet" href="style2.css">
</head>
<body>

<div class="top-icons">
        <div class="top-bar2">
            <a href="../index.php">
                <img id="cadastro_img" src="../Pics/gerais/home.png" alt="Home">
            </a>
        </div>

        <div class="top-bar">
            <a href="../Settings">
                <img id="cadastro_img" src="../Pics/gerais/settings.png" alt="Cadastro">
            </a>
        </div>
    </div>
<h2>Lista de Vendas</h2>

<form method="GET" class="top">
    <div>
        <label>Início (YYYY-MM-DD)</label>
        <input type="date" name="inicio" value="<?= htmlspecialchars($data_inicio) ?>">
    </div>
    <div>
        <label>Fim (YYYY-MM-DD)</label>
        <input type="date" name="fim" value="<?= htmlspecialchars($data_fim) ?>">
    </div>
    <div>
        <label>Tipo</label>
        <input type="text" name="tipo" placeholder="Ex: Cerveja" value="<?= htmlspecialchars($tipo) ?>">
    </div>
    <div>
        <label>Marca</label>
        <input type="text" name="marca" placeholder="Ex: Manica" value="<?= htmlspecialchars($marca) ?>">
    </div>
    <div>
        <button class="btn" type="submit">Filtrar</button>
        <button class="btn2" type="submit" name="export_csv" value="1">⬇ Exportar CSV</button>

        <a class="btn2" href="index.php" style="text-decoration:none; display:inline-block;">Limpar</a>
    </div>
</form>

<div class="totals">
    <b>Total quantidade:</b> <?= number_format($total_qtd, 0, ",", ".") ?>
    &nbsp; | &nbsp;
    <b>Total valor:</b> <?= number_format($total_valor, 2, ",", ".") ?>
</div>


<a class="btn2"
   href="Grafico/relatorio_graficos.php?<?= http_build_query($_GET) ?>"
   style="text-decoration:none; display:inline-block;">
   📊 Ver Gráficos
</a>

<div class="wrap">
    <?php if (empty($rows)): ?>
        <p>Nenhum registo encontrado.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Quantidade</th>
                <th>Preço Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr class="sale-row" data-id="<?= (int)$r["id"] ?>">
                    <td><?= htmlspecialchars($r["id"]) ?></td>
                    <td><?= htmlspecialchars($r["dataa"]) ?></td>
                    <td><?= htmlspecialchars($r["tipo"]) ?></td>
                    <td><?= htmlspecialchars($r["marca"]) ?></td>
                    <td><?= htmlspecialchars($r["quantidade"]) ?></td>
                    <td><?= number_format((float)$r["preco_total"], 2, ",", ".") ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
let pressTimer;

document.querySelectorAll(".sale-row").forEach(row => {

  // MOBILE: long press
  row.addEventListener("touchstart", () => {
    pressTimer = setTimeout(() => {
      confirmDelete(row);
    }, 600); // 600ms long press
  });

  row.addEventListener("touchend", () => {
    clearTimeout(pressTimer);
  });

  // DESKTOP: right click
  row.addEventListener("contextmenu", e => {
    e.preventDefault();
    confirmDelete(row);
  });

});

function confirmDelete(row){
  const id = row.dataset.id;

  Swal.fire({
    title: "Eliminar registo?",
    text: "Esta ação não pode ser desfeita.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sim, eliminar",
    cancelButtonText: "Cancelar"
  }).then(result => {
    if(result.isConfirmed){
      deleteRow(id, row);
    }
  });
}

function deleteRow(id, row){
  fetch("delete_venda.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      row.remove();
      Swal.fire("Eliminado!", "O registo foi removido.", "success");
    }else{
      Swal.fire("Erro", data.message || "Falha ao eliminar.", "error");
    }
  })
  .catch(() => {
    Swal.fire("Erro", "Erro de ligação ao servidor.", "error");
  });
}
</script>

</body>


</html>
