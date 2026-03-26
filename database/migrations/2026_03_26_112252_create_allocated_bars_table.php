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
        Schema::create('allocated_bars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('deposit_id')->constrained('deposits');
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('metal_type_id')->constrained('metal_types');
            $table->string('serial_number')->unique();
            $table->decimal('weight_kg', 10, 2);
            $table->enum('status', ['allocated', 'unallocated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocated_bars');
    }
};
