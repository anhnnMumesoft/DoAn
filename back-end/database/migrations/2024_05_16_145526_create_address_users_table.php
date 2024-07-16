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
        Schema::create('AddressUsers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('ship_name')->nullable();
            $table->string('ship_address')->nullable();
            $table->string('ship_email')->nullable();
            $table->string('ship_phonenumber')->nullable();
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
        Schema::dropIfExists('AddressUsers');
    }
};
