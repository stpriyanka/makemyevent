<?php
session_start();
require_once '../config/database.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['create_album'])) {
            // Create new album directory
            $albumName = trim($_POST['album_name']);
            $albumNum = (int)$_POST['album_number'];
            
            if (!empty($albumName) && $albumNum > 0) {
                $albumDir = "../assets/images/album{$albumNum}";
                
                if (!file_exists($albumDir)) {
                    mkdir($albumDir, 0755, true);
                    
                    // Insert album info into database
                    $stmt = $conn->prepare("INSERT INTO gallery_albums (album_number, album_name, created_by, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$albumNum, $albumName, $_SESSION['username']]);
                    
                    $message = "Album created successfully!";
                    $messageType = 'success';
                } else {
                    $message = "Album directory already exists!";
                    $messageType = 'error';
                }
            }
        }
        
        if (isset($_POST['upload_gallery']) && isset($_FILES['gallery_images'])) {
            $albumNum = (int)$_POST['album_num'];
            $albumDir = "../assets/images/album{$albumNum}";
            
            if (!file_exists($albumDir)) {
                mkdir($albumDir, 0755, true);
            }
            
            $uploadCount = 0;
            $files = $_FILES['gallery_images'];
            
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $fileName = $files['name'][$i];
                    $fileSize = $files['size'][$i];
                    $fileType = $files['type'][$i];
                    
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!in_array($fileType, $allowedTypes)) {
                        continue;
                    }
                    
                    // Validate file size (max 5MB)
                    if ($fileSize > 5 * 1024 * 1024) {
                        continue;
                    }
                    
                    // Generate new filename
                    $nextImg = getNextImageNumber($albumDir);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    
                    $largeFileName = "img{$nextImg}-large.{$extension}";
                    $thumbFileName = "img{$nextImg}-thumb.{$extension}";
                    
                    // Move uploaded file
                    if (move_uploaded_file($tmpName, $albumDir . '/' . $largeFileName)) {
                        // Create thumbnail (simple copy for now - could add image resizing)
                        copy($albumDir . '/' . $largeFileName, $albumDir . '/' . $thumbFileName);
                        
                        // Insert into database
                        $stmt = $conn->prepare("INSERT INTO gallery_images (album_number, image_number, filename_large, filename_thumb, uploaded_by, uploaded_at) VALUES (?, ?, ?, ?, ?, NOW())");
                        $stmt->execute([$albumNum, $nextImg, $largeFileName, $thumbFileName, $_SESSION['username']]);
                        
                        $uploadCount++;
                    }
                }
            }
            
            $message = "{$uploadCount} images uploaded successfully!";
            $messageType = 'success';
        }
        
    } catch(Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Get existing albums
$stmt = $conn->prepare("SELECT DISTINCT album_number, album_name, created_at FROM gallery_albums ORDER BY album_number");
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function
function getNextImageNumber($albumDir) {
    $maxNum = 0;
    if (is_dir($albumDir)) {
        $files = scandir($albumDir);
        foreach ($files as $file) {
            if (preg_match('/img(\d+)-large\./', $file, $matches)) {
                $maxNum = max($maxNum, (int)$matches[1]);
            }
        }
    }
    return $maxNum + 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Manager - Make My Event CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #8B5A5A 0%, #4A4A4A 100%);
            min-height: 100vh;
            color: #333;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: "Playfair Display", serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #8B5A5A;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #8B5A5A;
        }

        .main-content {
            padding: 3rem 0;
        }

        .page-title {
            font-family: "Playfair Display", serif;
            font-size: 2.5rem;
            color: white;
            text-align: center;
            margin-bottom: 3rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .gallery-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .section-title {
            font-family: "Playfair Display", serif;
            font-size: 1.8rem;
            color: #8B5A5A;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #8B5A5A;
        }

        .file-upload {
            border: 2px dashed #8B5A5A;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload:hover {
            background-color: rgba(139, 90, 90, 0.05);
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .upload-label {
            display: block;
            cursor: pointer;
            color: #8B5A5A;
            font-weight: 500;
        }

        .btn {
            background: linear-gradient(135deg, #8B5A5A, #A67B7B);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 90, 90, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #666, #888);
        }

        .albums-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .album-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .album-card:hover {
            transform: translateY(-5px);
        }

        .album-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #8B5A5A;
            margin-bottom: 0.5rem;
        }

        .album-name {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .album-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .album-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .message.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .albums-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="logo">Make My Event CMS</div>
                <div class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="image-manager.php">Images</a>
                    <a href="gallery-manager.php">Gallery</a>
                    <a href="../api/logout.php">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1 class="page-title">Gallery Manager</h1>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Create New Album -->
            <div class="gallery-section">
                <h2 class="section-title">Create New Album</h2>
                <form method="POST" class="form-grid">
                    <div class="form-group">
                        <label for="album_number">Album Number</label>
                        <input type="number" id="album_number" name="album_number" min="1" max="50" required>
                    </div>
                    <div class="form-group">
                        <label for="album_name">Album Name</label>
                        <input type="text" id="album_name" name="album_name" placeholder="e.g., John & Sarah Wedding" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="create_album" class="btn">Create Album</button>
                    </div>
                </form>
            </div>

            <!-- Upload Images -->
            <div class="gallery-section">
                <h2 class="section-title">Upload Gallery Images</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="album_num">Select Album</label>
                            <select id="album_num" name="album_num" required>
                                <option value="">Choose Album...</option>
                                <?php for ($i = 1; $i <= 15; $i++): ?>
                                    <option value="<?php echo $i; ?>">Album <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Images (JPG, PNG - Max 5MB each)</label>
                            <div class="file-upload">
                                <label for="gallery_images" class="upload-label">
                                    📷 Click to select multiple images<br>
                                    <small>You can select multiple images at once</small>
                                </label>
                                <input type="file" id="gallery_images" name="gallery_images[]" multiple accept="image/jpeg,image/jpg,image/png">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="upload_gallery" class="btn">Upload Images</button>
                </form>
            </div>

            <!-- Existing Albums -->
            <div class="gallery-section">
                <h2 class="section-title">Existing Albums</h2>
                <?php if (empty($albums)): ?>
                    <p>No albums created yet. Create your first album above!</p>
                <?php else: ?>
                    <div class="albums-grid">
                        <?php foreach ($albums as $album): ?>
                            <div class="album-card">
                                <div class="album-number">Album <?php echo $album['album_number']; ?></div>
                                <div class="album-name"><?php echo htmlspecialchars($album['album_name'] ?? 'Unnamed Album'); ?></div>
                                <div class="album-date">Created: <?php echo date('M j, Y', strtotime($album['created_at'])); ?></div>
                                <div class="album-actions">
                                    <a href="album-editor.php?album=<?php echo $album['album_number']; ?>" class="btn btn-small">Edit</a>
                                    <a href="../assets/images/album<?php echo $album['album_number']; ?>" target="_blank" class="btn btn-secondary btn-small">View Files</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // File upload preview
        document.getElementById('gallery_images').addEventListener('change', function(e) {
            const files = e.target.files;
            const label = document.querySelector('.upload-label');
            
            if (files.length > 0) {
                label.innerHTML = `📷 ${files.length} file(s) selected<br><small>Ready to upload</small>`;
            } else {
                label.innerHTML = '📷 Click to select multiple images<br><small>You can select multiple images at once</small>';
            }
        });
    </script>
</body>
</html>