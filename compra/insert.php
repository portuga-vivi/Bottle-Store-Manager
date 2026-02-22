<?php
header("Content-Type: application/json; charset=utf-8");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set('Africa/Maputo');
// DB
include ('../ConexaoPHP/db_credentials.php');

try {
    $conn = new mysqli($servername, $username, $password, $database);
    $conn->set_charset("utf8mb4");

    // Recibos (cabeçalho)
    $conn->query("CREATE TABLE IF NOT EXISTS recibos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dataa DATE NOT NULL,
        hora TIME NULL,
        cliente VARCHAR(100) NULL,
        total_produtos DECIMAL(10,2) NOT NULL DEFAULT 0,
        desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_pagar DECIMAL(10,2) NOT NULL DEFAULT 0,
        pago_com VARCHAR(30) NULL,
        recibo VARCHAR(50) NULL unique,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Itens do recibo
    $conn->query("CREATE TABLE IF NOT EXISTS recibo_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recibo_id INT NOT NULL,
        product_id INT NOT NULL DEFAULT 0,   
        dataa DATE NOT NULL,     
        tipo VARCHAR(50),
        marca VARCHAR(100) NOT NULL,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        preco_total DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (recibo_id) REFERENCES recibos(id) ON DELETE CASCADE
    )");

    // Ler JSON
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data["items"]) || !is_array($data["items"])) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Invalid JSON or missing items[]"]);
        exit;
    }

    $tipo = trim($data["tipo"] ?? "");
    if ($tipo === "") {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Missing tipo"]);
        exit;
    }

    // Data d/m/Y -> Y-m-d
    $data_venda = $data["data_venda"] ?? "";
    if ($data_venda === "") {
        $data_mysql = date("Y-m-d");
    } else {
        $dt = DateTime::createFromFormat("d/m/Y", $data_venda);
        if (!$dt) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "Invalid data_venda format. Use d/m/Y."]);
            exit;
        }
        $data_mysql = $dt->format("Y-m-d");
    }

    // Hora (opcional)
    $hora = $data["hora"] ?? date("H:i:s");

    // Campos extras do recibo (podes mandar do JS ou deixar default)
    $cliente     = trim($data["cliente"] ?? "");
    $pago_com    = trim($data["pago_com"] ?? "");
    $desconto    = (float)($data["desconto"] ?? 0);
    $recibo_offline  = trim($data["offline_receipt"] ?? "");

    // Limpar itens + calcular total
    $cleanItems = [];
    $total_produtos = 0.0;

    foreach ($data["items"] as $it) {
        $product_id  = (int)($it["product_id"] ?? 0);
        $marca       = trim($it["marca"] ?? "");
        $quantidade  = (int)($it["quantidade"] ?? 0);
        $preco_total = (float)($it["preco_total"] ?? 0);

        if ($marca === "" || $quantidade <= 0) continue;

        // unitário calculado no momento da venda (para reimpressão fiel)
        $preco_unitario = $quantidade > 0 ? ($preco_total / $quantidade) : 0;

        $cleanItems[] = [
            "product_id" => $product_id,
            "marca" => $marca,
            "quantidade" => $quantidade,
            "preco_unitario" => $preco_unitario,
            "preco_total" => $preco_total
        ];

        $total_produtos += $preco_total;
    }

    if (count($cleanItems) === 0) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "No valid items to insert"]);
        exit;
    }

    $total_pagar = max(0, $total_produtos - $desconto);

    // ===== TRANSACTION =====
    $conn->begin_transaction();

    // 1) Inserir cabeçalho do recibo
    $stmtRec = $conn->prepare("
        INSERT INTO recibos
        (dataa, hora, cliente, total_produtos, desconto, total_pagar, pago_com, recibo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtRec->bind_param(
        "sssdddss",
        $data_mysql, $hora, $cliente, $total_produtos, $desconto, $total_pagar, $pago_com, $recibo_offline
    );
    $stmtRec->execute();
    $recibo_id = $conn->insert_id;
    $stmtRec->close();

    // Statements: vendas, recibo_itens, stock
    //$stmtVenda = $conn->prepare("INSERT INTO vendas (dataa, tipo, marca, quantidade, preco_total) VALUES (?, ?, ?, ?, ?)");
    $stmtItem  = $conn->prepare("INSERT INTO recibo_itens (recibo_id, product_id, dataa, tipo, marca, quantidade, preco_unitario, preco_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtStock = $conn->prepare("SELECT stock FROM products WHERE tipo = ? AND marca = ? LIMIT 1");
    $stmtUpd   = $conn->prepare("UPDATE products SET stock = ? WHERE tipo = ? AND marca = ?");

    $inserted = 0;

    foreach ($cleanItems as $it) {
        $product_id     = $it["product_id"];
        $marca          = $it["marca"];
        $quantidade     = $it["quantidade"];
        $preco_unitario = $it["preco_unitario"];
        $preco_total    = $it["preco_total"];

        // 2) Ver stock antes (para não gravar e depois falhar)
        $stmtStock->bind_param("ss", $tipo, $marca);
        $stmtStock->execute();
        $stmtStock->store_result();

        if ($stmtStock->num_rows === 0) {
            throw new Exception("Produto não encontrado em products: tipo={$tipo}, marca={$marca}");
        }

        $stmtStock->bind_result($stockAtual);
        $stmtStock->fetch();
        $stmtStock->free_result();

        $stockAtual = (int)$stockAtual;
        if ($stockAtual < $quantidade) {
            throw new Exception("Stock insuficiente para {$marca}. Stock={$stockAtual}, solicitado={$quantidade}");
        }

        // 3) Atualizar stock
        $novoStock = $stockAtual - $quantidade;
        $stmtUpd->bind_param("iss", $novoStock, $tipo, $marca);
        $stmtUpd->execute();

        // 4) Guardar item no recibo (reimpressão fiel)
        $stmtItem->bind_param("iisssidd", $recibo_id, $product_id, $data_mysql, $tipo, $marca, $quantidade, $preco_unitario, $preco_total);
        $stmtItem->execute();

        // 5) Guardar também em vendas (se quiseres continuar a usar)
        // (nota: tua tabela vendas.marca é VARCHAR(50) — se marca > 50, vai cortar)
        //$marca_vendas = mb_substr($marca, 0, 50);
       // $stmtVenda->bind_param("sssid", $data_mysql, $tipo, $ $marca_vendas, $quantidade, $preco_total);
       // $stmtVenda->execute();

        $inserted++;
    }

   // $stmtVenda->close();
    $stmtItem->close();
    $stmtStock->close();
    $stmtUpd->close();

    $conn->commit();

    echo json_encode([
        "ok" => true,
        "recibo" => $recibo_id,
        "inserted" => $inserted,
        "total_produtos" => round($total_produtos, 2),
        "desconto" => round($desconto, 2),
        "total_pagar" => round($total_pagar, 2),
        "dataa" => $data_mysql,
        "hora" => $hora
    ]);
    exit;

} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
    exit;
}
