<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleEnroll extends Model
{
    use HasFactory;

    protected $connection = 'moodle_database';
    protected $table = 'mdl_enrol';
}
