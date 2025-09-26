<?php
session_start();
require_once 'config/database.php';

// Force admin session
$_SESSION['user_logged_in'] = true;
$_SESSION['username'] = 'admin';
$_SESSION['user_id'] = 'admin';
$_SESSION['user_role'] = 'admin';
$_SESSION['user_name'] = 'Administrator';

echo "<h1>Content Update Test</h1>";

$db = new Database();
$conn = $db->getConnection();

// Get current hero content
$query = "SELECT content FROM content_sections WHERE section_name = ?";
$stmt = $conn->prepare($query);
$stmt->execute(['hero']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $content = json_decode($result['content'], true);
    echo "<h2>Current Hero Content:</h2>";
    echo "<p><strong>Title:</strong> " . htmlspecialchars($content['title']) . "</p>";
    
    // Update with test content
    $testTime = date('H:i:s');
    $content['title'] = "UPDATED at {$testTime}: Because your love story deserves a masterpiece!";
    
    $updateQuery = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $updateStmt = $conn->prepare($updateQuery);
    $success = $updateStmt->execute(['hero', json_encode($content), 'test-admin']);
    
    if ($success) {
        echo "<p>✅ Content updated successfully!</p>";
        echo "<p><strong>New Title:</strong> " . htmlspecialchars($content['title']) . "</p>";
    } else {
        echo "<p>❌ Failed to update content</p>";
    }
} else {
    echo "<p>❌ No hero content found</p>";
}
?>

<h2>Test Links:</h2>
<p><a href="index.html" target="_blank">🌐 Original Site (should show updated content)</a></p>
<p><a href="index-cms.html" target="_blank">🔧 CMS Site (should show updated content)</a></p>
<p><a href="api/content.php" target="_blank">📄 API Response (raw JSON)</a></p>
<p><a href="api/content.php?section=hero" target="_blank">📄 Hero Section API</a></p>

<form method="POST">
    <h2>Reset Content:</h2>
    <button type="submit" name="reset">Reset to Original Content</button>
</form>

<?php
if (isset($_POST['reset'])) {
    $originalContent = [
        'eyebrow' => 'Wedding Profile',
        'title' => 'Because your love story deserves a masterpiece.',
        'script' => 'MAKE MY EVENT',
        'stats' => [
            ['number' => '10+ Years', 'label' => 'Experience'],
            ['number' => '1200+', 'label' => 'Happy Couples'],
            ['number' => '30%', 'label' => 'Recurrent Customers']
        ]
    ];
    
    $resetStmt = $conn->prepare($updateQuery);
    $resetStmt->execute(['hero', json_encode($originalContent), 'test-admin']);
    echo "<p>✅ Content reset to original</p>";
    echo '<meta http-equiv="refresh" content="2">';
}
?>