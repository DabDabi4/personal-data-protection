<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'file_url'];

    // Відношення до тестових результатів
    public function results()
    {
        return $this->hasMany(TestResult::class);
    }
    // App\Models\Test.php
public function tags()
{
    return $this->belongsToMany(Tag::class);
}

}

