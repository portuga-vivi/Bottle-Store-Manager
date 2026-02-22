<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('conexao.php');

$nome    = trim($_POST['nome'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$type    = 'vendedor';
$pp      = $_POST['pass'] ?? '';
$pp2     = $_POST['pass2'] ?? '';

if (empty($nome) || empty($usuario) || empty($pp) || empty($pp2)) {
    $_SESSION['campo_vazio'] = true;
    header('Location: register.php');
    exit;
}

if ($pp !== $pp2) {
    $_SESSION['senha_desigual'] = true;
    header('Location: register.php');
    exit;
}

$CriarTabela = "
CREATE TABLE IF NOT EXISTS usuario_distrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    username VARCHAR(200) NOT NULL UNIQUE,
    type VARCHAR(10) NOT NULL,
    pass VARCHAR(255) NOT NULL
)";
mysqli_query($conexao, $CriarTabela);



/* check if user exists */
$stmt = mysqli_prepare($conexao, "SELECT id FROM usuario_distrito WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    $_SESSION['usuario_ocupado'] = true;
    header('Location: register.php');
    exit;
}

/* insert */
$hash = password_hash($pp, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conexao, "INSERT INTO usuario_distrito (nome, type, username, pass) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $nome, $type, $usuario, $hash);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['registado'] = true;
} else {
    $_SESSION['general_error'] = true;
}

header('Location: index.php');
exit;

