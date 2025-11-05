# Q Medis API — Endpoint Documentation

Base URL
- http://localhost:8000

Security
- Bearer JWT on protected endpoints via header: `Authorization: Bearer <access_token>`
- Obtain tokens via `POST /api/auth/login` or Google OAuth callback

Postman Environment Variables
- base_url: API base URL (default http://localhost:8000)
- email, password: credentials for login
- jwt_token, refresh_token: tokens managed by Auth endpoints
- loket_id, status, page, per_page, search, order_by, order_dir: helper variables for requests

---

## System

- GET `/api`
  - **Summary**: API health (welcome)
  - **Auth**: Public
  - **Response 200 (example)**:
```json
{
  "status": "success",
  "message": "API is up",
  "data": {
    "service": "Q Medis API",
    "laravel": "12.x",
    "php": "8.2.x",
    "time": "2025-01-01T12:00:00Z"
  }
}
```

---

## Auth

- POST `/api/auth/login`
  - **Summary**: Login with email/password
  - **Auth**: Public
  - **Body**:
    - `email` string (email) — required
    - `password` string — required
  - **Response 200 (example)**:
```json
{
  "status": "success",
  "message": "login success",
  "data": {
    "access_token": "<jwt>",
    "refresh_token": "<uuid>"
  }
}
```
  - **Response 401 (example)**:
```json
{"status":"error","message":"invalid credentials","errors":{}}
```

- POST `/api/auth/refresh`
  - **Summary**: Refresh access token using refresh token
  - **Auth**: Public
  - **Body (optional)**:
    - `refresh_token` string — optional
  - **Response 200 (example)**:
```json
{
  "status": "success",
  "message": "token refreshed",
  "data": {
    "access_token": "<jwt>"
  }
}
```
  - **Response 401 (example)**:
```json
{"status":"error","message":"invalid refresh token","errors":{}}
```

- GET `/api/auth/me`
  - **Summary**: Get authenticated user
  - **Auth**: Bearer
  - **Response 200 (example)**:
```json
{
  "status": "success",
  "message": "profile retrieved",
  "data": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "role": "admin",
    "avatar": null,
    "created_at": "2024-11-01T10:00:00Z",
    "updated_at": "2024-11-01T10:00:00Z"
  }
}
```
  - **Response 401 (example)**:
```json
{"status":"error","message":"unauthorized","errors":{}}
```

- POST `/api/auth/logout`
  - **Summary**: Logout and revoke refresh token
  - **Auth**: Public
  - **Response 200 (example)**:
```json
{"status":"success","message":"logged out","data":null}
```

- GET `/auth/google/redirect`
  - **Summary**: Start Google OAuth (manual browser flow)
  - **Auth**: Public
  - **Notes**: Returns an HTML redirect to Google login.

- GET `/auth/google/callback`
  - **Summary**: Google OAuth callback; returns access/refresh tokens
  - **Auth**: Public
  - **Notes**: Typically invoked by browser after Google login.

---

## Dashboard

- GET `/api/dashboard`
  - **Summary**: Get dashboard data (role-based)
  - **Auth**: Bearer
  - **Response 200 (schema)**: `AdminDashboard` or `PetugasDashboard`
  - **Response 200 (example, AdminDashboard)**:
```json
{
  "statistics": {"total_lokets": 5, "total_users": 12, "total_petugas": 8, "total_antrians_today": 45},
  "antrian_by_status": {"menunggu": 12, "dipanggil": 3, "selesai": 30},
  "loket_statistics": [{"id":1,"nama_loket":"Loket A","kode_prefix":"A","total_today":15,"menunggu":5,"dipanggil":1,"selesai":9}],
  "recent_antrians": [{"id":45,"loket_id":1,"nomor_antrian":"A015","status":"menunggu","waktu_panggil":null,"created_at":"2024-11-01T14:30:00Z","updated_at":"2024-11-01T14:30:00Z","loket":{"id":1,"nama_loket":"Loket A","kode_prefix":"A","deskripsi":"Pendaftaran"}}],
  "hourly_statistics": [{"hour":8,"total":5},{"hour":9,"total":12},{"hour":10,"total":15}]
}
```

---

## Display (Public)

- GET `/api/display/lokets`
  - **Summary**: List lokets (public)
  - **Auth**: Public
  - **Response 200 (example)**:
```json
{"status":"success","message":"lokets retrieved","data":[{"id":1,"nama_loket":"Loket A","kode_prefix":"A","deskripsi":"Pendaftaran","created_at":"2024-11-01T10:00:00Z","updated_at":"2024-11-01T10:00:00Z"}]}
```

- GET `/api/display/lokets/{loket}`
  - **Summary**: Get display data for a loket
  - **Auth**: Public
  - **Path Params**:
    - `loket` integer — required
  - **Response 200 (example)**:
```json
{
  "status":"success",
  "message":"display retrieved",
  "data":{
    "loket":{"id":1,"nama_loket":"Loket A","kode_prefix":"A","deskripsi":"Pendaftaran","created_at":"2024-11-01T10:00:00Z","updated_at":"2024-11-01T10:00:00Z"},
    "current":{"id":12,"loket_id":1,"nomor_antrian":"A012","status":"dipanggil","waktu_panggil":"2024-11-01T10:45:00Z","created_at":"2024-11-01T10:40:00Z","updated_at":"2024-11-01T10:45:00Z"},
    "next":[{"id":13,"loket_id":1,"nomor_antrian":"A013","status":"menunggu","waktu_panggil":null,"created_at":"2024-11-01T10:46:00Z","updated_at":"2024-11-01T10:46:00Z"}]
  }
}
```

- GET `/api/display/overview`
  - **Summary**: Display overview for all lokets
  - **Auth**: Public

- POST `/api/display/antrians`
  - **Summary**: Create an antrian (take a number)
  - **Auth**: Public
  - **Body**:
    - `loket_id` integer — required
  - **Response 201 (example)**:
```json
{"status":"success","message":"antrian created","data":{"id":20,"loket_id":1,"nomor_antrian":"A020","status":"menunggu","waktu_panggil":null,"created_at":"2024-11-01T11:10:00Z","updated_at":"2024-11-01T11:10:00Z"}}
```
  - **Response 422 (example)**:
```json
{"status":"error","message":"validation error","errors":{"loket_id":["The loket id field is required."]}}
```

---

## Lokets

- GET `/api/lokets`
  - **Summary**: List lokets (paginated)
  - **Auth**: Bearer
  - **Query Params**:
    - `page` integer (default 1)
    - `per_page` integer (default 15)
    - `search` string
    - `order_by` enum: id, nama_loket, kode_prefix, created_at, updated_at
    - `order_dir` enum: asc, desc
  - **Response 200 (example)**:
```json
{
  "status":"success",
  "message":"lokets retrieved",
  "data":{
    "data":[{"id":1,"nama_loket":"Loket A","kode_prefix":"A","deskripsi":"Pendaftaran","created_at":"2024-11-01T10:00:00Z","updated_at":"2024-11-01T10:00:00Z"}],
    "pagination":{"current_page":1,"per_page":15,"total":2,"last_page":1}
  }
}
```

- POST `/api/lokets`
  - **Summary**: Create a loket
  - **Auth**: Bearer
  - **Body**:
    - `nama_loket` string — required
    - `kode_prefix` string — required
    - `deskripsi` string (nullable)
  - **Response 201 (example)**:
```json
{"status":"success","message":"loket created","data":{"id":2,"nama_loket":"Loket B","kode_prefix":"B","deskripsi":"Pembayaran","created_at":"2024-11-01T11:00:00Z","updated_at":"2024-11-01T11:00:00Z"}}
```

- GET `/api/lokets/{loket}`
  - **Summary**: Get a loket by ID
  - **Auth**: Bearer
  - **Path Params**: `loket` integer — required

- PUT `/api/lokets/{loket}`
  - **Summary**: Update a loket
  - **Auth**: Bearer
  - **Path Params**: `loket` integer — required
  - **Body**: any of `nama_loket`, `kode_prefix`, `deskripsi`

- DELETE `/api/lokets/{loket}`
  - **Summary**: Delete a loket
  - **Auth**: Bearer
  - **Path Params**: `loket` integer — required

---

## Users

- GET `/api/users`
  - **Summary**: List users (paginated)
  - **Auth**: Bearer
  - **Query Params**:
    - `page` integer (default 1)
    - `per_page` integer (default 15)
    - `search` string
    - `order_by` enum: id, name, email, role, created_at, updated_at
    - `order_dir` enum: asc, desc

- POST `/api/users`
  - **Summary**: Create user
  - **Auth**: Bearer
  - **Body**:
    - `name` string — required
    - `email` string — required
    - `password` string — required (min 6)
    - `role` string (nullable)
    - `avatar` string (nullable)
    - `google_id` string (nullable)

- GET `/api/users/{user}`
  - **Summary**: Get user by ID
  - **Auth**: Bearer
  - **Path Params**: `user` integer — required

- PUT `/api/users/{user}`
  - **Summary**: Update user
  - **Auth**: Bearer
  - **Path Params**: `user` integer — required
  - **Body**: any of `name`, `email`, `password`, `role`, `avatar`, `google_id`

- DELETE `/api/users/{user}`
  - **Summary**: Delete user
  - **Auth**: Bearer
  - **Path Params**: `user` integer — required

---

## Antrians

- GET `/api/antrians`
  - **Summary**: List antrians (paginated)
  - **Auth**: Bearer
  - **Query Params**:
    - `page` integer (default 1)
    - `per_page` integer (default 15)
    - `loket_id` integer
    - `status` enum: menunggu, dipanggil, selesai
    - `search` string
    - `order_by` enum: id, loket_id, nomor_antrian, status, waktu_panggil, created_at, updated_at
    - `order_dir` enum: asc, desc

- GET `/api/antrians/{antrian}`
  - **Summary**: Get an antrian by ID
  - **Auth**: Bearer
  - **Path Params**: `antrian` integer — required

- PUT `/api/antrians/{antrian}`
  - **Summary**: Update an antrian status
  - **Auth**: Bearer
  - **Path Params**: `antrian` integer — required
  - **Body**:
    - `status` string — required (e.g. `dipanggil`)

---

## Schemas (Requests)

- **LoketCreateRequest**
  - `nama_loket` string — required
  - `kode_prefix` string — required
  - `deskripsi` string (nullable)

- **LoketUpdateRequest**
  - `nama_loket` string
  - `kode_prefix` string
  - `deskripsi` string (nullable)

- **AntrianCreateRequest**
  - `loket_id` integer — required

- **AntrianUpdateRequest**
  - `status` string — required (example: `dipanggil`)

- **UserCreateRequest**
  - `name` string — required
  - `email` string — required (email)
  - `password` string — required (min 6)
  - `role` string (nullable)
  - `avatar` string (nullable)
  - `google_id` string (nullable)

- **UserUpdateRequest**
  - `name` string
  - `email` string (email)
  - `password` string (min 6)
  - `role` string (nullable)
  - `avatar` string (nullable)
  - `google_id` string (nullable)

---

## Example cURL Snippets

- Login
```bash
curl -s -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"<email>","password":"<password>"}' \
  <base_url>/api/auth/login
```

- Authenticated request (Me)
```bash
curl -s -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>" \
  <base_url>/api/auth/me
```

- Create Antrian (Public)
```bash
curl -s -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"loket_id": 1}' \
  <base_url>/api/display/antrians
```
