<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'time_limit'
    ];
    public function questions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Question::class);
    }
    public function images()
    {
        return $this->hasMany(QuizImage::class);
    }
}
