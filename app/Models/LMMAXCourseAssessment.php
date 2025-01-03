<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LMMAXCourseAssessment extends Model
{
    use HasFactory;

    protected $connection = 'assessments_database'; // Database connection name
    protected $table = 'course_assessments';
}
