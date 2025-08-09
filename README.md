# Panda Digital V3 - Modern Platform

## Overview
Panda Digital V3 is a modern, clean-architecture version of the Panda Digital platform designed to empower women entrepreneurs in Tanzania through digital skills training, business opportunities, and community support.

## 🚀 Features

### Modern UI/UX
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **Clean Interface**: Modern, intuitive user experience
- **Smooth Animations**: AOS (Animate On Scroll) for engaging interactions
- **Fast Loading**: Optimized assets and lazy loading

### Security & Performance
- **Environment Configuration**: Secure .env-based configuration management
- **PDO Database**: Secure database connections with prepared statements
- **Modern PHP**: Latest PHP practices and security standards
- **Input Validation**: Comprehensive form validation and sanitization
- **CSRF Protection**: Built-in CSRF token protection
- **Security Headers**: Modern security headers and XSS protection

### User Experience
- **Interactive Forms**: Real-time validation and feedback
- **Modal Dialogs**: Clean login/signup experience
- **Smooth Scrolling**: Enhanced navigation experience
- **Loading States**: Visual feedback for user actions

## 📁 Project Structure

```
pandadigitalV3/
├── assets/
│   ├── css/
│   │   └── style.css          # Modern CSS with custom properties
│   ├── js/
│   │   └── script.js          # Interactive JavaScript
│   ├── images/                # All platform images
│   └── fonts/                 # Custom fonts
├── config/
│   ├── init.php               # Application initialization
│   ├── Environment.php        # Environment configuration class
│   └── database.php           # Database configuration
├── controllers/               # Business logic controllers
├── models/                    # Data models
├── views/                     # View templates
├── includes/
│   ├── header.php             # Modern header with navigation
│   └── footer.php             # Footer with modals
├── api/                       # API endpoints
├── uploads/                   # File uploads
├── logs/                      # Application logs
├── cache/                     # Cache files
├── .env                       # Environment variables (create from env.example)
├── env.example                # Example environment configuration
├── .gitignore                 # Git ignore rules
├── setup.php                  # Setup wizard
└── index.php                  # Homepage
```

## 🛠️ Technology Stack

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with CSS custom properties
- **Bootstrap 5**: Responsive framework
- **JavaScript (ES6+)**: Modern JavaScript features
- **Font Awesome 6**: Icon library
- **AOS**: Scroll animations

### Backend
- **PHP 8+**: Modern PHP with type hints
- **PDO**: Secure database abstraction
- **MySQL/MariaDB**: Database
- **Apache/Nginx**: Web server

## ⚙️ Environment Configuration

### Quick Setup
1. Copy the example environment file:
   ```bash
   cp env.example .env
   ```

2. Edit `.env` with your configuration:
   ```env
   # Database Configuration
   DB_HOST=localhost
   DB_NAME=pandadigital
   DB_USER=root
   DB_PASSWORD=your_password
   DB_CHARSET=utf8mb4

   # Application Configuration
   APP_NAME="Panda Digital"
   APP_URL=http://localhost/pandadigitalV3
   APP_ENV=development
   APP_DEBUG=true
   APP_KEY=your-32-character-secret-key-here
   ```

3. Run the setup wizard:
   ```
   http://localhost/pandadigitalV3/setup.php?setup
   ```

### Environment Variables

#### Database Configuration
- `DB_HOST`: Database host (default: localhost)
- `DB_NAME`: Database name (default: pandadigital)
- `DB_USER`: Database username (default: root)
- `DB_PASSWORD`: Database password
- `DB_CHARSET`: Database charset (default: utf8mb4)

#### Application Configuration
- `APP_NAME`: Application name
- `APP_URL`: Application URL
- `APP_ENV`: Environment (development/production)
- `APP_DEBUG`: Debug mode (true/false)
- `APP_TIMEZONE`: Application timezone
- `APP_KEY`: 32-character secret key for encryption

#### Security Configuration
- `SESSION_SECURE`: Secure session cookies (true/false)
- `SESSION_HTTP_ONLY`: HTTP-only session cookies (true/false)
- `SESSION_SAME_SITE`: Same-site cookie policy

#### Mail Configuration
- `MAIL_HOST`: SMTP host
- `MAIL_PORT`: SMTP port
- `MAIL_USERNAME`: SMTP username
- `MAIL_PASSWORD`: SMTP password
- `MAIL_ENCRYPTION`: SMTP encryption (tls/ssl)
- `MAIL_FROM_ADDRESS`: From email address
- `MAIL_FROM_NAME`: From name

#### Payment Configuration
- `AZAMPAY_API_KEY`: AzamPay API key
- `AZAMPAY_SECRET_KEY`: AzamPay secret key
- `AZAMPAY_ENVIRONMENT`: AzamPay environment (sandbox/production)

#### Analytics Configuration
- `GOOGLE_ANALYTICS_ID`: Google Analytics measurement ID
- `FACEBOOK_PIXEL_ID`: Facebook Pixel ID

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependencies)

### Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd pandadigitalV3
   ```

2. **Set up environment configuration**
   ```bash
   cp env.example .env
   # Edit .env with your settings
   ```

3. **Set up the database**
   - Create a MySQL database named `pandadigital`
   - Import the database schema from `../pandadigitalV2/dump_database/pandadigital.sql`

4. **Run the setup wizard**
   - Navigate to `http://localhost/pandadigitalV3/setup.php?setup`
   - Follow the setup instructions

5. **Set up web server**
   - Point your web server to the `pandadigitalV3` directory
   - Ensure PHP has write permissions for the `uploads`, `logs`, and `cache` directories

6. **Access the platform**
   - Navigate to `http://localhost/pandadigitalV3`

## 🔧 Helper Functions

The platform includes several helper functions for common tasks:

### Environment Functions
- `env($key, $default)`: Get environment variable
- `app_url($path)`: Get application URL
- `asset($path)`: Get asset URL
- `upload_url($path)`: Get upload URL

### Security Functions
- `csrf_token()`: Generate CSRF token
- `verify_csrf_token($token)`: Verify CSRF token

### Session Functions
- `flash($key, $message)`: Set flash message
- `get_flash($key)`: Get flash message
- `has_flash($key)`: Check if flash message exists
- `old($key, $default)`: Get old input value

### Utility Functions
- `redirect($url)`: Redirect to URL
- `back()`: Redirect back to previous page

## 📱 Responsive Design

The platform is fully responsive and optimized for:
- **Mobile**: 320px - 768px
- **Tablet**: 768px - 1024px
- **Desktop**: 1024px+

## 🔧 Customization

### Adding New Pages
1. Create a new PHP file in the root directory
2. Include the initialization: `require_once 'config/init.php';`
3. Include the header: `include 'includes/header.php';`
4. Add your content
5. Include the footer: `include 'includes/footer.php';`

### Styling
- Main styles are in `assets/css/style.css`
- Use CSS custom properties for consistent theming
- Follow the established design patterns

### JavaScript
- Main functionality is in `assets/js/script.js`
- Modular approach for easy maintenance
- Event-driven architecture

## 🔒 Security Features

- **Environment Variables**: Secure configuration management
- **SQL Injection Protection**: PDO prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Token-based protection for forms
- **Input Validation**: Comprehensive client and server-side validation
- **Secure Headers**: Modern security headers
- **Session Security**: Secure session configuration

## 📊 Performance Optimizations

- **Lazy Loading**: Images load as needed
- **Minified Assets**: Optimized CSS and JavaScript
- **CDN Resources**: External libraries from CDN
- **Caching**: Browser caching for static assets
- **Compression**: Gzip compression for faster loading
- **Environment-based Optimization**: Different settings for dev/prod

## 🌐 Browser Support

- **Chrome**: 90+
- **Firefox**: 88+
- **Safari**: 14+
- **Edge**: 90+
- **Mobile Browsers**: iOS Safari 14+, Chrome Mobile 90+

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For support and questions:
- Email: support@pandadigital.co.tz
- Phone: +255 734 283 34
- Website: https://pandadigital.co.tz

## 🔄 Migration from V2

The V3 platform maintains compatibility with the existing database structure while providing:
- Modern, responsive design
- Improved security with environment configuration
- Better performance
- Enhanced user experience
- Clean, maintainable code
- Environment-based configuration management

## 🛠️ Development

### Environment Setup
```bash
# Development environment
APP_ENV=development
APP_DEBUG=true

# Production environment
APP_ENV=production
APP_DEBUG=false
```

### Database Migration
The platform uses the existing V2 database structure. No migration needed.

### Testing
Run the setup wizard to test your installation:
```
http://localhost/pandadigitalV3/setup.php?setup
```

---

**Built with ❤️ for empowering women entrepreneurs in Tanzania**
