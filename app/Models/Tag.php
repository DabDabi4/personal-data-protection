<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
     protected $fillable = ['name'];

    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }
    public function lectures()
{
    return $this->belongsToMany(Lecture::class);
}

}
