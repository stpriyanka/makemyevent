# Make My Event CMS - Installation Guide

This Content Management System allows admin users to edit website content and upload images for the Make My Event wedding portfolio website.

## Prerequisites

- **Web Server**: Apache/Nginx with PHP support OR PHP Development Server
- **PHP**: Version 7.4 or higher with SQLite extension
- **Extensions**: PDO, PDO_SQLite, GD (for image handling)

## Installation Steps

### 1. Database Setup

**Automatic Setup (Recommended):**
The system uses SQLite database which is automatically created and initialized when you first access any admin page.

**Manual Setup:**
- The database file `makemyevent_cms.db` will be created automatically in the `config/` directory
- No additional database server installation required
- All tables and default content are created automatically

### 2. File Permissions

Set proper permissions for upload directories:
```bash
chmod 755 uploads/
chmod 755 assets/images/
chmod 755 assets/images/team/
```

### 3. Web Server Configuration

**Apache (.htaccess)**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

**Nginx**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## Default Login Credentials

### Admin User
- **Username**: `admin`
- **Password**: `admin123`
- **Role**: Administrator (full access)

### Regular User  
- **Username**: `mmeuser`
- **Password**: `mme123`
- **Role**: MME User (content editing)

⚠️ **Important**: Change these default passwords in `config/database.php` after installation!

## Features

### Content Management
- ✅ Hero Section (banner text, statistics)
- ✅ About Section (company info, descriptions)
- ✅ Services/Segments (packages, descriptions)
- ✅ Contact Information (address, phone, email)
- ✅ Navigation Menu (links, text)
- ✅ Footer Content

### Image Management
- ✅ Gallery Images (wedding photos, albums)
- ✅ Team Photos (staff members)
- ✅ Testimonial Images (customer reviews)
- ✅ Decoration Photos (event decorations)
- ✅ About Section Images

### Admin Features
- ✅ Role-based access control
- ✅ Secure file uploads
- ✅ Image preview and management
- ✅ Content versioning (who/when updated)
- ✅ Real-time content updates

## File Structure

```
MakeMyEvent/
├── admin/                 # Admin panel files
│   ├── dashboard.php      # Main admin dashboard
│   ├── edit-section.php   # Content editing interface
│   ├── image-manager.php  # Image upload/management
│   └── setup-database.php # Database initialization
├── api/                   # API endpoints
│   ├── content.php        # Content delivery API
│   ├── check-session.php  # Session validation
│   └── logout.php         # Logout handler
├── config/                # Configuration files
│   └── database.php       # Database settings
├── database/              # Database schema
│   └── schema.sql         # SQL setup script
├── uploads/               # User uploaded images
├── assets/                # Static website assets
├── index-cms.html         # CMS-enabled website version
├── index.html             # Original static version
└── login.php              # Admin login page
```

## Usage

### For Administrators

1. **Login**: Visit `http://your-domain.com/login.php`
2. **Dashboard**: Access all management features from the dashboard
3. **Edit Content**: Click on any section to edit text content
4. **Upload Images**: Use the Image Manager to upload photos
5. **Preview Changes**: Use the "Preview Website" button to see changes

### For Website Visitors

- **Static Version**: `index.html` (original luxury wedding site)
- **CMS Version**: `index-cms.html` (loads content from database)

### API Endpoints

- `api/content.php` - Get all content or specific sections
- `api/content.php?section=hero` - Get hero section content
- `api/check-session.php` - Check if admin is logged in

## Security Considerations

1. **Change Default Passwords**: Update credentials in `config/database.php`
2. **File Upload Security**: Only allows specific image types
3. **SQL Injection Protection**: Uses prepared statements
4. **XSS Prevention**: All output is escaped with `htmlspecialchars()`
5. **Session Security**: Proper session management and validation

## Troubleshooting

### Database Connection Issues
- The system uses SQLite - no separate database server required
- Database file is automatically created in `config/makemyevent_cms.db`
- Ensure PHP has SQLite extension enabled (`php -m | grep sqlite`)

### File Upload Problems
- Check directory permissions (755 for directories)
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Ensure `uploads/` directory exists and is writable

### Permission Errors
```bash
# Fix permissions
sudo chown -R www-data:www-data /path/to/makemyevent/
chmod -R 755 /path/to/makemyevent/
chmod -R 777 /path/to/makemyevent/uploads/
```

### Quick Development Server
```bash
# Start PHP development server
cd /path/to/makemyevent/
php -S localhost:8080
```

## Support

For technical support or feature requests, please check the documentation or contact the development team.

---

**Make My Event CMS v1.0** - A luxury wedding portfolio content management system.