# Make My Event - Wedding Planning CMS

A premium wedding event planning website with a complete Content Management System (CMS) for "Make My Event" - a luxury Bangladeshi event management company established in 2015.

## 🌟 Features

- **Luxury Wedding Portfolio**: Immersive visual experience with sophisticated interactions
- **Dynamic Content Management**: Admin panel for editing all website content
- **Database-Driven**: Both original and CMS websites fetch content from SQLite database
- **Responsive Design**: Mobile-first approach with fluid typography and layouts
- **Gallery System**: LightGallery integration with album browsing
- **Admin Authentication**: Secure login system with role-based access
- **Real-time Updates**: Changes made in admin panel appear immediately on both sites

## 🏗️ Architecture

- **Frontend**: Pure HTML/CSS/JavaScript (no build process)
- **Backend**: PHP 8.0+ with SQLite database
- **Database**: SQLite with PDO operations and auto-initialization
- **CDN Dependencies**: LightGallery (2.7.2) for photo galleries
- **Fonts**: Google Fonts (Playfair Display, Great Vibes, Inter) + Local fonts

## 📋 Prerequisites

- **PHP 8.0 or higher**
- **SQLite support** (usually included with PHP)
- **Web browser** with JavaScript enabled
- **Git** for cloning the repository

## 🚀 Quick Setup

### 1. Clone the Repository

```bash
git clone https://github.com/stpriyanka/makemyevent.git
cd makemyevent
```

### 2. Start the PHP Development Server

```bash
php -S localhost:8080
```

### 3. Access the Application

Open your web browser and visit:

- **Original Website**: http://localhost:8080/index.html
- **CMS Website**: http://localhost:8080/index-cms.html
- **Admin Login**: http://localhost:8080/login.php

## 🔐 Admin Access

### Default Credentials

- **Username**: `admin`
- **Password**: `admin123`

*Alternative user:*
- **Username**: `mmeuser`
- **Password**: `mme123`

### Admin Features

1. **Login**: Click the green "🔧 Admin Login" button in navigation
2. **Dashboard**: Overview of content management options
3. **Edit Sections**: Click "✏️ Edit" buttons on any section
4. **Content Management**: Update text, upload images
5. **Real-time Updates**: Changes appear immediately on both websites

## 📁 Project Structure

```
makemyevent/
├── index.html              # Original website (now dynamic)
├── index-cms.html          # CMS website with admin features
├── login.php               # Admin login page
├── styles.css              # Global styles
├── script.js               # JavaScript functionality
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   └── edit-section.php    # Section editing interface
├── api/
│   ├── content.php         # Content API endpoint
│   ├── check-session.php   # Session validation
│   └── logout.php          # Logout functionality
├── config/
│   └── database.php        # Database configuration
├── assets/
│   ├── fonts/              # Local font files
│   └── images/             # Website images and gallery albums
└── database/
    └── cms.sqlite          # SQLite database (auto-created)
```

## 🎨 Key Sections

### Hero Section
- **Title**: Main headline
- **Eyebrow**: Small text above title
- **Script**: Decorative script text
- **Stats**: Company statistics

### About Section
- **Title**: Section heading
- **Description**: Company description
- **Eyebrow**: Section label

### Gallery System
- **Albums**: 11 wedding photo albums
- **Structure**: `album[1-11]/` with `cover-thumb.jpg` and `img[N]-large/thumb.jpg`

## 🛠️ Development

### Making Content Changes

1. **Through Admin Panel**:
   - Login at `/login.php`
   - Navigate to sections and edit
   - Changes save automatically to database

2. **Direct Database Access**:
   - Database file: `database/cms.sqlite`
   - Content stored in `content_sections` table as JSON

### Adding New Sections

1. **Database**: Add new section to `content_sections` table
2. **HTML**: Add IDs to content elements
3. **JavaScript**: Update content loading functions
4. **Admin**: Add editing interface in `admin/edit-section.php`

### Asset Management

- **Images**: Place in `assets/images/`
- **Fonts**: Place in `assets/fonts/`
- **Gallery**: Follow naming convention: `album[N]/img[N]-large.jpg` and `img[N]-thumb.jpg`

## 🔧 Configuration

### Database Configuration

The SQLite database is automatically initialized on first run. Configuration in `config/database.php`:

```php
private $db_file = __DIR__ . '/../database/cms.sqlite';
```

### User Management

Default users are defined in `config/database.php`. To add users, modify the `getUsers()` method.

## 🧪 Testing Tools

The project includes several testing utilities:

- **Workflow Test**: `http://localhost:8080/workflow-test.php`
- **Content Sync Test**: `http://localhost:8080/test-content-sync.php`
- **Login Debug**: `http://localhost:8080/debug-comprehensive.php`
- **Direct Login Test**: `http://localhost:8080/login-test.html`

## 🌐 Browser Compatibility

- **Chrome**: Full support
- **Firefox**: Full support
- **Safari**: Full support (including backdrop-filter effects)
- **Mobile**: Responsive design with safe-area support

## 📱 Mobile Features

- **Responsive Layout**: Fluid typography and grid systems
- **Safe Areas**: iPhone notch support with `env(safe-area-inset-*)`
- **Touch Interactions**: Optimized hover states for touch devices
- **Performance**: GPU-accelerated animations

## 🔒 Security Notes

- **Session Management**: PHP sessions with proper validation
- **SQL Injection Protection**: PDO prepared statements
- **Input Validation**: Server-side validation for all inputs
- **File Upload Security**: Restricted file types and locations

## 🚨 Troubleshooting

### Common Issues

1. **Port Already in Use**:
   ```bash
   lsof -i :8080
   kill [PID]
   php -S localhost:8080
   ```

2. **Database Not Found**:
   - Database auto-creates on first run
   - Check write permissions in project directory

3. **Images Not Loading**:
   - Verify images exist in `assets/images/`
   - Check file paths and naming conventions

4. **Admin Login Issues**:
   - Clear browser cache and cookies
   - Check PHP error logs
   - Use debug tools in `/debug-comprehensive.php`

### Development Server Logs

Monitor the PHP development server output for debugging:

```bash
php -S localhost:8080
# Server output will show requests and errors
```

## 📄 License

This project is proprietary software for Make My Event company.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly using provided testing tools
5. Submit a pull request

## 📞 Support

For technical support or questions about the project setup, please check:

1. **Testing Tools**: Use built-in debugging utilities
2. **Error Logs**: Check PHP development server output
3. **Database Issues**: Use SQLite browser tools
4. **Browser Console**: Check for JavaScript errors

---

## 🎯 Quick Start Checklist

- [ ] Clone repository
- [ ] Ensure PHP 8.0+ is installed
- [ ] Run `php -S localhost:8080`
- [ ] Visit http://localhost:8080/index.html
- [ ] Test admin login with admin/admin123
- [ ] Make a content change and verify it appears on both sites
- [ ] Check that database file is created in `database/cms.sqlite`

**The website should now be fully functional with dynamic content management!**