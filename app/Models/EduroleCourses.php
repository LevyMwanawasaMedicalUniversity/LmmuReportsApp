<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EduroleCourses extends Model
{
    use HasFactory;

    protected $connection = 'edurole_database';
    protected $table = 'courses';

    // Your BasicInformation model properties and methods

    
}
