<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();                         // Snapshot penghuni saat tagihan dibuat
            $table->foreignId('fee_type_id')->constrained('fee_types')->restrictOnDelete();
 
            $table->date('billing_month');                  // Format: YYYY-MM-01 (awal bulan)
            $table->decimal('amount', 10, 2);               // Nominal tagihan (bisa berbeda dari default fee_type)
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
 
            $table->timestamps();
 
            // Satu rumah tidak boleh punya 2 tagihan jenis yang sama di bulan yang sama
            $table->unique(['house_id', 'fee_type_id', 'billing_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_bills');
    }
};
