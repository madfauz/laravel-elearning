<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $primaryKey = 'quiz_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    protected $fillable = [
        'quiz_id',
        'course_id',
        'title',
        'description',
        'time_limit',
        'start_time',
        'end_time',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    public function getEndTimeFormattedAttribute(): string
    {
        return $this->end_time
            ? $this->end_time->format('l d/m/Y H:i')
            : '-';
    }

    public function getStartTimeFormattedAttribute(): string
    {
        return $this->start_time
            ? $this->start_time->format('l d/m/Y H:i')
            : '-';
    }
}
