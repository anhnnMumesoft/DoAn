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
        Schema::create('Products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('contentHTML')->nullable();
            $table->text('contentMarkdown')->nullable();
            $table->string('statusId')->nullable();
            $table->string('categoryId')->nullable();
            $table->integer('view')->default(0);
            $table->string('madeby')->nullable();
            $table->string('material')->nullable();
            $table->string('brandId')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
