<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_material_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'course_material_id',
        'course_id',
        'title',
        'description',
        'content_text',
        'file_path',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
