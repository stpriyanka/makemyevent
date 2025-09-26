<?php
session_start();
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Login Debug Analysis</h1>";

// Check session
echo "<h2>1. Session Status:</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session Data: " . print_r($_SESSION, true);
echo "</pre>";

// Check POST data
echo "<h2>2. POST Data:</h2>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

// Check users
echo "<h2>3. Available Users:</h2>";
$users = Database::getUsers();
echo "<pre>" . print_r($users, true) . "</pre>";

// Test login manually
if (isset($_POST['test_login'])) {
    echo "<h2>4. Manual Login Test:</h2>";
    
    $username = 'admin';
    $password = 'admin123';
    
    echo "<p>Testing with username: $username, password: $password</p>";
    
    if (isset($users[$username])) {
        echo "<p>✅ User found in database</p>";
        if ($users[$username]['password'] === $password) {
            echo "<p>✅ Password matches</p>";
            
            // Set session variables
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $username;
            $_SESSION['user_role'] = $users[$username]['role'];
            $_SESSION['user_name'] = $users[$username]['name'];
            
            echo "<p>✅ Session variables set</p>";
            echo "<pre>" . print_r($_SESSION, true) . "</pre>";
            
            echo '<p><a href="admin/dashboard.php">Test Admin Dashboard Access</a></p>';
        } else {
            echo "<p>❌ Password doesn't match. Expected: " . $users[$username]['password'] . "</p>";
        }
    } else {
        echo "<p>❌ User not found</p>";
    }
}

// Process regular login
if (isset($_POST['username']) && isset($_POST['password'])) {
    echo "<h2>4. Regular Login Process:</h2>";
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    echo "<p>Username: '$username'</p>";
    echo "<p>Password: '$password'</p>";
    
    if (isset($users[$username])) {
        echo "<p>✅ User exists</p>";
        if ($users[$username]['password'] === $password) {
            echo "<p>✅ Password correct</p>";
            
            $_SESSION['user_logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $username;
            $_SESSION['user_role'] = $users[$username]['role'];
            $_SESSION['user_name'] = $users[$username]['name'];
            
            echo "<p>✅ Session set - redirecting...</p>";
            echo '<p><strong>If redirect doesn\'t work, <a href="admin/dashboard.php">click here</a></strong></p>';
            
            // Don't redirect for debugging
            // header('Location: admin/dashboard.php');
            // exit();
        } else {
            echo "<p>❌ Wrong password</p>";
        }
    } else {
        echo "<p>❌ User not found</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        form { background: #e8f4fd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        input, button { padding: 8px; margin: 5px; }
        button { background: #007cba; color: white; border: none; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <form method="POST">
        <h3>Manual Login Test</h3>
        <button type="submit" name="test_login">Test Login with admin/admin123</button>
    </form>
    
    <form method="POST">
        <h3>Form Login Test</h3>
        <input type="text" name="username" placeholder="Username" value="admin">
        <input type="password" name="password" placeholder="Password" value="admin123">
        <button type="submit">Login</button>
    </form>
    
    <p><a href="login.php">Back to Regular Login</a></p>
    <p><a href="admin/dashboard.php">Try Admin Dashboard</a></p>
    <p><a href="index-cms.html">View CMS Website</a></p>
</body>
</html>