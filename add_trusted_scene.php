<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $initial_content = [
        'badge_image' => 'assets/images/trusted%20feedback%20info%20image-03.png',
        'line_1' => "We don't want to be a one-time choice, for our clients.<br>\nOur goal is to ensure that, whenever a special moment arises in their lives,<br>\nthe first name they think of is",
        'line_2' => ' MAKE MY EVENT.',
        'background_image' => 'assets/images/Trusted%20Feedback%2098%25%20Bg-02.jpg'
    ];
    
    $query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $stmt->execute(['trusted-scene', json_encode($initial_content), 'system']);
    
    echo "Trusted-scene content added successfully!\n";
    echo "Content: " . json_encode($initial_content, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>