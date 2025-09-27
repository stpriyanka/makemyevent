<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Make My Event</title>
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
            font-size: 1.8rem;
            color: #f1e7c9;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-badge {
            background: rgba(241, 231, 201, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .welcome {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .welcome h2 {
            font-family: "Playfair Display", serif;
            color: #4d0e16;
            margin: 0 0 1rem 0;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #e9d3a5 0%, #f1e7c9 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #4d0e16;
        }
        
        .card-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .quick-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        
        .quick-actions h3 {
            color: #4d0e16;
            margin: 0 0 1rem 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #4d0e16 0%, #62131b 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(77, 14, 22, 0.3);
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .container {
                padding: 1rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>Make My Event CMS</h1>
            </div>
            <div class="user-info">
                <div class="user-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?> 
                    (<?php echo htmlspecialchars($_SESSION['user_role']); ?>)
                </div>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
            <p>Manage your Make My Event website content from this dashboard. You can edit text, upload images, and customize all sections of your website.</p>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card" onclick="location.href='edit-section.php?section=hero'">
                <div class="card-header">
                    <div class="card-icon">🏠</div>
                    <div class="card-title">Hero Section</div>
                </div>
                <div class="card-description">
                    Edit the main banner text, tagline, and statistics on your homepage
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='edit-section.php?section=about'">
                <div class="card-header">
                    <div class="card-icon">ℹ️</div>
                    <div class="card-title">About Section</div>
                </div>
                <div class="card-description">
                    Update your company story, description, and upload new images
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='edit-section.php?section=segments'">
                <div class="card-header">
                    <div class="card-icon">💎</div>
                    <div class="card-title">Our Segments</div>
                </div>
                <div class="card-description">
                    Manage your service packages: Classic, Premium, and Signature offerings
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='gallery-manager.php'">
                <div class="card-header">
                    <div class="card-icon">📸</div>
                    <div class="card-title">Gallery Management</div>
                </div>
                <div class="card-description">
                    Upload new photos, organize albums, and manage your portfolio gallery
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='team-manager.php'">
                <div class="card-header">
                    <div class="card-icon">👥</div>
                    <div class="card-title">Team Section</div>
                </div>
                <div class="card-description">
                    Add team members, update profiles, and manage team photos
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='testimonials-manager.php'">
                <div class="card-header">
                    <div class="card-icon">⭐</div>
                    <div class="card-title">Testimonials</div>
                </div>
                <div class="card-description">
                    Upload customer review images and manage testimonial content
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='edit-section.php?section=contact'">
                <div class="card-header">
                    <div class="card-icon">📞</div>
                    <div class="card-title">Contact Information</div>
                </div>
                <div class="card-description">
                    Update contact details, address, phone numbers, and map location
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='decoration-manager.php'">
                <div class="card-header">
                    <div class="card-icon">🎨</div>
                    <div class="card-title">Decoration Section</div>
                </div>
                <div class="card-description">
                    Upload new decoration photos and manage the decoration showcase
                </div>
            </div>
            
            <div class="dashboard-card" onclick="location.href='edit-section.php?section=navigation'">
                <div class="card-header">
                    <div class="card-icon">🧭</div>
                    <div class="card-title">Navigation Menu</div>
                </div>
                <div class="card-description">
                    Edit menu items, links, and navigation structure
                </div>
            </div>
        </div>
        
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="../index-cms.html" class="action-btn">
                    � Back to CMS Site
                </a>
                <a href="../index.html" class="action-btn" target="_blank">
                    👁️ Preview Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>