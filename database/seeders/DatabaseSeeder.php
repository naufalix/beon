<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\House;
use App\Models\Resident;
use App\Models\FeeType;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Admin::create([
            "name" => "Naufal Ulinnuha",  
            "email" => "admin@naufal.dev",  
            "password" => bcrypt('admin'),
            "role" => 'admin'
        ]);

        $houses = json_decode(File::get("database/data/houses.json"));
        foreach ($houses as $key => $value) {
            House::create([
                "id" => $value->id,
                "house_number" => $value->house_number,
                "address" => $value->address,
                "status" => $value->status,
            ]);
        }

        $residents = json_decode(File::get("database/data/residents.json"));
        foreach ($residents as $key => $value) {
            Resident::create([
                "house_id" => $value->house_id,
                "full_name" => $value->full_name,
                "status" => $value->status,
                "phone_number" => $value->phone_number,
                "is_married" => $value->is_married,
                "is_head_of_family" => $value->is_head_of_family ?? false,
                "is_active_resident" => $value->is_active_resident,
                "move_in_date" => $value->move_in_date,
                "move_out_date" => $value->move_out_date,
            ]);
        }

        // Seed Fee Types
        FeeType::create(['name' => 'Satpam', 'amount' => 100000, 'is_active' => true]);
        FeeType::create(['name' => 'Kebersihan', 'amount' => 15000, 'is_active' => true]);
        FeeType::create(['name' => 'Perbaikan', 'amount' => 50000, 'is_active' => true]);

        // Seed Expense Categories
        ExpenseCategory::create(['name' => 'Gaji Satpam', 'is_recurring' => true]);
        ExpenseCategory::create(['name' => 'Token Listrik Pos', 'is_recurring' => true]);
        ExpenseCategory::create(['name' => 'Perbaikan Jalan', 'is_recurring' => false]);
        ExpenseCategory::create(['name' => 'Perbaikan Selokan', 'is_recurring' => false]);
        ExpenseCategory::create(['name' => 'Lain-lain', 'is_recurring' => false]);

        // Seed Payment Bills, Payments, and Expenses
        $this->call(PaymentBillSeeder::class);
    }
}
