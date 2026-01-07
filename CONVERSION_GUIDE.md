# NestJS to Laravel Conversion Guide

This document outlines what has been converted and what still needs to be done.

## ✅ Completed Components

### Core Infrastructure
- ✅ Laravel 10 project setup
- ✅ PostgreSQL database configuration
- ✅ JWT authentication setup
- ✅ Repository pattern base classes
- ✅ Service layer structure
- ✅ Form Request validation classes
- ✅ API routes structure
- ✅ Middleware (JWT Auth, Roles Guard)

### Authentication Module
- ✅ User model (V5\User)
- ✅ Role model
- ✅ UserRole model (pivot table)
- ✅ AuthService (login, register, Google sign-in, password reset)
- ✅ AuthController
- ✅ LoginRequest, CreateUserRequest, UpdateUserRequest
- ✅ JWT middleware
- ✅ User migrations

### User Management
- ✅ UserRepository
- ✅ UserService
- ✅ UserController
- ✅ User CRUD operations
- ✅ User search and filtering

### Mail System
- ✅ MailService (structure)
- ✅ SendMailJob (queue job)
- ⚠️ Email templates (need Blade templates)

## ⚠️ Partially Completed

### Models
- ⚠️ User model relationships (commented out - need other models)
- ⚠️ UserRole relationships (commented out)

## ❌ Not Yet Converted

### Models (Need to be created)
- ❌ Club
- ❌ Team
- ❌ Tournament
- ❌ TournamentGroup
- ❌ Season
- ❌ Sport
- ❌ SeasonSport
- ❌ Venue
- ❌ Court
- ❌ Game
- ❌ League
- ❌ Pool
- ❌ Round
- ❌ Person
- ❌ Coach
- ❌ Player
- ❌ Referee
- ❌ Reservation
- ❌ TimeSlot
- ❌ Conflict
- ❌ Message
- ❌ GamePlan
- ❌ GamePenalty
- ❌ GameNote
- ❌ Suggestion
- ❌ BlockedPeriod
- ❌ System
- ❌ And many more...

### Services (Need to be created)
- ❌ ClubService
- ❌ TeamService
- ❌ TournamentService
- ❌ GameService
- ❌ And all other services...

### Controllers (Need to be created)
- ❌ ClubController
- ❌ TeamController
- ❌ TournamentController
- ❌ GameController
- ❌ And all other controllers...

### Repositories (Need to be created)
- ❌ ClubRepository
- ❌ TeamRepository
- ❌ TournamentRepository
- ❌ GameRepository
- ❌ And all other repositories...

### Migrations (Need to be created)
- ❌ All table migrations for remaining models

### Form Requests (Need to be created)
- ❌ All validation request classes for remaining modules

### Additional Features
- ❌ Custom validators (IsUnique, IsDanishNumber)
- ❌ File upload handling (complete implementation)
- ❌ Queue workers configuration
- ❌ Email templates (Blade/Handlebars)
- ❌ Old user service (MySQL connection)
- ❌ Calendar service
- ❌ Date utilities
- ❌ Transaction decorator equivalent

## Conversion Pattern

For each module, follow this pattern:

### 1. Create Model
```php
// app/Models/V5/ModuleName.php
namespace App\Models\V5;
use Illuminate\Database\Eloquent\Model;

class ModuleName extends Model
{
    protected $table = 'module_names';
    protected $fillable = [...];
    
    // Relationships
}
```

### 2. Create Migration
```bash
php artisan make:migration create_module_names_table
```

### 3. Create Repository
```php
// app/Repositories/V5/ModuleNameRepository.php
namespace App\Repositories\V5;
use App\Repositories\BaseRepository;

class ModuleNameRepository extends BaseRepository
{
    // Custom methods
}
```

### 4. Create Service
```php
// app/Services/V5/ModuleNameService.php
namespace App\Services\V5;

class ModuleNameService
{
    protected $repository;
    
    // Business logic
}
```

### 5. Create Form Request
```php
// app/Http/Requests/V5/CreateModuleNameRequest.php
namespace App\Http\Requests\V5;

class CreateModuleNameRequest extends FormRequest
{
    public function rules()
    {
        return [...];
    }
}
```

### 6. Create Controller
```php
// app/Http/Controllers/Api/V5/ModuleNameController.php
namespace App\Http\Controllers\Api\V5;

class ModuleNameController extends Controller
{
    // CRUD methods
}
```

### 7. Add Routes
```php
// routes/api.php
Route::prefix('v5/module-names')->group(function () {
    Route::get('/', [ModuleNameController::class, 'index']);
    Route::post('/', [ModuleNameController::class, 'create']);
    // etc.
});
```

## Key Differences from NestJS

1. **Dependency Injection**: Laravel uses service container, not decorators
2. **Validation**: Form Requests instead of DTOs with decorators
3. **Guards**: Middleware instead of Guards
4. **Models**: Eloquent instead of Sequelize
5. **Database**: Migrations are PHP files, not JS
6. **Routes**: Defined in routes/api.php instead of decorators
7. **Services**: Plain PHP classes, not Injectable decorators

## Next Steps

1. Convert remaining models one by one
2. Create corresponding migrations
3. Create repositories and services
4. Create controllers and routes
5. Test each module
6. Implement email templates
7. Set up queue workers
8. Complete file upload handling
9. Implement custom validators

## Notes

- The project structure follows Laravel conventions
- Repository pattern is used for data access
- Services contain business logic
- Form Requests handle validation
- Controllers are thin and delegate to services
- All routes are API-only (no views)

