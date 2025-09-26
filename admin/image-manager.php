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

// Create uploads directory if it doesn't exist
$upload_dir = '../uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle file upload
if ($_POST && isset($_POST['upload_image'])) {
    $section = $_POST['section'];
    $alt_text = $_POST['alt_text'] ?? '';
    $caption = $_POST['caption'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                try {
                    $query = "INSERT INTO images (section, image_name, image_path, alt_text, caption, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$section, $file['name'], 'uploads/' . $new_filename, $alt_text, $caption, $_SESSION['username']]);
                    
                    $success_message = "Image uploaded successfully!";
                } catch (Exception $e) {
                    $error_message = "Error saving image to database: " . $e->getMessage();
                }
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        $error_message = "No file selected or upload error.";
    }
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $image_id = $_GET['delete_image'];
    
    try {
        // Get image path before deleting from database
        $query = "SELECT image_path FROM images WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$image_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Delete from database
            $query = "DELETE FROM images WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$image_id]);
            
            // Delete physical file
            $file_path = '../' . $result['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            $success_message = "Image deleted successfully!";
        }
    } catch (Exception $e) {
        $error_message = "Error deleting image: " . $e->getMessage();
    }
}

// Fetch all images by section
$sections = ['gallery', 'team', 'testimonials', 'decoration', 'about'];
$images_by_section = [];

foreach ($sections as $section_name) {
    try {
        $query = "SELECT * FROM images WHERE section = ? ORDER BY sort_order, uploaded_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute([$section_name]);
        $images_by_section[$section_name] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $images_by_section[$section_name] = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Manager - Make My Event CMS</title>
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
            max-width: 1200px;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .upload-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .upload-section h2 {
            font-family: "Playfair Display", serif;
            color: #4d0e16;
            margin: 0 0 1rem 0;
        }
        
        .upload-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #4d0e16;
        }
        
        select,
        input[type="text"],
        input[type="file"] {
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .upload-btn {
            background: linear-gradient(135deg, #4d0e16 0%, #62131b 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(77, 14, 22, 0.3);
        }
        
        .section-images {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-family: "Playfair Display", serif;
            color: #4d0e16;
            margin: 0 0 1rem 0;
            text-transform: capitalize;
        }
        
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .image-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }
        
        .image-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .image-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .image-info {
            padding: 1rem;
        }
        
        .image-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }
        
        .image-meta {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .image-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
        }
        
        .view-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
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
        
        .empty-section {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-style: italic;
        }
        
        .file-info {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>Image Manager</h1>
            </div>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="upload-section">
            <h2>📤 Upload New Image</h2>
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label for="section">Section</label>
                    <select name="section" id="section" required>
                        <option value="">Select Section</option>
                        <option value="gallery">Gallery</option>
                        <option value="team">Team</option>
                        <option value="testimonials">Testimonials</option>
                        <option value="decoration">Decoration</option>
                        <option value="about">About</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Image File</label>
                    <input type="file" name="image" id="image" accept="image/*" required>
                    <div class="file-info">Supported: JPG, PNG, GIF (Max: 10MB)</div>
                </div>
                
                <div class="form-group">
                    <label for="alt_text">Alt Text</label>
                    <input type="text" name="alt_text" id="alt_text" placeholder="Describe the image">
                </div>
                
                <div class="form-group">
                    <label for="caption">Caption</label>
                    <input type="text" name="caption" id="caption" placeholder="Optional caption">
                </div>
                
                <div class="form-group">
                    <button type="submit" name="upload_image" class="upload-btn">
                        📤 Upload Image
                    </button>
                </div>
            </form>
        </div>
        
        <?php foreach ($sections as $section_name): ?>
            <div class="section-images">
                <h2 class="section-title"><?php echo $section_name; ?> Images</h2>
                
                <?php if (empty($images_by_section[$section_name])): ?>
                    <div class="empty-section">
                        No images uploaded for this section yet.
                    </div>
                <?php else: ?>
                    <div class="images-grid">
                        <?php foreach ($images_by_section[$section_name] as $image): ?>
                            <div class="image-card">
                                <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['alt_text']); ?>"
                                     loading="lazy">
                                <div class="image-info">
                                    <div class="image-name"><?php echo htmlspecialchars($image['image_name']); ?></div>
                                    <?php if ($image['caption']): ?>
                                        <div class="image-meta"><?php echo htmlspecialchars($image['caption']); ?></div>
                                    <?php endif; ?>
                                    <div class="image-meta">
                                        Uploaded: <?php echo date('M j, Y', strtotime($image['uploaded_at'])); ?>
                                    </div>
                                    <div class="image-actions">
                                        <a href="../<?php echo htmlspecialchars($image['image_path']); ?>" 
                                           target="_blank" class="view-btn">View</a>
                                        <a href="?delete_image=<?php echo $image['id']; ?>" 
                                           class="delete-btn"
                                           onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>