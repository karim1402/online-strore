# API Versioning Plan

## Goal

- Keep all existing `/api/user/*` routes **completely unchanged** — same controllers, same `auth:api` guard, same behavior.
- Add new `/api/v2/user/*` routes with new v2 controllers.
- The `auth:api` guard (JWT / `User` model) is **shared** between v1 and v2 — no new guard needed.

---

## Current State

| Item | Value |
|---|---|
| v1 base URL | `/api/user/*` |
| Auth guard | `auth:api` (JWT, `users` provider → `App\Models\User`) |
| Route loader | `bootstrap/app.php` → `prefix('api')` |
| v1 controllers | `app/Http/Controllers/Api/User/` |

---

## Implementation Steps

### Step 1 — Create v2 Route File

**File:** `routes/api/v2/user.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\User\AuthController;
// ... other v2 controllers

Route::prefix('user')->group(function () {

    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('send-otp',   'sendOtp')->name('v2.user.send-otp');
        Route::post('register',   'register')->name('v2.user.register');
        Route::post('login',      'login')->name('v2.user.login');
        Route::post('logout',     'logout')->middleware('auth:api')->name('v2.user.logout');
        Route::post('refresh',    'refresh')->middleware('auth:api')->name('v2.user.refresh');
        Route::get('profile',     'profile')->middleware('auth:api')->name('v2.user.profile');
        Route::post('update-profile',  'updateProfile')->middleware('auth:api')->name('v2.user.update-profile');
        Route::post('change-password', 'changePassword')->middleware('auth:api')->name('v2.user.change-password');
        Route::delete('delete-account','deleteAccount')->middleware('auth:api')->name('v2.user.delete-account');
    });

    // ... add only the route groups that change in v2

});
```

> Only include route groups that actually change in v2. Routes that stay identical can stay in v1.

---

### Step 2 — Register v2 Routes in `bootstrap/app.php`

Add a **new block** inside the `then` callback. Do **not** touch the existing v1 block.

```php
// EXISTING v1 block — do not change
Route::middleware('api')
    ->prefix('api')
    ->group(function () {
        require base_path('routes/api/user.php');
        require base_path('routes/api/store.php');
        require base_path('routes/api/admin.php');
        require base_path('routes/api/delivery.php');
    });

// NEW v2 block — add below
Route::middleware('api')
    ->prefix('api/v2')
    ->group(function () {
        require base_path('routes/api/v2/user.php');
    });
```

---

### Step 3 — Create v2 Controllers

**Directory:** `app/Http/Controllers/Api/V2/User/`

#### Option A — Extend v1 (when only a few methods change)

Inherit all unchanged methods from v1 and override only what is different.

```php
namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Api\User\AuthController as V1AuthController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends V1AuthController
{
    public function login(Request $request): JsonResponse
    {
        // new v2 login logic here
    }

    // all other methods are inherited from v1 automatically
}
```

#### Option B — Full new controller (when logic is significantly different)

```php
namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    // full v2 implementation
}
```

> The guard is still `auth:api` in both options. No changes to `config/auth.php`.

---

## Final File Structure

```
routes/
  api/
    user.php              ← v1 (no changes)
    admin.php             ← v1 (no changes)
    store.php             ← v1 (no changes)
    delivery.php          ← v1 (no changes)
    v2/
      user.php            ← new

app/Http/Controllers/Api/
  User/                   ← v1 controllers (no changes)
    AuthController.php
    CartController.php
    OrderController.php
    ...
  V2/
    User/                 ← new v2 controllers
      AuthController.php
      ...
```

---

## What Is NOT Changed

| Item | Status |
|---|---|
| `routes/api/user.php` | Unchanged |
| `routes/api/admin.php` | Unchanged |
| `routes/api/store.php` | Unchanged |
| `routes/api/delivery.php` | Unchanged |
| All v1 controllers | Unchanged |
| `config/auth.php` guards | Unchanged |
| `auth:api` guard / JWT / `User` model | Shared between v1 and v2 |
| Existing v1 route names | Unchanged |

---

## URL Comparison

| Version | Example URL |
|---|---|
| v1 (current) | `POST /api/user/login` |
| v2 (new) | `POST /api/v2/user/login` |

---

## Checklist

- [x] Create `routes/api/v2/user.php`
- [x] Add v2 route block in `bootstrap/app.php`
- [x] Create `app/Http/Controllers/Api/V2/User/` directory
- [x] Create v2 controllers (extend v1 or new, per endpoint)
- [x] Test v1 routes still work after adding v2 block
- [x] Test v2 routes respond correctly
- [x] Confirm `auth:api` guard works on both v1 and v2 protected routes

