<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $initial_content = [
        'eyebrow' => 'About Us',
        'title' => 'We bring your events to life with creativity & precision.',
        'description' => 'Make My Event was established in 2015 in Bangladesh with the purpose to bring your events to life with creativity, precision and a passion for excellence. We specialize in designing and executing dynamic experiences — from weddings and corporate events such as product launches and conferences to galas and team-building retreats. Our expert team handles every detail, blending innovative concepts with seamless logistics to deliver inspiring and connecting events. With a client-first approach and flawless execution, we transform ideas into unforgettable events. Trusted by fortune 1000+ clients across Bangladesh, we help businesses make a bold statement.',
        'background_image' => 'assets/images/about-bg new-01.jpg',
        'frame_portrait' => 'assets/images/frame-portrait.jpg',
        'frame_top' => 'assets/images/Framed couple top.jpg',
        'frame_bottom' => 'assets/images/frame-bottom.jpg'
    ];
    
    $query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $stmt->execute(['about', json_encode($initial_content), 'system']);
    
    echo "About section content updated successfully!\n";
    echo "Content: " . json_encode($initial_content, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>