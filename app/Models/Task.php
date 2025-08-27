<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'content',
        'file_path',
    ];
}
