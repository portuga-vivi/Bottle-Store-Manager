<?php
// listar_products.php

// DATABASE CONNECTION
include ('../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Erro de ligação: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset("utf8mb4");

// GET FILTERS (optional)
$data_inicio = $_GET["inicio"] ?? ""; // YYYY-MM-DD
$data_fim    = $_GET["fim"] ?? "";    // YYYY-MM-DD
$tipo        = trim($_GET["tipo"] ?? "");
$marca       = trim($_GET["marca"] ?? "");

// BUILD QUERY
$sql = "SELECT id, dataa, tipo, marca, preco, link, stock FROM products WHERE 1=1";
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
$total_produtos = 0;
$total_valor = 0.0;
$total_stock = 0;

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;

    $total_produtos++;
    $total_valor += (float)$row["preco"];
    $total_stock += (int)$row["stock"];
}

$stmt->close();
$conn->close();

// ==========================
// EXPORT CSV (Products)
// ==========================
if (isset($_GET["export_csv"]) && $_GET["export_csv"] == "1") {

    $filename = "products_" . date("Ymd_His") . ".csv";

    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    $out = fopen("php://output", "w");

    // UTF-8 BOM (helps Excel + accents)
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

    // Header row
    fputcsv($out, ["ID","Data","Tipo","Marca","Preco","Stock","Link"]);

    // Data rows
    foreach ($rows as $r) {
        fputcsv($out, [
            $r["id"],
            $r["dataa"],
            $r["tipo"],
            $r["marca"],
            $r["preco"],
            $r["stock"],
            $r["link"]
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
    <title>Produtos</title>
    <style>
        body{font-family: Arial, sans-serif; padding:18px;}
        .wrap{overflow-x:auto;}
        table{border-collapse:collapse; width:100%; min-width:920px;}
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
        a.link{color:#111827; text-decoration:underline;}
        td.num{text-align:right;}
    </style>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style_products.css">

    <link rel="stylesheet" href="../sweetalert/sweetalert2.min.css">
    <script src="../sweetalert/sweetalert2.all.min.js"></script>
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

    
<h2>Lista de Produtos</h2>

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
        <input type="text" name="tipo" placeholder="Ex: semente" value="<?= htmlspecialchars($tipo) ?>">
    </div>
    <div>
        <label>Marca</label>
        <input type="text" name="marca" placeholder="Ex: Manica" value="<?= htmlspecialchars($marca) ?>">
    </div>
    <div>
        <button class="btn" type="submit">Filtrar</button>
        <button class="btn2" type="submit" name="export_csv" value="1">⬇ Exportar CSV</button>
        <button type="button" class="btn2" onclick="deleteLine()">🗑 Delete line</button>


    </div>
</form>

<div class="totals">
    <b>Total produtos:</b> <?= number_format($total_produtos, 0, ",", ".") ?>
    &nbsp; | &nbsp;
    <b>Soma preços:</b> <?= number_format($total_valor, 2, ",", ".") ?>
    &nbsp; | &nbsp;
    <b>Stock total:</b> <?= number_format($total_stock, 0, ",", ".") ?>
</div>

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
                <th>Preço</th>
                <th>Stock</th>
                <th>Link</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr class="product-row" data-id="<?= (int)$r["id"] ?>">
                    <td><?= htmlspecialchars($r["id"]) ?></td>
                    <td><?= htmlspecialchars($r["dataa"]) ?></td>
                    <td><?= htmlspecialchars($r["tipo"]) ?></td>
                    <td><?= htmlspecialchars($r["marca"]) ?></td>
                    <td class="num"><?= number_format((float)$r["preco"], 2, ",", ".") ?></td>
                    <td class="num"><?= htmlspecialchars($r["stock"]) ?></td>
                    <td>
                        <?php
                        $link = "../".$r["link"] ?? "";
                        if ($link === "") {
                            echo "-";
                        } else {
                            $ext = strtolower(pathinfo(parse_url($link, PHP_URL_PATH), PATHINFO_EXTENSION));
                            $isImg = in_array($ext, ["png","jpg","jpeg","gif","webp"]);

                            if ($isImg) {
                                echo '<div class="thumb-wrap">
                    <a href="'.htmlspecialchars($link).'" target="_blank">
                      <img class="thumb" src="'.htmlspecialchars($link).'" alt="img">
                    </a>
                    <a class="link" href="'.htmlspecialchars($link).'" target="_blank">Abrir</a>
                  </div>';
                            } else {
                                echo '<a class="link" href="'.htmlspecialchars($link).'" target="_blank">Abrir</a>';
                            }
                        }
                        ?>
                    </td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="../sweetalert/sweetalert2.all.min.js"></script>


<script src="../sweetalert/sweetalert2.all.min.js"></script>


<script src="../sweetalert/sweetalert2.all.min.js"></script>
<script>
let pressTimer;

document.querySelectorAll(".product-row").forEach(row => {

  // 📱 Mobile: long press
  row.addEventListener("touchstart", () => {
    pressTimer = setTimeout(() => {
      confirmDelete(row);
    }, 600);
  });

  row.addEventListener("touchend", () => {
    clearTimeout(pressTimer);
  });

  // 🖱 Desktop: right click
  row.addEventListener("contextmenu", e => {
    e.preventDefault();
    confirmDelete(row);
  });

});

function confirmDelete(row){
  const id = row.dataset.id;

  Swal.fire({
    title: "Eliminar produto?",
    html: `<b>ID:</b> ${id}<br>Esta ação não pode ser desfeita.`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Sim, eliminar",
    cancelButtonText: "Cancelar"
  }).then(result => {
    if(result.isConfirmed){
      deleteProduct(id, row);
    }
  });
}

function deleteProduct(id, row){
  fetch("delete_product.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "id=" + encodeURIComponent(id)
  })
  .then(r => r.json())
  .then(data => {
    if(data.success){
      row.remove();
      Swal.fire("Eliminado!", "Produto removido com sucesso.", "success");
    }else{
      Swal.fire("Erro", data.message || "Falha ao eliminar.", "error");
    }
  })
  .catch(() => {
    Swal.fire("Erro", "Erro de comunicação com o servidor.", "error");
  });
}
</script>








</body>
</html>