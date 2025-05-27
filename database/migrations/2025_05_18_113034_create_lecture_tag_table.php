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
       Schema::create('lecture_tag', function (Blueprint $table) {
            $table->id();

            // Зовнішні ключі
            $table->foreignId('lecture_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // Унікальна комбінація lecture_id + tag_id, щоб уникати дублікатів
            $table->unique(['lecture_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('lecture_tag');
    }
};
