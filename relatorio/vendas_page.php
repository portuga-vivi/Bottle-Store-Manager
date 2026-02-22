<?php
// vendas_page.php (just a page; the data comes from get_vendas.php)
?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Vendas - Tabela</title>
    <style>
        body{font-family:Arial, sans-serif; padding:18px;}
        .top{display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin:12px 0 18px;}
        label{font-size:14px; display:block; margin-bottom:6px;}
        input{padding:10px; border:1px solid #ddd; border-radius:8px;}
        button{padding:10px 14px; border:0; border-radius:8px; cursor:pointer;}
        .btn{background:#111827; color:#fff;}
        .btn2{background:#e5e7eb;}
        .wrap{overflow-x:auto;}
        table{border-collapse:collapse; width:100%; min-width:720px;}
        th,td{border:1px solid #ddd; padding:10px; text-align:left;}
        th{background:#f3f4f6;}
        tr:nth-child(even){background:#fafafa;}
        .totals{margin:12px 0; padding:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px;}
        .error{padding:12px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px;}
    </style>
</head>
<body>

<h2>Lista de Vendas</h2>

<div class="top">
    <div>
        <label>Início</label>
        <input type="date" id="inicio">
    </div>
    <div>
        <label>Fim</label>
        <input type="date" id="fim">
    </div>
    <div>
        <label>Marca</label>
        <input type="text" id="marca" placeholder="Ex: DK, Dekalb...">
    </div>

    <div>
        <button class="btn" id="btnFiltrar">Carregar</button>
        <button class="btn2" id="btnLimpar" type="button">Limpar</button>
    </div>
</div>

<div id="msg"></div>

<div class="totals">
    <b>Total quantidade:</b> <span id="tQtd">0</span>
    &nbsp; | &nbsp;
    <b>Total valor:</b> <span id="tValor">0.00</span>
</div>

<div class="wrap">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Marca</th>
            <th>Quantidade</th>
            <th>Preço Total</th>
        </tr>
        </thead>
        <tbody id="tbody">
        <tr><td colspan="5">Carregando...</td></tr>
        </tbody>
    </table>
</div>

<script>
    const tbody = document.getElementById("tbody");
    const msg = document.getElementById("msg");
    const tQtd = document.getElementById("tQtd");
    const tValor = document.getElementById("tValor");

    function moneyPT(value){
        // 1234.5 -> "1.234,50"
        return Number(value || 0).toLocaleString("pt-PT", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function intPT(value){
        return Number(value || 0).toLocaleString("pt-PT");
    }

    async function loadVendas(){
        msg.innerHTML = "";
        tbody.innerHTML = `<tr><td colspan="5">Carregando...</td></tr>`;

        const inicio = document.getElementById("inicio").value;
        const fim = document.getElementById("fim").value;
        const marca = document.getElementById("marca").value.trim();

        const params = new URLSearchParams();
        if (inicio) params.append("inicio", inicio);
        if (fim) params.append("fim", fim);
        if (marca) params.append("marca", marca);

        try{
            const res = await fetch("get_vendas.php?" + params.toString(), { cache: "no-store" });
            const data = await res.json();

            if (!data.ok){
                msg.innerHTML = `<div class="error">Erro: ${data.error || "Falha ao carregar dados"}</div>`;
                tbody.innerHTML = `<tr><td colspan="5">Sem dados</td></tr>`;
                return;
            }

            tQtd.textContent = intPT(data.total_quantidade);
            tValor.textContent = moneyPT(data.total_preco);

            if (!data.rows || data.rows.length === 0){
                tbody.innerHTML = `<tr><td colspan="5">Nenhum registo encontrado.</td></tr>`;
                return;
            }

            tbody.innerHTML = data.rows.map(r => `
        <tr>
          <td>${escapeHtml(r.id)}</td>
          <td>${escapeHtml(r.dataa)}</td>
          <td>${escapeHtml(r.marca)}</td>
          <td>${escapeHtml(r.quantidade)}</td>
          <td>${moneyPT(r.preco_total)}</td>
        </tr>
      `).join("");

        } catch(e){
            msg.innerHTML = `<div class="error">Erro: ${escapeHtml(e.message)}</div>`;
            tbody.innerHTML = `<tr><td colspan="5">Sem dados</td></tr>`;
        }
    }

    function escapeHtml(v){
        return String(v ?? "").replace(/[&<>"']/g, (m) => ({
            "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
        }[m]));
    }

    document.getElementById("btnFiltrar").addEventListener("click", loadVendas);

    document.getElementById("btnLimpar").addEventListener("click", () => {
        document.getElementById("inicio").value = "";
        document.getElementById("fim").value = "";
        document.getElementById("marca").value = "";
        loadVendas();
    });

    // Auto-load on open
    loadVendas();
</script>

</body>
</html>
