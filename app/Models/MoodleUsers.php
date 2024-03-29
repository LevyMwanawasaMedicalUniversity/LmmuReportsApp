<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleUsers extends Model
{
    use HasFactory;

    protected $connection = 'moodle_database';
    protected $table = 'mdl_user';
    public $timestamps = false;

    protected $fillable = [
        'confirmed',
        'policyagreed',
        'deleted',
        'suspended',
        'mnethostid',
        'username',
        'idnumber',
        'firstname',
        'lastname',
        'email',
        'emailstop',
        'calendertype',
        'auth',
        'lang',
        'timemodified',
        'timecreated',
    ];
}
