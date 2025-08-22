<?php
session_start();

function is_user_authenticated() {
    return isset($_SESSION['admin_user_id']) && isset($_SESSION['admin_username']);
}

function require_authentication() {
    if (!is_user_authenticated()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required', 'redirect' => 'login.php']);
        exit;
    }
}

function login_user($user_id, $username) {
    $_SESSION['admin_user_id'] = $user_id;
    $_SESSION['admin_username'] = $username;
    $_SESSION['login_time'] = time();
}

function logout_user() {
    session_unset();
    session_destroy();
}

function get_current_user_id() {
    return $_SESSION['admin_user_id'] ?? null;
}

function get_current_username() {
    return $_SESSION['admin_username'] ?? null;
}
?>