<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'test_id', 'score','user_answers'];

    protected $casts = [
        'user_answers' => 'array',
    ];
    // Відношення до тесту
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    // Відношення до користувача
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

