# Aplikasi Registrasi Pengguna dengan PHP dan MySQL

## Deskripsi
Aplikasi ini merupakan pengembangan dari Tugas Minggu 06, yaitu halaman registrasi pengguna yang dapat menyimpan data pendaftaran ke dalam database MySQL.

Pengguna mengisi nama lengkap, alamat email, username, dan password. Setelah validasi berhasil, data akan disimpan ke tabel `users`.

## Fitur
- Form registrasi berisi nama lengkap, email, username, dan password.
- Validasi seluruh input.
- Validasi format email.
- Username dibatasi 4-20 karakter, terdiri dari huruf, angka, dan underscore.
- Password minimal 8 karakter.
- Perlindungan XSS menggunakan `htmlspecialchars()`.
- Query database menggunakan PDO prepared statement.
- Password disimpan dalam bentuk hash menggunakan `password_hash()`.
- Pemeriksaan agar email dan username tidak digunakan lebih dari satu kali.
- Pesan berhasil atau gagal saat registrasi.

## Teknologi
- PHP
- MySQL
- HTML
- CSS
- PDO

## Struktur File
```text
registrasi_database/
├── index.php
├── database.sql
└── README.md
```

## Cara Menjalankan

1. Install dan jalankan XAMPP.
2. Aktifkan **Apache** dan **MySQL**.
3. Salin folder `registrasi_database` ke dalam folder `htdocs`.
4. Buka `http://localhost/phpmyadmin`.
5. Pilih menu **Import**, lalu impor file `database.sql`.
   - Alternatif: buka tab SQL dan jalankan isi file `database.sql`.
6. Pastikan pengaturan koneksi pada bagian awal `index.php` sesuai dengan konfigurasi MySQL lokal:
   - Host: `localhost`
   - Database: `db_registrasi`
   - Username: `root`
   - Password: kosong (pengaturan bawaan XAMPP).
7. Buka `http://localhost/registrasi_database/`.

## Alur Aplikasi
1. Pengguna membuka halaman registrasi.
2. Pengguna mengisi nama lengkap, email, username, dan password.
3. PHP memeriksa apakah input sudah valid.
4. Aplikasi mengecek apakah email atau username sudah terdaftar.
5. Jika lolos validasi, password di-hash.
6. Data disimpan ke tabel `users` menggunakan prepared statement.
7. Aplikasi menampilkan pesan bahwa registrasi berhasil.

## Struktur Database

Nama database: `db_registrasi`

Nama tabel: `users`

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT, AUTO_INCREMENT | Primary key |
| nama | VARCHAR(100) | Nama lengkap |
| email | VARCHAR(150) | Email, harus unik |
| username | VARCHAR(20) | Username, harus unik |
| password | VARCHAR(255) | Password dalam bentuk hash |
| created_at | TIMESTAMP | Waktu pendaftaran |

## Pengujian
- Isi seluruh data dengan benar: registrasi berhasil.
- Kosongkan salah satu kolom: validasi menampilkan pesan kesalahan.
- Masukkan email tidak valid: registrasi ditolak.
- Masukkan username kurang dari 4 karakter: registrasi ditolak.
- Masukkan password kurang dari 8 karakter: registrasi ditolak.
- Daftarkan email atau username yang sudah digunakan: registrasi ditolak.

## Catatan
Untuk melihat data yang berhasil disimpan, buka phpMyAdmin, pilih database `db_registrasi`, lalu buka tabel `users`. Kolom password berisi hash, bukan password asli.
