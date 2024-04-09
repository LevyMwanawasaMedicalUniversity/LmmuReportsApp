<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NMCZRepeatCourses extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'academic_year',
        'studnent_number',
    ];
}
