<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LMMAXAssessmentTypes extends Model
{
    use HasFactory;

    protected $connection = 'assessments_database'; // Database connection name
    protected $table = 'assessment_types';
}
