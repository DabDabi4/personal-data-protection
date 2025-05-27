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
    Schema::table('tests', function (Blueprint $table) {
        $table->string('file_url')->nullable(); // додаємо нове поле
    });
}

public function down(): void
{
    Schema::table('tests', function (Blueprint $table) {
        $table->dropColumn('file_url'); // забираємо поле, якщо відкат
    });
}

};
