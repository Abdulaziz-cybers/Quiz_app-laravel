<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizImage extends Model
{
    protected $fillable = [
        'quiz_id',
        'image_path',
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

}
