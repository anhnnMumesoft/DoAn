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
        Schema::create('Vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('fromDate')->nullable();
            $table->string('toDate')->nullable();
            $table->integer('typeVoucherId')->nullable();
            $table->integer('amount')->nullable();
            $table->string('codeVoucher')->nullable();
            $table->timestamps(); // Tự động tạo createdAt và updatedAt
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
        Schema::dropIfExists('vouchers');
    }
};
