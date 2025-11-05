# Q-Medis API Server

Queue Management System API built with Laravel 12 for medical facilities. Supports queue management, loket (counter) administration, and real-time display integration.

## 🛠️ Tech Stack

### Core Framework
- **Laravel 12** - Modern PHP web framework
- **PHP 8.2+** - Backend language
- **PostgreSQL** - Primary database with custom ENUM types

### Key Dependencies
- **php-open-source-saver/jwt-auth** (^2.3) - JWT authentication
- **laravel/socialite** (^5.23) - Google OAuth integration
- **zircote/swagger-php** (^4.8) - OpenAPI documentation

### Development Tools
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Unit and feature testing
- **Laravel Pail** - Real-time log tailing
- **Laragon** - Local development environment

---

## 📁 Project Structure

```
q-medis-server/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/      # API controllers
│   │   ├── Middleware/           # JwtMiddleware, RoleMiddleware
│   │   ├── Requests/             # Form request validation
│   │   └── Resources/            # API response resources
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic layer
│   ├── Repositories/             # Data access layer
│   ├── Traits/                   # Reusable traits
│   └── OpenApi/                  # OpenAPI schema definitions
├── config/
│   ├── auth.php                  # JWT guard configuration
│   ├── services.php              # Google OAuth credentials
│   └── jwt.php                   # JWT settings
├── database/
│   ├── migrations/               # Database schema
│   └── seeders/                  # Database seeders
├── routes/
│   ├── api.php                   # Main API router
│   ├── api/                      # Modular route files
│   │   ├── auth.php
│   │   ├── lokets.php
│   │   ├── antrians.php
│   │   ├── users.php
│   │   ├── dashboard.php
│   │   └── display.php
│   └── web.php                   # Web routes (OAuth, docs)
└── docs/
    ├── api_endpoints.md          # Complete API documentation
    └── postman/                  # Postman collections
```

### Architecture Layers

1. **Controllers** - Handle HTTP requests/responses
2. **Requests** - Validate incoming data
3. **Services** - Implement business logic
4. **Repositories** - Abstract database queries
5. **Models** - Database entities (Loket, Antrian, User, etc.)
6. **Resources** - Transform data for API responses
7. **Traits** - Shared behaviors (ApiResponse, GenerateNomorAntrian)

---

## 🗄️ Database Schema

### Main Tables
- **users** - System users (admin, petugas)
- **lokets** - Service counters
- **petugas_lokets** - Loket staff assignments
- **antrians** - Queue entries
- **jwt_sessions** - Refresh token tracking

### Custom ENUM Types
- `role_type`: admin | petugas
- `status_type`: menunggu | dipanggil | selesai | dibatalkan

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- PostgreSQL 12 or higher
- Composer
- Node.js & NPM (for frontend assets)
- Laragon (recommended) or similar local server

### Step 1: Clone Repository

```bash
git clone <repository-url>
cd q-medis-server
```

### Step 2: Install Dependencies

```bash
composer install
npm install
```

### Step 3: Environment Configuration

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Configure your `.env` file:

```env
APP_NAME="Q-Medis API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:9000

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=q_medis_db
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# JWT Configuration
JWT_SECRET=your_jwt_secret
JWT_ACCESS_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=7d
JWT_ALGO=HS256

# Queue Settings
QUEUE_DIGITS=3
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Generate JWT Secret

```bash
php artisan jwt:secret
```

### Step 6: Database Setup

Enable PostgreSQL extensions in Laragon:
- Edit `C:\laragon\bin\php\php-8.x.x\php.ini`
- Uncomment: `extension=pdo_pgsql` and `extension=pgsql`
- Restart Laragon

Create database:
```bash
createdb q_medis_db
```

Run migrations:
```bash
php artisan migrate
```

Seed database (optional):
```bash
php artisan db:seed
```

### Step 7: Start Development Server

```bash
php artisan serve
```

Or use Laragon's built-in server.

---

## 🔐 Google OAuth Setup

### 1. Create Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable **Google+ API**
4. Navigate to **Credentials** → **Create Credentials** → **OAuth 2.0 Client ID**
5. Configure OAuth consent screen
6. Set application type to **Web application**
7. Add authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback`
   - Add production URLs when deploying

### 2. Configure Environment

Add credentials to `.env`:

```env
GOOGLE_CLIENT_ID=123456789-abcdefg.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your_secret_key
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 3. OAuth Flow

**Frontend Initiation:**
```
GET http://localhost:8000/auth/google/redirect?return_url=http://localhost:9000/auth/callback
```

**Backend Callback:**
```
GET http://localhost:8000/auth/google/callback
→ Redirects to: http://localhost:9000/auth/callback?access_token=xxx&refresh_token=xxx
```

**Frontend Token Handling:**
- Extract tokens from URL query parameters
- Store `access_token` for API requests
- Store `refresh_token` for token renewal

### 4. Using Access Token

Include in API requests:
```
Authorization: Bearer <access_token>
```

---

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Interactive Documentation

**Swagger UI:**
```
http://localhost:8000/docs
```

### Authentication

All protected endpoints require JWT Bearer token:

```
Authorization: Bearer <access_token>
```

### Main Endpoints

#### Authentication
- `POST /api/auth/login` - Login with email/password
- `POST /api/auth/refresh` - Refresh access token
- `POST /api/auth/logout` - Logout and invalidate tokens
- `GET /api/auth/me` - Get authenticated user profile

#### OAuth
- `GET /auth/google/redirect` - Initiate Google OAuth
- `GET /auth/google/callback` - OAuth callback handler

#### Lokets (Counters)
- `GET /api/lokets` - List all lokets
- `GET /api/lokets/{id}` - Get loket details
- `POST /api/lokets` - Create loket (admin only)
- `PUT /api/lokets/{id}` - Update loket (admin only)
- `DELETE /api/lokets/{id}` - Delete loket (admin only)

#### Antrians (Queue)
- `GET /api/antrians` - List queues (filterable)
- `GET /api/antrians/{id}` - Get queue details
- `PUT /api/antrians/{id}` - Update queue status (admin/petugas)

#### Users
- `GET /api/users` - List users (admin only)
- `GET /api/users/{id}` - Get user details
- `POST /api/users` - Create user (admin only)
- `PUT /api/users/{id}` - Update user (admin only)
- `DELETE /api/users/{id}` - Delete user (admin only)

#### Display
- `GET /api/display/lokets` - List all lokets with queue info
- `GET /api/display/lokets/{id}` - Get loket details with current queue
- `GET /api/display/overview` - Get overview of all queues
- `POST /api/display/antrians` - Create new queue entry (public, rate-limited)

#### Dashboard
- `GET /api/dashboard` - Get dashboard statistics (requires authentication)

For complete API documentation with request/response examples, see:
- **[docs/api_endpoints.md](docs/api_endpoints.md)** - Detailed endpoint documentation
- **[http://localhost:8000/docs](http://localhost:8000/docs)** - Interactive Swagger UI

---

## 🔑 Environment Variables Reference

### Application
| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | Laravel |
| `APP_ENV` | Environment (local/production) | local |
| `APP_DEBUG` | Debug mode | true |
| `APP_URL` | Backend URL | http://localhost |
| `FRONTEND_URL` | Frontend URL for CORS/OAuth | http://localhost:9000 |

### Database
| Variable | Description | Default |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | pgsql |
| `DB_HOST` | Database host | 127.0.0.1 |
| `DB_PORT` | Database port | 5432 |
| `DB_DATABASE` | Database name | - |
| `DB_USERNAME` | Database user | postgres |
| `DB_PASSWORD` | Database password | - |

### Google OAuth
| Variable | Description | Required |
|----------|-------------|----------|
| `GOOGLE_CLIENT_ID` | OAuth Client ID | Yes |
| `GOOGLE_CLIENT_SECRET` | OAuth Client Secret | Yes |
| `GOOGLE_REDIRECT_URI` | OAuth Callback URL | Yes |

### JWT
| Variable | Description | Default |
|----------|-------------|---------|
| `JWT_SECRET` | JWT signing key | - |
| `JWT_ACCESS_EXPIRES_IN` | Access token TTL | 15m |
| `JWT_REFRESH_EXPIRES_IN` | Refresh token TTL | 7d |
| `JWT_ALGO` | JWT algorithm | HS256 |

### Queue Settings
| Variable | Description | Default |
|----------|-------------|---------|
| `QUEUE_DIGITS` | Queue number padding | 3 |

---

## 🚢 Deployment

### Build for Production

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Configuration

Update production `.env`:
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Use strong `APP_KEY` and `JWT_SECRET`
- Configure production database
- Update `GOOGLE_REDIRECT_URI` to production URL
- Set proper `FRONTEND_URL`

---

## 📝 Business Rules

### Queue Management
1. Only one antrian per loket can have status `dipanggil` at a time
2. When calling a new queue, existing `dipanggil` must be manually completed via "Selesaikan" button
3. Queue numbers are auto-generated with format: Loket Code + Sequential Number (e.g., A001)

### Authentication
- JWT access tokens expire after 15 minutes
- Refresh tokens expire after 7 days
- Sessions are tracked in `jwt_sessions` table
- Google OAuth users are auto-created with role `petugas`

### Authorization
- **Admin**: Full access to all resources
- **Petugas**: Read lokets, read/update antrians, read own profile

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 📞 Support

For issues and questions:
- Check [docs/api_endpoints.md](docs/api_endpoints.md)
- Review Laravel documentation: https://laravel.com/docs
- Contact development team

---

**Built with ❤️ using Laravel 12**
