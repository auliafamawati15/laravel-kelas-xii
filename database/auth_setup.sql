-- =====================================================================
-- PANDUAN STRUKTUR TABEL & QUERY SQL UNTUK SISTEM ADMINISTRATOR FILM
-- Proyek: Website Film (Laravel 13 + SQLite / MySQL)
-- =====================================================================

-- 1. STRUKTUR TABEL ROLES (Tabel Hak Akses Pengguna)
CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT, -- (Pada MySQL gunakan: INT AUTO_INCREMENT PRIMARY KEY)
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Data Awal Roles:
-- ID 1 = 'user' (default)
-- ID 2 = 'admin' (akses dashboard)
INSERT INTO roles (id, name, created_at, updated_at) VALUES 
(1, 'user', datetime('now'), datetime('now')),
(2, 'admin', datetime('now'), datetime('now'));


-- 2. STRUKTUR TABEL PROFILES (Biodata Pengguna)
CREATE TABLE IF NOT EXISTS profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    umur INTEGER NOT NULL DEFAULT 0,
    bio TEXT NULL,
    alamat TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);


-- 3. STRUKTUR TABEL USERS (Tabel Akun)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    profile_id INTEGER NULL REFERENCES profiles(id) ON DELETE SET NULL,
    role_id INTEGER NOT NULL DEFAULT 1 REFERENCES roles(id) ON DELETE RESTRICT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);


-- 4. CARA MEMBUAT 1 AKUN ADMIN VIA SQL (Password: admin123)
-- Catatan: Password di-hash menggunakan algoritma Bcrypt (cost 12):
-- Hash berikut adalah hasil dari: Hash::make('admin123')
-- $2y$12$pEaF8R3Xp0u02j9KxM.b.OFq6g8qC/9g7yT2N9p5F7GZc9n4hO0W2

-- Langkah A: Buat profil admin
INSERT INTO profiles (id, umur, bio, alamat, created_at, updated_at)
VALUES (999, 25, 'Super Administrator OneMovies', 'Kantor Pusat', datetime('now'), datetime('now'));

-- Langkah B: Buat user admin (Login: admin_utama@example.com / Password: admin123)
INSERT INTO users (name, email, password, role_id, profile_id, created_at, updated_at)
VALUES (
    'admin_utama',
    'admin_utama@example.com',
    '$2y$12$pEaF8R3Xp0u02j9KxM.b.OFq6g8qC/9g7yT2N9p5F7GZc9n4hO0W2', -- hash dari: admin123
    2, -- role_id 2 = admin
    999,
    datetime('now'),
    datetime('now')
);

-- ATAU: Mengubah user yang sudah ada menjadi Admin via SQL:
-- UPDATE users SET role_id = 2 WHERE email = 'email_user_anda@gmail.com';
