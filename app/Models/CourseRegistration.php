<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    use HasFactory;

    protected $table = 'course_registration';

    protected $fillable = [
        'StudentID',
        'CourseID', 
        'EnrolmentDate',
        'Approved',
        'PeriodID',
        'Year',
        'Semester',
        'Moodle',
        'LateRegistration',
    ];
}
