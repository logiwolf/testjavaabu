##  System Requirements

To run this Laravel project locally, ensure your environment meets the following requirements:

### Backend (PHP)

- PHP: **^8.2**
- Composer: **Latest stable version**
- Database: **SQLite** 
- PHP Extensions:
  - `openssl`
  - `pdo`
  - `mbstring`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `fileinfo`
  - `bcmath`
  - `curl`

### Frontend (Node & NPM)

- Node.js: **>= 16.15.0**
- NPM: **>= 9.0.0** (Note: Tagify requires npm >=9)

---

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

### Tech Stack

 - Laravel ^12
 - PHP ^8.2
 - Tailwind CSS ^4
 - Vite
 - Alpine.js (optional, loaded)
 - Tagify (for tag inputs)
 - SQLite (default DB used in .env)

























## 🔐 How Login Works

This project includes a complete **user authentication system** built using Laravel. It allows users to register, log in, log out, and access protected resources. The system uses Laravel's built-in authentication features along with custom enhancements.

---

### ✅ Features

- User registration with email & password  
- Login and logout functionality  
- "Forgot password" with email-based reset  
- Middleware protection for authenticated routes  
- Custom login validation and session handling  

---

### 📂 Directory Structure

| File / Directory               | Purpose                                                       |
| ------------------------------ | ------------------------------------------------------------- |
| `routes/web.php`               | Defines web routes including login, register, and dashboard   |
| `app/Http/Controllers/Auth/`   | Contains authentication-related controllers                   |
| `resources/views/auth/`        | Blade templates for login, register, and password reset forms |
| `app/Models/User.php`          | User model with authentication features                       |

---

### 🚀 Login Implementation

#### 1. **Login Form (Frontend)**

The login form is located at `resources/views/auth/login.blade.php` and includes the required fields and CSRF token:

```html
<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>
    <button type="submit">Login</button>
</form>

```
### 2. 🔐 Login Logic (Controller)

The login functionality is handled inside a controller method, typically within `LoginController.php`. Below is an example of how the logic works:

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}
```


#### 🧠 Explanation

- Credentials are validated using Laravel's built-in `validate()` method.
- `Auth::attempt()` tries to authenticate the user using the provided email and password.

**If authentication is successful:**

- The session is **regenerated** for security.
- The user is **redirected to the intended route** (usually the home page).

**If authentication fails:**

- The user is **redirected back** with an error message.
- Only the **email input** is retained in the form for user convenience.

### 🔒 Session Handling

- A **secure session** is created upon successful login.
- Laravel **automatically regenerates the session** to prevent session fixation attacks.
- Authenticated user data can be accessed using:

  ```php
  Auth::user()
  ```

### 🔁 Custom Password Reset with OTP (One-Time Password)

This project includes a **custom password reset flow** using OTP (One-Time Password) instead of the default token-based reset.

---

#### 🧾 Flow Overview

1. User visits the **Forgot Password** page and submits their email.
2. An OTP is **generated and emailed** to the user.
3. User enters the OTP on the verification page.
4. OTP is **validated** and **expires in 10 minutes**.
5. If valid, user is redirected to a **custom password reset form**.
6. The password is securely updated, and the OTP record is deleted.

---

#### 📂 Blade Views

| File                              | Purpose                          |
|-----------------------------------|----------------------------------|
| `auth/passwords/email.blade.php`  | Email input form for OTP request |
| `auth/passwords/otp.blade.php`    | OTP input form                   |
| `auth/passwords/reset-custom.blade.php` | Custom reset password form |

---

#### ⚙️ Routes

Custom routes for this OTP flow are defined as:

```php
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('/forgot-password', 'sendOtp')->name('password.otp.send');
    Route::get('/otp-verify', 'showOtpForm')->name('password.otp.verify');
    Route::post('/otp-verify', 'verifyOtp')->name('password.otp.check');
    Route::get('/reset-custom-password', 'showCustomResetForm')->name('password.custom.reset');
    Route::post('/reset-custom-password', 'updatePassword')->name('password.custom.update');
});

