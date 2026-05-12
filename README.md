# BEON - Sistem Manajemen Perumahan

Aplikasi manajemen perumahan berbasis web untuk mengelola data rumah, penghuni, tagihan bulanan, pembayaran, dan pengeluaran perumahan.

## 📋 Fitur Utama

- **Manajemen Rumah**: Kelola data rumah dan status (occupied/vacant)
- **Manajemen Penghuni**: Data penghuni, kepala keluarga, status aktif/non-aktif
- **Tagihan Bulanan**: Generate dan kelola tagihan iuran bulanan
- **Pembayaran**: Catat pembayaran tagihan (satuan & bulk)
- **Pengeluaran**: Kelola pengeluaran perumahan (recurring & non-recurring)
- **Laporan Keuangan**: Laporan pemasukan dan pengeluaran


## 📦 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/naufalix/beon
cd beon
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

Copy file `.env.example` menjadi `.env`:

```bash
copy .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beon
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Jalankan Migration & Seeder

```bash
php artisan migrate:fresh --seed
```

### 7. Jalankan Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

## 🔐 Login Credentials

**Admin:**
- Email: `admin@naufal.dev`
- Password: `admin`

## 🎯 Fitur Detail

### 1. Manajemen Rumah
- Tambah, edit, hapus data rumah
- Status rumah: Occupied / Vacant
- Lihat daftar penghuni per rumah

### 2. Manajemen Penghuni
- Data lengkap penghuni (nama, telepon, status pernikahan, dll)
- Status: Permanent / Contract
- Kepala keluarga & anggota keluarga
- Status aktif/non-aktif dengan tanggal pindah

### 3. Tagihan Bulanan (Payment Bills)
- **Generate Tagihan**: Generate otomatis untuk semua rumah
- **Tambah Tagihan Satuan**: Tambah tagihan manual dengan pilih penghuni
- **Bayar Tagihan**: Catat pembayaran satuan
- **Bayar Bulk**: Bayar beberapa bulan sekaligus
- Filter berdasarkan bulan
- Status: Paid / Unpaid

### 4. Jenis Iuran (Fee Types)
- Kelola jenis iuran (Satpam, Kebersihan, Perbaikan, dll)
- Set nominal iuran
- Status aktif/non-aktif

### 5. Pengeluaran (Expenses)
- Catat pengeluaran perumahan
- Kategori recurring (Gaji Satpam, Token Listrik)
- Kategori non-recurring (Perbaikan, Lain-lain)
- Filter berdasarkan periode

### 6. Laporan Keuangan
- Total pemasukan dari iuran
- Total pengeluaran
- Saldo
- Filter berdasarkan periode