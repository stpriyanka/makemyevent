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
        if (isset($_POST['add_member'])) {
            $name = trim($_POST['member_name']);
            $position = trim($_POST['member_position']);
            $bio = trim($_POST['member_bio']);
            
            // Handle file upload
            $fileName = null;
            if (isset($_FILES['member_photo']) && $_FILES['member_photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../assets/images/team/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $tmpName = $_FILES['member_photo']['tmp_name'];
                $originalName = $_FILES['member_photo']['name'];
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $fileName = strtolower(str_replace(' ', '_', $name)) . '.' . $extension;
                
                // Validate file
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $fileType = $_FILES['member_photo']['type'];
                $fileSize = $_FILES['member_photo']['size'];
                
                if (in_array($fileType, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) {
                    if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                        // Insert into database
                        $stmt = $conn->prepare("INSERT INTO team_members (name, position, bio, photo_filename, added_by, added_at) VALUES (?, ?, ?, ?, ?, NOW())");
                        $stmt->execute([$name, $position, $bio, $fileName, $_SESSION['username']]);
                        
                        $message = "Team member added successfully!";
                        $messageType = 'success';
                    } else {
                        $message = "Error uploading photo.";
                        $messageType = 'error';
                    }
                } else {
                    $message = "Invalid file type or size. Please use JPG/PNG under 5MB.";
                    $messageType = 'error';
                }
            } else {
                // Add without photo
                $stmt = $conn->prepare("INSERT INTO team_members (name, position, bio, added_by, added_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $position, $bio, $_SESSION['username']]);
                
                $message = "Team member added successfully!";
                $messageType = 'success';
            }
        }
        
        if (isset($_POST['update_member'])) {
            $memberId = (int)$_POST['member_id'];
            $name = trim($_POST['member_name']);
            $position = trim($_POST['member_position']);
            $bio = trim($_POST['member_bio']);
            
            // Handle photo update
            if (isset($_FILES['member_photo']) && $_FILES['member_photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../assets/images/team/';
                $extension = pathinfo($_FILES['member_photo']['name'], PATHINFO_EXTENSION);
                $fileName = strtolower(str_replace(' ', '_', $name)) . '.' . $extension;
                
                if (move_uploaded_file($_FILES['member_photo']['tmp_name'], $uploadDir . $fileName)) {
                    $stmt = $conn->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, photo_filename = ?, updated_by = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$name, $position, $bio, $fileName, $_SESSION['username'], $memberId]);
                } else {
                    $message = "Error uploading new photo.";
                    $messageType = 'error';
                }
            } else {
                $stmt = $conn->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, updated_by = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$name, $position, $bio, $_SESSION['username'], $memberId]);
            }
            
            $message = "Team member updated successfully!";
            $messageType = 'success';
        }
        
        if (isset($_POST['delete_member'])) {
            $memberId = (int)$_POST['member_id'];
            
            // Get photo filename before deletion
            $stmt = $conn->prepare("SELECT photo_filename FROM team_members WHERE id = ?");
            $stmt->execute([$memberId]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
            $stmt->execute([$memberId]);
            
            // Delete photo file if exists
            if ($member && $member['photo_filename']) {
                $photoPath = '../assets/images/team/' . $member['photo_filename'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
            
            $message = "Team member deleted successfully!";
            $messageType = 'success';
        }
        
    } catch(Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Get all team members
$stmt = $conn->prepare("SELECT * FROM team_members ORDER BY name");
$stmt->execute();
$teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Manager - Make My Event CMS</title>
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

        .team-section {
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

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8B5A5A;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #666, #888);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            text-align: center;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }

        .member-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            border: 4px solid #8B5A5A;
        }

        .member-photo.placeholder {
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #8B5A5A;
        }

        .member-name {
            font-family: "Playfair Display", serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #8B5A5A;
            margin-bottom: 0.5rem;
        }

        .member-position {
            font-weight: 500;
            color: #666;
            margin-bottom: 1rem;
        }

        .member-bio {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .member-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
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

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .close {
            font-size: 2rem;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #8B5A5A;
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

            .team-grid {
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
                    <a href="team-manager.php">Team</a>
                    <a href="../api/logout.php">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1 class="page-title">Team Manager</h1>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Add New Team Member -->
            <div class="team-section">
                <h2 class="section-title">Add New Team Member</h2>
                <form method="POST" enctype="multipart/form-data" class="form-grid">
                    <div class="form-group">
                        <label for="member_name">Name</label>
                        <input type="text" id="member_name" name="member_name" required>
                    </div>
                    <div class="form-group">
                        <label for="member_position">Position</label>
                        <input type="text" id="member_position" name="member_position" placeholder="e.g., Wedding Planner, Designer" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="member_bio">Bio</label>
                        <textarea id="member_bio" name="member_bio" placeholder="Brief description about the team member..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Photo (Optional)</label>
                        <div class="file-upload">
                            <label for="member_photo" class="upload-label">
                                👤 Click to select photo<br>
                                <small>JPG, PNG - Max 5MB</small>
                            </label>
                            <input type="file" id="member_photo" name="member_photo" accept="image/jpeg,image/jpg,image/png">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_member" class="btn">Add Team Member</button>
                    </div>
                </form>
            </div>

            <!-- Team Members -->
            <div class="team-section">
                <h2 class="section-title">Team Members (<?php echo count($teamMembers); ?>)</h2>
                <?php if (empty($teamMembers)): ?>
                    <p>No team members added yet. Add your first team member above!</p>
                <?php else: ?>
                    <div class="team-grid">
                        <?php foreach ($teamMembers as $member): ?>
                            <div class="team-card">
                                <?php if ($member['photo_filename']): ?>
                                    <img src="../assets/images/team/<?php echo htmlspecialchars($member['photo_filename']); ?>" 
                                         alt="<?php echo htmlspecialchars($member['name']); ?>" class="member-photo">
                                <?php else: ?>
                                    <div class="member-photo placeholder">👤</div>
                                <?php endif; ?>
                                
                                <div class="member-name"><?php echo htmlspecialchars($member['name']); ?></div>
                                <div class="member-position"><?php echo htmlspecialchars($member['position']); ?></div>
                                
                                <?php if ($member['bio']): ?>
                                    <div class="member-bio"><?php echo htmlspecialchars($member['bio']); ?></div>
                                <?php endif; ?>
                                
                                <div class="member-actions">
                                    <button onclick="editMember(<?php echo $member['id']; ?>)" class="btn btn-small">Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this team member?');">
                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" name="delete_member" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Edit Member Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Team Member</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" id="edit_member_id" name="member_id">
                <div class="form-group">
                    <label for="edit_member_name">Name</label>
                    <input type="text" id="edit_member_name" name="member_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_member_position">Position</label>
                    <input type="text" id="edit_member_position" name="member_position" required>
                </div>
                <div class="form-group full-width">
                    <label for="edit_member_bio">Bio</label>
                    <textarea id="edit_member_bio" name="member_bio"></textarea>
                </div>
                <div class="form-group">
                    <label>New Photo (Optional)</label>
                    <div class="file-upload">
                        <label for="edit_member_photo" class="upload-label">
                            👤 Click to select new photo<br>
                            <small>Leave empty to keep current photo</small>
                        </label>
                        <input type="file" id="edit_member_photo" name="member_photo" accept="image/jpeg,image/jpg,image/png">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" name="update_member" class="btn">Update Team Member</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const teamMembers = <?php echo json_encode($teamMembers); ?>;

        function editMember(memberId) {
            const member = teamMembers.find(m => m.id == memberId);
            if (!member) return;

            document.getElementById('edit_member_id').value = member.id;
            document.getElementById('edit_member_name').value = member.name;
            document.getElementById('edit_member_position').value = member.position;
            document.getElementById('edit_member_bio').value = member.bio || '';
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // File upload preview
        document.getElementById('member_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = this.parentNode.querySelector('.upload-label');
            
            if (file) {
                label.innerHTML = `👤 ${file.name}<br><small>Ready to upload</small>`;
            } else {
                label.innerHTML = '👤 Click to select photo<br><small>JPG, PNG - Max 5MB</small>';
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>