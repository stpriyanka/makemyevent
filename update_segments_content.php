<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $initial_content = [
        'title' => 'OUR SEGMENTS',
        'classic_title' => '<strong>CLASSIC</strong> <small>TOUCH</small>',
        'classic_text' => '<strong>Traditional yet graceful,</strong> this package covers all the essential décor elements to make your wedding simple, beautiful, and memorable without compromise.',
        'premium_title' => '<strong>PREMIUM</strong> <small>TOUCH</small>',
        'premium_text' => '<strong>A touch of royal sophistication —</strong> designed for couples who want refined elegance, stylish details, and a charming ambience that leaves a lasting impression.',
        'signature_title' => '<strong>SIGNATURE</strong> <small>TOUCH</small>',
        'signature_text' => '<strong>The ultimate grandeur experience —</strong> a lavish, bespoke décor journey where every detail is tailored to perfection, creating a truly unforgettable celebration.'
    ];
    
    $query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $stmt->execute(['segments', json_encode($initial_content), 'system']);
    
    echo "Segments content updated successfully!\n";
    echo "Content: " . json_encode($initial_content, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>