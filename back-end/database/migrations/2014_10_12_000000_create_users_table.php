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
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('address')->nullable();
            $table->string('genderId')->nullable();
            $table->string('phonenumber')->nullable();
            $table->binary('image')->nullable();
            $table->string('dob')->nullable();
            $table->string('roleId')->nullable();
            $table->string('statusId')->nullable();
            $table->timestamp('deleted_at')->nullable(); // Corrected column name
            $table->boolean('isActiveEmail')->default(false);
            $table->string('usertoken')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
