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
        Schema::create('Banners', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('name');
            $table->string('statusId');
            $table->binary('image'); // Laravel không có kiểu BLOB 'long', sử dụng binary thay thế
            $table->timestamps(); // Tự động tạo các cột createdAt và updatedAt
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('Banners');
    }
};
