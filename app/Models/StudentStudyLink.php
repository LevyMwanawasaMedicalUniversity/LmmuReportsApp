<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStudyLink extends Model
{
    use HasFactory;

    protected $connection = 'edurole_database';
    protected $table = 'student-study-link';
}
