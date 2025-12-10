# Laravel Sanctum Setup

Laravel Sanctum has been added to `composer.json`. To complete the installation:

## Installation Steps

1. **Install dependencies**:
   ```bash
   composer install
   ```

2. **Publish Sanctum configuration**:
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

3. **Run migrations** (creates personal_access_tokens table):
   ```bash
   php artisan migrate
   ```

4. **Configure Sanctum** (already done in `config/sanctum.php`):
   - Stateful domains configured for frontend
   - Token expiration set to 7 days

## Configuration

Sanctum is already configured in:
- `backend/config/sanctum.php` - API token settings
- `backend/app/Models/User.php` - HasApiTokens trait added
- `backend/routes/api.php` - Auth routes use `auth:sanctum` middleware

## Usage

The authentication controllers (RegisterController, LoginController) are already set up to use Sanctum tokens.

**Note**: This setup is for API token authentication. For SPA authentication with cookies, additional configuration may be needed.

