<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $primaryKey = 'answer_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'answer_id',
        'attempt_id',
        'question_id',
        'option_id',
        'answer_text',
        'is_correct',
    ];

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(QuizOption::class, 'option_id');
    }
}
