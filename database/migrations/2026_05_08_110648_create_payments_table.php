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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('payment_bills')->cascadeOnDelete();
 
            $table->datetime('paid_at');                    // Tanggal & jam pembayaran
            $table->string('payment_method')->nullable();   // Transfer, cash, dll
            $table->text('note')->nullable();               // Catatan tambahan
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
