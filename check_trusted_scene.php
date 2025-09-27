<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Check trusted-scene content
    $query = "SELECT * FROM content_sections WHERE section_name = 'trusted-scene'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "Trusted Scene content found in database:\n";
        echo "Section: " . $result['section_name'] . "\n";
        echo "Updated: " . $result['updated_at'] . "\n";
        echo "Content: " . json_encode(json_decode($result['content'], true), JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "No trusted-scene content found in database.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>