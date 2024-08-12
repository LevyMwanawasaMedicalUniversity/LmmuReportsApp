<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LMMAXCATypeMarksAllocation extends Model
{
    use HasFactory;

    protected $connection = 'assessments_database'; // Database connection name
    protected $table = 'c_a_type_marks_allocations';
}
