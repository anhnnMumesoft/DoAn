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
        Schema::create('ProductDetailSizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productdetail_id')->nullable();
            $table->string('width');
            $table->string('height');
            $table->string('weight');
            $table->string('size_id');
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
        Schema::dropIfExists('ProductDetailSizes');
    }
};
