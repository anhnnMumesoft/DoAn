<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up(): void
    {
        Schema::create('Productdetails', function (Blueprint $table) {
            $table->id();
            $table->integer('productId')->nullable();
            $table->text('description')->nullable();
            $table->string('nameDetail')->nullable();
            $table->bigInteger('originalPrice')->nullable();
            $table->bigInteger('discountPrice')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down(): void
    {
        Schema::dropIfExists('Productdetails');
    }
};
