<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    echo json_encode([
        'logged_in' => true,
        'username' => $_SESSION['username'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>