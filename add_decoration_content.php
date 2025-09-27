<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $initial_content = [
        'frame_left' => 'assets/images/frame-left.jpg',
        'frame_center' => 'assets/images/frame-center.jpg',
        'frame_right' => 'assets/images/frame-right.jpg'
    ];
    
    $query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $stmt->execute(['decoration', json_encode($initial_content), 'system']);
    
    echo "Decoration (frame images) content added successfully!\n";
    echo "Content: " . json_encode($initial_content, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>