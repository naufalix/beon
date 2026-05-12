<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\Resident;
use App\Models\FeeType;
use App\Models\ExpenseCategory;
use App\Models\PaymentBill;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class PaymentBillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load data dari JSON
        $housesData = json_decode(File::get("database/data/houses.json"), true);
        $residentsData = json_decode(File::get("database/data/residents.json"), true);

        // Get fee types dan expense categories dari database
        $feeTypes = FeeType::where('is_active', true)->get();
        $expenseCategories = ExpenseCategory::all();
        $recurringExpenses = ExpenseCategory::where('is_recurring', true)->get();

        // Periode: 12 bulan ke belakang dari sekarang (exclude bulan sekarang)
        $currentMonth = Carbon::now()->startOfMonth();
        $endMonth = $currentMonth->copy()->subMonth(); // Bulan lalu
        $startMonth = $endMonth->copy()->subMonths(11); // 12 bulan ke belakang

        echo "Generating payment bills and expenses from {$startMonth->format('Y-m')} to {$endMonth->format('Y-m')}\n";

        // Loop untuk setiap bulan
        for ($month = $startMonth->copy(); $month <= $endMonth; $month->addMonth()) {
            echo "Processing month: {$month->format('Y-m')}\n";

            // 1. Generate Payment Bills untuk semua rumah
            foreach ($housesData as $houseData) {
                $house = House::find($houseData['id']);
                
                if (!$house) continue;

                // Get residents untuk rumah ini
                $houseResidents = array_filter($residentsData, function($r) use ($houseData) {
                    return $r['house_id'] == $houseData['id'] && $r['is_active_resident'];
                });

                // Jika rumah vacant
                if ($houseData['status'] === 'vacant') {
                    // 30% chance untuk membuat tagihan dan membayar
                    if (rand(1, 100) <= 30) {
                        // Ambil resident pertama yang aktif (jika ada)
                        $resident = !empty($houseResidents) ? Resident::where('house_id', $house->id)
                            ->where('is_active_resident', true)
                            ->first() : null;

                        if ($resident) {
                            foreach ($feeTypes as $feeType) {
                                $bill = PaymentBill::create([
                                    'house_id' => $house->id,
                                    'resident_id' => $resident->id,
                                    'fee_type_id' => $feeType->id,
                                    'billing_month' => $month->format('Y-m-d'),
                                    'amount' => $feeType->amount,
                                    'status' => 'paid',
                                ]);

                                // Buat payment
                                Payment::create([
                                    'bill_id' => $bill->id,
                                    'paid_at' => $month->copy()->addDays(rand(1, 28)),
                                    'payment_method' => rand(0, 1) ? 'cash' : 'transfer',
                                    'note' => 'Pembayaran rumah vacant',
                                ]);
                            }
                        }
                    }
                } 
                // Jika rumah occupied
                else {
                    if (empty($houseResidents)) continue;

                    // Ambil semua resident aktif untuk rumah ini
                    $activeResidents = Resident::where('house_id', $house->id)
                        ->where('is_active_resident', true)
                        ->get();

                    if ($activeResidents->isEmpty()) continue;

                    // Pilih resident secara random (tidak selalu kepala keluarga)
                    $selectedResident = $activeResidents->random();

                    foreach ($feeTypes as $feeType) {
                        // Buat tagihan
                        $bill = PaymentBill::create([
                            'house_id' => $house->id,
                            'resident_id' => $selectedResident->id,
                            'fee_type_id' => $feeType->id,
                            'billing_month' => $month->format('Y-m-d'),
                            'amount' => $feeType->amount,
                            'status' => 'unpaid',
                        ]);

                        // 100% dibayar untuk rumah occupied
                        $bill->update(['status' => 'paid']);

                        // Buat payment
                        Payment::create([
                            'bill_id' => $bill->id,
                            'paid_at' => $month->copy()->addDays(rand(1, 28)),
                            'payment_method' => rand(0, 1) ? 'cash' : 'transfer',
                            'note' => null,
                        ]);
                    }
                }
            }

            // 2. Generate Expenses untuk bulan ini
            // Expense recurring harus ada setiap bulan
            foreach ($recurringExpenses as $category) {
                $amount = $this->getExpenseAmount($category->name);
                
                Expense::create([
                    'category_id' => $category->id,
                    'amount' => $amount,
                    'description' => "Pengeluaran rutin {$category->name} bulan {$month->format('F Y')}",
                    'expense_date' => $month->copy()->addDays(rand(1, 28)),
                ]);
            }

            // Expense non-recurring: 1-2 per bulan (dikurangi dari 2-5)
            $nonRecurringCount = rand(1, 2);
            $nonRecurringCategories = ExpenseCategory::where('is_recurring', false)->get();

            for ($i = 0; $i < $nonRecurringCount; $i++) {
                $category = $nonRecurringCategories->random();
                $amount = rand(50000, 150000); // Maksimal 150 ribu

                Expense::create([
                    'category_id' => $category->id,
                    'amount' => $amount,
                    'description' => $this->getExpenseDescription($category->name),
                    'expense_date' => $month->copy()->addDays(rand(1, 28)),
                ]);
            }
        }

        echo "Seeding completed!\n";
    }

    /**
     * Get realistic expense amount based on category name
     */
    private function getExpenseAmount($categoryName)
    {
        $amounts = [
            'Gaji Satpam' => 1500000, // Flat 1.5 juta
            'Token Listrik Pos' => rand(50000, 100000), // Range 50k - 100k
            'Perbaikan Jalan' => rand(100000, 150000), // Dikurangi, maksimal 150k
            'Perbaikan Selokan' => rand(80000, 150000), // Dikurangi, maksimal 150k
            'Lain-lain' => rand(50000, 150000), // Maksimal 150k
        ];

        return $amounts[$categoryName] ?? rand(50000, 150000);
    }

    /**
     * Get realistic expense description
     */
    private function getExpenseDescription($categoryName)
    {
        $descriptions = [
            'Perbaikan Jalan' => [
                'Perbaikan jalan berlubang di blok A',
                'Pengaspalan jalan utama',
                'Perbaikan jalan rusak akibat hujan',
            ],
            'Perbaikan Selokan' => [
                'Pembersihan selokan tersumbat',
                'Perbaikan selokan bocor',
                'Normalisasi selokan',
            ],
            'Lain-lain' => [
                'Pembelian alat kebersihan',
                'Biaya administrasi',
                'Pembelian perlengkapan pos',
                'Biaya rapat RT',
            ],
        ];

        if (isset($descriptions[$categoryName])) {
            return $descriptions[$categoryName][array_rand($descriptions[$categoryName])];
        }

        return "Pengeluaran {$categoryName}";
    }
}
