<?php
session_start();
include('conexao.php');

/* ===============================
   REMOVE REMEMBER-ME TOKEN
================================ */

if (isset($_COOKIE['remember_me'])) {

    list($user_id, $token) = explode(':', $_COOKIE['remember_me']);

    // Remove token from database
    $stmt = mysqli_prepare($conexao, "
        DELETE FROM remember_tokens WHERE user_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Delete remember-me cookie
    setcookie('remember_me', '', time() - 3600, '/', '', true, true);
}

/* ===============================
   NORMAL SESSION LOGOUT (YOUR CODE)
================================ */

/* Unset all session variables */
$_SESSION = [];

/* Destroy session */
session_destroy();

/* Delete session cookie */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Redirect to login */
header("Location: index.php");
exit;
