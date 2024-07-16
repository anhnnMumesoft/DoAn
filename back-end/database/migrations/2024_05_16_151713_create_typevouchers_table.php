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
        Schema::create('Typevouchers', function (Blueprint $table) {
            $table->id();
            $table->string('typeVoucher');
            $table->bigInteger('value');
            $table->bigInteger('maxValue');
            $table->bigInteger('minValue');
            $table->timestamps(); // Tự động thêm createdAt và updatedAt
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
        Schema::dropIfExists('typevouchers');
    }
};
