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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('deposit_number')->unique();
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('metal_type_id')->constrained('metal_types');
            $table->enum('storage_type', ['allocated', 'unallocated']);
            $table->decimal('quantity_kg', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
