# Payment Bill Seeder Documentation

## Overview
Seeder ini akan menggenerate data untuk:
- **Payment Bills** (Tagihan Bulanan)
- **Payments** (Pembayaran)
- **Expenses** (Pengeluaran)

Untuk periode **12 bulan ke belakang** dari bulan lalu (exclude bulan sekarang).
Contoh: Jika sekarang Mei 2026, maka generate dari Juni 2025 - April 2026.

## Ketentuan Seeder

### 1. Payment Bills & Payments

#### Rumah Occupied (Ada Penghuni)
- ✅ **Semua rumah** yang ada penghuni aktif dibuat tagihan **setiap bulan**
- ✅ **100% tagihan dibayar** (status: paid)
- ✅ **Resident yang bayar dipilih secara random** (tidak selalu kepala keluarga)
- ✅ Semua fee_type yang aktif dibuat tagihannya

#### Rumah Vacant (Kosong)
- ✅ **30% chance** untuk membuat tagihan dan membayar
- ✅ **70% chance** tidak ada tagihan
- ✅ Jika ada tagihan, langsung dibayar (status: paid)

### 2. Expenses (Pengeluaran)

#### Recurring Expenses (Pengeluaran Rutin)
- ✅ **Harus ada setiap bulan**
- ✅ Kategori: 
  - **Gaji Satpam**: Rp 1.500.000 (flat)
  - **Token Listrik Pos**: Rp 50.000 - Rp 100.000 (random)

#### Non-Recurring Expenses
- ✅ **1-2 pengeluaran per bulan** (dikurangi dari 2-5)
- ✅ Kategori: Perbaikan Jalan, Perbaikan Selokan, Lain-lain
- ✅ Amount: **Rp 50.000 - Rp 150.000** (maksimal 150k)

## Cara Menjalankan Seeder

### Fresh Migration + Seed (Recommended)
```bash
php artisan migrate:fresh --seed
```

### Hanya Jalankan Seeder Tertentu
```bash
php artisan db:seed --class=PaymentBillSeeder
```

### Jalankan Semua Seeder
```bash
php artisan db:seed
```

## Data yang Dihasilkan

### Estimasi Data
Untuk 8 rumah dengan 3 fee types selama 12 bulan:

**Payment Bills:**
- Rumah Occupied (6 rumah): 6 × 3 × 12 = **216 bills**
- Rumah Vacant (2 rumah): ~2 × 3 × 12 × 30% = **~22 bills**
- **Total: ~238 payment bills**

**Payments:**
- Sama dengan jumlah bills yang dibayar: **~238 payments**

**Expenses:**
- Recurring: 2 kategori × 12 bulan = **24 expenses**
- Non-recurring: ~1.5 × 12 bulan = **~18 expenses**
- **Total: ~42 expenses** (dikurangi dari ~66)
- **Amount per expense: Maksimal Rp 150.000**

## Struktur Data

### Payment Bills
```php
[
    'house_id' => 1,
    'resident_id' => 2, // Random resident, tidak selalu kepala keluarga
    'fee_type_id' => 1,
    'billing_month' => '2025-05-01',
    'amount' => 100000,
    'status' => 'paid', // atau 'unpaid'
]
```

### Payments
```php
[
    'bill_id' => 1,
    'paid_at' => '2025-05-15 10:30:00',
    'payment_method' => 'cash', // atau 'transfer'
    'note' => null,
]
```

### Expenses
```php
[
    'category_id' => 1, // Bukan expense_category_id
    'amount' => 1500000, // Gaji Satpam flat 1.5 juta
    'description' => 'Pengeluaran rutin Gaji Satpam bulan April 2026',
    'expense_date' => '2026-04-10',
]
```

## Notes
- Seeder membaca data dari `database/data/houses.json` dan `database/data/residents.json`
- Fee Types dan Expense Categories dibaca dari database (sudah di-seed di DatabaseSeeder)
- Tanggal pembayaran dan pengeluaran di-random dalam range 1-28 hari di bulan tersebut
- Payment method di-random antara 'cash' dan 'transfer'
- **Bulan sekarang (Mei 2026) tidak di-generate** - hanya sampai bulan lalu (April 2026)
- **Gaji Satpam**: Flat Rp 1.500.000 per bulan
- **Token Listrik Pos**: Rp 50.000 - Rp 100.000 (random)
- **Expense lainnya**: Maksimal Rp 150.000 per pengeluaran
