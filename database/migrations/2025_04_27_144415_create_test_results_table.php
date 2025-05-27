<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('test_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID користувача
        $table->foreignId('test_id')->constrained()->onDelete('cascade'); // ID тесту
        $table->integer('score'); // Результат тесту (кількість правильних відповідей)
        $table->timestamps(); // Додати поля created_at і updated_at
    });
}

public function down()
{
    Schema::dropIfExists('test_results');
}
};
