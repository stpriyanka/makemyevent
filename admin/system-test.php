<?php
/**
 * Make My Event CMS - System Test Script
 * This script tests all major functionality of the CMS system
 */

require_once '../config/database.php';

echo "<h1>Make My Event CMS - System Test Results</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
    .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn === null) {
        throw new Exception("Database connection returned null");
    }
    
    echo "<div class='test-section success'><h3>✅ Database Connection</h3>Successfully connected to database.</div>";
} catch(Exception $e) {
    echo "<div class='test-section error'><h3>❌ Database Connection</h3>Error: " . $e->getMessage() . "</div>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running</li>";
    echo "<li>Check database credentials in config/database.php</li>";
    echo "<li>Run setup-database.php to initialize the database</li>";
    echo "</ul>";
    exit();
}

// Test 1: Check required tables exist
echo "<div class='test-section info'><h3>🔍 Testing Database Tables</h3>";
$requiredTables = ['content_sections', 'images', 'gallery_albums', 'gallery_images', 'team_members'];
$existingTables = [];

foreach ($requiredTables as $table) {
    try {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existingTables[] = $table;
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' missing<br>";
        }
    } catch(Exception $e) {
        echo "❌ Error checking table '$table': " . $e->getMessage() . "<br>";
    }
}
echo "</div>";

// Test 2: Check content sections
echo "<div class='test-section info'><h3>📝 Testing Content Sections</h3>";
try {
    $stmt = $conn->query("SELECT section_name FROM content_sections");
    $sections = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredSections = ['hero', 'navigation', 'about', 'segments', 'gallery', 'team', 'testimonials', 'contact', 'footer'];
    
    foreach ($requiredSections as $section) {
        if (in_array($section, $sections)) {
            echo "✅ Section '$section' configured<br>";
        } else {
            echo "❌ Section '$section' missing<br>";
        }
    }
    
    echo "<br><strong>Total sections found:</strong> " . count($sections) . "<br>";
} catch(Exception $e) {
    echo "❌ Error checking content sections: " . $e->getMessage() . "<br>";
}
echo "</div>";

// Test 3: Check file system structure
echo "<div class='test-section info'><h3>📁 Testing File System</h3>";
$requiredDirs = [
    '../assets/images/',
    '../uploads/',
    '../assets/images/team/',
    '../assets/images/album1/'
];

foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        $fileCount = count(scandir($dir)) - 2; // subtract . and ..
        echo "✅ Directory '$dir' exists ($fileCount files)<br>";
    } else {
        echo "❌ Directory '$dir' missing<br>";
        // Try to create it
        if (mkdir($dir, 0755, true)) {
            echo "   ✅ Created directory '$dir'<br>";
        } else {
            echo "   ❌ Failed to create directory '$dir'<br>";
        }
    }
}
echo "</div>";

// Test 4: Check admin files
echo "<div class='test-section info'><h3>🔧 Testing Admin Files</h3>";
$adminFiles = [
    'dashboard.php',
    'edit-section.php', 
    'image-manager.php',
    'gallery-manager.php',
    'team-manager.php',
    'setup-database.php'
];

foreach ($adminFiles as $file) {
    if (file_exists($file)) {
        echo "✅ Admin file '$file' exists<br>";
    } else {
        echo "❌ Admin file '$file' missing<br>";
    }
}
echo "</div>";

// Test 5: Check API endpoints
echo "<div class='test-section info'><h3>🌐 Testing API Endpoints</h3>";
$apiFiles = [
    '../api/content.php',
    '../api/check-session.php',
    '../api/logout.php'
];

foreach ($apiFiles as $file) {
    if (file_exists($file)) {
        echo "✅ API file '$file' exists<br>";
    } else {
        echo "❌ API file '$file' missing<br>";
    }
}
echo "</div>";

// Test 6: Sample content retrieval
echo "<div class='test-section info'><h3>📄 Testing Content Retrieval</h3>";
try {
    $stmt = $conn->prepare("SELECT section_name, content FROM content_sections WHERE section_name = 'hero'");
    $stmt->execute();
    $heroContent = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($heroContent) {
        echo "✅ Hero section content retrieved successfully<br>";
        $content = json_decode($heroContent['content'], true);
        echo "<pre>Sample content: " . json_encode($content, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "❌ Hero section content not found<br>";
    }
} catch(Exception $e) {
    echo "❌ Error retrieving content: " . $e->getMessage() . "<br>";
}
echo "</div>";

// Test 7: Session simulation
echo "<div class='test-section info'><h3>🔐 Testing User Authentication</h3>";
require_once '../config/database.php';

// Test hardcoded users
$testUsers = [
    ['username' => 'admin', 'expected_role' => 'admin'],
    ['username' => 'mmeuser', 'expected_role' => 'mmeuser']
];

foreach ($testUsers as $testUser) {
    $users = Database::getUsers();
    if (isset($users[$testUser['username']])) {
        echo "✅ User '{$testUser['username']}' configured<br>";
        if ($users[$testUser['username']]['role'] === $testUser['expected_role']) {
            echo "   ✅ Role '{$testUser['expected_role']}' correct<br>";
        } else {
            echo "   ❌ Role mismatch for '{$testUser['username']}'<br>";
        }
    } else {
        echo "❌ User '{$testUser['username']}' not found<br>";
    }
}
echo "</div>";

// Test 8: Overall system health
echo "<div class='test-section info'><h3>🏥 System Health Check</h3>";

$healthScore = 0;
$totalTests = 8;

// Database connection (already tested above)
$healthScore += 1;

// Required tables
if (count($existingTables) >= 4) $healthScore += 1;

// Content sections  
if (count($sections ?? []) >= 8) $healthScore += 1;

// File system
$dirsExist = 0;
foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) $dirsExist++;
}
if ($dirsExist >= 3) $healthScore += 1;

// Admin files
$adminExists = 0;
foreach ($adminFiles as $file) {
    if (file_exists($file)) $adminExists++;
}
if ($adminExists >= 5) $healthScore += 1;

// API files
$apiExists = 0;
foreach ($apiFiles as $file) {
    if (file_exists($file)) $apiExists++;
}
if ($apiExists >= 2) $healthScore += 1;

// Content retrieval
if (isset($heroContent) && $heroContent) $healthScore += 1;

// Authentication
if (count(Database::getUsers()) >= 2) $healthScore += 1;

$healthPercentage = round(($healthScore / $totalTests) * 100);

if ($healthPercentage >= 90) {
    echo "<div class='success'>";
    echo "<h4>🎉 Excellent Health Score: $healthPercentage% ($healthScore/$totalTests tests passed)</h4>";
    echo "<p>Your Make My Event CMS is fully operational and ready for production use!</p>";
} elseif ($healthPercentage >= 70) {
    echo "<div class='info'>";
    echo "<h4>✅ Good Health Score: $healthPercentage% ($healthScore/$totalTests tests passed)</h4>";
    echo "<p>Your system is mostly functional. Check any failed tests above.</p>";
} else {
    echo "<div class='error'>";
    echo "<h4>⚠️ Poor Health Score: $healthPercentage% ($healthScore/$totalTests tests passed)</h4>";
    echo "<p>Several issues detected. Please review the test results and fix any problems.</p>";
}

echo "</div></div>";

// Final recommendations
echo "<div class='test-section info'><h3>💡 Recommendations</h3>";
echo "<ul>";
echo "<li>Change default passwords in <code>config/database.php</code></li>";
echo "<li>Set proper file permissions: <code>chmod 755 uploads/ assets/images/</code></li>";
echo "<li>Configure SSL for production deployment</li>";
echo "<li>Set up regular database backups</li>";
echo "<li>Test image upload functionality through the admin interface</li>";
echo "<li>Verify email configuration for contact forms</li>";
echo "</ul>";
echo "</div>";

echo "<div class='test-section success'>";
echo "<h3>🚀 Next Steps</h3>";
echo "<ol>";
echo "<li><strong>Login:</strong> <a href='../login.php' target='_blank'>Access Admin Panel</a> (admin/admin123 or mmeuser/mme123)</li>";
echo "<li><strong>Dashboard:</strong> <a href='dashboard.php' target='_blank'>Go to Dashboard</a></li>";
echo "<li><strong>Website:</strong> <a href='../index-cms.html' target='_blank'>View CMS Website</a></li>";
echo "<li><strong>Original:</strong> <a href='../index.html' target='_blank'>View Original Site</a></li>";
echo "</ol>";
echo "</div>";

?>