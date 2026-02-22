<?php
include ('../../ConexaoPHP/db_credentials.php');

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Recibo inválido.");

$stmt = $conn->prepare("SELECT * FROM recibos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$rec = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rec) die("Recibo não encontrado.");

$stmt2 = $conn->prepare("SELECT * FROM recibo_itens WHERE recibo_id = ? ORDER BY id ASC");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
$conn->close();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function pad($s, $n) {
  $s = (string)$s;
  return str_pad(mb_substr($s, 0, $n), $n, " ", STR_PAD_RIGHT);
}

$LINE_WIDTH = 36 ;

// ===== Header (igual à imagem) =====
$titleText = "======== RECIBO DE VENDA ========";
$titleLine = str_pad($titleText, $LINE_WIDTH, " ", STR_PAD_BOTH);

$reciboNo = "Nº " . str_pad((string)$rec["id"], 6, "0", STR_PAD_LEFT);
// alinhado à direita
$reciboLine = str_pad($reciboNo, $LINE_WIDTH, " ", STR_PAD_LEFT);


$colNr    = 3;
$colMarca = 15;
$colQde   = 4;
$colPreco = 8;
$colTotal = 12;

$headerCols =
 pad("Nr",    $colNr) .
  pad("Marca", $colMarca) .
  pad("Qde",   $colQde) .
  pad("",2 ) .  
  pad("Preco", $colPreco) .
  pad("Total", $colTotal);

// ===== Items =====
$linesHtml = "";
$i = 1;
foreach ($items as $it) {
  $idx   = str_pad((string)$i, 2, "0", STR_PAD_LEFT);
  $marca = pad($it["marca"] ?? "", 18);
  $qtd   = str_pad((string)($it["quantidade"] ?? 0), 2, " ", STR_PAD_LEFT);
  $times   = "×";
  $unit  = str_pad(number_format((float)($it["preco_unitario"] ?? 0), 2), 7, " ", STR_PAD_LEFT);
  $tot   = str_pad(number_format((float)($it["preco_total"] ?? 0), 2), 8, " ", STR_PAD_LEFT)." MT";

  $linesHtml .= h(
  pad($idx,   $colNr) .
  pad($marca, $colMarca) .
  pad($qtd,   $colQde) .
  pad($times, 2) .  
  pad($unit,  $colPreco) .
  pad($tot,   $colTotal)
) . "\n";

  $i++;
}

$dataFmt = date("d/m/Y", strtotime($rec["dataa"]));
$horaFmt = substr($rec["hora"] ?? "", 0, 5);
$recibo = $rec["recibo"];

?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Recibo #<?php echo (int)$rec["id"]; ?></title>
  <style>
    body{
      font-family: system-ui, Arial, sans-serif;
      background:#f3f3f3;
      margin:0;
      padding:16px;
    }
    .page{ display:flex; justify-content:center; }
    .card{
      width: min(95vw, 560px);
      background:#fff;
      border-radius:16px;
      padding:16px;
      box-shadow: 0 12px 30px rgba(0,0,0,.10);
    }
    .center{ text-align:center; }
    img{ max-width:90px; border-radius:12px; }

    .btns{
      display:flex; gap:10px; justify-content:center;
      margin:12px 0 14px; flex-wrap:wrap;
    }
    button{
      padding:10px 14px; border:0; border-radius:10px; cursor:pointer;
      background:#6c63ff; color:#fff; font-weight:700;
    }
    button.secondary{ background:#e5e7eb; color:#111827; }

    /* ===== Receipt look ===== */
    .receiptCard{
      width: fit-content;
      max-width: 95vw;
      margin: 0 auto;
      border: 1px solid #e6e9f2;
      border-radius: 14px;
      background:#fff;
      box-shadow: 0 10px 28px rgba(0,0,0,.10);
      padding: 12px;
    }

    .mono{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono","Courier New", monospace;
      font-size: 12.5px;
      line-height: 1.35;
      color:#111827;
      white-space: pre;
    }

    .titleLine{ text-align:center; font-weight:800; }
    .numLine{ text-align:right; }
    .numLine .num{ color:#e11d48; font-weight:800; }

   
    

    .receiptBody{
      margin:0;
      overflow-x:auto;
      overflow-y:auto;
      max-height: 65vh;
      padding: 10px;
      border-radius: 12px;
      background:#fbfcff;
      border: 1px solid #eef1f7;
      -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 420px){
      .card{ padding:12px; }
      img{ max-width:76px; }
      .mono{ font-size:11px; line-height:1.28; }
      .receiptBody{ padding:8px; max-height:70vh; }
    }

   @page{
  margin: 0;
}

/* Força cores e fundos */
*{
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
}

@media print{

  /* ❌ Esconde tudo */
  body *{
    visibility: hidden;
  }

  /* ✅ Mostra só o recibo */
  .receiptCard,
  .receiptCard *{
    visibility: visible;
  }

  /* Centraliza o recibo no papel */
  .receiptCard{
    position: absolute;
    left: 50%;
    top: 20mm;
    transform: translateX(-50%);

    margin: 0 !important;
    box-shadow: none !important; /* opcional */
  }

  /* remove scroll no print */
  pre, .receiptBody{
    max-height: none !important;
    overflow: visible !important;
  }
}
.colsHead{
  font-weight: 800;
}
  </style>
</head>
<body>
  <div class="page">
    <div class="card">
      <div class="center">
        <h3>Recibo #<?php echo (int)$rec["id"]; ?></h3>
        <img src="../../Pics/distrito.jpeg" alt="Logo">
      </div>

      <div class="btns">
        <button type="button" onclick="window.print()">Imprimir</button>
        <button type="button" class="secondary" onclick="window.print()">Baixar PDF</button>
        <button type="button" class="secondary" onclick="history.back()">Voltar</button>
      </div>

      <div class="receiptCard">
        <div class="mono titleLine"><?php echo h($titleLine); ?></div>
        <div class="mono numLine"><span class="num"><?php echo h($reciboLine); ?></span></div>

        <div class="receiptBody mono"><?php
          echo h(str_repeat("-", $LINE_WIDTH))."\n";
          echo h("Estab: NO DISTRITO, Bottle Store & Take Away")."\n";
          echo h("Endereço: Namapa, EN1-Passatempo")."\n";
          echo h("Contacto: 869721193")."\n";
          echo h("Data: {$dataFmt}  Hora: {$horaFmt}")."\n\n";
          echo h("Recibo N⁰.: ".($rec["recibo"] ?? ""))."\n\n";
          echo h("Cliente: ".($rec["cliente"] ?? ""))."\n";
          echo h("Descrição dos produtos:")."\n";
          echo h(str_repeat("-", $LINE_WIDTH))."\n";
          echo '<span class="colsHead">'.$headerCols."</span>\n";
          echo h(str_repeat("-", $LINE_WIDTH))."\n";
          echo $linesHtml;
          echo h(str_repeat("-", $LINE_WIDTH))."\n";
          echo h("Valor Total:          ".number_format((float)$rec["total_produtos"],2)." MT")."\n";
          echo h("Desconto:             ".number_format((float)$rec["desconto"],2)." MT")."\n";
          echo h("Valor Total a pagar:  ".number_format((float)$rec["total_pagar"],2)." MT")."\n\n";
          echo h("Pago com: ".($rec["pago_com"] ?? ""))."\n\n";
          echo h("Obrigado pela preferência!")."\n";
          echo h(str_repeat("=", $LINE_WIDTH))."\n";
        ?></div>
      </div>
    </div>
  </div>
</body>
</html>
