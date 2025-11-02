# Frontend (Laravel + Livewire) - Display Antrian

Dokumen ini menjelaskan struktur folder yang direkomendasikan, bersifat modular, DRY, dan sesuai pola MVC untuk proyek frontend terpisah (tidak digabung dengan backend) menggunakan Laravel + Livewire v3 untuk mengonsumsi API backend yang sudah ada.

UI berfokus pada Display Antrian publik menggunakan Livewire `wire:poll`, serta halaman admin opsional. Login Gmail didukung melalui Google OAuth (lihat bagian Autentikasi).

## Teknologi
- Laravel 10/11 (proyek terpisah dari backend)
- Livewire v3
- TailwindCSS (opsional)
- Laravel HTTP Client (untuk memanggil API backend)

## Variabel Lingkungan (.env)
Atur variabel berikut di proyek frontend:
- API_BASE_URL=http://localhost:8000
- API_TIMEOUT=5
- API_POLL_MS=5000
- FRONTEND_URL=http://localhost:9000 (atau host frontend Anda)
- BACKEND_GOOGLE_REDIRECT=http://localhost:8000/auth/google/redirect
- BACKEND_GOOGLE_CALLBACK=http://localhost:8000/auth/google/callback

Jika nanti dipasang pada domain yang sama, sesuaikan CORS dan cookies.

## Struktur Folder (Berbasis Fitur)

app/
  Livewire/
    Display/
      OverviewBoard.php          # wire:poll overview of all lokets (current + next[2])
      LoketBoard.php             # wire:poll for a single loket {loketId}
      Partials/
        CurrentTicket.php
        NextTicketList.php       # render up to 2 next items
    Auth/
      LoginForm.php              # optional admin login form (email/password)
    Users/
      Index.php                  # admin user list (optional)
    Shared/
      Navbar.php
      Footer.php
  Services/
    Api/
      BaseApi.php                # baseUrl, headers (Bearer), retry, timeouts
      DisplayApi.php             # /api/display/* endpoints
      LoketApi.php               # /api/lokets CRUD
      AntrianApi.php             # /api/antrians CRUD
      UserApi.php                # /api/users CRUD
      AuthApi.php                # /api/auth/* (login/me/refresh/logout)
  DTOs/
    Display/
      LoketDisplay.php           # { loket, current, next[] }
    Common/
      PaginatedResponse.php
  ViewModels/
    Display/
      OverviewVM.php             # optional: format data for view
  Support/
    ApiResponse.php
    PollingBackoff.php           # optional backoff logic for polling
  Http/
    Controllers/
      Page/
        DisplayController.php    # routes to pages (loads Livewire components)
        AdminController.php      # example admin pages
    Middleware/
      AttachApiToken.php         # inject JWT from session to API services (optional)

resources/
  views/
    layouts/
      app.blade.php              # default layout (navbar/footer)
      display.blade.php          # fullscreen board layout for TVs
    livewire/
      display/
        overview-board.blade.php
        loket-board.blade.php
        partials/
          current-ticket.blade.php
          next-ticket-list.blade.php
      auth/
        login-form.blade.php
      shared/
        navbar.blade.php
        footer.blade.php
    pages/
      display/
        overview.blade.php       # @livewire('display.overview-board')
        loket.blade.php          # @livewire('display.loket-board', ['loketId'=>...])

routes/
  web.php
  web/
    display.php                  # public routes: /display/overview, /display/loket/{id}
    admin.php                    # admin routes (optional)

config/
  api.php                        # base_url, timeouts, retry, poll interval

## Endpoint (dikonsumsi dari backend)
Display Publik:
- GET /api/display/lokets
- GET /api/display/lokets/{loket}
- GET /api/display/overview

Terproteksi (JWT):
- /api/lokets (GET/POST/PUT/DELETE)
- /api/antrians (GET/POST/PUT)
- /api/users (GET/POST/GET{id}/PUT/DELETE)
- /api/auth (login/me/refresh/logout)

## Penggunaan wire:poll di Livewire (Display)
Contoh: resources/views/livewire/display/overview-board.blade.php

<div wire:poll.{{ config('api.poll_ms', 3000) }}ms="refresh">
  @foreach($items as $row)
    @include('livewire.display.partials.current-ticket', ['ticket' => $row->current])
    @include('livewire.display.partials.next-ticket-list', ['tickets' => $row->next])
  @endforeach
</div>

Catatan:
- Backend sudah mengembalikan hingga 2 item pada `next` untuk setiap loket.
- Disarankan interval polling 3–5 detik; sesuaikan melalui API_POLL_MS.

## Lapisan Services (Contoh)
app/Services/Api/BaseApi.php
- Konfigurasi base URL dari API_BASE_URL
- Timeout dari API_TIMEOUT
- Header Accept: application/json
- Authorization: Bearer {jwt} jika tersedia (dibaca dari session via middleware)
- Sentralisasi error handling dan retry ringan (opsional)

## Autentikasi (Gmail melalui Google OAuth)
Tersedia dua pola integrasi:

### Opsi A — Gunakan OAuth di Backend (disarankan jika backend sudah memakai Socialite)
Alur:
1) Tombol/route frontend mengarahkan user ke backend:
   - GET: {BACKEND_GOOGLE_REDIRECT}?return_url={FRONTEND_URL}/auth/google/callback
2) User login via Google.
3) Callback backend menukar code dengan Google dan menerbitkan JWT (access/refresh token).
4) Backend me-redirect ke `{return_url}?access_token=...&refresh_token=...`.
5) Halaman frontend `/auth/google/callback` membaca token dari query, menyimpannya ke session, lalu redirect ke aplikasi.

Kebutuhan di Backend:
- Ubah callback Google backend agar mendukung `return_url` opsional dan melakukan redirect dengan token sebagai query parameter. Jika belum ada, tambahkan perubahan kecil ini.

Langkah Frontend:
- Buat routes:
  - GET /login → tampilkan form login (email/password + tombol Google)
  - GET /login/google → redirect ke BACKEND_GOOGLE_REDIRECT dengan `return_url`
  - GET /auth/google/callback → baca token dari query, simpan ke session (opsional juga ke cookie/localstorage), lalu redirect ke dashboard.
- Middleware `AttachApiToken` membaca token dari session dan menambahkan header Authorization untuk Services API.

### Opsi B — Socialite di Frontend (jika ingin OAuth ditangani FE)
Alur:
1) Implementasikan Socialite di proyek frontend dengan kredensial Google Anda.
2) Di callback, dapatkan profil Google dan ID token.
3) Panggil backend untuk menukar identitas Google menjadi JWT (butuh endpoint backend yang menerima token/ID Google dan mengembalikan JWT). Jika belum ada, perlu diimplementasikan di backend.

Rekomendasi: Gunakan Opsi A untuk menghindari duplikasi logika OAuth dan menjaga autentikasi tetap terpusat di backend.

## Langkah Instalasi
1) Buat proyek Laravel baru (terpisah dari backend):
   - composer create-project laravel/laravel frontend-livewire
   - cd frontend-livewire
   - composer require livewire/livewire
2) Konfigurasi variabel .env seperti di atas.
3) Tambahkan file routes: routes/web/display.php, routes/web/admin.php; sertakan di routes/web.php.
4) Buat komponen Livewire (OverviewBoard, LoketBoard, dll.).
5) Bangun Services di app/Services/Api dan petakan endpoint.
6) Buat layouts dan partials di resources/views.
7) Jalankan: php artisan serve (atau server pilihan Anda) dan verifikasi /display/overview menampilkan data.

## Pengujian
- Feature test untuk komponen Livewire (Display) dengan mock Services API.
- Unit test untuk pemetaan Services (parsing DTO, error handling).

## Catatan Deploy
- Setel API_BASE_URL yang benar di production.
- Jika menggunakan Opsi A (OAuth via backend), pastikan redirect callback backend diizinkan di Google Console dan FRONTEND_URL sudah benar.

## Keamanan
- Gunakan HTTPS di production.
- Simpan JWT di HttpOnly cookies atau session sisi server; hindari mengekspos token ke JS klien jika memungkinkan.
- Atur CORS dengan tepat antara host frontend dan backend.

## Peta Jalan (Opsional)
- Tambahkan filter (search/order) pada tabel admin menggunakan query param.
- Tambahkan skeleton loading state untuk polling yang lebih halus.
- Pertimbangkan caching layer (jika perlu) untuk mengurangi beban API.
