

# Laravel Blog Application

This is a Laravel-based blog application with features such as post creation, tagging (via Tagify), file uploads, custom OTP password reset, and testing setup using PHPUnit.

---

## System Requirements

| Tool          | Required Version  |
|---------------|-------------------|
| PHP           | ^8.2              |
| Node.js       | >=16.15.0         |
| NPM           | >=9.0.0           |
| Laravel       | ^12.0             |
| MySQL/SQLite  | Compatible version|

###  Required PHP Extensions

Make sure these PHP extensions are installed and enabled:

- `pdo`
- `pdo_sqlite` or `pdo_mysql`
- `openssl`
- `mbstring`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `fileinfo`
- `curl`
- `bcmath` *(optional but recommended)*
- `gd` *(required for image/media uploads)*

---

## 📦 PHP Dependencies (`composer.json`)

```json
"require": {
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "laravel/tinker": "^2.10.1",
  "blade-ui-kit/blade-heroicons": "^2.6",
  "blade-ui-kit/blade-icons": "^1.8",
}
```


### Frontend (Node & NPM)

- Node.js: **>= 16.15.0**
- NPM: **>= 9.0.0** (Note: Tagify requires npm >=9)

---

## 📦 JavaScript Dependencies

### Runtime Dependencies

These are installed in `dependencies` and required for your app in production:

| Package                    | Version     | Purpose                              |
|----------------------------|-------------|--------------------------------------|
| `@fortawesome/fontawesome-free` | ^6.7.2   | Icons (Font Awesome)                 |
| `@yaireo/tagify`           | ^4.35.1     | Tag input for tagging functionality |

### Development Dependencies

These are installed in `devDependencies` and used during development/building:

| Package                   | Version     | Purpose                                      |
|---------------------------|-------------|----------------------------------------------|
| `vite`                    | ^6.2.4      | Modern frontend build tool                   |
| `laravel-vite-plugin`     | ^1.2.0      | Integrates Vite with Laravel                 |
| `tailwindcss`             | ^4.1.11     | Utility-first CSS framework                  |
| `@tailwindcss/vite`       | ^4.0.0      | Tailwind integration for Vite                |
| `@tailwindcss/postcss`    | ^4.1.11     | Tailwind plugin for PostCSS                  |
| `postcss`                 | ^8.5.6      | CSS processing tool                          |
| `autoprefixer`            | ^10.4.21    | Adds vendor prefixes to CSS                  |
| `axios`                   | ^1.8.2      | Promise-based HTTP client for browser/Node.js|
| `concurrently`            | ^9.0.1      | Run multiple commands concurrently           |


##  Installation Guide

Follow these steps to set up the project locally.

###  Clone the repository

```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo
```

### Install PHP Dependencies via Composer
```
composer install
```

### Install JS Dependencies via NPM
```
npm install
```

### Copy and Configure Environment File
```
cp .env.example .env
php artisan key:generate
```

### Ensure the following .env settings are correct for your local dev:
```
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

### Create a SQLite file if it doesn’t exist:
```
touch database/database.sqlite
```

### Update .env with the absolute path:
```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```
### Run Migrations
```
php artisan migrate
```

###Run the Application
You can use the following commands to start the backend and frontend:
```
npm run dev      # Starts Vite
php artisan serve
```
## Run Your Tests
Run all your tests with:

```
php artisan test
```

Or using PHPUnit directly:

```
./vendor/bin/phpunit
```

### Test Coverage
This project includes feature tests for the following:

 - User registration, login, logout
 - Route protection
 - Post CRUD operations
 - OTP-based password reset

You can find all tests inside the tests/Feature/ directory:

```
tests/Feature/
```

##  Mail Configuration (for OTP Password Reset)

This project uses **Mailtrap** to handle email sending in development — for OTP-based password reset.

###  `.env` Settings

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io 
MAIL_PORT=2525
MAIL_USERNAME=17f51a51d34832
MAIL_PASSWORD=864870def27f35
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="ihsan"
```

# ⚠️ Important: Handling Vite Asset Loading in Layout for Tagify & Testing

##  Vite Configuration in Layout

To ensure the Tagify and other JavaScript components work correctly, use the following `@vite` directive only when the `manifest.json` file exists.

###  Production / Development (Normal App Usage)

Keep this code only in your `layout.blade.php` file to ensure assets like Tagify work properly:

```blade
@vite([
  'resources/css/app.css',
  'resources/js/app.js',
  'resources/js/tagify-init.js',
  'resources/js/like.js'
  ]) 
```
and remove or cooment 
```
@if (file_exists(public_path('build/manifest.json')))
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/tagify-init.js', 'resources/js/like.js'])
  @endif
])
```



### For Testing (e.g., PHPUnit, Feature Tests)
During tests, Vite’s manifest might not be available, which can break the layout.
REMOVE this line below in test environment, which is in `layout.blade.php` :

```

@vite([
  'resources/css/app.css',
  'resources/js/app.js',
  'resources/js/tagify-init.js',
  'resources/js/like.js'
])

```
instead use 
```
@if (file_exists(public_path('build/manifest.json')))
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/tagify-init.js', 'resources/js/like.js'])
  @endif
])
```


### Tech Stack


 - Laravel ^12
 - PHP ^8.2
 - Tailwind CSS ^4
 - Vite
 - Alpine.js (optional, loaded)
 - Tagify (for tag inputs)
 - SQLite (default DB used in .env)

























