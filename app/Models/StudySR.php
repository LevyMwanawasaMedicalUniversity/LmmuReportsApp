<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudySR extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_name',
        'study_shortname',
        'parent_id',
        'study_id'
    ];
}
