<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_enrollment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'course_enrollment_id',
        'course_id',
        'student_id',
        'joined_at',
    ];

    protected $dates = [
        'joined_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
