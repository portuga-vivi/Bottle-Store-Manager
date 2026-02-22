<?php


session_start();
include('conexao.php');


$CriarTabela_remember = "
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conexao, $CriarTabela_remember);


if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    $_SESSION['campo_vazio'] = true;
    header('Location: index.php');
    exit;
}

$usuario = trim($_POST['usuario']);
$senha   = $_POST['senha'];

/* get user */
$stmt = mysqli_prepare($conexao, "
    SELECT * 
    FROM usuario_distrito 
    WHERE username = ?
");
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {

    if (password_verify($senha, $user['pass'])) {

        /* =========================
           NORMAL LOGIN (SESSION)
        ========================== */
        $_SESSION['usuario'] = $user['username'];
        $_SESSION['type'] = $user['type'];

        /* =========================
           🔥 REMEMBER ME PART (NEW)
        ========================== */

        // 1. Generate secret token
        $token = bin2hex(random_bytes(32));

        // 2. Hash token before saving
        $token_hash = password_hash($token, PASSWORD_DEFAULT);

        // 3. Save token in database (30 days)
        $stmt2 = mysqli_prepare($conexao, "
            INSERT INTO remember_tokens (user_id, token_hash, expires_at)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
        ");
        mysqli_stmt_bind_param($stmt2, "is", $user['id'], $token_hash);
        mysqli_stmt_execute($stmt2);

        // 4. Save token in cookie (30 days)
        setcookie(
            'remember_me',
            $user['id'] . ':' . $token,
            time() + (30 * 24 * 60 * 60),
            '/',
            '',
            true,   // Secure (HTTPS)
            true    // HttpOnly
        );

        header('Location: ../index.php');
        exit;
    }
}

$_SESSION['nao_autenticado'] = true;
header('Location: index.php');
exit;
