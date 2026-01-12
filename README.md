# MVP Laravel Project

This is a Laravel 10 conversion of the NestJS MVP project. The project has been converted to use Laravel's architecture with Repository Pattern, Services, and Form Request validation classes.

## Project Structure

### Architecture
- **Repository Pattern**: All data access logic is in `app/Repositories/`
- **Services**: Business logic is in `app/Services/`
- **Form Requests**: Validation logic is in `app/Http/Requests/`
- **Models**: Eloquent models are in `app/Models/V5/`
- **Controllers**: API controllers are in `app/Http/Controllers/Api/`

### Key Features Converted

#### Authentication
- Laravel Sanctum API Token Authentication
- Login, Registration, Google Sign-In
- Password Reset and Change Password
- Email Verification

#### User Management
- User CRUD operations
- User roles and permissions
- Team and Club associations

#### Mail System
- Queue-based mail sending
- Email templates (to be implemented with Blade/Handlebars)

### Configuration

1. **Database**: PostgreSQL (configured in `config/database.php`)
2. **Authentication**: Laravel Sanctum (configured in `config/auth.php` and `config/sanctum.php`)
3. **Queue**: Redis (configured in `config/queue.php`)

### Environment Variables

Add these to your `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

FRONTEND_URL=http://localhost:3000
```

### Installation

1. Install dependencies:
```bash
composer install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Run migrations:
```bash
php artisan migrate
```

### API Routes

All API routes are prefixed with `/api`:

- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Register
- `GET /api/auth/me` - Get current user (protected)
- `POST /api/v5/users` - Create user (protected)
- `GET /api/v5/users` - List users (protected)
- `GET /api/v5/users/{id}` - Get user (protected)
- `PUT /api/v5/users/{id}` - Update user (protected)
- `DELETE /api/v5/users/{id}` - Delete user (protected)

### Remaining Work

This is a partial conversion. The following still need to be implemented:

1. **All other models** (Clubs, Teams, Tournaments, Games, etc.)
2. **All other services and repositories**
3. **All other controllers**
4. **All other migrations**
5. **Mail templates** (Blade templates for emails)
6. **Queue workers** (for processing mail jobs)
7. **File upload handling** (for user pictures)
8. **Custom validators** (like IsUnique validator)
9. **Role-based access control** (complete implementation)
10. **All other modules** from the NestJS project

### Notes

- The User model relationships are commented out until related models are created
- The MailService uses a queue job that needs proper template rendering
- Some services reference models that don't exist yet (commented out)
- The old user service integration (MySQL connection) is not implemented

### Testing

Run tests with:
```bash
php artisan test
```

### License

Same as the original NestJS project.
