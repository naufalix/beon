# Implementation Plan: Fitur Pembayaran & Pengeluaran RT

## Ringkasan

Membangun fitur administrasi pembayaran iuran bulanan (Satpam 100k, Kebersihan 15k) dan pengeluaran RT. Termasuk generate tagihan otomatis, pencatatan pembayaran, pencatatan pengeluaran, serta dashboard report dengan grafik pemasukan vs pengeluaran selama 1 tahun.

## Keputusan Desain

- **Bayar lunas** — Tidak ada pembayaran parsial, status hanya `unpaid`/`paid`
- **Generate tagihan otomatis** — Tombol generate per bulan, skip jika tagihan sudah ada (dibuat manual)
- **Tanpa bukti bayar** — Tidak perlu upload bukti transfer
- **Saldo** — Dihitung murni dari `SUM(pemasukan) - SUM(pengeluaran)`, tanpa saldo awal

---

## Proposed Changes

### 1. Models — Tambah Relationships & Casts

#### [MODIFY] FeeType.php
- Tambah cast `is_active` → boolean, `amount` → decimal
- Tambah relasi `paymentBills()`

#### [MODIFY] PaymentBill.php
- Tambah relasi: `house()`, `resident()`, `feeType()`, `payment()`
- Tambah cast: `billing_month` → date, `amount` → decimal
- Tambah scope `scopePaid()`, `scopeUnpaid()`

#### [MODIFY] Payment.php
- Tambah relasi: `bill()` (belongsTo PaymentBill)
- Tambah cast: `paid_at` → datetime

#### [MODIFY] ExpenseCategory.php
- Tambah cast `is_recurring` → boolean
- Tambah relasi `expenses()`

#### [MODIFY] Expense.php
- Tambah relasi: `category()` (belongsTo ExpenseCategory)
- Tambah cast: `expense_date` → date, `amount` → decimal

---

### 2. Seeder — Data Awal fee_types & expense_categories

#### [MODIFY] DatabaseSeeder.php

Tambahkan seeder untuk:

**fee_types:**
| id | name | amount |
|----|------|--------|
| 1 | Satpam | 100000 |
| 2 | Kebersihan | 15000 |

**expense_categories:**
| id | name | is_recurring |
|----|------|-------------|
| 1 | Gaji Satpam | true |
| 2 | Token Listrik Pos | true |
| 3 | Perbaikan Jalan | false |
| 4 | Perbaikan Selokan | false |
| 5 | Lain-lain | false |

---

### 3. CRUD Fee Types (Jenis Iuran)

#### [NEW] AdminFeeType.php

Controller CRUD sederhana mengikuti pola `AdminUser`:
- `index()` — List semua fee types
- `postHandler()` → `store()`, `update()`, `destroy()`
- Validasi: `name` required, `amount` required numeric, `is_active` boolean

#### [NEW] fee-type.blade.php

View tabel + modal (tambah/edit/hapus) dengan kolom:
- Nama iuran
- Nominal (format Rupiah)
- Status aktif/nonaktif (badge)
- Action (edit, hapus)

---

### 4. CRUD Expense Categories (Kategori Pengeluaran)

#### [NEW] AdminExpenseCategory.php

Controller CRUD mengikuti pola `AdminUser`:
- `index()` — List semua kategori
- `postHandler()` → `store()`, `update()`, `destroy()`
- Validasi: `name` required, `is_recurring` boolean

#### [NEW] expense-category.blade.php

View tabel + modal (tambah/edit/hapus) dengan kolom:
- Nama kategori
- Tipe: Rutin/Insidental (badge)
- Action

---

### 5. Tagihan Bulanan (Payment Bills) — Fitur Utama

#### [NEW] AdminPaymentBill.php

**Method utama:**

##### `index(Request $request)`
- Parameter filter: `month` (default bulan ini, format `Y-m`)
- Tampilkan tabel tagihan per rumah per jenis iuran
- Eager load: `house`, `resident`, `feeType`, `payment`

##### `generate(Request $request)`
- Input: `billing_month` (format `Y-m`)
- Logic:
  1. Ambil semua `fee_types` yang `is_active = true`
  2. Ambil semua `houses` yang punya `activeResidents` (minimal 1 penghuni aktif)
  3. Untuk setiap kombinasi house × fee_type:
     - **Cek** apakah `payment_bills` sudah ada untuk `house_id + fee_type_id + billing_month`
     - **Skip** jika sudah ada (yang dibuat manual admin)
     - **Create** jika belum ada, dengan `amount` dari `fee_type.amount` dan `resident_id` dari salah satu active resident
  4. Return jumlah tagihan yang berhasil di-generate

##### `pay(Request $request)`
- Input: `bill_id`
- Logic:
  1. Cari `PaymentBill` by id
  2. Cek status, jika sudah `paid` → return error
  3. Buat record `Payment` dengan `paid_at = now()`, `payment_method` dari request
  4. Update `PaymentBill.status` → `paid`

##### `payBulk(Request $request)` — Bayar 1 Tahun
- Input: `house_id`, `fee_type_id`, `start_month`, `months` (jumlah bulan, max 12)
- Logic:
  1. Loop dari `start_month` sebanyak `months` bulan
  2. Untuk setiap bulan:
     - Cek/buat `payment_bill` jika belum ada
     - Cek apakah sudah `paid`, skip jika sudah
     - Buat `Payment` + update status `paid`
  3. Return jumlah tagihan yang berhasil dibayar

##### `destroy(Request $request)`
- Hapus tagihan (hanya jika belum dibayar)

#### [NEW] payment-bill.blade.php

**Layout:**
- Filter bulan di atas (input month picker + tombol filter)
- Tombol **"Generate Tagihan Bulan Ini"** (modal konfirmasi)
- Tombol **"Bayar 1 Tahun"** (modal form: pilih rumah, jenis iuran, bulan mulai, jumlah bulan)

**Tabel tagihan:**
| No | Rumah | Penghuni | Jenis Iuran | Bulan | Nominal | Status | Action |
|----|-------|----------|-------------|-------|---------|--------|--------|
- Status: badge hijau `Lunas` / merah `Belum Bayar`
- Action:
  - Jika unpaid: tombol **Bayar** (modal konfirmasi + pilih metode bayar)
  - Jika paid: tampilkan info tanggal bayar
  - Tombol hapus (hanya jika unpaid)

---

### 6. CRUD Pengeluaran (Expenses)

#### [NEW] AdminExpense.php

Controller CRUD:
- `index(Request $request)` — Filter by bulan (`Y-m`), default bulan ini
- `postHandler()` → `store()`, `update()`, `destroy()`
- Validasi: `category_id` required, `amount` required numeric, `expense_date` required date, `description` nullable
- Pass `categories` ke view untuk dropdown

#### [NEW] expense.blade.php

View tabel + modal + filter bulan:
- Kategori (badge warna berbeda untuk rutin vs insidental)
- Nominal (format Rupiah)
- Tanggal
- Keterangan
- Action (edit, hapus)
- **Total pengeluaran bulan** ditampilkan di bawah tabel

---

### 7. Dashboard Report & Grafik

#### [MODIFY] AdminHome.php

Tambahkan data untuk dashboard:

##### Summary Cards:
- **Saldo Kas** = Total pemasukan (all time) - Total pengeluaran (all time)
- **Pemasukan Bulan Ini** = SUM `payment_bills.amount` WHERE `status = paid` AND `billing_month` = bulan ini
- **Pengeluaran Bulan Ini** = SUM `expenses.amount` WHERE month(`expense_date`) = bulan ini
- **Tagihan Belum Lunas** = COUNT `payment_bills` WHERE `status = unpaid`

##### Grafik 1 Tahun (Chart.js):
- Data: 12 bulan terakhir
- 2 dataset: Pemasukan (hijau) vs Pengeluaran (merah)
- Query:
  ```php
  // Pemasukan per bulan (12 bulan terakhir)
  PaymentBill::where('status', 'paid')
    ->where('billing_month', '>=', now()->subMonths(11)->startOfMonth())
    ->selectRaw("DATE_FORMAT(billing_month, '%Y-%m') as month, SUM(amount) as total")
    ->groupBy('month')
    ->orderBy('month')
    ->get();

  // Pengeluaran per bulan
  Expense::where('expense_date', '>=', now()->subMonths(11)->startOfMonth())
    ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
    ->groupBy('month')
    ->orderBy('month')
    ->get();
  ```

> **IMPORTANT:** Database menggunakan SQLite, jadi query `DATE_FORMAT` perlu diganti ke `strftime('%Y-%m', column)`. Perlu dicek `.env` → `DB_CONNECTION`.

#### [MODIFY] View dashboard (admin home)
- Tambahkan 4 summary cards di atas
- Tambahkan canvas Chart.js untuk grafik bar/line pemasukan vs pengeluaran
- Library Chart.js bisa di-include via CDN di layout atau di halaman

---

### 8. Detail Report Bulanan

#### [NEW] AdminFinanceReport.php

> **NOTE:** Buat controller baru `AdminFinanceReport.php` agar tidak bentrok dengan `AdminReport.php` yang sudah ada (untuk user report).

**Method:** `index(Request $request)`
- Input: `month` (format `Y-m`, default bulan ini)
- Data yang ditampilkan:
  - **Ringkasan:** Total pemasukan, total pengeluaran, selisih
  - **Tabel Pemasukan:** List tagihan yang sudah dibayar di bulan tersebut (rumah, jenis, nominal, tgl bayar)
  - **Tabel Pengeluaran:** List expense di bulan tersebut (kategori, nominal, tgl, keterangan)

#### [NEW] finance-report.blade.php

- Filter bulan (month picker)
- 3 summary cards: Pemasukan | Pengeluaran | Selisih
- Tab atau section: Pemasukan / Pengeluaran
- Masing-masing tabel dengan total di bawah

---

### 9. Routes

#### [MODIFY] web.php

```php
// Dalam group admin middleware
Route::get('/fee-type', [AdminFeeType::class, 'index']);
Route::post('/fee-type', [AdminFeeType::class, 'postHandler']);

Route::get('/expense-category', [AdminExpenseCategory::class, 'index']);
Route::post('/expense-category', [AdminExpenseCategory::class, 'postHandler']);

Route::get('/payment-bill', [AdminPaymentBill::class, 'index']);
Route::post('/payment-bill', [AdminPaymentBill::class, 'postHandler']);
Route::post('/payment-bill/generate', [AdminPaymentBill::class, 'generate']);
Route::post('/payment-bill/pay', [AdminPaymentBill::class, 'pay']);
Route::post('/payment-bill/pay-bulk', [AdminPaymentBill::class, 'payBulk']);

Route::get('/expense', [AdminExpense::class, 'index']);
Route::post('/expense', [AdminExpense::class, 'postHandler']);

Route::get('/finance-report', [AdminFinanceReport::class, 'index']);
```

API routes:
```php
Route::get('fee-type/{data:id}', [APIController::class, 'feeType']);
Route::get('expense-category/{data:id}', [APIController::class, 'expenseCategory']);
Route::get('payment-bill/{data:id}', [APIController::class, 'paymentBill']);
Route::get('expense/{data:id}', [APIController::class, 'expense']);
```

---

### 10. Sidebar Navigation

#### [MODIFY] admin-sidebar.blade.php

Tambahkan menu group baru:

```
Data Master
├── Pegawai
├── Kantor
├── Rumah
├── Jenis Iuran        ← NEW
└── Kategori Pengeluaran ← NEW

Keuangan                ← NEW GROUP
├── Tagihan Bulanan     ← NEW
├── Pengeluaran         ← NEW
└── Laporan Keuangan    ← NEW
```

---

## Urutan Implementasi

| Step | Task | Estimasi |
|------|------|----------|
| 1 | Update semua Models (relationships + casts) | Ringan |
| 2 | Seeder fee_types & expense_categories | Ringan |
| 3 | CRUD Fee Types | Sedang |
| 4 | CRUD Expense Categories | Sedang |
| 5 | CRUD Expenses | Sedang |
| 6 | CRUD Payment Bills + Generate + Pay + Pay Bulk | **Berat** |
| 7 | Dashboard Report + Grafik Chart.js | Sedang |
| 8 | Detail Report Bulanan | Sedang |
| 9 | Update Sidebar | Ringan |
| 10 | Seeder data contoh tagihan & pengeluaran | Ringan |

---

## Verification Plan

### Automated Tests
```bash
php artisan migrate:fresh --seed
```
- Pastikan seeder jalan tanpa error
- Cek data fee_types, expense_categories terisi

### Manual Verification (Browser)
1. Buka `/admin/fee-type` → CRUD jenis iuran
2. Buka `/admin/expense-category` → CRUD kategori pengeluaran
3. Buka `/admin/payment-bill` → Generate tagihan → Bayar → Bayar 1 tahun
4. Buka `/admin/expense` → CRUD pengeluaran
5. Buka `/admin` (dashboard) → Cek grafik & summary cards
6. Buka `/admin/finance-report` → Cek detail per bulan
7. Cek generate tagihan tidak duplikat jika dijalankan 2x di bulan yang sama
