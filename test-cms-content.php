<?php
session_start();
require_once 'config/database.php';

// Force admin session for testing
$_SESSION['user_logged_in'] = true;
$_SESSION['username'] = 'admin';
$_SESSION['user_role'] = 'admin';

echo "<h1>CMS Content Loading Test</h1>";

$db = new Database();
$conn = $db->getConnection();

// Test updating hero content
if (isset($_POST['update_hero'])) {
    $heroContent = [
        'eyebrow' => $_POST['eyebrow'],
        'title' => $_POST['title'],
        'script' => $_POST['script'],
        'stats' => [
            ['number' => '10+ Years', 'label' => 'Experience'],
            ['number' => '1200+', 'label' => 'Happy Couples'],
            ['number' => '30%', 'label' => 'Recurrent Customers']
        ]
    ];
    
    $query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $result = $stmt->execute(['hero', json_encode($heroContent), 'test-admin']);
    
    if ($result) {
        echo "<p>✅ Hero content updated successfully!</p>";
    } else {
        echo "<p>❌ Failed to update hero content</p>";
    }
}

// Get current content
$query = "SELECT section_name, content FROM content_sections";
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Current Database Content:</h2>";
foreach ($results as $row) {
    $content = json_decode($row['content'], true);
    echo "<h3>" . ucfirst($row['section_name']) . " Section:</h3>";
    echo "<pre>" . print_r($content, true) . "</pre>";
}
?>

<form method="POST" style="background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px;">
    <h3>Update Hero Section:</h3>
    <p><label>Eyebrow: <input type="text" name="eyebrow" value="Wedding Profile" style="width: 300px;"></label></p>
    <p><label>Title: <input type="text" name="title" value="TEST: Dynamic content loading works!" style="width: 500px;"></label></p>
    <p><label>Script: <input type="text" name="script" value="MAKE MY EVENT" style="width: 300px;"></label></p>
    <p><button type="submit" name="update_hero" style="background: #007cba; color: white; padding: 10px 20px; border: none;">Update Hero Content</button></p>
</form>

<h3>Test Links:</h3>
<p><a href="index-cms.html" target="_blank">🔧 View CMS Website (should show updated content)</a></p>
<p><a href="index.html" target="_blank">🌐 View Original Website (should also show updated content)</a></p>
<p><a href="api/content.php" target="_blank">📄 View Raw API Response</a></p>