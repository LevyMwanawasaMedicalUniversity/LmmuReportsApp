<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleUserEnrolments extends Model
{
    use HasFactory;

    protected $connection = 'moodle_database';
    protected $table = 'mdl_user_enrolments';
    public $timestamps = false;

    protected $fillable = [
        'enrolid',
        'userid',
        'status',
        'timestart',
        'timeend',
        'modifierid',
        'timecreated',
        'timemodified',
    ];
}
