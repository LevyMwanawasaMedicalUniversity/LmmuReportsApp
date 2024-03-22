<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStudyLinkSR extends Model
{
    use HasFactory;

    protected $fillable = [
        'ssl_id',
        'student_id',
        'study_id'
    ];
}
