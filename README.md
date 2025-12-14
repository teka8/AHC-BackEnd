# 🏥 Africa Health Collaborative - Backend

> Powerful Laravel CMS backend for the Africa Health Collaborative platform, featuring role-based access control, content management, Google Analytics integration, and comprehensive API for health education collaboration.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2|8.3|8.4-777BB4?logo=php)
![Livewire](https://img.shields.io/badge/Livewire-3.6-4E56A6?logo=livewire)
![Google Analytics](https://img.shields.io/badge/GA4-API-4285F4?logo=google-analytics)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)

## 📸 Screenshots

| Admin Dashboard | Analytics Dashboard | Settings |
|-----------------|---------------------|----------|
| Overview & stats | GA4 integration | System configuration |

## ✨ Features

### Core CMS
- 📄 **Page Management** - Create, edit, and publish dynamic pages
- 📰 **Blog/News System** - Post management with categories and tags
- 📅 **Event Management** - Events with registration and tracking
- 📚 **Document Repository** - File uploads with Spatie Media Library
- 👥 **Team/Leader Management** - Showcase staff and leadership
- 🤝 **Partner Management** - Local and international partners
- 💼 **Program Management** - Health programs and initiatives
- 📧 **Contact Messages** - Inquiry management with tracking
- 🔔 **Announcements** - System-wide notifications

### Advanced Features
- 🔐 **Authentication** - Laravel Sanctum (cookie + token-based)
- 👤 **Role & Permission Management** - Spatie Permission with granular control
- 🌐 **Multi-Language Support** - Translation system ready
- 📱 **Navigation Builder** - Dynamic menu management
- 🎨 **Settings Management** - Global configuration UI
- 📊 **Activity Logging** - Spatie Activity Log for audit trails
- 🔍 **API Documentation** - Auto-generated with Scramble
- ⚡ **Performance Monitoring** - Laravel Pulse & Telescope
- 🛡️ **Security** - Rate limiting, CORS, CSRF protection

### Google Analytics Integration
- 📊 **GA4 Dashboard** (`/admin/analytics`) with:
  - Real-time active users
  - Overview stats (users, page views, sessions, bounce rate)
  - Time-based trends (7/30/90 days)
  - Top pages with views and users
  - Top events (custom business events)
  - Traffic sources (organic, direct, referral, social)
  - Top countries with geographic distribution
  - Device breakdown (desktop, mobile, tablet)
  - Browser statistics (Chrome, Safari, Firefox, etc.)
  - Operating systems (Windows, macOS, iOS, Android)
  - Landing pages analysis
- ⚙️ **Admin Configuration** (`/admin/settings?tab=integrations`):
  - GA4 Measurement ID (frontend tracking)
  - GA4 Property ID (backend API access)
  - Service Account JSON upload (Google Cloud authentication)
  - Enable/disable toggle
  - IP anonymization option
  - Cookie consent requirement toggle
- 🔄 **Auto Cache Clearing** - Clears analytics cache when settings update
- 📈 **Public API** - Frontend gets config via `/api/v1/public/google-analytics-config`
- 🔒 **Secure Storage** - JSON keys in `storage/app/` (not public)
- 💾 **Smart Caching** - 30-minute cache for dashboard data

### Developer Features
- 📦 **Modular Architecture** - Laravel Modules for extensibility
- 🔌 **Hook System** - Event-driven customization (Eventy)
- 🎨 **Livewire Components** - Reactive data tables
- 📊 **Excel Export** - Data export with Maatwebsite Excel
- 🖼️ **PDF Generation** - Browsershot for PDF/screenshots
- 🔄 **Query Builder** - Spatie Query Builder for advanced filtering
- 🧪 **Testing Ready** - PHPUnit test structure
- 📝 **Code Quality** - Laravel Pint for formatting

## 🛠️ Tech Stack

| Category | Technology | Version |
|----------|------------|---------|
| **Framework** | Laravel | 12.0 |
| **Language** | PHP | 8.2 / 8.3 / 8.4 |
| **Frontend (Admin)** | Livewire + Alpine.js | 3.6 / 3.x |
| **Database** | MySQL/MariaDB | 8.0+ |
| **Cache** | Redis (recommended) | Latest |
| **Queue** | Redis/Database | - |
| **Authentication** | Laravel Sanctum | 4.1 |
| **Permissions** | Spatie Permission | 6.4 |
| **Media** | Spatie Media Library | 11.13 |
| **Logging** | Spatie Activity Log | 4.0 |
| **Analytics** | Google Analytics Data API | 0.22 |
| **Excel** | Maatwebsite Excel | Latest |
| **PDF** | Spatie Browsershot | 5.0 |
| **Monitoring** | Laravel Telescope + Pulse | 5.10 / 1.4 |
| **API Docs** | Scramble | 0.12 |

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+ (with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- Composer 2.x
- MySQL 8.0+ or MariaDB 10.3+
- Node.js 18+ and npm (for asset compilation)
- Redis (recommended for cache/queue)

### Installation

```bash
# Clone the repository
git clone https://github.com/your-org/AHC-BackEnd.git
cd AHC-BackEnd

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ahc_database
DB_USERNAME=root
DB_PASSWORD=your_password

# Run database migrations
php artisan migrate

# Seed database (optional - creates admin user)
php artisan db:seed

# Install Node dependencies
npm install

# Build frontend assets
npm run build

# Create storage symlink
php artisan storage:link

# Start development server
php artisan serve
```

Application runs at: **http://localhost:8000**

### Default Admin Credentials

After seeding:
```
Email: admin@admin.com
Password: password
```

**⚠️ Change immediately in production!**

## 🔐 Environment Configuration

Essential `.env` variables:

```env
APP_NAME="Africa Health Collaborative"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ahc.tewostech.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=ahc_database
DB_USERNAME=ahc_user
DB_PASSWORD=secure_password

# Cache & Queue (Redis recommended)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Session & Cookie
SESSION_DRIVER=database
SESSION_LIFETIME=120

# CORS (allow frontend)
FRONTEND_URL=https://ahc.tewostech.com

# Sanctum (API authentication)
SANCTUM_STATEFUL_DOMAINS=ahc.tewostech.com,localhost:5173

# Google Analytics (configured via admin UI, but can be in .env)
# FRONTEND_GA_MEASUREMENT_ID=G-XXXXXXXXXX
# FRONTEND_GA_PROPERTY_ID=123456789

# Mail (for contact forms, notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@ahc.tewostech.com
MAIL_FROM_NAME="${APP_NAME}"

# Optional: Telescope (dev only)
TELESCOPE_ENABLED=false

# Optional: Pulse (production monitoring)
PULSE_ENABLED=true
```

## 📁 Project Structure

```
AHC-BackEnd/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           # API controllers (frontend)
│   │   │   │   ├── SettingController.php  # Public GA config
│   │   │   │   ├── EventController.php
│   │   │   │   └── ...
│   │   │   └── Backend/       # Admin panel controllers
│   │   │       ├── AnalyticsController.php # GA dashboard
│   │   │       ├── SettingController.php   # Settings UI
│   │   │       └── ...
│   │   ├── Middleware/        # Custom middleware
│   │   └── Requests/          # Form requests
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic
│   │   ├── GoogleAnalyticsService.php (700+ lines)
│   │   ├── PermissionService.php
│   │   ├── MenuService/
│   │   └── ...
│   ├── Policies/              # Authorization policies
│   │   └── AnalyticsPolicy.php
│   └── Providers/             # Service providers
├── config/                    # Configuration files
│   ├── cors.php               # CORS settings
│   ├── sanctum.php            # API auth
│   └── settings.php           # Dynamic settings
├── database/
│   ├── migrations/            # Database schema
│   ├── seeders/               # Data seeders
│   └── factories/             # Model factories
├── resources/
│   ├── views/
│   │   ├── backend/           # Admin panel views
│   │   │   ├── pages/
│   │   │   │   ├── analytics/ # GA dashboard views
│   │   │   │   ├── settings/  # Settings forms
│   │   │   │   └── ...
│   │   │   └── layouts/       # Admin layouts
│   │   └── livewire/          # Livewire components
│   ├── js/                    # Frontend assets (admin)
│   │   ├── app.js
│   │   └── components/
│   └── css/                   # Styles
├── routes/
│   ├── web.php                # Admin routes
│   ├── api.php                # API routes (frontend)
│   └── channels.php           # Broadcasting
├── storage/
│   ├── app/
│   │   ├── google/            # GA service account JSON
│   │   └── public/            # Public uploads
│   ├── logs/                  # Application logs
│   └── framework/             # Cache, sessions, views
├── tests/                     # PHPUnit tests
├── .env.example               # Environment template
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
├── artisan                    # CLI tool
└── README.md                  # This file
```

## 📡 API Endpoints

### Public API (v1)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| **Navigation** | | | |
| GET | `/api/v1/navigation` | Main navigation menu | No |
| GET | `/api/v1/footer` | Footer navigation | No |
| **Content** | | | |
| GET | `/api/v1/pages` | List all pages | No |
| GET | `/api/v1/pages/{slug}` | Single page by slug | No |
| GET | `/api/v1/posts` | Blog posts listing | No |
| GET | `/api/v1/posts/{slug}` | Single post | No |
| **Events** | | | |
| GET | `/api/v1/events` | Events listing | No |
| GET | `/api/v1/events/{id}` | Event details | No |
| POST | `/api/v1/events/{id}/register` | Register for event | Yes |
| **Resources** | | | |
| GET | `/api/v1/resources` | Document library | No |
| GET | `/api/v1/resources/{id}` | Resource details | No |
| **Contact** | | | |
| POST | `/api/v1/contact` | Submit contact form | No |
| **Settings** | | | |
| GET | `/api/v1/public/google-analytics-config` | GA4 configuration | No |
| GET | `/api/v1/settings/company-info` | Company information | No |

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | User login (Sanctum cookie) |
| POST | `/api/auth/register` | User registration |
| GET | `/api/auth/user` | Get authenticated user |
| POST | `/api/auth/logout` | User logout |
| POST | `/api/auth/forgot-password` | Password reset request |
| POST | `/api/auth/reset-password` | Reset password |

### Admin Analytics API (Protected)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/analytics/realtime` | Real-time active users |
| GET | `/admin/analytics/overview?days=30` | Overview stats |
| GET | `/admin/analytics/users-trend?days=30` | User growth chart |
| GET | `/admin/analytics/top-pages` | Top 10 pages |
| GET | `/admin/analytics/top-events` | Top 10 custom events |
| GET | `/admin/analytics/traffic-sources` | Traffic source breakdown |
| GET | `/admin/analytics/geography` | Top countries |
| GET | `/admin/analytics/devices` | Device types |
| GET | `/admin/analytics/browsers` | Browser statistics |
| GET | `/admin/analytics/operating-systems` | OS breakdown |
| GET | `/admin/analytics/landing-pages` | Entry pages |

## 🔐 Authentication & Authorization

### Laravel Sanctum

**Cookie-based auth for SPA:**

```javascript
// Frontend: Login request
axios.post('/api/auth/login', {
  email: 'user@example.com',
  password: 'password'
}, { withCredentials: true });

// Subsequent requests automatically include cookie
axios.get('/api/auth/user', { withCredentials: true });
```

**Token-based auth for mobile/3rd party:**

```javascript
// Get token
const response = await axios.post('/api/auth/login', credentials);
const token = response.data.token;

// Use token in headers
axios.get('/api/user/profile', {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

### Permissions

Built-in permission groups:

| Group | Permissions | Description |
|-------|-------------|-------------|
| **Dashboard** | `view_dashboard` | Access admin panel |
| **Content** | `view/create/edit/delete_pages` | Page management |
| **Blog** | `view/create/edit/delete_posts` | Blog management |
| **Events** | `view/create/edit/delete_events` | Event management |
| **Users** | `view/create/edit/delete_users` | User management |
| **Roles** | `view/create/edit/delete_roles` | Role management |
| **Settings** | `manage_settings` | System settings |
| **Analytics** | `view_frontend_analytics` | GA dashboard access |

**Check in controller:**

```php
$this->authorize('viewAny', 'analytics');
// or
if (!auth()->user()->can('view_frontend_analytics')) {
    abort(403);
}
```

## 📊 Google Analytics Setup

### Step-by-Step Configuration

#### 1. Create GA4 Property
1. Go to [Google Analytics](https://analytics.google.com/)
2. Create new property → Choose GA4
3. Add data stream (Web) → Get **Measurement ID** (G-XXXXXXXXXX)

#### 2. Setup Google Cloud
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create project or select existing
3. Enable **Google Analytics Data API**:
   - APIs & Services → Library
   - Search "Google Analytics Data API" → Enable
4. Create **Service Account**:
   - APIs & Services → Credentials
   - Create Credentials → Service Account
   - Name it (e.g., "AHC Backend API")
   - Click on service account → Keys → Add Key → JSON
   - Download JSON file (keep secure!)
5. Copy service account email (e.g., `xxx@xxx.iam.gserviceaccount.com`)

#### 3. Grant Analytics Access
1. Back to Google Analytics → Admin
2. Property Access Management → Add users
3. Paste service account email
4. Role: **Viewer**
5. Click Add

#### 4. Configure in Laravel
1. Navigate to `/admin/settings?tab=integrations`
2. Fill in:
   - **Measurement ID:** `G-XXXXXXXXXX` (from step 1)
   - **Property ID:** `123456789` (numeric, from GA property)
   - **Service Account JSON:** Upload the JSON file (from step 2.4)
   - **Enable Frontend Tracking:** Toggle ON
   - **Cookie Consent Required:** Toggle ON (recommended)
3. Click **Save Settings**

#### 5. Verify
- Visit `/admin/analytics` → Should see dashboard with data
- Frontend should show cookie banner
- After accepting, frontend events tracked in GA4

### Cache Management

**Auto-clear implemented!** Cache clears automatically when you update:
- Property ID
- Measurement ID
- Service Account JSON
- Enable/Disable toggle

**Manual clear (if needed):**
```bash
php artisan cache:clear
```

## 🎨 Admin Panel Features

### Dashboard (`/admin`)
- Overview statistics
- Recent activities
- Quick actions
- System status

### Pages Management (`/admin/pages`)
- WYSIWYG editor
- SEO metadata
- Featured images
- Draft/Publish status
- URL slug customization

### Event Management (`/admin/events`)
- Event details (date, location, description)
- Registration management
- Attendee tracking
- Export registrations

### Settings (`/admin/settings`)

**Tabs:**
- **General:** Site title, logo, contact info
- **Integrations:** Google Analytics configuration
- **Email:** SMTP settings
- **Advanced:** Cache, maintenance mode

## 🔧 Development

### Artisan Commands

```bash
# Database
php artisan migrate              # Run migrations
php artisan db:seed              # Seed database
php artisan migrate:fresh --seed # Fresh DB with seed

# Cache
php artisan cache:clear          # Clear application cache
php artisan config:clear         # Clear config cache
php artisan route:clear          # Clear route cache
php artisan view:clear           # Clear compiled views

# Queue
php artisan queue:work           # Start queue worker
php artisan queue:listen         # Listen for jobs

# Maintenance
php artisan down                 # Enable maintenance mode
php artisan up                   # Disable maintenance mode

# Custom Commands
php artisan ahc:clear-ga-cache   # Clear GA cache (if added)
```

### Asset Compilation

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build

# Format code (Laravel Pint)
./vendor/bin/pint
```

### Adding New Permissions

```php
// In PermissionService.php
'new_group' => [
    'view_something' => 'View Something',
    'create_something' => 'Create Something',
    'edit_something' => 'Edit Something',
    'delete_something' => 'Delete Something',
],

// Run migration to create permissions
php artisan migrate
```

## 🌐 Deployment

### Production Checklist

```bash
# 1. Clone repository
git clone https://github.com/your-org/AHC-BackEnd.git
cd AHC-BackEnd

# 2. Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# 3. Configure environment
cp .env.example .env
# Edit .env with production values

# 4. Generate key
php artisan key:generate

# 5. Run migrations
php artisan migrate --force

# 6. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 8. Build assets
npm install
npm run build

# 9. Link storage
php artisan storage:link

# 10. Setup queue worker (supervisor)
```

### Supervisor Configuration

Create `/etc/supervisor/conf.d/ahc-worker.conf`:

```ini
[program:ahc-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ahc-backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/ahc-backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ahc-worker:*
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name api.ahc.tewostech.com;
    root /var/www/ahc-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Scheduled Tasks

Add to crontab:

```bash
* * * * * cd /var/www/ahc-backend && php artisan schedule:run >> /dev/null 2>&1
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter AnalyticsControllerTest

# With coverage
php artisan test --coverage
```

## 📝 API Documentation

Auto-generated API docs available at:

**Development:** http://localhost:8000/docs/api  
**Production:** https://api.ahc.tewostech.com/docs/api

Powered by [Scramble](https://scramble.dedoc.co/)

## 🐛 Troubleshooting

### Analytics Dashboard Shows No Data

**Check:**
1. Property ID correct? (numeric only, or `properties/123456789`)
2. JSON key uploaded successfully?
3. Service account has "Viewer" role in GA4?
4. Google Analytics Data API enabled in Cloud Console?
5. Check logs: `tail -f storage/logs/laravel.log`

### CORS Errors from Frontend

**Solution:**
```php
// config/cors.php
'allowed_origins' => [
    env('FRONTEND_URL', 'http://localhost:5173'),
],
```

### Storage Link Not Working

```bash
# Remove existing link and recreate
rm public/storage
php artisan storage:link
```

### Queue Jobs Not Processing

```bash
# Check queue worker is running
sudo supervisorctl status ahc-worker:*

# Manual test
php artisan queue:work --once
```

## 📄 License

MIT License - Africa Health Collaborative © 2024

## 🤝 Support

- **Admin Panel:** [https://ahc.tewostech.com/admin](https://ahc.tewostech.com/admin)
- **API Docs:** [https://api.ahc.tewostech.com/docs/api](https://api.ahc.tewostech.com/docs/api)
- **Email:** support@ahc.tewostech.com
- **Frontend Repo:** [AHC-FrontEnd_2](https://github.com/your-org/AHC-FrontEnd_2)

---

**Built with ❤️ by Tewos Technology for Africa Health Collaborative**

*Empowering health education infrastructure across Africa*
