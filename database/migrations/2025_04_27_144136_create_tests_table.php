<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id(); // Поле для унікального ідентифікатора тесту
            $table->string('title'); // Назва тесту
            $table->text('description')->nullable(); // Опис тесту (не обов'язкове)
            $table->timestamps(); // Технічні поля для створення та оновлення записів
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
