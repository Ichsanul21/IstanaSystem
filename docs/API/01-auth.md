# API: Auth

## POST /api/v1/auth/login

Authenticate user and return token.

**Request:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "token": "1|abc123def456...",
        "user": {
            "id": 1,
            "name": "Admin",
            "email": "admin@example.com",
            "role": "super_admin",
            "branch_id": 1,
            "branch": { "id": 1, "name": "Cabang A" }
        }
    }
}
```

**Response (401):**
```json
{
    "success": false,
    "message": "Email atau password salah"
}
```

## POST /api/v1/auth/logout

Revoke current token.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "success": true,
    "message": "Berhasil logout"
}
```

## GET /api/v1/auth/me

Get authenticated user profile.

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com",
        "phone": "0812xxxx",
        "photo": "/uploads/photos/admin.jpg",
        "role": "super_admin",
        "permissions": ["view_orders", "create_orders", ...],
        "branch": { "id": 1, "name": "Cabang A" }
    }
}
```

## PUT /api/v1/auth/profile

Update password or photo.

**Request (multipart):**
```
password: newpassword (optional)
password_confirmation: newpassword (optional)
photo: [file] (optional, max 2MB, jpg/png)
```
