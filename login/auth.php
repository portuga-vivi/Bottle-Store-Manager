<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario']) && isset($_COOKIE['remember_me'])) {

    list($user_id, $token) = explode(':', $_COOKIE['remember_me']);

    $stmt = mysqli_prepare($conexao, "
        SELECT token_hash 
        FROM remember_tokens 
        WHERE user_id = ? AND expires_at > NOW()
    ");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($token, $row['token_hash'])) {

            // Restore session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['usuario'] = $user_id; // or username if you prefer
        }
    }
}
