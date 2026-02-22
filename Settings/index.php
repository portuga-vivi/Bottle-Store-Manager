<?php

session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login/index.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestão Comercial</title>
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-->
    <link rel="stylesheet" href="../sweetalert/sweetalert2.min.css" />
    <script src="../sweetalert/sweetalert2.all.min.js"></script>

    <style>
      :root {
        --bg1: rgba(168, 218, 220, 0.18);
        --bg2: rgba(99, 179, 237, 0.12);
        --card: #ffffff;
        --text: #0f172a;
        --muted: #64748b;
        --border: #e2e8f0;
        --shadow: 0 18px 40px rgba(15, 23, 42, 0.1);
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        min-height: 100vh;
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial,
          sans-serif;
        background: radial-gradient(
            900px 520px at 10% 0%,
            var(--bg1),
            transparent 65%
          ),
          radial-gradient(900px 520px at 90% 10%, var(--bg2), transparent 60%),
          linear-gradient(180deg, #f4f7fb, #e9eef5);
        color: var(--text);
        display: grid;
        place-items: center;
        padding: 22px;
      }

      .container {
        width: min(920px, 100%);
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 22px;
        box-shadow: var(--shadow);
        padding: 32px;
      }

      /* Optional title (if you add one later) */
      .page-title {
        text-align: center;
        margin: 0 0 18px;
        font-size: 20px;
        font-weight: 800;
      }

      .top-icons {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
      }

      /* each icon becomes a button card */
      .icon-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid var(--border);
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        transition: transform 0.14s ease, box-shadow 0.2s ease,
          border-color 0.2s ease;
      }

      .icon-btn:hover {
        transform: translateY(-2px);
        border-color: rgba(127, 198, 201, 0.55);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.1);
      }

      .icon-img {
        width: 64px;
        height: 64px;
        object-fit: contain;
        border-radius: 18px;
        padding: 12px;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
      }

      .icon-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
      }

      .icon-text strong {
        font-size: 16px;
      }

      .icon-text span {
        font-size: 14px;
        color: var(--muted);
      }

      /* Mobile */
      @media (max-width: 520px) {
        .top-icons {
          grid-template-columns: 1fr;
        }
        .container {
          padding: 18px;
          border-radius: 18px;
        }
        .icon-img {
          width: 52px;
          height: 52px;
        }
      }
    </style>
  </head>

  <body>


    <div class="container">

      <div class="top-icons">

        <a class="icon-btn" href="../index.php">
          <img class="icon-img" src="../Pics/gerais/home.png" alt="Home" />
          <div class="icon-text">
            <strong>Home</strong>
            <span>Página inicial</span>
          </div>
        </a>

        <a class="icon-btn" href="../relatorio">
          <img
            class="icon-img"
            src="../relatorio/relatorio.png"
            alt="Relatório"
          />
          <div class="icon-text">
            <strong>Vendas</strong>
            <span>Relatório de vendas</span>
          </div>
        </a>

          <?php if ($_SESSION["type"] == "admin"){  ?>
        <a class="icon-btn" href="../relatorio/products_page.php">
          <img
            class="icon-img"
            src="../relatorio/produtos.png"
            alt="Produtos"
          />
          <div class="icon-text">
            <strong>Produtos</strong>
            <span>Lista de produtos</span>
          </div>
        </a>
          <?php } ?>

          <?php if ($_SESSION["type"] == "admin"){  ?>
          <a class="icon-btn" href="../Cadastro">
          <img
            class="icon-img"
            src="../Pics/gerais/cadastro.png"
            alt="Cadastro"
          />
          <div class="icon-text">
            <strong>Cadastro</strong>
            <span>Adicionar produto</span>
          </div>
        </a>
          <?php } ?>

          <?php if ($_SESSION["type"] == "admin"){  ?>
        <a class="icon-btn" href="../Cadastro/stock.php">
          <img class="icon-img" src="../Pics/gerais/stock.png" alt="Stock" />
          <div class="icon-text">
            <strong>Stock</strong>
            <span>Atualizar Stock</span>
          </div>
        </a>
          <?php } ?>

        <a class="icon-btn" href="../relatorio/recibos/recibos_lista.php">
          <img class="icon-img" src="../Pics/gerais/recibos.png" alt="Stock" />
          <div class="icon-text">
            <strong>Recibos</strong>
            <span>Imprimir Recibos</span>
          </div>
        </a>
      </div>
    </div>
  </body>
</html>


