<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('Voucheruseds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucherId')->constrained('vouchers')->onDelete('cascade'); // Corrected table name
            $table->foreignId('userId')->constrained('users')->onDelete('cascade');
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('voucheruseds');
    }
};
