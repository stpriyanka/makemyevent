<?php
session_start();
require_once 'config/database.php';

if ($_POST && isset($_POST['test_login'])) {
    $username = 'admin';
    $password = 'admin123';
    
    $users = Database::getUsers();
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $username;
        $_SESSION['user_role'] = $users[$username]['role'];
        $_SESSION['user_name'] = $users[$username]['name'];
        
        echo "<h2>Login Successful!</h2>";
        echo "<p>Welcome, " . $_SESSION['user_name'] . "</p>";
        echo '<p><a href="admin/dashboard.php">Go to Dashboard</a></p>';
        echo '<p><a href="admin/edit-section.php?section=hero">Edit Hero Section</a></p>';
        exit();
    } else {
        echo "<h2>Login Failed!</h2>";
        echo "<p>Invalid credentials</p>";
    }
}

// Test the users array
$users = Database::getUsers();
echo "<h2>Available Users:</h2>";
foreach ($users as $user => $data) {
    echo "<p><strong>$user</strong>: " . $data['password'] . " (" . $data['role'] . ")</p>";
}
?>

<form method="POST">
    <h2>Test Login</h2>
    <p>This will automatically test login with admin/admin123</p>
    <button type="submit" name="test_login">Test Login Process</button>
</form>

<form method="POST" action="login.php">
    <h2>Manual Login Test</h2>
    <input type="text" name="username" value="admin" placeholder="Username">
    <input type="password" name="password" value="admin123" placeholder="Password">
    <button type="submit">Login via Original Form</button>
</form>