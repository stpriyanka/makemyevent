-- Create database
CREATE DATABASE IF NOT EXISTS makemyevent_cms;
USE makemyevent_cms;

-- Content management tables
CREATE TABLE IF NOT EXISTS content_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) UNIQUE NOT NULL,
    content JSON NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(50)
);

-- Image management table
CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(50) NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    sort_order INT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uploaded_by VARCHAR(50)
);

-- Gallery Albums table
CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_number INT NOT NULL UNIQUE,
    album_name VARCHAR(255),
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gallery Images table
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_number INT NOT NULL,
    image_number INT NOT NULL,
    filename_large VARCHAR(255) NOT NULL,
    filename_thumb VARCHAR(255) NOT NULL,
    uploaded_by VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_album (album_number)
);

-- Team Members table
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NOT NULL,
    bio TEXT,
    photo_filename VARCHAR(255),
    added_by VARCHAR(100),
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100),
    updated_at TIMESTAMP NULL
);

-- Insert default content
INSERT INTO content_sections (section_name, content) VALUES 
('hero', '{
    "eyebrow": "Wedding Profile",
    "title": "Because your love story deserves a masterpiece.",
    "script": "MAKE MY EVENT",
    "stats": [
        {"number": "10+ Years", "label": "Experience"},
        {"number": "1200+", "label": "Happy Couples"},
        {"number": "30%", "label": "Recurrent Customers"}
    ]
}'),

('navigation', '{
    "links": [
        {"text": "About", "href": "#about"},
        {"text": "Segments", "href": "#segments"},
        {"text": "Gallery", "href": "#gallery"},
        {"text": "Team", "href": "#team"},
        {"text": "Testimonials", "href": "#testimonials"},
        {"text": "Contact", "href": "#contact"}
    ]
}'),

('about', '{
    "eyebrow": "About Us",
    "title": "We bring your events to life with creativity & precision.",
    "description": "Make My Event was established in 2015 in Bangladesh with the purpose to bring your events to life with creativity, precision and a passion for excellence. We specialize in designing and executing dynamic experiences — from weddings and corporate events such as product launches and conferences to galas and team-building retreats. Our expert team handles every detail, blending innovative concepts with seamless logistics to deliver inspiring and connecting events. With a client-first approach and flawless execution, we transform ideas into unforgettable events. Trusted by fortune 1000+ clients across Bangladesh, we help businesses make a bold statement."
}'),

('segments', '{
    "title": "OUR SEGMENTS",
    "items": [
        {
            "name": "CLASSIC",
            "subtitle": "TOUCH",
            "description": "Traditional yet graceful, this package covers all the essential décor elements to make your wedding simple, beautiful, and memorable without compromise."
        },
        {
            "name": "PREMIUM", 
            "subtitle": "TOUCH",
            "description": "A touch of royal sophistication — designed for couples who want refined elegance, stylish details, and a charming ambience that leaves a lasting impression."
        },
        {
            "name": "SIGNATURE",
            "subtitle": "TOUCH", 
            "description": "The ultimate grandeur experience — a lavish, bespoke décor journey where every detail is tailored to perfection, creating a truly unforgettable celebration."
        }
    ]
}'),

('gallery', '{
    "title": "OUR GALLERY"
}'),

('team', '{
    "title": "OUR TEAM"
}'),

('testimonials', '{
    "title": "OUR TESTIMONIALS"
}'),

('contact', '{
    "eyebrow": "Contact",
    "title": "Let''s make your event unforgettable",
    "address": "94 Road No. 2, Block A, Niketon, Gulshan, Dhaka 1212",
    "phone": "+88 017 99 888 222",
    "email": "smrafat@gmail.com",
    "map_query": "94+Road+No.+2,+Block+A,+Niketon,+Gulshan,+Dhaka+1212"
}'),

('footer', '{
    "text": "Make My Event. All rights reserved."
}')

ON DUPLICATE KEY UPDATE content = VALUES(content);