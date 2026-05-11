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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->nullable()->constrained('houses')->nullOnDelete();
 
            $table->string('full_name');
            $table->string('ktp_photo')->nullable();
            $table->enum('status', ['permanent', 'contract']);
            $table->string('phone_number')->nullable();
            $table->boolean('is_married')->default(false);
 
            $table->date('move_in_date')->nullable(); 
            $table->date('move_out_date')->nullable();
            $table->boolean('is_active_resident')->default(false);
            $table->boolean('is_head_of_family')->default(false);
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
