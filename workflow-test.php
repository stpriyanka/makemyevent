<?php
session_start();
require_once 'config/database.php';

echo "<h1>Complete Admin Workflow Test</h1>";

// Step 1: Login
if (!isset($_SESSION['user_logged_in'])) {
    $_SESSION['user_logged_in'] = true;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_id'] = 'admin';
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_name'] = 'Administrator';
    echo "<p>✅ Step 1: Login successful</p>";
} else {
    echo "<p>✅ Step 1: Already logged in</p>";
}

// Step 2: Get current hero content
$db = new Database();
$conn = $db->getConnection();

$query = "SELECT content FROM content_sections WHERE section_name = ?";
$stmt = $conn->prepare($query);
$stmt->execute(['hero']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $content = json_decode($result['content'], true);
    echo "<p>✅ Step 2: Retrieved hero content</p>";
    echo "<p><strong>Current title:</strong> " . htmlspecialchars($content['title']) . "</p>";
} else {
    echo "<p>❌ Step 2: Failed to retrieve hero content</p>";
    exit();
}

// Step 3: Update content with a test message
$originalTitle = $content['title'];
$testTitle = "ADMIN TEST: Content updated at " . date('H:i:s') . " - Your love story deserves a masterpiece!";
$content['title'] = $testTitle;

$query = "INSERT OR REPLACE INTO content_sections (section_name, content, updated_by, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
$stmt = $conn->prepare($query);
$result = $stmt->execute(['hero', json_encode($content), 'admin-test']);

if ($result) {
    echo "<p>✅ Step 3: Content updated successfully</p>";
    echo "<p><strong>New title:</strong> " . htmlspecialchars($testTitle) . "</p>";
} else {
    echo "<p>❌ Step 3: Failed to update content</p>";
    exit();
}

// Step 4: Verify API returns updated content
$apiUrl = 'http://localhost:8080/api/content.php?section=hero';
$apiResponse = file_get_contents($apiUrl);
$apiData = json_decode($apiResponse, true);

if ($apiData && isset($apiData['title']) && $apiData['title'] === $testTitle) {
    echo "<p>✅ Step 4: API returns updated content</p>";
} else {
    echo "<p>❌ Step 4: API doesn't return updated content</p>";
    echo "<p>API Response: " . htmlspecialchars($apiResponse) . "</p>";
}

// Step 5: Display links to verify
echo "<hr>";
echo "<h2>Verification Links:</h2>";
echo '<p><a href="api/content.php?section=hero" target="_blank">📄 Check API Response</a></p>';
echo '<p><a href="index-cms.html" target="_blank">🌐 View CMS Website (should show updated content)</a></p>';
echo '<p><a href="admin/edit-section.php?section=hero" target="_blank">✏️ Edit Hero Section in Admin</a></p>';
echo '<p><a href="admin/dashboard.php" target="_blank">🏠 Admin Dashboard</a></p>';

// Step 6: Restore original content
echo "<hr>";
echo '<form method="POST">';
echo '<h2>Cleanup:</h2>';
echo '<input type="hidden" name="restore_title" value="' . htmlspecialchars($originalTitle) . '">';
echo '<button type="submit" name="restore">Restore Original Title</button>';
echo '</form>';

if (isset($_POST['restore'])) {
    $content['title'] = $_POST['restore_title'];
    $stmt = $conn->prepare($query);
    $stmt->execute(['hero', json_encode($content), 'admin-test']);
    echo "<p>✅ Content restored to original</p>";
    echo '<meta http-equiv="refresh" content="2">';
}
?>