<?php
// Database setup script
require_once '../config/database.php';

try {
    // Create database connection without specifying database name first
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute SQL schema
    $sql = file_get_contents('../database/schema.sql');
    
    // Split SQL by semicolon and execute each statement
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $conn->exec($statement);
        }
    }
    
    echo "Database setup completed successfully!<br>";
    echo "Database: " . DB_NAME . " created<br>";
    echo "Tables created and sample data inserted<br>";
    echo "<a href='../login.php'>Go to Login Page</a>";
    
} catch(PDOException $e) {
    echo "Database setup failed: " . $e->getMessage();
}
?>