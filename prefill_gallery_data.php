<?php
require_once 'config/database.php';

// Sample album titles and descriptions
$albumData = [
    1 => [
        'title' => 'Asif & Bahar Wedding',
        'description' => 'A beautiful traditional Bengali wedding ceremony'
    ],
    2 => [
        'title' => 'Royal Palace Reception',
        'description' => 'Grand reception with elegant decorations'
    ],
    3 => [
        'title' => 'Garden Party Wedding',
        'description' => 'Outdoor ceremony with natural beauty'
    ],
    4 => [
        'title' => 'Haldi Ceremony',
        'description' => 'Traditional turmeric ceremony celebrations'
    ],
    5 => [
        'title' => 'Mehndi Night',
        'description' => 'Colorful henna and dance celebrations'
    ],
    6 => [
        'title' => 'Gaye Holud',
        'description' => 'Bengali pre-wedding festivities'
    ],
    7 => [
        'title' => 'Sangam Celebration',
        'description' => 'Union of families and traditions'
    ],
    8 => [
        'title' => 'Ring Ceremony',
        'description' => 'Beautiful engagement moments'
    ],
    9 => [
        'title' => 'Bridal Photography',
        'description' => 'Stunning bridal portrait sessions'
    ],
    10 => [
        'title' => 'Reception Elegance',
        'description' => 'Sophisticated evening celebration'
    ]
];

// Function to check if an image file exists
function imageExists($path) {
    return file_exists($path);
}

// Function to get images for an album
function getAlbumImages($albumNum) {
    $images = [];
    $albumPath = "assets/images/album{$albumNum}/";
    
    // Check for cover image
    $coverPath = $albumPath . "cover-thumb.jpg";
    if (imageExists($coverPath)) {
        $images['cover'] = "assets/images/album{$albumNum}/cover-thumb.jpg";
    }
    
    // Check for individual images (up to 10 images per album)
    $imageList = [];
    for ($i = 1; $i <= 10; $i++) {
        $largePath = $albumPath . "img{$i}-large.jpg";
        $thumbPath = $albumPath . "img{$i}-thumb.jpg";
        
        if (imageExists($largePath) && imageExists($thumbPath)) {
            $imageList[] = [
                'large' => "assets/images/album{$albumNum}/img{$i}-large.jpg",
                'thumb' => "assets/images/album{$albumNum}/img{$i}-thumb.jpg"
            ];
        }
    }
    
    $images['images'] = $imageList;
    return $images;
}

// Build the complete gallery data structure
$galleryContent = [
    'title' => 'Our',
    'description' => 'Gallery',
    'background_image' => 'assets/images/Header Background-01.jpg',
    'albums' => []
];

// Generate data for all 10 albums
for ($i = 1; $i <= 10; $i++) {
    $albumImages = getAlbumImages($i);
    
    if (!empty($albumImages['images']) || !empty($albumImages['cover'])) {
        $album = [
            'title' => $albumData[$i]['title'],
            'description' => $albumData[$i]['description'],
            'cover' => $albumImages['cover'] ?? 'assets/images/album' . $i . '/cover-thumb.jpg',
            'images' => $albumImages['images']
        ];
        
        $galleryContent['albums'][] = $album;
    }
}

try {
    // Connect to database
    $pdo = new PDO("sqlite:config/makemyevent_cms.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if gallery section exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content_sections WHERE section_id = 'gallery'");
    $stmt->execute();
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        // Update existing gallery data
        $stmt = $pdo->prepare("UPDATE content_sections SET content = ?, updated_at = CURRENT_TIMESTAMP WHERE section_id = 'gallery'");
        $stmt->execute([json_encode($galleryContent)]);
        echo "Gallery data updated successfully!\n";
    } else {
        // Insert new gallery data
        $stmt = $pdo->prepare("INSERT INTO content_sections (section_id, content, created_at, updated_at) VALUES ('gallery', ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        $stmt->execute([json_encode($galleryContent)]);
        echo "Gallery data inserted successfully!\n";
    }
    
    // Display the prefilled data
    echo "\n=== PREFILLED GALLERY DATA ===\n";
    echo "Section Title: " . $galleryContent['title'] . " " . $galleryContent['description'] . "\n";
    echo "Background Image: " . $galleryContent['background_image'] . "\n";
    echo "Total Albums: " . count($galleryContent['albums']) . "\n\n";
    
    foreach ($galleryContent['albums'] as $index => $album) {
        echo "Album " . ($index + 1) . ":\n";
        echo "  Title: " . $album['title'] . "\n";
        echo "  Description: " . $album['description'] . "\n";
        echo "  Cover Image: " . $album['cover'] . "\n";
        echo "  Images Count: " . count($album['images']) . "\n";
        
        if (!empty($album['images'])) {
            echo "  Image Files:\n";
            foreach ($album['images'] as $imgIndex => $img) {
                echo "    " . ($imgIndex + 1) . ". Large: " . $img['large'] . "\n";
                echo "       Thumb: " . $img['thumb'] . "\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>