-- SQLite Schema for MakeMyEvent CMS
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

-- Insert default content
INSERT OR REPLACE INTO content_sections (section_name, content) VALUES 
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
    "description": "Make My Event was established in 2015 in Bangladesh with the purpose to bring your events to life with creativity, precision and a passion for excellence. We specialize in designing and executing dynamic experiences — from weddings and corporate events such as product launches and conferences to galas and team-building retreats. Our expert team handles every detail, blending innovative concepts with seamless logistics to deliver inspiring and connecting events. With a client-first approach and flawless execution, we transform ideas into unforgettable events. Trusted by fortune 1000+ clients across Bangladesh, we help businesses make a bold statement.",
    "frame_portrait": "assets/images/frame-portrait.jpg",
    "frame_top": "assets/images/Framed couple top.jpg",
    "frame_bottom": "assets/images/frame-bottom.jpg",
    "background_image": "assets/images/about-bg new-01.jpg"
}'),

('segments', '{
    "title": "OUR SEGMENTS",
    "classic_title": "CLASSIC<br><span class=\"segment-subtitle\">TOUCH</span>",
    "classic_text": "Traditional yet graceful, this package covers all the essential décor elements to make your wedding simple, beautiful, and memorable without compromise.",
    "premium_title": "PREMIUM<br><span class=\"segment-subtitle\">TOUCH</span>",
    "premium_text": "A touch of royal sophistication — designed for couples who want refined elegance, stylish details, and a charming ambience that leaves a lasting impression.",
    "signature_title": "SIGNATURE<br><span class=\"segment-subtitle\">TOUCH</span>",
    "signature_text": "The ultimate grandeur experience — a lavish, bespoke décor journey where every detail is tailored to perfection, creating a truly unforgettable celebration."
}'),

('gallery', '{
    "title": "OUR",
    "description": "GALLERY",
    "background_image": "assets/images/Header Background-01.jpg",
    "albums": [
        {
            "cover": "assets/images/album1/cover-thumb.jpg",
            "title": "Asif Bahar''s Haldi Ceremony",
            "images": [
                {"src": "assets/images/album1/img1-large.jpg", "thumb": "assets/images/album1/img1-thumb.jpg"},
                {"src": "assets/images/album1/img4-large.jpg", "thumb": "assets/images/album1/img4-thumb.jpg"},
                {"src": "assets/images/album1/img3-large.jpg", "thumb": "assets/images/album1/img3-thumb.jpg"}
            ]
        }
    ]
}'),

('decoration', '{
    "frame_left": "assets/images/frame-left.jpg",
    "frame_center": "assets/images/frame-center.jpg",
    "frame_right": "assets/images/frame-right.jpg"
}'),

('trusted-scene', '{
    "line_1": "Trusted by 98%<br>Happy couples",
    "line_2": "of Bangladesh",
    "badge_image": "assets/images/trusted feedback info image-03.png",
    "background_image": "assets/images/Trusted Feedback 98% Bg-02.jpg"
}'),

('team', '{
    "title": "OUR",
    "description": "TEAM"
}'),

('testimonials', '{
    "title": "OUR",
    "description": "TESTIMONIALS"
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
}');