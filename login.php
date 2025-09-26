<?php
session_start();
require_once 'config/database.php';

// Check if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: admin/dashboard.php');
    exit();
}

// Handle login
if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $users = Database::getUsers();
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $username; // Add user_id for consistency
        $_SESSION['user_role'] = $users[$username]['role'];
        $_SESSION['user_name'] = $users[$username]['name'];
        
        header('Location: admin/dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make My Event - Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: Inter, system-ui, sans-serif;
            background: linear-gradient(135deg, #4d0e16 0%, #62131b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f7f5ef;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(233, 211, 165, 0.3);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-family: "Playfair Display", serif;
            font-size: 2rem;
            color: #f1e7c9;
            margin: 0;
            font-weight: 700;
        }
        
        .logo p {
            color: #cbbfa5;
            margin: 5px 0 0 0;
            font-size: 0.9rem;
            letter-spacing: 0.1em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #e9d3a5;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(233, 211, 165, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #f7f5ef;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #f1e7c9;
            box-shadow: 0 0 0 2px rgba(241, 231, 201, 0.2);
        }
        
        input::placeholder {
            color: rgba(241, 231, 201, 0.5);
        }
        
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e9d3a5 0%, #f1e7c9 100%);
            color: #4d0e16;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 211, 165, 0.3);
        }
        
        .error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff6b7a;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .credentials {
            margin-top: 30px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            border-left: 3px solid #e9d3a5;
        }
        
        .credentials h4 {
            margin: 0 0 10px 0;
            color: #e9d3a5;
            font-size: 0.9rem;
        }
        
        .credentials div {
            font-size: 0.8rem;
            margin: 5px 0;
            color: #cbbfa5;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #e9d3a5;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: #f1e7c9;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Make My Event</h1>
            <p>Content Management System</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="login-btn">Login to Admin Panel</button>
        </form>
        
        <div class="credentials">
            <h4>Demo Credentials:</h4>
            <div><strong>Admin:</strong> admin / admin123</div>
            <div><strong>User:</strong> mmeuser / mme123</div>
        </div>
        
        <div class="back-link">
            <a href="index.html">← Back to Website</a>
        </div>
    </div>
</body>
</html>