<?php
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get current hero content
$query = "SELECT content FROM content_sections WHERE section_name = ?";
$stmt = $conn->prepare($query);
$stmt->execute(['hero']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $current_content = json_decode($result['content'], true);
    echo "<h2>Current Hero Content:</h2>";
    echo "<pre>" . json_encode($current_content, JSON_PRETTY_PRINT) . "</pre>";
    
    // Update the title to test
    $current_content['title'] = 'TESTING: Because your love story deserves a masterpiece - UPDATED!';
    
    // Save the updated content
    $query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $stmt->execute(['hero', json_encode($current_content), 'test-script']);
    
    echo "<h2>Content Updated!</h2>";
    echo "<p>New title: " . htmlspecialchars($current_content['title']) . "</p>";
    echo '<p><a href="index-cms.html">View updated website</a></p>';
    echo '<p><a href="api/content.php?section=hero">Check API response</a></p>';
    
} else {
    echo "No hero content found in database!";
}
?>