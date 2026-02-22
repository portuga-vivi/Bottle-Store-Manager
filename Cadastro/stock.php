<?php

require_once "../ConexaoPHP/db.php";  // ✅ CONNECT FIRST
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema de Gestão Comercial</title>

<link rel="stylesheet" href="../sweetalert/sweetalert2.min.css">
<script src="../sweetalert/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="style.css">

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

</head>

<body>

<div class="container">


<form action="insert_stock.php" method="post" enctype="multipart/form-data">
    
    <div class="header">
        <div class="top-bar2">
            <a href="../index.php">
                <img id="index_img" src="../Pics/gerais/home.png">
            </a>
        </div>

        <div class="top-bar">
            <a href="../relatorio/products_page.php">
                <img id="cadastro_img" src="../relatorio/produtos.png">
            </a>
        </div>
    </div>

    <h3 id="today" na style="align-content: center"></h3>
<h1>Atualização do Stock<br>-No Distrito-</h1>

    <h2>1) Tipo de Produto</h2>
    <div class="habit-list">
        <div style="display: flex"><label><input value="CERVEJAS" name="tipo" type="checkbox"> CERVEJAS</label>
        <label><input value="SECAS" name="tipo" type="checkbox"> SECAS</label></div>
        <div style="display: flex"><label><input value="VINHOS" name="tipo" type="checkbox"> VINHOS</label><label>
        <input  name="tipo" value="CIDRAS" type="checkbox"> CIDRAS</label></div>
        <label><input  name="tipo" value="OUTRAS BEBIDAS" type="checkbox"> OUTRAS BEBIDAS</label>
        <label><input  name="tipo" value="OUTROS PRODUTOS" type="checkbox"> OUTROS PRODUTOS</label>
    </div>

    

<h2>2) Nome do Produto</h2>
<label for="country" id="label"></label><br>
<select name="marca" id="stock" class="select-modern">
                <option value="">-- Select --</option>
</select>

    <h2 id="stockTitle">2) Stock de Produto</h2>
    <input type="number" name="stock" step="any"  id="stockInput" placeholder="Quantidade a ser aumentada">

    <br>
    <button type="submit" name="submit" >Atualizar Stock</button>


    <div class="footer">
        No Distrito,o lugar perfeito para Espairecer.
    </div>

</form>
</div>


<script>
const checkboxes = document.querySelectorAll('.habit-list input[type="checkbox"]');
const input = document.getElementById("label");
const stock = document.getElementById("stock");

function fillSelectByTipo(tipo){
  stock.length = 1; // keep "-- Select --"
  allProducts
    .filter(p => p.tipo === tipo)
    .forEach(p => {
      const opt = document.createElement("option");
      opt.value = p.marca;
      opt.textContent = p.marca;
      stock.appendChild(opt);
    });
}

    checkboxes.forEach(cb => {
  cb.addEventListener("change", () => {
    if(cb.checked){
      checkboxes.forEach(o => { if(o !== cb) o.checked = false; });
      fillSelectByTipo(cb.value);
      input.textContent = "SELECIONE UM PRODUTO ENTRE "+cb.value
    } else {
      stock.length = 1;
    }
  });
});

const stockTitle = document.getElementById("stockTitle");
const stockInput = document.getElementById("stockInput");

// When marca is selected
stock.addEventListener("change", () => {

  const marca = stock.value;

  if (marca !== "") {
    stockTitle.textContent = `2) Stock de ${marca}`;
    stockInput.placeholder = `Que Quantidade de ${marca} quer aumentar?`;
  } else {
    // Reset if no selection
    stockTitle.textContent = "2) Stock de Produto";
    stockInput.placeholder = "Quantidade a ser aumentada";
  }

});

    </script>
</body>
</html>
