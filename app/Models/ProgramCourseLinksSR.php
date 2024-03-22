<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCourseLinksSR extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'course_id',
        'mandatory',
        'year',
        'pcl_id'
    ];
}
