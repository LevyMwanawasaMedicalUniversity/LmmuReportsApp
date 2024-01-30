<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradesPublished extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'userdate',    
        'usertime', 
        'StudentNo',
        'AcademicYear',
        'Semester',
        'ProgramNo',
        'CourseNo',
        'CAMarks',
        'ExamMarks',
        'TotalMarks',
        'Grade',
        'Points',
        'Comment',
        'KeySet',
        'Published',
        'PeriodID',
    ];

}
