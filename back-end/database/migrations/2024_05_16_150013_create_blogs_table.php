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
        Schema::create('Blogs', function (Blueprint $table) {
            $table->id();
            $table->text('shortdescription')->nullable();
            $table->string('title');
            $table->string('subjectId');
            $table->string('statusId');
            $table->binary('image');
            $table->longText('contentMarkdown');
            $table->longText('contentHTML');
            $table->unsignedBigInteger('userId');
            $table->integer('view')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
