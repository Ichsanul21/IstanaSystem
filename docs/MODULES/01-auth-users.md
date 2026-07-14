# Module 01: Auth & User Management

## Overview

Multi-level authentication with 8 roles, role hierarchy, branch scoping, and Spatie Permission.

## Tables

- `users` — User accounts with `branch_id`, `is_protected`, `last_login_at`
- `roles` (Spatie) — Name, guard_name
- `permissions` (Spatie) — Name, guard_name
- `model_has_roles`, `role_has_permissions`, `model_has_permissions`

## Features

### Authentication (Laravel Breeze)
- Login with email + password
- Forgot password / reset password
- Session management
- "Remember me" checkbox

### User CRUD (Admin only)
- **Developer, Super Admin, Branch Admin** can create/edit users
- Fields: name, email, password, phone, photo, role, branch_id
- **Developer account** (`is_protected = true`) cannot be deactivated/deleted
- Self-edit: only password and photo

### Role Assignment
- `spatie/laravel-permission v8.3.0` for role/permission management
- 8 roles seeded via `RolePermissionSeeder`
- Role assignment in user create/edit form
- Branch Admin can assign roles below their level

### User Hierarchy

```
Developer ── can manage ALL roles
  Owner ── can manage Super Admin & below
    Super Admin ── can manage Branch Admin & below
      Branch Admin ── can manage Workshop Admin, CS, Cashier, Workshop Staff
```

### Branch Scope
- Users can be assigned to a branch (or null for central roles)
- `SetBranchContext` middleware sets global branch scope
- `HasBranchScope` trait on models automatically filters queries

### UI

```
USERS → Index
┌──────────────────────────────────────────────┐
│  Users                           [+ Add User]│
├──────────────────────────────────────────────┤
│  [Cari nama/email...]  [Role ▼]  [Branch ▼] │
├────┬────────┬────────────┬──────┬──────┬──────┤
│ #  │ Name   │ Email      │ Role │ Branch│ Aksi │
├────┼────────┼────────────┼──────┼──────┼──────┤
│ 1  │ Admin  │ a@b.com   │ SA   │ Cab A│ [E][H]│
└────┴────────┴────────────┴──────┴──────┴──────┘

CREATE / EDIT USER
┌─────────────────────────────────┐
│  Nama        : [______________] │
│  Email       : [______________] │
│  Password    : [______________] │
│  No. HP      : [______________] │
│  Role        : [Select ▼]       │
│  Branch      : [Select ▼]       │
│  Foto        : [Upload]         │
│  Status      : [✔ Aktif]        │
│                                  │
│  [Simpan]    [Batal]            │
└─────────────────────────────────┘
```

## Files

```
app/Models/User.php
app/Http/Middleware/SetBranchContext.php
app/Http/Controllers/Web/UserController.php
app/Http/Controllers/Auth/ (Breeze)
database/seeders/RolePermissionSeeder.php
resources/views/users/index.blade.php
resources/views/users/create.blade.php
resources/views/users/edit.blade.php
```

## Routes (web.php)

All admin routes use the `admin.*` name prefix, nested under `auth` → `verified` → `branch` middleware.

```php
Route::middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::middleware(['branch'])->group(function () {
        // Users (resource CRUD)
        Route::resource('users', UserController::class)
            ->middleware('permission:user.read|user.create|user.update|user.delete');
    });
});
```

**Route names:**
| Method | Path | Name | Middleware |
|--------|------|------|-----------|
| GET | `/admin/users` | `admin.users.index` | `permission:user.read\|user.create\|user.update\|user.delete` |
| GET | `/admin/users/create` | `admin.users.create` | (same) |
| POST | `/admin/users` | `admin.users.store` | (same) |
| GET | `/admin/users/{user}` | `admin.users.show` | (same) |
| GET | `/admin/users/{user}/edit` | `admin.users.edit` | (same) |
| PUT | `/admin/users/{user}` | `admin.users.update` | (same) |
| DELETE | `/admin/users/{user}` | `admin.users.destroy` | (same) |

**Profile routes** (managed via Breeze `auth.php`):
- `GET /profile` → Breeze `ProfileController@edit`
- `PUT /profile` → Breeze `ProfileController@update`
