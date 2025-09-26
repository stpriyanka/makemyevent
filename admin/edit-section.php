<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get section parameter
$section = $_GET['section'] ?? '';
$valid_sections = ['hero', 'about', 'segments', 'contact', 'navigation', 'footer'];

if (!in_array($section, $valid_sections)) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if ($_POST && isset($_POST['update_content'])) {
    try {
        $content = $_POST['content'];
        $updated_by = $_SESSION['username'];
        
        $query = "INSERT INTO content_sections (section_name, content, updated_by) VALUES (?, ?, ?) 
                 ON DUPLICATE KEY UPDATE content = VALUES(content), updated_by = VALUES(updated_by)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$section, json_encode($content), $updated_by]);
        
        $success_message = "Content updated successfully!";
    } catch (Exception $e) {
        $error_message = "Error updating content: " . $e->getMessage();
    }
}

// Fetch current content
try {
    $query = "SELECT content FROM content_sections WHERE section_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$section]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $current_content = $result ? json_decode($result['content'], true) : [];
} catch (Exception $e) {
    $current_content = [];
}

// Section configurations
$section_configs = [
    'hero' => [
        'title' => 'Hero Section',
        'description' => 'Main banner content and statistics',
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow Text', 'type' => 'text'],
            'title' => ['label' => 'Main Title', 'type' => 'textarea'],
            'script' => ['label' => 'Script Text', 'type' => 'text'],
            'stats' => ['label' => 'Statistics', 'type' => 'stats']
        ]
    ],
    'about' => [
        'title' => 'About Section',
        'description' => 'Company information and story',
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow Text', 'type' => 'text'],
            'title' => ['label' => 'Section Title', 'type' => 'textarea'],
            'description' => ['label' => 'Description', 'type' => 'textarea']
        ]
    ],
    'segments' => [
        'title' => 'Our Segments',
        'description' => 'Service packages and offerings',
        'fields' => [
            'title' => ['label' => 'Section Title', 'type' => 'text'],
            'items' => ['label' => 'Service Items', 'type' => 'segments']
        ]
    ],
    'contact' => [
        'title' => 'Contact Information',
        'description' => 'Contact details and location',
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow Text', 'type' => 'text'],
            'title' => ['label' => 'Section Title', 'type' => 'text'],
            'address' => ['label' => 'Address', 'type' => 'textarea'],
            'phone' => ['label' => 'Phone Number', 'type' => 'text'],
            'email' => ['label' => 'Email Address', 'type' => 'email'],
            'map_query' => ['label' => 'Map Search Query', 'type' => 'text']
        ]
    ],
    'navigation' => [
        'title' => 'Navigation Menu',
        'description' => 'Website navigation links',
        'fields' => [
            'links' => ['label' => 'Navigation Links', 'type' => 'navigation']
        ]
    ],
    'footer' => [
        'title' => 'Footer',
        'description' => 'Footer text and copyright',
        'fields' => [
            'text' => ['label' => 'Footer Text', 'type' => 'text']
        ]
    ]
];

$config = $section_configs[$section];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo $config['title']; ?> - Make My Event CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: Inter, system-ui, sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #4d0e16 0%, #62131b 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .logo h1 {
            font-family: "Playfair Display", serif;
            margin: 0;
            font-size: 1.5rem;
            color: #f1e7c9;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .section-header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .section-header h2 {
            font-family: "Playfair Display", serif;
            color: #4d0e16;
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
        }
        
        .section-header p {
            color: #666;
            margin: 0;
        }
        
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #4d0e16;
        }
        
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            outline: none;
            border-color: #e9d3a5;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .stats-container,
        .segments-container,
        .navigation-container {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
        }
        
        .stat-item,
        .segment-item,
        .nav-item {
            background: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        
        .stat-item:last-child,
        .segment-item:last-child,
        .nav-item:last-child {
            margin-bottom: 0;
        }
        
        .item-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .item-title {
            font-weight: 600;
            color: #4d0e16;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
        }
        
        .add-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            margin-top: 1rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .save-btn {
            background: linear-gradient(135deg, #4d0e16 0%, #62131b 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(77, 14, 22, 0.3);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>Edit <?php echo $config['title']; ?></h1>
            </div>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
    
    <div class="container">
        <div class="section-header">
            <h2><?php echo $config['title']; ?></h2>
            <p><?php echo $config['description']; ?></p>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <?php foreach ($config['fields'] as $field_name => $field_config): ?>
                    <div class="form-group">
                        <label><?php echo $field_config['label']; ?></label>
                        
                        <?php if ($field_config['type'] === 'text' || $field_config['type'] === 'email'): ?>
                            <input type="<?php echo $field_config['type']; ?>" 
                                   name="content[<?php echo $field_name; ?>]" 
                                   value="<?php echo htmlspecialchars($current_content[$field_name] ?? ''); ?>">
                        
                        <?php elseif ($field_config['type'] === 'textarea'): ?>
                            <textarea name="content[<?php echo $field_name; ?>]" rows="3"><?php echo htmlspecialchars($current_content[$field_name] ?? ''); ?></textarea>
                        
                        <?php elseif ($field_config['type'] === 'stats'): ?>
                            <div class="stats-container">
                                <?php $stats = $current_content[$field_name] ?? []; ?>
                                <?php foreach ($stats as $index => $stat): ?>
                                    <div class="stat-item">
                                        <div class="item-header">
                                            <span class="item-title">Statistic <?php echo $index + 1; ?></span>
                                        </div>
                                        <input type="text" placeholder="Number (e.g., 10+ Years)" 
                                               name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][number]" 
                                               value="<?php echo htmlspecialchars($stat['number'] ?? ''); ?>">
                                        <input type="text" placeholder="Label (e.g., Experience)" 
                                               name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][label]" 
                                               value="<?php echo htmlspecialchars($stat['label'] ?? ''); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        
                        <?php elseif ($field_config['type'] === 'segments'): ?>
                            <div class="segments-container">
                                <?php $segments = $current_content[$field_name] ?? []; ?>
                                <?php foreach ($segments as $index => $segment): ?>
                                    <div class="segment-item">
                                        <div class="item-header">
                                            <span class="item-title"><?php echo htmlspecialchars($segment['name'] ?? 'Segment ' . ($index + 1)); ?></span>
                                        </div>
                                        <input type="text" placeholder="Package Name (e.g., CLASSIC)" 
                                               name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][name]" 
                                               value="<?php echo htmlspecialchars($segment['name'] ?? ''); ?>">
                                        <input type="text" placeholder="Subtitle (e.g., TOUCH)" 
                                               name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][subtitle]" 
                                               value="<?php echo htmlspecialchars($segment['subtitle'] ?? ''); ?>">
                                        <textarea placeholder="Description" rows="3"
                                                  name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][description]"><?php echo htmlspecialchars($segment['description'] ?? ''); ?></textarea>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        
                        <?php elseif ($field_config['type'] === 'navigation'): ?>
                            <div class="navigation-container">
                                <?php $links = $current_content[$field_name] ?? []; ?>
                                <?php foreach ($links as $index => $link): ?>
                                    <div class="nav-item">
                                        <div class="item-header">
                                            <span class="item-title"><?php echo htmlspecialchars($link['text'] ?? 'Link ' . ($index + 1)); ?></span>
                                        </div>
                                        <input type="text" placeholder="Link Text" 
                                               name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][text]" 
                                               value="<?php echo htmlspecialchars($link['text'] ?? ''); ?>">
                                        <input type="text" placeholder="Link URL (e.g., #about)" 
                                               name="content[<?php echo $field_name; ?>][<?php echo $index; ?>][href]" 
                                               value="<?php echo htmlspecialchars($link['href'] ?? ''); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-actions">
                    <button type="submit" name="update_content" class="save-btn">
                        💾 Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>