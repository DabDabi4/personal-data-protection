<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    'description',
    'module_id',
    'file_url', // обов'язково додайте це!
    'video_url',
    'order',
    ];
        public function module()
    {
        return $this->belongsTo(Module::class);
    }
    public function scopeOrdered($query)
{
    return $query->orderBy('order');
}
public function tags()
{
    return $this->belongsToMany(Tag::class);
}

}
