<?php
// Database configuration - SQLite version for easy development
define('DB_PATH', __DIR__ . '/makemyevent_cms.db');

// Database connection
class Database {
    private $db_path = DB_PATH;
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("sqlite:" . $this->db_path);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables if they don't exist
            $this->initializeTables();
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    private function initializeTables() {
        $sql = "
        -- Content management tables
        CREATE TABLE IF NOT EXISTS content_sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section_name TEXT UNIQUE NOT NULL,
            content TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_by TEXT
        );

        -- Image management table
        CREATE TABLE IF NOT EXISTS images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section TEXT NOT NULL,
            image_name TEXT NOT NULL,
            image_path TEXT NOT NULL,
            alt_text TEXT,
            caption TEXT,
            sort_order INTEGER DEFAULT 0,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            uploaded_by TEXT
        );

        -- Gallery Albums table
        CREATE TABLE IF NOT EXISTS gallery_albums (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            album_number INTEGER NOT NULL UNIQUE,
            album_name TEXT,
            created_by TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Gallery Images table
        CREATE TABLE IF NOT EXISTS gallery_images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            album_number INTEGER NOT NULL,
            image_number INTEGER NOT NULL,
            filename_large TEXT NOT NULL,
            filename_thumb TEXT NOT NULL,
            uploaded_by TEXT,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Team Members table
        CREATE TABLE IF NOT EXISTS team_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            position TEXT NOT NULL,
            bio TEXT,
            photo_filename TEXT,
            added_by TEXT,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_by TEXT,
            updated_at TIMESTAMP
        );
        ";
        
        $this->conn->exec($sql);
        
        // Insert default content if not exists
        $this->insertDefaultContent();
    }
    
    private function insertDefaultContent() {
        // Check if content already exists
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM content_sections");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $defaultContent = [
                'hero' => [
                    'eyebrow' => 'Wedding Profile',
                    'title' => 'Because your love story deserves a masterpiece.',
                    'script' => 'MAKE MY EVENT',
                    'stats' => [
                        ['number' => '10+ Years', 'label' => 'Experience'],
                        ['number' => '1200+', 'label' => 'Happy Couples'],
                        ['number' => '30%', 'label' => 'Recurrent Customers']
                    ]
                ],
                'navigation' => [
                    'links' => [
                        ['text' => 'About', 'href' => '#about'],
                        ['text' => 'Segments', 'href' => '#segments'],
                        ['text' => 'Gallery', 'href' => '#gallery'],
                        ['text' => 'Team', 'href' => '#team'],
                        ['text' => 'Testimonials', 'href' => '#testimonials'],
                        ['text' => 'Contact', 'href' => '#contact']
                    ]
                ],
                'about' => [
                    'eyebrow' => 'About Us',
                    'title' => 'We bring your events to life with creativity & precision.',
                    'description' => 'Make My Event was established in 2015 in Bangladesh with the purpose to bring your events to life with creativity, precision and a passion for excellence. We specialize in designing and executing dynamic experiences — from weddings and corporate events such as product launches and conferences to galas and team-building retreats. Our expert team handles every detail, blending innovative concepts with seamless logistics to deliver inspiring and connecting events. With a client-first approach and flawless execution, we transform ideas into unforgettable events. Trusted by fortune 1000+ clients across Bangladesh, we help businesses make a bold statement.'
                ],
                'segments' => [
                    'title' => 'OUR SEGMENTS',
                    'items' => [
                        [
                            'name' => 'CLASSIC',
                            'subtitle' => 'TOUCH',
                            'description' => 'Traditional yet graceful, this package covers all the essential décor elements to make your wedding simple, beautiful, memorable without compromise.'
                        ],
                        [
                            'name' => 'PREMIUM',
                            'subtitle' => 'TOUCH',
                            'description' => 'A touch of royal sophistication — designed for couples who want refined elegance, stylish details, and a charming ambience that leaves a lasting impression.'
                        ],
                        [
                            'name' => 'SIGNATURE',
                            'subtitle' => 'TOUCH',
                            'description' => 'The ultimate grandeur experience — a lavish, bespoke décor journey where every detail is tailored to perfection, creating a truly unforgettable celebration.'
                        ]
                    ]
                ],
                'gallery' => [
                    'title' => 'OUR GALLERY'
                ],
                'team' => [
                    'title' => 'OUR TEAM'
                ],
                'testimonials' => [
                    'title' => 'OUR TESTIMONIALS'
                ],
                'contact' => [
                    'eyebrow' => 'Contact',
                    'title' => 'Let\'s make your event unforgettable',
                    'address' => '94 Road No. 2, Block A, Niketon, Gulshan, Dhaka 1212',
                    'phone' => '+88 017 99 888 222',
                    'email' => 'smrafat@gmail.com',
                    'map_query' => '94+Road+No.+2,+Block+A,+Niketon,+Gulshan,+Dhaka+1212'
                ],
                'footer' => [
                    'text' => 'Make My Event. All rights reserved.'
                ]
            ];
            
            foreach ($defaultContent as $section => $content) {
                $stmt = $this->conn->prepare("INSERT INTO content_sections (section_name, content) VALUES (?, ?)");
                $stmt->execute([$section, json_encode($content)]);
            }
        }
    }
    
    public static function getUsers() {
        return [
            'admin' => [
                'password' => 'admin123',
                'role' => 'admin',
                'name' => 'Administrator'
            ],
            'mmeuser' => [
                'password' => 'mme123',
                'role' => 'mmeUser',
                'name' => 'MME User'
            ]
        ];
    }
}
?>