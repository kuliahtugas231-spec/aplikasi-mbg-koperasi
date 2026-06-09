# Rencana Implementasi: Fondasi Aplikasi Koperasi MBG (Tahap 1)

Sesuai dengan *roadmap* yang telah disepakati, fokus tahap pertama ini adalah membangun struktur dasar aplikasi, antarmuka login yang premium dan modern untuk **Synergy Team**, serta fitur CRUD (Create, Read, Update, Delete) agar Petani dapat mengelola katalog bahan baku.

## User Review Required

> [!IMPORTANT]
> **Nama Database**: Aplikasi akan menggunakan database MySQL bernama `koperasi_mbg`. Pastikan Anda sudah menjalankan XAMPP (Apache & MySQL) sebelum kita melanjutkan.
> **Desain Antarmuka**: Saya akan menggunakan **Vanilla CSS murni** dengan desain kelas atas (*premium look*), efek transparan (*glassmorphism*), warna dinamis, dan font modern (Google Fonts: Inter) untuk memastikan aplikasi ini terlihat profesional dan siap demo.

## Open Questions

> [!TIP]
> 1. Apakah ada warna tema khusus atau logo untuk **Synergy Team** yang ingin digunakan di halaman login? (Jika tidak, saya akan merancang palet warna biru-ungu elegan yang modern).
> 2. Untuk *password* default saat pembuatan akun testing nanti, apakah Anda setuju kita gunakan `synergy123` untuk semua aktor sementara waktu?

## Proposed Changes

### 1. Skema Database Inti (Minggu 1)
Kita akan membuat script SQL untuk menginisialisasi database:
- **Tabel `users`**: Menyimpan data autentikasi (Petani, Koperasi, Dapur).
- **Tabel `dompet`**: Otomatis dibuat saat user diregistrasi (saldo awal 0).
- **Tabel `katalog_bahan`**: Menyimpan data stok bahan baku yang diinput oleh Petani (nama bahan, harga, stok, gambar).

### 2. Struktur Direktori & Konfigurasi Utama
- #### [NEW] `config/koneksi.php`
  File koneksi database yang aman menggunakan PDO.
- #### [NEW] `assets/css/style.css`
  Sistem desain utama (warna, font, styling komponen).
- #### [NEW] `assets/js/main.js`
  Untuk interaksi mikro dan animasi di halaman (notifikasi, form validation).

### 3. Sistem Autentikasi (Login Synergy Team)
- #### [MODIFY] `index.php`
  Halaman login utama dengan desain premium, *card* login interaktif, dan *branding* "Synergy Team".
- #### [NEW] `auth/login_process.php`
  Logika validasi password (dengan `password_verify()`) dan inisialisasi session.
- #### [NEW] `auth/logout.php`
  Menghapus session dan mengarahkan kembali ke halaman login.

### 4. Dashboard & CRUD Petani
- #### [NEW] `dashboard_petani.php`
  Halaman utama untuk Petani. Menampilkan tabel daftar stok bahan baku yang mereka miliki.
- #### [NEW] `petani/tambah_bahan.php`
  Formulir dengan desain modern untuk menambahkan bahan pangan baru (Telur, Beras, dll).
- #### [NEW] `petani/edit_bahan.php`
  Formulir untuk memperbarui harga dan stok.
- #### [NEW] `petani/hapus_bahan.php`
  Endpoint proses untuk menghapus data bahan pangan dari database.
- #### [NEW] `dashboard_dapur.php`
  Halaman utama untuk Dapur Umum. Di minggu pertama ini, fokusnya adalah menampilkan katalog bahan pangan yang sudah diinput oleh semua Petani.

## Verification Plan

### Manual Verification
1. Menguji koneksi database untuk memastikan PDO berjalan baik.
2. Menguji fitur Login untuk ketiga *Role* (Petani, Koperasi, Dapur) secara bergantian dan memastikan mereka diarahkan ke dashboard yang benar.
3. Sebagai Petani, saya akan menguji penambahan, pengeditan, dan penghapusan bahan baku.
4. Sebagai Dapur Umum, saya akan memverifikasi bahwa bahan baku yang baru saja diinput oleh Petani langsung muncul di katalog dengan desain antarmuka yang cantik.
