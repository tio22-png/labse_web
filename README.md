# Lab Software Engineering Website

Website modern untuk Laboratorium Software Engineering dengan Bootstrap 5, AOS animations, dan PostgreSQL database.

## 🎨 Fitur Utama

- **Design Modern**: Menggunakan Bootstrap 5 dengan warna dominan biru muda, putih, dan abu-abu lembut
- **Smooth Animations**: AOS (Animate On Scroll) untuk animasi smooth pada setiap elemen
- **Responsive Design**: Tampil sempurna di semua device (desktop, tablet, mobile)
- **Database Integration**: PostgreSQL untuk manajemen data dinamis
- **Sticky Navbar**: Navbar yang sticky dengan efek transisi smooth saat scroll

## 📁 Struktur Folder

```
labse_web/
├── index.php                 # Landing Page dengan hero section
├── assets/
│   ├── css/style.css        # Custom styling
│   ├── js/main.js           # JavaScript untuk AOS & animasi
│   └── img/                 # Folder untuk gambar
├── includes/
│   ├── config.php           # Koneksi PostgreSQL
│   ├── header.php           # HTML head & navbar include
│   ├── navbar.php           # Navigation menu
│   └── footer.php           # Footer dengan social media
├── pages/
│   ├── profile/             # Halaman profil lab
│   ├── personil/            # Daftar & detail personil
│   ├── recruitment/         # Form pendaftaran mahasiswa
│   └── blog/                # Artikel & publikasi
├── admin/                   # Dashboard admin (optional)
└── database/
    └── labse.sql            # Database structure & sample data
```

## 🚀 Instalasi

### 1. Persiapan Database

1. Buat database PostgreSQL baru:
```sql
CREATE DATABASE labse;
```

2. Import struktur database:
```bash
psql -U postgres -d labse -f database/labse.sql
```

### 2. Konfigurasi

Edit file `includes/config.php` dan sesuaikan dengan konfigurasi database Anda:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'labse');
define('DB_USER', 'postgres');
define('DB_PASS', 'your_password'); // Ganti dengan password Anda
```

### 3. Setup Laragon/XAMPP

1. Copy folder `labse_web` ke dalam folder `www` (Laragon) atau `htdocs` (XAMPP)
2. Pastikan PHP dan PostgreSQL sudah terinstall dan berjalan
3. Akses website melalui browser: `http://localhost/labse_web`

## 📄 Halaman-Halaman

### Public Pages

- **Home** (`/index.php`) - Landing page dengan hero section dan overview lab
- **Profile Pages** (`/pages/profile/`)
  - Tentang Lab
  - Visi & Misi
  - Roadmap
  - Focus & Scope
  - Informasi Lainnya
- **Personil** (`/pages/personil/`) - Daftar tim dan detail personil
- **Blog** (`/pages/blog/`) - Artikel dan publikasi
- **Recruitment** (`/pages/recruitment/`) - Pendaftaran mahasiswa baru

### Admin Pages (Optional)

- **Dashboard** (`/admin/index.php`) - Overview statistik
- **Kelola Personil** (`/admin/add_personil.php`)
- **Kelola Artikel** (`/admin/add_artikel.php`)
- **Kelola Mahasiswa** (`/admin/add_mahasiswa.php`)

## 🎯 Database Tables

### 1. lab_profile
Menyimpan konten profil lab (tentang, visi, misi, focus area)

### 2. personil
Data dosen dan staff lab (nama, jabatan, deskripsi, email)

### 3. mahasiswa
Data mahasiswa yang mendaftar (nama, NIM, jurusan, email, alasan)

### 4. artikel
Artikel dan publikasi lab (judul, isi, penulis, tanggal)

## 🎨 Customization

### Mengubah Warna

Edit file `assets/css/style.css` pada bagian `:root`:

```css
:root {
    --primary-color: #4A90E2;      /* Warna utama */
    --secondary-color: #68BBE3;     /* Warna sekunder */
    --light-bg: #F5F8FA;            /* Background terang */
    --dark-text: #2C3E50;           /* Warna teks */
}
```

### Mengubah Logo/Icon

Ganti icon pada `includes/navbar.php`:

```html
<i class="bi bi-code-square fs-3 text-primary me-2"></i>
```

Lihat [Bootstrap Icons](https://icons.getbootstrap.com/) untuk icon lainnya.

## 🔧 Teknologi yang Digunakan

- **Frontend**:
  - Bootstrap 5.3.2
  - Bootstrap Icons 1.11.1
  - AOS (Animate On Scroll) 2.3.1
  - Custom CSS & JavaScript

- **Backend**:
  - PHP 7.4+
  - PostgreSQL 12+

- **External APIs**:
  - UI Avatars (untuk foto placeholder)
  - Picsum Photos (untuk gambar placeholder)

## 📝 Cara Menggunakan

### Menambah Mahasiswa Baru (via Form)

1. Buka `/pages/recruitment/form.php`
2. Isi formulir lengkap
3. Data otomatis tersimpan ke database `mahasiswa`
4. Mahasiswa akan muncul di halaman recruitment

### Menambah Data Manual (via Database)

Contoh menambah personil:

```sql
INSERT INTO personil (nama, jabatan, deskripsi, foto, email) 
VALUES (
    'Dr. John Doe', 
    'Dosen Senior', 
    'Ahli dalam web development...', 
    'john-doe.jpg', 
    'john.doe@university.ac.id'
);
```

## 🐛 Troubleshooting

### Database Connection Error

- Pastikan PostgreSQL service berjalan
- Cek kredensial di `includes/config.php`
- Pastikan extension `php_pgsql` aktif di `php.ini`

### Halaman Tidak Ditemukan

- Cek path BASE_URL di `includes/config.php`
- Pastikan file ada di lokasi yang benar

### Animasi Tidak Jalan

- Pastikan koneksi internet aktif (untuk load AOS dari CDN)
- Cek console browser untuk error JavaScript

## 📞 Support

Untuk pertanyaan atau bantuan, hubungi:
- Email: labse@university.ac.id
- GitHub: [Your Repository]

## 📄 License

This project is open source and available under the MIT License.

---

**Dibuat dengan ❤️ untuk Lab Software Engineering**
