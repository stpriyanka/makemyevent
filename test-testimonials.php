<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Testimonials Section Updates</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .update-form { margin: 10px 0; }
        .links { margin: 20px 0; }
        .links a { margin: 0 10px; padding: 10px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; }
        .status { padding: 10px; background: #f0f8ff; border-left: 4px solid #007cba; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🧪 Test Testimonials Section Workflow</h1>
    
    <div class="status">
        <h3>✅ All Sections Now Have Required IDs!</h3>
        <p>Both <code>index.html</code> and <code>index-cms.html</code> now have proper IDs for all sections:</p>
        <ul>
            <li><strong>Hero:</strong> heroEyebrow, heroTitle, heroScript</li>
            <li><strong>About:</strong> aboutEyebrow, aboutTitle, aboutDescription</li>
            <li><strong>Segments:</strong> segmentsTitle, segmentsDescription</li>
            <li><strong>Team:</strong> teamTitle, teamDescription</li>
            <li><strong>Testimonials:</strong> testimonialsTitle, testimonialsDescription</li>
            <li><strong>Contact:</strong> contactTitle, contactDescription</li>
            <li><strong>Gallery:</strong> galleryTitle, galleryDescription</li>
            <li><strong>Decoration:</strong> decorationTitle, decorationDescription</li>
        </ul>
    </div>

    <div class="test-section">
        <h3>📋 Complete Edit Workflow Test</h3>
        <p><strong>Follow these steps to test any section:</strong></p>
        <ol>
            <li>Go to <a href="index-cms.html">CMS Site</a></li>
            <li>Scroll to any section (e.g., Testimonials)</li>
            <li>Click the "✏️ Edit" button</li>
            <li>Update the content and click "Save"</li>
            <li>Return to both sites and verify changes appear</li>
        </ol>
    </div>

    <div class="test-section">
        <h3>🔗 Quick Test: Update Testimonials</h3>
        <form action="admin/edit-section.php?section=testimonials" method="POST" class="update-form">
            <label>Testimonials Title (first word):</label><br>
            <input type="text" name="content[testimonialsTitle]" value="OUR" style="width: 300px; margin: 5px 0;"><br>
            
            <label>Testimonials Description (second word):</label><br>
            <input type="text" name="content[testimonialsDescription]" value="TESTIMONIALS" style="width: 300px; margin: 5px 0;"><br>
            
            <button type="submit" name="update_content" style="padding: 10px 20px; margin: 10px 0;">Update Testimonials</button>
        </form>
    </div>

    <div class="links">
        <h3>🔗 Test Links:</h3>
        <a href="index-cms.html">CMS Site (Admin)</a>
        <a href="index.html">Original Site</a>
        <a href="admin/edit-section.php?section=testimonials">Edit Testimonials</a>
        <a href="admin/edit-section.php?section=team">Edit Team</a>
        <a href="admin/edit-section.php?section=segments">Edit Segments</a>
        <a href="admin/edit-section.php?section=contact">Edit Contact</a>
    </div>

    <div class="test-section">
        <h3>📊 Current Database Content</h3>
        <?php
        try {
            require_once 'config/database.php';
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "SELECT section_name, content, updated_at FROM content_sections ORDER BY section_name";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($sections) {
                echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
                echo "<tr><th>Section</th><th>Content</th><th>Last Updated</th></tr>";
                foreach ($sections as $section) {
                    echo "<tr>";
                    echo "<td style='padding: 8px;'>" . htmlspecialchars($section['section_name']) . "</td>";
                    echo "<td style='padding: 8px;'>" . htmlspecialchars(substr($section['content'], 0, 100)) . "...</td>";
                    echo "<td style='padding: 8px;'>" . htmlspecialchars($section['updated_at']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No content found in database.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Error loading content: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</body>
</html>