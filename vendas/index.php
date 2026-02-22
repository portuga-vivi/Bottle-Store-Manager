<?php require_once "../ConexaoPHP/db.php"; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Gestão Comercial</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/cerveja.css">
  
  <link rel="stylesheet" href="style.css">

  <link rel="stylesheet" href="../sweetalert/sweetalert2.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <script src="../sweetalert/sweetalert2.all.min.js"></script>

  <script>
    // ========= UUID SAFE (Android old) =========
    function safeUUID() {
      if (window.crypto && typeof crypto.randomUUID === "function") {
        return crypto.randomUUID();
      }
      return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === "x" ? r : (r & 0x3 | 0x8);
        return v.toString(16);
      });
    }

    function getDeviceId() {
      let id = localStorage.getItem("device_id");
      if (!id) {
        id = safeUUID().slice(0, 8);
        localStorage.setItem("device_id", id);
      }
      return id;
    }
  </script>

  <link rel="manifest" href="../manifest.json">
  <meta name="theme-color" content="#111827">

 
</head>

<body id="top">
  <header class="top-header">
  <div class="header-inner">
    <div class="menu-toggle" id="menuToggle">☰</div>

    
    <a href="../" class="brand">
  <i class="fa-solid fa-house"></i>
  <h1 class="logo">NO <span>DISTRITO</span></h1>
</a>

    
    <nav id="navMenu">

    

  <a href="#cervejas">
    <i class="fa-solid fa-beer-mug-empty"></i>
    <span>Cervejas</span>
  </a>

  <a href="#cidras">
    <i class="fa-solid fa-apple-whole"></i>
    <span>Cidras</span>
  </a>

  <a href="#secas">
    <i class="fa-solid fa-wine-bottle"></i>
    <span>Secas</span>
  </a>

  <a href="#vinhos">
    <i class="fa-solid fa-champagne-glasses"></i>
    <span>Vinhos</span>
  </a>

  <a href="#outras_bebidas">
    <i class="fa-solid fa-bottle-water"></i>
    <span>Outras Bebidas</span>
  </a>

  <a href="#outros_produtos">
    <i class="fa-solid fa-cookie"></i>
    <span>Outros Produtos</span>
  </a>
</nav>

    </nav>
  </div>
</header>
<div class="container">

  <div class="header">
    
    <div class="pendingWrap" title="Vendas guardadas offline">
      <span>Offline</span>
      <span class="pendingBadge" id="pendingCount">0</span>
    </div>

    <button onclick="openDialog()" class="cartBtn" aria-label="Open cart">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
        <g id="cartGroup" fill="none" stroke="currentColor" stroke-width="3.2"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 10h7l6 30h30l6-20H18"/>
          <path d="M24 20h34"/>
          <path d="M22 28h34"/>
          <path d="M20 36h34"/>
          <circle cx="26" cy="52" r="4.5"/>
          <circle cx="48" cy="52" r="4.5"/>
          <path d="M21 44h33"/>
        </g>
      </svg>
      <span class="cartCount" id="cartCount">0</span>
    </button>
  </div>

  <h1>Selecione o que deseja vender</h1>
  <h2 id="today"></h2>

  <script>
    const d = new Date();
    const formattedDate =
      String(d.getDate()).padStart(2,"0") + "/" +
      String(d.getMonth()+1).padStart(2,"0") + "/" +
      d.getFullYear();
    document.getElementById("today").textContent = "Data: " + formattedDate;
  </script>

<section id="cervejas">
  <?php
    $sql = "SELECT id, dataa, tipo, marca, preco, link, stock
            FROM products
            WHERE tipo = 'CERVEJAS'
            ORDER BY id DESC";
    $result = $conn->query($sql);
  ?>

  <h2 class="section-title">CERVEJAS</h2>
  <div class="image-row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="image-box"
          onclick="toggleProduct(this)"
          data-id="<?= (int)$row['id']; ?>"
          data-preco="<?= htmlspecialchars($row['preco']); ?>"
          data-tipo="<?= htmlspecialchars($row['tipo']); ?>"
          data-marca="<?= htmlspecialchars($row['marca']); ?>"
          data-link="<?= htmlspecialchars($row['link']); ?>"
          data-stock="<?= (int)$row['stock']; ?>">

        <img class="base" src="../<?= htmlspecialchars($row["link"]); ?>" alt="<?= htmlspecialchars($row["marca"]); ?>">

        <div class="productPrice">
          <span class="priceValue"><?= number_format((float)$row["preco"], 2); ?></span>
          <span class="priceCurrency">MT</span>
        </div>

        <img src="../Cadastro/uploads/check.png" class="overlay" alt="Selecionado">
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
  </div></section>

  <section id="cidras">
  <?php

    $sql = "SELECT id, dataa, tipo, marca, preco, link, stock
            FROM products
            WHERE tipo = 'CIDRAS'
            ORDER BY id DESC";
    $result = $conn->query($sql);
  ?>

  <h2 class="section-title">CIDRAS</h2>
  <div class="image-row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="image-box"
          onclick="toggleProduct(this)"
          data-id="<?= (int)$row['id']; ?>"
          data-preco="<?= htmlspecialchars($row['preco']); ?>"
          data-tipo="<?= htmlspecialchars($row['tipo']); ?>"
          data-marca="<?= htmlspecialchars($row['marca']); ?>"
          data-link="<?= htmlspecialchars($row['link']); ?>"
          data-stock="<?= (int)$row['stock']; ?>">

        <img class="base" src="../<?= htmlspecialchars($row["link"]); ?>" alt="<?= htmlspecialchars($row["marca"]); ?>">

        <div class="productPrice">
          <span class="priceValue"><?= number_format((float)$row["preco"], 2); ?></span>
          <span class="priceCurrency">MT</span>
        </div>

        <img src="../Cadastro/uploads/check.png" class="overlay" alt="Selecionado">
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
  </div></section>

  <section id="secas">
  <?php

    $sql = "SELECT id, dataa, tipo, marca, preco, link, stock
            FROM products
            WHERE tipo = 'SECAS'
            ORDER BY id DESC";
    $result = $conn->query($sql);
  ?>

  <h3 class="section-title">SECAS</h3>
  <div class="image-row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="image-box"
          onclick="toggleProduct(this)"
          data-id="<?= (int)$row['id']; ?>"
          data-preco="<?= htmlspecialchars($row['preco']); ?>"
          data-tipo="<?= htmlspecialchars($row['tipo']); ?>"
          data-marca="<?= htmlspecialchars($row['marca']); ?>"
          data-link="<?= htmlspecialchars($row['link']); ?>"
          data-stock="<?= (int)$row['stock']; ?>">

        <img class="base" src="../<?= htmlspecialchars($row["link"]); ?>" alt="<?= htmlspecialchars($row["marca"]); ?>">

        <div class="productPrice">
          <span class="priceValue"><?= number_format((float)$row["preco"], 2); ?></span>
          <span class="priceCurrency">MT</span>
        </div>

        <img src="../Cadastro/uploads/check.png" class="overlay" alt="Selecionado">
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
  </div></section>

  <section id="vinhos">
  <?php

    $sql = "SELECT id, dataa, tipo, marca, preco, link, stock
            FROM products
            WHERE tipo = 'VINHOS'
            ORDER BY id DESC";
    $result = $conn->query($sql);
  ?>

  <h2 class="section-title">VINHOS</h2>
  <div class="image-row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="image-box"
          onclick="toggleProduct(this)"
          data-id="<?= (int)$row['id']; ?>"
          data-preco="<?= htmlspecialchars($row['preco']); ?>"
          data-tipo="<?= htmlspecialchars($row['tipo']); ?>"
          data-marca="<?= htmlspecialchars($row['marca']); ?>"
          data-link="<?= htmlspecialchars($row['link']); ?>"
          data-stock="<?= (int)$row['stock']; ?>">

        <img class="base" src="../<?= htmlspecialchars($row["link"]); ?>" alt="<?= htmlspecialchars($row["marca"]); ?>">

        <div class="productPrice">
          <span class="priceValue"><?= number_format((float)$row["preco"], 2); ?></span>
          <span class="priceCurrency">MT</span>
        </div>

        <img src="../Cadastro/uploads/check.png" class="overlay" alt="Selecionado">
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
  </div></section>

  <section id="outras_bebidas">
  <?php

    $sql = "SELECT id, dataa, tipo, marca, preco, link, stock
            FROM products
            WHERE tipo = 'OUTRAS BEBIDAS'
            ORDER BY id DESC";
    $result = $conn->query($sql);
  ?>

  <h2 class="section-title">OUTRAS BEBIDAS</h2>
  <div class="image-row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="image-box"
          onclick="toggleProduct(this)"
          data-id="<?= (int)$row['id']; ?>"
          data-preco="<?= htmlspecialchars($row['preco']); ?>"
          data-tipo="<?= htmlspecialchars($row['tipo']); ?>"
          data-marca="<?= htmlspecialchars($row['marca']); ?>"
          data-link="<?= htmlspecialchars($row['link']); ?>"
          data-stock="<?= (int)$row['stock']; ?>">

        <img class="base" src="../<?= htmlspecialchars($row["link"]); ?>" alt="<?= htmlspecialchars($row["marca"]); ?>">

        <div class="productPrice">
          <span class="priceValue"><?= number_format((float)$row["preco"], 2); ?></span>
          <span class="priceCurrency">MT</span>
        </div>

        <img src="../Cadastro/uploads/check.png" class="overlay" alt="Selecionado">
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
  </div></section>

  
  <section id="outros_produtos">
  <?php

    $sql = "SELECT id, dataa, tipo, marca, preco, link, stock
            FROM products
            WHERE tipo = 'OUTROS PRODUTOS'
            ORDER BY id DESC";
    $result = $conn->query($sql);
  ?>

  <h2 class="section-title">OUTROS PRODUTOS</h2>
  <div class="image-row">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="image-box"
          onclick="toggleProduct(this)"
          data-id="<?= (int)$row['id']; ?>"
          data-preco="<?= htmlspecialchars($row['preco']); ?>"
          data-tipo="<?= htmlspecialchars($row['tipo']); ?>"
          data-marca="<?= htmlspecialchars($row['marca']); ?>"
          data-link="<?= htmlspecialchars($row['link']); ?>"
          data-stock="<?= (int)$row['stock']; ?>">

        <img class="base" src="../<?= htmlspecialchars($row["link"]); ?>" alt="<?= htmlspecialchars($row["marca"]); ?>">

        <div class="productPrice">
          <span class="priceValue"><?= number_format((float)$row["preco"], 2); ?></span>
          <span class="priceCurrency">MT</span>
        </div>

        <img src="../Cadastro/uploads/check.png" class="overlay" alt="Selecionado">
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
  </div></section>

  <div class="footer">
    No Distrito, o lugar perfeito para Espairecer.
  </div>

</div>

<script>
/* ==========================================================
   1) OFFLINE QUEUE (IndexedDB)
   ========================================================== */
const DB_NAME = "no_distrito_sales_db";
const DB_VER = 1;
const STORE = "sales_queue";

function openDB() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open(DB_NAME, DB_VER);

    req.onupgradeneeded = () => {
      const db = req.result;
      if (!db.objectStoreNames.contains(STORE)) {
        db.createObjectStore(STORE, { keyPath: "uuid" });
      }
    };

    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

function makeUUID() {
  return safeUUID();
}

function makeOfflineReceiptNo() {
  const d = new Date();

  const h = String(d.getHours()).padStart(2, "0");
  const min = String(d.getMinutes()).padStart(2, "0");
  const s = String(d.getSeconds()).padStart(2, "0");

  const timeKey = `${h}${min}${s}`;

  const deviceId = getDeviceId();
  const key = `offline_seq_${deviceId}_${timeKey}`;

  const seq = (Number(localStorage.getItem(key)) || 0) + 1;
  localStorage.setItem(key, String(seq));

  return `OFF-${timeKey}-${deviceId}-${String(seq).padStart(3, "0")}`;
}


async function getQueuedSales() {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction(STORE, "readonly");
    const req = tx.objectStore(STORE).getAll();
    req.onsuccess = () => resolve(req.result || []);
    req.onerror = () => reject(req.error);
  });
}

async function deleteQueuedSale(uuid) {
  const db = await openDB();
  await new Promise((resolve, reject) => {
    const tx = db.transaction(STORE, "readwrite");
    tx.objectStore(STORE).delete(uuid);
    tx.oncomplete = () => resolve(true);
    tx.onerror = () => reject(tx.error);
  });
}

async function markQueuedSaleSending(uuid){
  const db = await openDB();
  const tx = db.transaction(STORE, "readwrite");
  const store = tx.objectStore(STORE);

  const row = await new Promise((resolve, reject) => {
    const req = store.get(uuid);
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });

  if (!row) return;
  row.status = "sending";
  row.sending_at = Date.now();

  await new Promise((resolve, reject) => {
    const req = store.put(row);
    req.onsuccess = () => resolve(true);
    req.onerror = () => reject(req.error);
  });
}

async function markQueuedSalePending(uuid){
  const db = await openDB();
  const tx = db.transaction(STORE, "readwrite");
  const store = tx.objectStore(STORE);

  const row = await new Promise((resolve, reject) => {
    const req = store.get(uuid);
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });

  if (!row) return;
  row.status = "pending";
  delete row.sending_at;

  await new Promise((resolve, reject) => {
    const req = store.put(row);
    req.onsuccess = () => resolve(true);
    req.onerror = () => reject(req.error);
  });
}

// ✅ simple dedupe in local queue by offline_receipt (prevents double queue)
async function existsQueuedReceipt(receiptNo){
  const list = await getQueuedSales().catch(()=>[]);
  return list.some(r => r?.payload?.offline_receipt === receiptNo);
}

async function queueSaleLocally(payload) {
  // ✅ avoid duplicate queue rows for same receipt
  if (payload?.offline_receipt && await existsQueuedReceipt(payload.offline_receipt)) {
    updatePendingBadge();
    return;
  }

  const db = await openDB();
  await new Promise((resolve, reject) => {
    const tx = db.transaction(STORE, "readwrite");
    const store = tx.objectStore(STORE);

    store.put({
      uuid: makeUUID(),
      payload,
      created_at: Date.now(),
      status: "pending"
    });

    tx.oncomplete = () => resolve(true);
    tx.onerror = () => reject(tx.error);
  });

  updatePendingBadge();
}

/* ==========================================================
   2) OFFLINE CHIP / BADGE (FAST + SAFE)
   ========================================================== */
function renderOfflineInstant(){
  const wrap  = document.querySelector(".pendingWrap");
  const badge = document.getElementById("pendingCount");
  if (!wrap || !badge) return;

  if (navigator.onLine === false) {
    wrap.style.display = "flex";
    badge.style.display = "none";
  }
}

async function updatePendingBadge(){
  const wrap  = document.querySelector(".pendingWrap");
  const badge = document.getElementById("pendingCount");
  if (!wrap || !badge) return;

  renderOfflineInstant();

  try {
    const list = await getQueuedSales();
    const n = Array.isArray(list) ? list.length : 0;

    badge.textContent = String(n);
    badge.style.display = (n > 0) ? "inline-block" : "none";

    const offline = (navigator.onLine === false);
    wrap.style.display = (offline || n > 0) ? "flex" : "none";
  } catch (err) {
    console.warn("[BADGE] updatePendingBadge failed:", err);
    const offline = (navigator.onLine === false);
    badge.textContent = "0";
    badge.style.display = "none";
    wrap.style.display = offline ? "flex" : "none";
  }
}

/* ==========================================================
   3) SYNC (LOCK + LESS SPAM)
   ========================================================== */
let SYNCING = false;

async function syncQueuedSales() {
  if (!navigator.onLine) return;
  if (SYNCING) return;
  SYNCING = true;

  try {
    const list = await getQueuedSales();
    if (!list.length) return;

    for (const row of list) {
      if (row.status === "sending" && Date.now() - (row.sending_at || 0) < 15000) continue;

      await markQueuedSaleSending(row.uuid);

      const r = await fetch("compra/insert.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(row.payload)
      });

      const raw = await r.text();
      let data = null;
      try { data = JSON.parse(raw); } catch { data = null; }

      // ✅ delete ONLY if server confirms ok or duplicate
      if (r.ok && data && (data.ok || data.duplicate)) {
        await deleteQueuedSale(row.uuid);
      } else {
        await markQueuedSalePending(row.uuid);
        return;
      }
    }
  } catch (e) {
    console.warn("[SYNC] error:", e);
  } finally {
    SYNCING = false;
    await updatePendingBadge();
  }
}

/* ==========================================================
   4) CART
   ========================================================== */
const SVG_NS = "http://www.w3.org/2000/svg";
const cartGroup = document.getElementById("cartGroup");
const cartCount = document.getElementById("cartCount");
const cart = new Map(); // id -> {id, marca, preco, link, stock}

function addItemsToCartIcon(){
  if (cartGroup.querySelector(".cartItems")) return;
  const itemsGroup = document.createElementNS(SVG_NS, "g");
  itemsGroup.setAttribute("class","cartItems");

  const elements = [
    { tag:"rect", attrs:{ x:26, y:16, width:6, height:8, rx:1 } },
    { tag:"rect", attrs:{ x:34, y:14, width:7, height:10, rx:1 } },
    { tag:"rect", attrs:{ x:44, y:18, width:6, height:6, rx:1 } },
    { tag:"circle", attrs:{ cx:31, cy:30, r:3 } },
    { tag:"circle", attrs:{ cx:40, cy:30, r:3 } },
    { tag:"circle", attrs:{ cx:49, cy:30, r:3 } }
  ];

  elements.forEach(el=>{
    const svgEl = document.createElementNS(SVG_NS, el.tag);
    Object.keys(el.attrs).forEach(k=> svgEl.setAttribute(k, el.attrs[k]));
    itemsGroup.appendChild(svgEl);
  });

  cartGroup.appendChild(itemsGroup);
}

function removeItemsFromCartIcon(){
  const items = cartGroup.querySelector(".cartItems");
  if (items) items.remove();
}

function updateCartBadge(){
  const n = cart.size;
  if (n > 0){
    cartCount.textContent = n;
    cartCount.style.display = "block";
    addItemsToCartIcon();
  } else {
    cartCount.style.display = "none";
    removeItemsFromCartIcon();
  }
}

function toggleProduct(el){
  const id = el.dataset.id;
  const tipo = el.dataset.tipo;
  const marca = el.dataset.marca;
  const preco = Number(el.dataset.preco) || 0;
  const link = el.dataset.link;
  const stock = Number(el.dataset.stock) || 0;

  const overlay = el.querySelector(".overlay");

  if (!cart.has(id)){
    cart.set(id, {id, marca, tipo, preco, link, stock});
    overlay.style.display = "block";
  } else {
    cart.delete(id);
    overlay.style.display = "none";
  }

  updateCartBadge();
}

function resetCart(){
  cart.clear();
  updateCartBadge();
  document.querySelectorAll(".overlay").forEach(o=> o.style.display = "none");
}

/* ==========================================================
   5) RECEIPT
   ========================================================== */
function escapeHtml(str){
  return String(str ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function buildReceipt(items, opts = {}){
  const estab = opts.estab ?? "NO DISTRITO BAR";
  const recibo = opts.recibo ?? ">";
  const data = opts.data ?? formattedDate;

  const now = new Date();
  const hora = opts.hora ?? now.toLocaleTimeString([], {hour:"2-digit", minute:"2-digit"});

  const cliente = opts.cliente ?? "______________________________";
  const pagoCom = opts.pagoCom ?? "_______________________";
  const desconto = Number(opts.desconto ?? 0);

  const totalProdutos = items.reduce((s,it)=> s + (Number(it.preco_total)||0), 0);
  const totalPagar = Math.max(0, totalProdutos - desconto);

  const money = (n) => (Number(n)||0).toFixed(2);
  const cut = (txt, max) => {
    const s = String(txt ?? "");
    return s.length > max ? (s.slice(0, max-1) + "…") : s;
  };
  const padEnd = (s, n) => String(s).padEnd ? String(s).padEnd(n, " ") : (String(s) + " ".repeat(Math.max(0,n-String(s).length)));
  const padStart = (s, n) => String(s).padStart ? String(s).padStart(n, " ") : (" ".repeat(Math.max(0,n-String(s).length)) + String(s));

  const lineWidth = 36;
  const sep = "-".repeat(lineWidth);

  const COL_NR    = 3;
  const COL_MARCA = 15;
  const COL_QDE   = 4;
  const COL_PRECO = 8;
  const COL_TOTAL = 8;

  const headerCols =
    padEnd("Nr",    COL_NR) +
    padEnd("Marca", COL_MARCA) +
    padEnd("Qde",   COL_QDE) +
    padEnd("",   2) +
    padEnd("Preço", COL_PRECO) +
    padEnd("Total", COL_TOTAL);

  let lines2 = "";
  items.forEach((it, i) => {
    const nr   = padEnd(String(i + 1).padStart(2, "0"), COL_NR);
    const marca = padEnd(cut(it.marca, COL_MARCA), COL_MARCA);

    const qdeNum = Math.max(1, Number(it.quantidade) || 1);
    const qde    = padEnd(String(qdeNum), COL_QDE);
    const st     = padEnd("×", 2);

    const total  = Number(it.preco_total) || 0;
    const unit   = total / qdeNum;

    const preco  = padEnd(money(unit), COL_PRECO);
    const totStr = padEnd(money(total), COL_TOTAL);

    lines2 += nr + marca + qde + st + preco + totStr + "\n";
  });

  const lines = lines2.length ? lines2.slice(0, -1) : "";

  const totalProdutosStr = padStart(money(totalProdutos), 10);
  const descontoStr      = padStart(money(desconto), 10);
  const totalPagarStr    = padStart(money(totalPagar), 10);

  return (
`======== RECIBO DE VENDA ========

${sep}
Estab: ${estab}
Endereço: Namapa, EN1-Passatempo
Contacto: 869721193
Data: ${data}  Hora: ${hora}

Recibo N⁰.: ${recibo}

Cliente: ${cliente}
Descrição dos produtos:
${sep}
${headerCols}
${sep}
${lines}
${sep}
Valor Total        : ${totalProdutosStr} MT
Desconto:            ${descontoStr} MT
Valor Total a pagar: ${totalPagarStr} MT

Pago com: ${pagoCom}

Obrigado pela preferência!
====================================`
  );
}

function showReceiptSwal(receiptNo, items, opts = {}) {
  const isOffline = !!opts.offline;
  const title = isOffline ? "Venda registada (offline) ✅" : "Venda registada com sucesso! ✅";

  const subtitle = isOffline
    ? `<div style="margin-top:6px;font-size:13px;color:#6b7280;">
         Vai sincronizar automaticamente quando houver internet.
       </div>`
    : "";

  const pagoLabel = (opts.pagoCom || "_______________________");
  const receiptText = buildReceipt(items, {
    estab: "NO DISTRITO, Bottle Store & Take Away",
    recibo: receiptNo || "______",
    data: formattedDate,
    pagoCom: pagoLabel,
    cliente: opts.cliente || "______________________________"
  });

  const linesArr = receiptText.split("\n");
  const receiptHtml = linesArr.map(line => {
    if (line.includes("RECIBO DE VENDA")) {
      return `<span class="colsTitle">${escapeHtml(line)}</span>`;
    }
    if (line.trimStart().startsWith("Nr") && line.includes("Marca") && line.includes("Preço")) {
      return `<span class="colsHead">${escapeHtml(line)}</span>`;
    }
    return escapeHtml(line);
  }).join("\n");

  Swal.fire({
    icon: "success",
    title,
    customClass: { popup: "receipt-popup", htmlContainer: "receipt-html" },
    html: `
      ${subtitle}
      <div class="receiptWrap">
        <div class="receiptHeader">
          <img src="../Pics/distrito.jpeg" alt="Logo" class="receiptLogo">
        </div>
        <div class="receiptCard">
          <pre class="receiptPre">${receiptHtml}</pre>
        </div>
      </div>
    `,
    showConfirmButton: true,
    confirmButtonText: "OK"
  });
}

/* ==========================================================
   6) DIALOG + PAGAMENTO + SUBMIT
   ========================================================== */
function normalizePayKey(val){
  const v = String(val || "").toUpperCase();
  if (!v) return "";
  if (v.includes("CARTEIRA")) return "mpesa";
  if (v === "POS") return "pos";
  if (v.includes("DINHEIRO")) return "dinheiro";
  if (v.includes("DÍVIDA") || v.includes("DIVIDA")) return "divida";
  return "";
}

function openDialog(){
  if (cart.size === 0){
    Swal.fire({icon:"info", title:"Carrinho vazio", text:"Selecione produtos primeiro."});
    return;
  }

  const semStock = [...cart.values()].filter(it => (Number(it.stock) || 0) < 1);
  if (semStock.length > 0){
    Swal.fire({
      icon: "error",
      title: "Stock insuficiente",
      html: semStock.map(it => `• ${it.marca} (stock: ${it.stock})`).join("<br>"),
      confirmButtonText: "OK"
    });
    return;
  }

  let htmlContent = "";

  [...cart.values()].forEach(item=>{
    htmlContent += `
      <div class="itemRow" data-id="${item.id}" data-tipo="${item.tipo}" data-marca="${item.marca}" data-unit-price="${item.preco}">
        <img src="../${item.link}" class="itemImg" alt="${item.marca}">

        <div class="qtyBox">
          <button type="button" class="qty-minus">−</button>
          <input class="swal-qty" type="number" value="1" min="1">
          <button type="button" class="qty-plus">＋</button>
        </div>

        <div class="priceBox">
          <span class="currency">MT</span>
          <input class="swal-price" type="number" value="${Number(item.preco).toFixed(2)}" readonly>
        </div>
      </div>
    `;
  });

  htmlContent += `
    <div class="payField">
      <div class="payControl">
        <span class="payIcon" id="payIcon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M3.5 7.5h15a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-13a2.5 2.5 0 0 1-2.5-2.5V7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M16.5 12.5h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M3.5 7.5V6.8A2.8 2.8 0 0 1 6.3 4h9.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </span>

        <select id="pagamento" class="select-modern">
          <option value="" selected disabled>Selecione o tipo de Pagamento</option>
          <option value="DINHEIRO FÍSICO">Dinheiro físico</option>
          <option value="CARTEIRA MÓVEL">Carteira Móvel</option>
          <option value="POS">POS</option>
          <option value="DÍVIDA">Dívida</option>
        </select>
      </div>
      <p class="payHint">Escolha como o cliente pagou para registar corretamente.</p>
    </div>

    <div class="debtField" id="debtField" style="display:none;">
      <input id="clienteNome" class="debtInput" type="text" placeholder="Nome do Cliente (Obrigatório)">
    </div>

    <style>
      .swal2-popup{ width: min(92vw, 680px) !important; padding: 14px !important; }
      .swal2-title{ font-size: 18px !important; line-height: 1.2 !important; }
      .swal2-html-container{ margin: 10px 0 0 !important; max-height: 65vh; overflow: auto; }

      .itemRow{
        display:grid;
        grid-template-columns: 80px 1fr 1fr;
        align-items:center;
        gap:12px;
        padding:10px;
        margin-bottom:10px;
        border-radius:10px;
        background:#f9f9f9;
        border:1px solid #e0e0e0;
      }
      .itemImg{
        width:70px; height:110px; object-fit:contain;
        background:#fff; border-radius:6px; border:1px solid #ddd;
      }
      .qtyBox{ display:flex; align-items:center; gap:8px; justify-content:center; }
      .qtyBox button{
        width:36px; height:36px; border-radius:50%;
        border:none; font-size:18px; font-weight:700;
        background:#ececec; cursor:pointer;
      }
      .qtyBox input{
        width:64px; text-align:center; padding:6px;
        border-radius:8px; border:1px solid #cfcfcf;
      }
      .priceBox{ display:flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap; }
      .priceBox input{
        width:110px; font-weight:800; color:#2e7d32;
        border:none; background:transparent; text-align:right;
      }
      .currency{ font-weight:700; color:#666; }

      @media (max-width: 520px){
        .itemRow{ grid-template-columns: 1fr; text-align:center; }
        .itemImg{ width:90px; height:120px; margin:0 auto; }
        .qtyBox, .priceBox{ justify-content:center; }
        .priceBox input{ width:140px; }
        .swal2-title{ font-size:16px !important; }
      }

      .payField{
        margin: 12px 10px 2px;
        padding: 12px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
      }
      .payControl{ display:flex; align-items:center; gap:10px; }
      .payIcon{
        width:42px; height:42px; display:grid; place-items:center;
        border-radius: 12px; border: 1px solid #e5e7eb; background: #f9fafb;
        color:#374151; flex: 0 0 auto;
      }
      .payIcon svg{ width:22px; height:22px; }
      .payHint{ margin: 10px 0 0; font-size: 12.5px; color:#6b7280; }

      .payIcon.divida {
        color: #dc2626;
        background: #fee2e2;
        border-color: #fecaca;
      }

      .debtField{
        margin: 10px 10px 0;
        padding: 12px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
      }
      @media (max-width: 520px){
        .payField{ margin: 10px 6px 2px; padding: 10px; }
        .payIcon{ width:40px; height:40px; }
        .debtField{ margin: 10px 6px 0; }
      }
    </style>
  `;

  Swal.fire({
    title: `Total por pagar: <span id="grandTotal">0.00</span> MT`,
    html: htmlContent,
    showCancelButton: true,
    confirmButtonText: "Registar venda",
    cancelButtonText: "Cancelar",

    didOpen: () => {
      const pagamentoSel = document.getElementById("pagamento");
      const payIcon = document.getElementById("payIcon");

      const ICONS = {
        mpesa: `
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M9 7h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M9 17h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="14" r="1" fill="currentColor"/>
          </svg>`,
        pos: `
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="5" y="4" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8 8h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M8 12h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>`,
        dinheiro: `
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="4" y="7" width="16" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M7 10h1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M16 14h1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>`,
        divida: `
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M7 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M7 14h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M16 13l2 2l3-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>`
      };

      function setPayIconFromSelect(){
        if (!payIcon || !pagamentoSel) return;

        const key = normalizePayKey(pagamentoSel.value);

        payIcon.classList.toggle("divida", key === "divida");

        if (!key || !ICONS[key]) {
          payIcon.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M3.5 7.5h15a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-13a2.5 2.5 0 0 1-2.5-2.5V7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
              <path d="M16.5 12.5h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              <path d="M3.5 7.5V6.8A2.8 2.8 0 0 1 6.3 4h9.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>`;
          return;
        }

        payIcon.innerHTML = ICONS[key];
      }

      if (pagamentoSel) {
        pagamentoSel.addEventListener("change", setPayIconFromSelect);
        setPayIconFromSelect();
      }

      // total
      const totalEl = document.getElementById("grandTotal");

      function updateGrandTotal(){
        const total = Array.from(document.querySelectorAll(".itemRow"))
          .reduce((sum,row)=> sum + (Number(row.querySelector(".swal-price").value)||0), 0);
        totalEl.textContent = total.toFixed(2);
      }

      document.querySelectorAll(".itemRow").forEach(row=>{
        const qtyInput = row.querySelector(".swal-qty");
        const priceInput = row.querySelector(".swal-price");
        const unitPrice = Number(row.dataset.unitPrice);

        function updatePrice(){
          const qty = Math.max(1, Number(qtyInput.value)||1);
          qtyInput.value = qty;
          priceInput.value = (unitPrice * qty).toFixed(2);
          updateGrandTotal();
        }

        row.querySelector(".qty-plus").addEventListener("click", ()=>{
          qtyInput.value = Number(qtyInput.value) + 1;
          updatePrice();
        });

        row.querySelector(".qty-minus").addEventListener("click", ()=>{
          qtyInput.value = Math.max(1, Number(qtyInput.value) - 1);
          updatePrice();
        });

        qtyInput.addEventListener("input", updatePrice);
        updatePrice();
      });

      updateGrandTotal();

      // dívida: nome obrigatório
      const debtField = document.getElementById("debtField");
      const clienteNome = document.getElementById("clienteNome");

      function toggleDebtName(){
        const isDebt = pagamentoSel && String(pagamentoSel.value).toUpperCase().includes("DÍVIDA");
        if (debtField) debtField.style.display = isDebt ? "block" : "none";

        if (!isDebt && clienteNome){
          clienteNome.classList.remove("is-error");
          clienteNome.value = "";
        }
        if (isDebt && clienteNome){
          setTimeout(() => clienteNome.focus(), 0);
        }
      }

      if (pagamentoSel){
        toggleDebtName();
        pagamentoSel.addEventListener("change", toggleDebtName);
      }
    },

    preConfirm: () => {
      const sel = document.getElementById("pagamento");
      sel.classList.remove("is-error");

      if (!sel.value){
        sel.classList.add("is-error");
        Swal.showValidationMessage("Selecione o tipo de pagamento.");
        return false;
      }

      const isDebt = String(sel.value).toUpperCase().includes("DÍVIDA");
      const nomeEl = document.getElementById("clienteNome");
      const nome = (nomeEl?.value || "").trim();
      if (nomeEl) nomeEl.classList.remove("is-error");

      if (isDebt && !nome){
        if (nomeEl) nomeEl.classList.add("is-error");
        Swal.showValidationMessage("Em Dívida, o nome do cliente é obrigatório.");
        return false;
      }

      const items = Array.from(document.querySelectorAll(".itemRow")).map(row=>({
        product_id: row.dataset.id,
        tipo: row.dataset.tipo,
        marca: row.dataset.marca,
        quantidade: Number(row.querySelector(".swal-qty").value),
        preco_total: Number(row.querySelector(".swal-price").value)
      }));

      return { items, pagamento: sel.value, clienteNome: isDebt ? nome : "" };
    }
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    const items = result.value.items;
    const pagamento = result.value.pagamento;
    const clienteNome = result.value.clienteNome;

    // ✅ IMPORTANT: generate receipt ONCE (idempotent key)
    const saleId = makeOfflineReceiptNo();

    const payload = {
      sale_id: saleId,           // ✅ (server should make this UNIQUE)
      offline_receipt: saleId,   // keep your existing field too
      items,
      data_venda: formattedDate,
      cliente: clienteNome,
      pago_com: pagamento,
      desconto: 0
    };

    showReceiptSwal(payload.offline_receipt, payload.items, {
      offline: !navigator.onLine,
      pagoCom: payload.pago_com,
      cliente: payload.cliente
    });

    resetCart();

    try {
      const r = await fetch("compra/insert.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      if (!r.ok) throw new Error("HTTP " + r.status);

      const raw = await r.text();
      const data = JSON.parse(raw);
      if (!data || !data.ok) throw new Error("Server refused");

      // ✅ ok online, no need queue
    } catch (e) {
      // ❌ any fail -> queue offline (deduped)
      await queueSaleLocally(payload);
    }
  });
}

/* ==========================================================
   7) BOOT (NO DUPLICATE LISTENERS)
   ========================================================== */
updateCartBadge();

document.addEventListener("DOMContentLoaded", () => {
  renderOfflineInstant();
  updatePendingBadge();
  syncQueuedSales();
});

window.addEventListener("online", () => {
  updatePendingBadge();
  syncQueuedSales();
});

window.addEventListener("offline", () => {
  updatePendingBadge();
});

// less aggressive than 20s on mobile
setInterval(syncQueuedSales, 60000);

document.addEventListener("visibilitychange", () => {
  if (!document.hidden) syncQueuedSales();
});

// Debug alerts (keep if you want)
window.addEventListener("error", (e) => {
  alert("JS ERROR:\n" + (e.message || "") + "\n" + (e.filename || "") + ":" + (e.lineno || ""));
});

window.addEventListener("unhandledrejection", (e) => {
  alert("PROMISE ERROR:\n" + (e.reason?.message || e.reason || "unknown"));
});
</script>

<script>
  const menuToggle = document.getElementById("menuToggle");
  const navMenu = document.getElementById("navMenu");

  menuToggle.addEventListener("click", () => {
    navMenu.classList.toggle("show");
  });
</script>


<a href="#top" class="scroll-top">
  <i class="fa-solid fa-arrow-up"></i>
</a>


<script>
  const scrollTopBtn = document.querySelector(".scroll-top");

  window.addEventListener("scroll", () => {
    if (window.scrollY > 300) {
      scrollTopBtn.style.display = "flex";
    } else {
      scrollTopBtn.style.display = "none";
    }
  });
</script>

</body>
</html>
