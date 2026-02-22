<?php
include ('../../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

// Buscar últimos recibos (ajusta LIMIT como quiseres)
$sql = "SELECT *
        FROM recibos
        ORDER BY id DESC
        LIMIT 200";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Histórico de Recibos</title>
  <style>
  :root{
    --bg: #f3f5f9;
    --card: #ffffff;
    --text: #111827;
    --muted: #6b7280;
    --border: #e5e7eb;
    --shadow: 0 12px 30px rgba(0,0,0,.08);
    --radius: 16px;
  }

  *{ box-sizing:border-box; }
  body{
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    margin: 0;
    padding: 16px;
  }

  /* Topbar */
  .top-icons{
    max-width: 980px;
    margin: 0 auto 12px auto;
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 12px;
    background: rgba(255,255,255,.8);
    backdrop-filter: blur(8px);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: 0 6px 16px rgba(0,0,0,.06);
    position: sticky;
    top: 10px;
    z-index: 10;
  }

  .top-icons a{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid var(--border);
    background: #fff;
    transition: transform .12s ease, box-shadow .12s ease;
  }

  .top-icons a:hover{
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(0,0,0,.08);
  }

  .top-icons img{
    width: 22px;
    height: 22px;
    object-fit: contain;
  }

  /* Card */
  .card{
    max-width: 980px;
    margin: 0 auto;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 16px;
  }

  .top{
    display:flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 8px;
  }

  h2{
    margin: 0;
    font-size: 18px;
    letter-spacing: .2px;
  }

  .muted{
    color: var(--muted);
    font-size: 13px;
    margin-top: 4px;
  }

  /* Search */
  .searchWrap{
    position: relative;
    width: min(420px, 100%);
  }
  .searchWrap input{
    width: 100%;
    padding: 10px 12px 10px 40px;
    border: 1px solid var(--border);
    border-radius: 12px;
    outline: none;
    background: #fff;
    transition: box-shadow .12s ease, border-color .12s ease;
  }
  .searchWrap input:focus{
    border-color: #c7d2fe;
    box-shadow: 0 0 0 4px rgba(99,102,241,.15);
  }
  .searchIcon{
    position:absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    opacity: .55;
  }

  /* Table */
  table{
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 10px;
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 14px;
  }

  thead th{
    background: #f9fafb;
    color: #374151;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 12px 10px;
    border-bottom: 1px solid var(--border);
    position: relative;
  }

  tbody td{
    padding: 12px 10px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
    vertical-align: middle;
  }

  tbody tr:hover{
    background: #fbfdff;
  }

  tbody tr:last-child td{
    border-bottom: none;
  }

  .idCell{ font-weight: 800; }
  .money{ font-weight: 800; }

  /* Buttons */
  .btn{
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    font-weight: 800;
    background: #111827;
    color: #fff;
    transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
  }
  .btn:hover{
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(0,0,0,.10);
  }
  .btn:active{ transform: translateY(0); opacity: .9; }

  .btnView{
    background: #111827;
  }

  /* Empty state */
  .empty{
    padding: 16px;
    color: var(--muted);
    text-align: center;
  }

  /* ✅ Mobile: table -> cards */
  @media (max-width: 720px){
    body{ padding: 12px; }
    thead{ display:none; }

    table{
      border: none;
      border-radius: 0;
      border-spacing: 0;
    }

    tbody tr{
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      padding: 12px;
      margin-bottom: 10px;
      border: 1px solid var(--border);
      border-radius: 14px;
      background: #fff;
      box-shadow: 0 10px 22px rgba(0,0,0,.06);
    }

    tbody td{
      border: none;
      padding: 0;
      font-size: 13px;
    }

    /* labels */
    tbody td::before{
      content: attr(data-label);
      display:block;
      font-size: 11px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: 4px;
    }

    .actions{
      grid-column: 1 / -1;
      margin-top: 6px;
    }
    .btn{ width: 100%; }
  }
</style>

</head>
<body>

<div class="top-icons">
        <div class="top-bar2">
            <a href="../../index.php">
                <img id="cadastro_img" src="../../Pics/gerais/home.png" alt="Home">
            </a>
        </div>

        <div class="top-bar">
            <a href="../../Settings">
                <img id="cadastro_img" src="../../Pics/gerais/settings.png" alt="Cadastro">
            </a>
        </div>
    </div>



  <div class="card">
    <div class="top">
      <div>
        <h2>Histórico de Recibos</h2>
        <div class="muted">Clique em “Ver” para abrir o recibo.</div>
      </div>
      <div class="searchWrap">
  <svg class="searchIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="11" cy="11" r="7"></circle>
    <path d="M21 21l-4.3-4.3"></path>
  </svg>
  <input id="search" type="text" placeholder="Pesquisar por ID, tipo, cliente...">
</div>

    </div>

    <table id="tabela">
      <thead>
        <tr>
          <th>ID</th>
          <th>Data</th>
          <th>Hora</th>
          <th>Cliente</th>
          <th>Total (MT)</th>
          <th>Pago com</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <?php
            $id = (int)$row["id"];
            $data = $row["dataa"] ? date("d/m/Y", strtotime($row["dataa"])) : "";
            $hora = $row["hora"] ? substr($row["hora"], 0, 5) : "";
            $cliente = htmlspecialchars($row["cliente"] ?? "");
            $total = number_format((float)($row["total_pagar"] ?? 0), 2);
            $pago_com = htmlspecialchars($row["pago_com"] ?? "");
          ?>
         <tr>
  <td class="idCell" data-label="ID">#<?= $id ?></td>
  <td data-label="Data"><?= $data ?></td>
  <td data-label="Hora"><?= $hora ?></td>
  <td data-label="Cliente"><?= $cliente ?: "-" ?></td>
  <td class="money" data-label="Total (MT)"><?= $total ?></td>
  <td data-label="Pago com"><?= $pago_com ?></td>
  <td class="actions" data-label="Ações">
    <button class="btn btnView"
      onclick="window.location.href='recibo.php?id=<?= $id ?>'">
      Ver / Imprimir
    </button>
  </td>
</tr>

        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">Sem recibos registados.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

<script>
  // Filtro simples (client-side)
  const search = document.getElementById("search");
  const rows = Array.from(document.querySelectorAll("#tabela tbody tr"));

  search.addEventListener("input", () => {
    const q = search.value.toLowerCase().trim();
    rows.forEach(tr => {
      tr.style.display = tr.textContent.toLowerCase().includes(q) ? "" : "none";
    });
  });
</script>
</body>
</html>
<?php $conn->close(); ?>
