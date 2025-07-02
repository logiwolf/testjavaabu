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

