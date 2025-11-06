# AI Coding Agent Instructions

## Project Overview
This is a **Laravel 9 Learning Management System (LMS)** with extensive e-commerce, payment gateway integrations, and multi-language support. The platform supports courses (live webinars, video courses, text lessons), products, bundles, subscriptions, and meetings with instructors.

## Architecture & Core Patterns

### Multi-Language & Translation
- Uses **Astrotomic Translatable** package for database translations
- Models with translatable fields implement `TranslatableContract` and use `Translatable` trait
- Translatable attributes defined in `$translatedAttributes` array (e.g., `Webinar` has `['title', 'description', 'summary', 'seo_description']`)
- Access translated values via custom getters (e.g., `getTitleAttribute()`) using `getTranslateAttributeValue()`
- 100+ language folders in `lang/` directory - check existing translations before adding new keys
- Frontend: Language strings passed to JavaScript via Blade templates (e.g., `var deleteAlertSuccess = '{{ trans('public.success') }}'`)

### Payment Architecture
- **40+ payment gateways** integrated via driver pattern in `app/PaymentChannels/Drivers/`
- Each gateway extends `BasePaymentChannel` and implements `IChannel` interface
- Gateway credentials stored in `PaymentChannel` model, loaded via `setCredentialItems()`
- Payment flow: `paymentRequest(Order)` → gateway redirect → `verify(Request)` → order status update
- Multi-currency support: `currency()` helper returns user's currency, `makeAmountByCurrency()` converts amounts
- Offline payments supported via `OfflinePayment` model with admin approval workflow
- Example gateways: Stripe, PayPal, Razorpay, Paystack, Xendit, Authorize.net

### Route Organization
- **Separate route files** for different contexts:
  - `routes/web.php` - Public web routes (courses, cart, payments, forums)
  - `routes/admin.php` - **ionCube encoded** admin panel (protected code)
  - `routes/panel.php` - User panel routes
  - `routes/api.php` - API entry point (delegates to `routes/api/*`)
  - `routes/custom_admin.php` - Custom admin extensions
- Routes use heavy grouping by prefix (e.g., `Route::group(['prefix' => 'course']`)
- Middleware: `web.auth` for authenticated web users, `api.auth` for API, `admin.auth` for admin

### Authentication & Authorization
- **Dual auth guards**: `web` (session) and `api` (JWT via tymon/jwt-auth)
- Admin uses custom role-based permissions (not Laravel policies) - check admin routes for authorization
- API auth: `api.auth` middleware validates JWT tokens
- User types: students, instructors, organizations (via `organ_id` relationship)
- Helper: `apiAuth()` returns authenticated API user

### Database & Models
- **No timestamps on many models**: `public $timestamps = false; protected $dateFormat = 'U';` (Unix timestamps)
- Extensive use of **static constants** for status/type enums (e.g., `Webinar::$active`, `Order::$paying`)
- Course types: `Webinar::$webinar` (live), `Webinar::$course` (video), `Webinar::$textLesson` (text)
- Models use `CascadeDeletes` trait for soft deletes
- Translatable models: check `$translatedAttributes` before accessing fields

### Helpers & Global Functions
- **2450+ lines** of helpers in `app/Helpers/helper.php` (auto-loaded via composer)
- Key helpers:
  - `getGeneralSettings($key)` - Access site settings
  - `handlePrice($amount)` - Format price with currency
  - `dateTimeFormat($timestamp, $format)` - Format dates with timezone
  - `getTranslateAttributeValue($model, $attribute)` - Get translated field
  - `sendNotification($template, $options, $userId)` - Send notifications
  - `makeAvatar($name, $size)` - Generate default avatars
- Additional helpers in `app/Helpers/`: `assets_helpers.php`, `settings.php`, `theme_helpers.php`
- API helpers in `app/Helpers/ApiHelper.php`

### Mixins (Business Logic Modules)
- Business logic organized in `app/Mixins/` (not Laravel mixins, but service classes):
  - `Cashback/CashbackAccounting.php` - Cashback calculations and rewards
  - `Installment/InstallmentPlans.php` - Installment payment logic
  - `Financial/MultiCurrency.php` - Multi-currency conversion
  - `Notifications/SendSMS.php` - SMS notification service
  - `OpenAI/AiContentGenerator.php` - AI content generation
  - `BunnyCDN/BunnyVideoStream.php` - Video streaming integration
- Geo location: `app/Mixins/Geo/Geo.php` (auto-loaded in composer files)

### Frontend Views
- **Theme system**: Views in `resources/views/design_1/` (current theme)
- Blade components use **Iconsax icons** via `blade-iconsax` package (e.g., `<x-iconsax-lin-add/>`)
- Admin views: `resources/views/admin/`
- API views: `resources/views/api/` (for web-rendered API endpoints)
- Landing builder: `resources/views/landingBuilder/` (custom landing pages)

### API Structure
- API routes in `routes/api/`: `auth.php` (login/register), `user.php` (authenticated), `guest.php`
- Controllers in `app/Http/Controllers/Api/Panel/`
- API responses use `apiResponse2($status, $code, $message, $data)` helper
- Validation: `validateParam($data, $rules)` helper for API requests

## Development Workflows

### Running the Application
```powershell
# Local development (XAMPP on Windows)
# Start Apache and MySQL via XAMPP Control Panel
# Access: http://localhost/Source/public

# Docker (Vietnamese docs in docker/README.md)
docker-compose up -d --build
docker-compose exec php composer install
docker-compose exec php php artisan migrate
# Access: http://localhost:8000
```

### Essential Commands
```bash
# Clear all caches (use this often!)
php artisan clear:all

# Run migrations
php artisan migrate --force

# Install dependencies
composer install
npm install

# Build assets
npm run dev      # Development
npm run watch    # Watch mode
npm run prod     # Production
```

### Testing & Debugging
- Laravel Debugbar enabled in dev (check `config/debugbar.php`)
- Logs: `storage/logs/laravel.log` (check here first for errors)
- Payment logs: `storage/logs/paypal.log` (for PayPal-specific issues)
- Database: MySQL, configured in `config/database.php`

## Common Patterns & Gotchas

### When Adding New Features
1. **Translations**: Add keys to `lang/en/` first, then copy to other languages if needed
2. **Models**: If translatable, add `TranslatableContract`, `Translatable` trait, and `$translatedAttributes`
3. **Routes**: Add to appropriate route file (`web.php` for public, `panel.php` for user panel)
4. **Payment gateways**: Extend `BasePaymentChannel`, implement `IChannel`, add credentials array
5. **Settings**: Use `getGeneralSettings()` for site-wide settings, stored in database

### Debugging Payment Issues
1. Check gateway credentials in `payment_channels` table
2. Verify currency compatibility (use `currency()` helper)
3. Test mode: Check `$test_mode` property in gateway driver
4. Callback URLs: Ensure they use `route('payment_verify', ['gateway' => 'GatewayName'])`
5. Order status: Track via `Order::$pending`, `Order::$paying`, `Order::$paid`

### Working with Courses (Webinars)
- Webinar = Live class, Course = Video course, Text Lesson = Text-based
- Use `$webinar->type` to determine course type (check against static constants)
- Learning progress tracked in `CourseLearning` model
- Assignments: `WebinarAssignment` with `WebinarAssignmentHistory` for submissions
- Forums: Course-specific forums via `ForumTopic` with `forum_id` relationship

### Multi-Currency Considerations
- Default currency: `getDefaultCurrency()` (from settings)
- User currency: `currency($user)` or `currency()` for current user
- Converting amounts: `makeAmountByCurrency($amount, $targetCurrency)` in payment channels
- Currency signs: `currencySign($currency)` helper

### File Uploads
- Uses **Laravel File Manager** (`unisharp/laravel-filemanager`)
- Routes: `routes/web.php` → `laravel-filemanager` prefix
- Storage: Configured in `config/filesystems.php` (local, S3, BunnyCDN supported)
- Video streaming: BunnyCDN integration in `app/Mixins/BunnyCDN/`

## Key Dependencies
- **Laravel 9.x** (PHP 8.1+)
- **astrotomic/laravel-translatable** - Database translations
- **tymon/jwt-auth** - API authentication
- **intervention/image** - Image manipulation
- **maatwebsite/excel** - Excel import/export
- **barryvdh/laravel-dompdf** - PDF generation
- **openai-php/laravel** - AI content generation
- **40+ payment packages** (stripe, paypal, razorpay, etc.)

## Emergency Procedures
- Emergency database update route: `/emergencyDatabaseUpdate` (runs migrations + seeders)
- Clear all caches if strange behavior: `php artisan clear:all`
- Admin panel encoded: Edit via `routes/custom_admin.php` for extensions only

## Documentation References
- Docker setup: `docker/README.md`
- Payment integration: Check driver files in `app/PaymentChannels/Drivers/`
- Helper functions: `app/Helpers/helper.php` (read this for available utilities)
- API endpoints: `routes/api/*.php` files
